<?php

namespace app\controllers\site;

use framework\request\Request;
use framework\session\Session;
use framework\security\Csrf;
use app\components\Controller;
use app\models\Product;
use app\models\Module;
use app\models\Card;
use app\models\Trans;
use app\models\TransCard;
use app\models\TransInfo;
use app\models\forms\OrderForm;

class ProductController extends Controller
{
	/**
	 * order form (each products)
	 *
	 * @param integer $productId , product id (primary key)
	 * @param string $productTag , product tag (seo)
	 *
	 * @access public
	 * @use http://example.com/index.php/site/product/index/{productId}/{productTag}
	 * @return void
	 */
	public function actionIndex( $productId = 0, $productTag = '' )
	{
		$session = Session::instance();

		$model = new OrderForm;

		$product = Product::model()->getProduct( $productId );

		if( $product ) {
			$modules = Module::model()->findAll( [ 'conditions' => [ 'moduleType = 0', 'moduleStatus = 1' ] ] );
		} else {
			$session->setFlash( 'danger', $this->lang->getIndex( 'order', 'productNotFound' ) );
		}

		if( Request::isPostRequest() and $product and Csrf::validate( Request::getPost('csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );
			$model->product = $productId;

			if( $model->validate() ) {

				$module = Module::model()->find( [ 'conditions' => [ 'moduleId = :id', 'moduleType = 0', 'moduleStatus = 1' ], 'params' => [ ':id' => $model->gateway ] ] );
				if( !$module ) {
					$session->setFlash( 'danger', $this->lang->getIndex( 'order', 'gatewayNotFound' ) );
				} else {
					 Card::model()->lock();

					$totalCards =  Card::model()->getTotalProductCards( $model->product );
					if( $totalCards->total < $model->quantity ) {
						$session->setFlash( 'danger', str_replace( '{total}', $totalCards->total, $this->lang->getIndex( 'order', 'quantityNotAvailable' ) ) );
						Card::model()->unlock();
					} else {
						$cards =  Card::model()->getProductCards( $model->product, $model->quantity );

						$nowTime = time();
						foreach( $cards as $card ) {
							Card::model()->update( [ 'fields' => [ 'cardReserveDate' => ':date' ], 'conditions' => 'cardId = :id', 'params' => [ ':date' => ($nowTime+$this->reserve_second), ':id' => $card->cardId ] ] );
						}
						
						Card::model()->unlock();

						$lastId = Trans::model()->find( [ 'select' => 'MAX(transId) as last' ] );
						$au = $this->random( 6, $lastId->last+1 );
						$transId = Trans::model()->insertData( $au, ($nowTime+$this->reserve_second), $model->product, ($product->productPrice*$model->quantity), $model->quantity, $model->email, $model->mobile, $model->content, $model->gateway );
						TransInfo::model()->insertData( $transId, base64_encode( serialize( $_SERVER ) ) );

						foreach( $cards as $card ) {
							TransCard::model()->insertData( $card->cardId, $transId );
						}

						Request::redirect( \Framework::createUrl( 'site/payment/factor', [ $au ] ) );
					}
				}
			}
		}

		$this->render( 'product/index', [ 'model' => $model, 'product' => $product, 'gateways' => ( $product ? $modules : [] ), 'quantity' => ( $model->quantity ? $model->quantity : 1) ] );
	}
}