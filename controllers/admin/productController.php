<?php

namespace app\controllers\admin;

use framework\pagination\Pagination;
use framework\request\Request;
use framework\security\Csrf;
use framework\session\Session;
use app\components\AdminController;
use app\components\UserControl;
use app\models\Product;
use app\models\Category;
use app\models\forms\ProductForm;

class ProductController extends AdminController
{
	/**
	 * product list and (delete)
	 *
	 * @access public
	 * @return void
	 */
	public function actionIndex()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		if( Request::getPost('pick') and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			foreach( Request::getPost('pick') as $id ) {
				Product::model()->delete( [ 'conditions' => 'productId = :id', 'params' => [ ':id' => $id ] ] );
			}

			Session::instance()->setFlash( 'success', $this->lang->getIndex( 'product', 'delete' ) );
		}

		$pagination = new Pagination();
		$config['fullRows'] = Product::model()->find( [ 'select' => 'COUNT(*) as total' ] )->total;
		$config['itemLimit'] = $this->per_page;
		$pagination->initialize( $config );

		$products = Product::model()->findAll( [ 'orderBy' => 'productId DESC', 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );

		$this->render( 'product/index', [ 'pagination' => $pagination, 'products' => $products ] );
	}

	/**
	 * create new product
	 *
	 * @access public
	 * @return void
	 */
	public function actionNew()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		$model = new ProductForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				Product::model()->insertData( $model->productName, $model->productCategory, $model->productTag, $model->productPrice, $model->productOrder, $model->productStatus );
				Session::instance()->setFlash( 'success', $this->lang->getIndex( 'product', 'success' ) );
				$this->refresh();
			}
		}

		$categories = Category::model()->findAll( [ 'conditions' => [ 'categoryStatus = 1' ], 'orderBy' => 'categoryOrder ASC' ] );

		$this->render( 'product/new', [ 'model' => $model, 'categories' => $categories ] );
	}

	/**
	 * edit product
	 *
	 * @param integer $productId , product id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionEdit( $productId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		$session = Session::instance();

		$product = Product::model()->find( [ 'conditions' => [ 'productId = :id' ], 'params' => [ ':id' => $productId ] ] );

		if( $product ) {
			$categories = Category::model()->findAll( [ 'conditions' => [ 'categoryStatus = 1' ], 'orderBy' => 'categoryOrder ASC' ] );
		} else {
			$session->setFlash( 'danger', $this->lang->getIndex( 'product', 'notFound' ) );
		}

		$model = new ProductForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				Product::model()->updateData( $productId, $model->productName, $model->productCategory, $model->productTag, $model->productPrice, $model->productOrder, $model->productStatus );
				$session->setFlash( 'success', $this->lang->getIndex( 'product', 'update' ) );
				$this->refresh();
			}
		}

		$this->render( 'product/edit', [ 'model' => $model, 'categories' => ( isset( $categories ) ? $categories : [ ] ), 'product' => $product ] );
	}
}