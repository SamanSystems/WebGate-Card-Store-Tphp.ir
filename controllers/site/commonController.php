<?php

namespace app\controllers\site;

use framework\request\Request;
use framework\captcha\Captcha;
use framework\session\Session;
use framework\security\Csrf;
use app\components\Controller;
use app\models\Product;
use app\models\Category;
use app\models\Module;
use app\models\Card;
use app\models\Trans;
use app\models\TransCard;
use app\models\TransInfo;
use app\models\Contact;
use app\models\forms\OrderForm;
use app\models\forms\ContactForm;

class CommonController extends Controller
{
	/**
	 * create captcha page
	 *
	 * @access public
	 * @use http://example.com/index.php/site/common/captcha
	 * @return void
	 */
	public function actionCaptcha()
	{
		$captcha = new Captcha( [ 'width' => 80, 'text' => substr( md5( rand() ), 0, 4) ] );

		$captcha->create();
	}

	/**
	 * order page (index) (ajax)
	 *
	 * @access public
	 * @use http://example.com/index.php/site/common/index
	 * @return void
	 */
	public function actionIndex()
	{
		$model = new OrderForm;

		if( Request::isAjaxRequest() ) {
			$model->setAttributes( Request::getPosts() );

			if( !$model->validate() ) {
				\Framework::end( json_encode( [ 'status' => 'error', 'content' => $model->getMessages() ] ) );
			}
			
			$product = Product::model()->find( [ 'conditions' => [ 'productId = :id', 'productStatus = 1' ], 'params' => [ ':id' => $model->product] ] );
			if( !$product ) {
				\Framework::end( json_encode( [ 'status' => 'error', 'content' => [ $this->lang->getIndex( 'order', 'productNotFound' ) ] ] ) );
			}

			$errors = [];

			$module = Module::model()->find( [ 'conditions' => [ 'moduleId = :id', 'moduleType = 0', 'moduleStatus = 1' ], 'params' => [ ':id' => $model->gateway ] ] );
			if( !$module ) {
				$errors[] = $this->lang->getIndex( 'order', 'gatewayNotFound' );
			}
			
			Card::model()->lock();

			$totalCards = Card::model()->getTotalProductCards( $model->product );
			if( $totalCards->total < $model->quantity ) {
				$errors[] = str_replace( '{total}', $totalCards->total, $this->lang->getIndex( 'order', 'quantityNotAvailable' ) );
			}

			if( $errors ) {
				Card::model()->unlock();
				\Framework::end( json_encode( [ 'status' => 'error', 'content' => $errors ] ) );
			}

			$cards = Card::model()->getProductCards( $model->product, $model->quantity );

			$nowTime = time();
			foreach( $cards as $card ) {
				Card::model()->update( [ 'fields' => [ 'cardReserveDate' => ':date' ], 'conditions' => 'cardId = :id', 'params' => [ ':id' => $card->cardId, ':date' => ( $nowTime + $this->reserve_second ) ] ] );
			}
			
			Card::model()->unlock();

			$lastId = Trans::model()->find( [ 'select' => 'MAX(transId) as last' ] );
			$au = $this->random( 6, $lastId->last+1 );

			$transId = Trans::model()->insertData( $au, ($nowTime+$this->reserve_second), $model->product, ( $product->productPrice*$model->quantity ), $model->quantity, $model->email, $model->mobile, $model->content, $model->gateway );
			TransInfo::model()->insertData( $transId, base64_encode( serialize( $_SERVER ) ) );

			foreach( $cards as $card ) {
				TransCard::model()->insertData( $card->cardId, $transId );
			}

			\Framework::end( json_encode( [ 'status' => 'success', 'redirect' => \Framework::createUrl( 'site/payment/factor', [ $au ] ) ] ) );
		}

		$products = Product::model()->getProducts();

		foreach( $products as $product ) {
			$total = Card::model()->getTotalProductCards( $product->productId );
			$product->productHaveCard = $total->total;
		}

		$categories = Category::model()->findAll( [ 'conditions' => [ 'categoryStatus = 1' ], 'orderBy' => 'categoryOrder ASC' ] );
		$gateways = Module::model()->findAll( [ 'conditions' => [ 'moduleType = 0', 'moduleStatus = 1' ] ] );

		$this->render( 'common/index', [ 'products' => $products, 'categories' => $categories, 'gateways' => $gateways ] );
	}

	/**
	 * contact page 
	 *
	 * @access public
	 * @use http://example.com/index.php/site/common/contact
	 * @return void
	 */
	public function actionContact()
	{
		$model = new ContactForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				$contact = Contact::model()->insertData( $model->name, $model->email, $model->subject, $model->content );

				Session::instance()->setFlash( 'success', $this->lang->getIndex( 'contact', 'success' ) );
				$this->refresh();
			}
		}

		$categories = explode( ',', $this->contact_category );
		$categories = array_combine( $categories, $categories );

		$this->render( 'common/contact', [ 'model' => $model, 'categories' => $categories ] );
	}
}