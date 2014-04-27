<?php

namespace app\controllers\site;

use framework\session\Session;
use app\components\Controller;
use app\models\Trans;
use app\models\Card;
use app\models\Module;

class PaymentController extends Controller
{
	public $defaultAction = 'factor';

	/**
	 * transaction factor page
	 *
	 * @param integer $transAu , transaction authority code
	 * @access public
	 * @return void
	 */
	public function actionFactor( $transAu = 0 )
	{
		$trans = Trans::model()->getTrans( $transAu );

		$valid = $this->transValidator( $trans );

		if( $valid ) {
			$timer = ( $trans->transReserveDate-time() );
		}

		$this->render( 'payment/factor', [ 'trans' => $trans, 'timer' => ( isset( $timer ) ? $timer : 0), 'valid' => $valid ] );
	}

	/**
	 * payment request (connect to gateway)
	 *
	 * @param integer $transAu , transaction authority code
	 * @access public
	 * @return void
	 */
	public function actionRequest( $transAu = 0 )
	{
		$trans = Trans::model()->getTrans( $transAu );

		$valid = $this->transValidator( $trans );

		if( $valid ) {
			$path = 'app/components/modules/payment/';

			\Framework::import( BASEPATH . $path . $trans->moduleFileName, true );
			$namespace = str_replace( '/', '\\', $path ) . ucfirst( $trans->moduleFileName );
			$class = new $namespace();

			$class->request( $trans->transId, $trans->transAu, $trans->transPrice, @unserialize( $trans->moduleData ), $trans->productName );
		}

		$this->render( 'payment/request' );
	}

	/**
	 * payment verify (connect to gateway and verify payment)
	 *
	 * @param integer $transAu , transaction authority code
	 * @access public
	 * @return void
	 */
	public function actionVerify( $transAu = 0 )
	{
		$trans = Trans::model()->getTrans( $transAu );

		$valid = $this->transValidator( $trans );

		if($valid) {
			$path = 'app/components/modules/payment/';

			\Framework::import( BASEPATH . $path . $trans->moduleFileName, true );
			$namespace = str_replace( '/', '\\', $path ) . ucfirst( $trans->moduleFileName );
			$class = new $namespace();

			$result = $class->verify( $trans->transId, $trans->transAu, $trans->transPrice, @unserialize( $trans->moduleData ), $trans->productName );

			if( is_array( $result ) and isset( $result['au'] ) ) {
				Trans::model()->update( [ 'fields' => [ 'transGatewayAu' => ':au', 'transStatus' => 1 ], 'conditions' => 'transId = :id', 'params' => [ ':au' => $result['au'], ':id' => $trans->transId ] ] );
				$success = $trans->transGatewayAu = $result['au'];
				$trans->transStatus = 1;

				$cards = Card::model()->getCardsByTransId( $trans->transId );

				Card::model()->lock();

				foreach( $cards as $card ) {
					Card::model()->update( [ 'fields' => [ 'cardStatus' => 1 ], 'conditions' => 'cardId = :id', 'params' => [ ':id' => $card->cardId ] ] );	
				}

				Card::model()->unlock();

				Session::instance()->setFlash( 'success', $this->lang->getIndex( 'payment', 'successPayment' ) );
			}
		}

		$this->render( 'payment/verify', [ 'trans' => $trans, 'success' => ( isset( $success ) ? true : false ), 'cards' => ( isset( $cards ) ? $cards : [] ) ] );

		if( isset( $success ) ) {
			set_time_limit(0);

			$modules = Module::model()->findAll( [ 'conditions' => [ 'moduleType = 1', 'moduleStatus = 1'  ] ] );
			foreach( $modules as $module ) {
				$path = 'app/components/modules/notification/';

				\Framework::import( BASEPATH . $path . $module->moduleFileName, true );
				$namespace = str_replace( '/', '\\', $path ) . ucfirst( $module->moduleFileName );

				$class = new $namespace();
				$class->run( $trans, @unserialize( $module->moduleData ), $cards );
			}
		}
	}

	/**
	 * transaction validator
	 *
	 * @param object $trans, transaction information
	 * @access private
	 * @return boolean
	 */
	private function transValidator( $trans )
	{
		$session = Session::instance();

		if( !$trans ) {
			$session->setFlash( 'danger', $this->lang->getIndex( 'payment', 'transNotFound' ) );
		} else if( $trans->transStatus == 1 ) {
			$session->setFlash( 'success', $this->lang->getIndex( 'payment', 'alreadySuccess' ) );
		} else if( $trans->transReserveDate <= time() ) {
			$session->setFlash( 'danger', $this->lang->getIndex( 'payment', 'transExpired' ) );
		} else if( !$trans->moduleId ) {
			$session->setFlash( 'danger', $this->lang->getIndex( 'payment', 'moduleNotFound' ) );
		} else if( !$trans->productId ) {
			$session->setFlash( 'danger', $this->lang->getIndex( 'payment', 'productNotFound' ) );
		} else {
			return true;
		}

		return false;
	}
}