<?php

namespace app\controllers\admin;

use framework\pagination\Pagination;
use framework\request\Request;
use framework\security\Csrf;
use framework\session\Session;
use app\components\AdminController;
use app\components\UserControl;
use app\models\Card;
use app\models\Trans;
use app\models\TransInfo;
use app\models\TransCard;
use app\models\forms\TransSearchForm;

class TransController extends AdminController
{
	/**
	 * transactions list and (delete)
	 *
	 * @access public
	 * @return void
	 */
	public function actionIndex()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		if( Request::getPost( 'pick' ) and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			foreach( Request::getPost( 'pick' ) as $id ) {
				Trans::model()->delete( [ 'conditions' => 'transId = :id', 'params' => [ ':id' => $id ] ] );
				TransInfo::model()->delete( [ 'conditions' => 'transInfoTransId = :id', 'params' => [ ':id' => $id ] ] );
				TransCard::model()->delete( [ 'conditions' => 'transCardTransId = :id', 'params' => [ ':id' => $id ] ] );
			}

			Session::instance()->setFlash( 'success', $this->lang->getIndex( 'trans', 'delete' ) );
		}

		$pagination = new Pagination();
		$config['fullRows'] = Trans::model()->find( [ 'select' => 'COUNT(*) as total' ] )->total;
		$config['itemLimit'] = $this->per_page;
		$pagination->initialize( $config );

		$transactions = Trans::model()->findAll( [ 'orderBy' => 'transId DESC', 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );

		$this->render( 'trans/index', [ 'pagination' => $pagination, 'transactions' => $transactions ] );
	}

	/**
	 * show transaction factor (cards and ...)
	 *
	 * @param integer $transId , trans id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionFactor( $transId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$trans = Trans::model()->getTransFactor( $transId );

		if( $trans ) {
			$trans->transInfoContent = (object)unserialize( base64_decode( $trans->transInfoContent ) );
			if( $trans->transStatus == 1 ) {
				$cards = Card::model()->getCardsByTransId( $transId, 1 );
			}
		} else {
			Session::instance()->setFlash( 'danger', $this->lang->getIndex( 'trans', 'notFound' ) );
		}

		$this->render( 'trans/factor', [ 'trans' => $trans, 'cards' => ( isset( $cards ) ? $cards : [] ) ] );
	}

	/**
	 * search trans with au,gateway au,email,status
	 *
	 * @access public
	 * @return void
	 */
	public function actionSearch()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		$model = new TransSearchForm;

		if( Request::isGetRequest() and Request::getQueries() ) {
			$model->setAttributes( Request::getQueries() );

			if( $model->validate() ) {
				$conditions[0] = ''; $params = [];

				if( !empty( $model->transAu ) ) {
					$conditions[0] .= 'transAu = :au AND ';
					$params[':au'] = $model->transAu;
				}

				if( !empty( $model->transGatewayAu ) ) {
					$conditions[0] .= 'transGatewayAu = :gau AND ';
					$params[':gau'] = $model->transGatewayAu;
				}

				if( !empty( $model->transEmail ) ) {
					$conditions[0] .= 'transEmail = :email AND ';
					$params[':email'] = $model->transEmail;
				}

				$conditions[0] .= 'transStatus = :status';
				$params[':status'] = $model->transStatus;

				$pagination = new Pagination();
				$config['fullRows'] = Trans::model()->find( [ 'select' => 'COUNT(*) as total', 'conditions' => $conditions, 'params' => $params ] )->total;
				$config['itemLimit'] = $this->per_page;
				$config['pageVar'] = '&page=';
				$pagination->initialize( $config );

				$transactions = Trans::model()->findAll( [ 'orderBy' => 'transId DESC', 'conditions' => $conditions, 'params' => $params, 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );

				if( !$transactions ) {
					Session::instance()->setFlash( 'danger', $this->lang->getIndex( 'trans', 'transNotFound' ) );
				}
			}
		}

		$this->render( 'trans/search', [ 'model' => $model, 'transactions' => ( isset( $transactions ) ? $transactions : [] ), 'pagination' => ( isset( $pagination ) ? $pagination : [] ) ] );
	}
}