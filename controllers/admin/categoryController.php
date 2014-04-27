<?php

namespace app\controllers\admin;

use framework\pagination\Pagination;
use framework\security\Csrf;
use framework\request\Request;
use framework\session\Session;
use framework\helpers\File;
use app\components\AdminController;
use app\components\UserControl;
use app\models\Category;
use app\models\forms\CategoryForm;

class CategoryController extends AdminController
{
	/**
	 * category list page
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
			foreach( Request::getPost('pick') as $id ) {
				$image = Category::model()->find( [ 'select' => 'categoryImage','conditions' => [ 'categoryId = :id' ], 'params' => [ ':id' => $id ] ] );
				Category::model()->delete( [ 'conditions' => 'categoryId = :id', 'params' => [ ':id' => $id ] ] );

				if( $image and $image->categoryImage ) {
					unlink( BASEPATH . $image->categoryImage );
				}
			}
			
			Session::instance()->setFlash( 'success', $this->lang->getIndex( 'category', 'delete' ) );
		}

		$pagination = new Pagination();
		$config['fullRows'] = Category::model()->find( [ 'select' => 'COUNT(*) as total' ] )->total;
		$config['itemLimit'] = $this->per_page;
		$pagination->initialize( $config );

		$categories = Category::model()->findAll( [ 'orderBy' => 'categoryOrder ASC', 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );

		$this->render( 'category/index', [ 'pagination' => $pagination, 'categories' => $categories ] );
	}

	/**
	 * create new category and (upload image)
	 *
	 * @access public
	 * @return void
	 */
	public function actionNew()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$model = new CategoryForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );
			$model->categoryImage = $_FILES['categoryImage'];

			if( $model->validate() ) {
				$allow = [ 'image/jpg', 'image/png', 'image/gif', 'image/jpeg' ];

				$session = Session::instance();

				$errors = false; $image = null;

				if( $model->categoryImage['name'] ) {
					$upload = File::upload( $model->categoryImage, BASEPATH . $this->upload_path, $allow );

					if( $model->categoryImage['error'] > 0 ) {
						$session->setFlash( 'danger', $this->lang->getIndex( 'category', 'uploadError' ) );
						$errors = true;
					} else if( $upload == 'pathNotValid' ) {
						$session->setFlash( 'danger', $this->lang->getIndex( 'category', 'uploadPathNotValid' ) );
						$errors = true;
					} else if( $upload == 'typeNotValid' ) {
						$session->setFlash( 'danger', $this->lang->getIndex( 'category', 'uploadTypeNotValid' ) );
						$errors = true;
					} else {
						$image = $this->upload_path . $upload['name'];
					}
				}

				if( !$errors ) {
					Category::model()->insertData( $model->categoryName, $image, $model->categoryDescription, $model->categoryOrder, $model->categoryStatus );
					$session->setFlash( 'success', $this->lang->getIndex( 'category', 'success' ) );
					$this->refresh();
				}
			}
		}

		$this->render( 'category/new', [ 'model' => $model ] );
	}

	/**
	 * edit category and (upload image)
	 *
	 * @param integer $categoryId, category id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionEdit( $categoryId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$category = Category::model()->find( [ 'conditions' => [ 'categoryId = :id' ], 'params' => [ ':id' => $categoryId ] ] );

		$session = Session::instance();

		if( !$category ) {
			$session->setFlash( 'danger', $this->lang->getIndex( 'category', 'notFound' ) );
		}
		
		$model = new CategoryForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );
			$model->categoryImage = $_FILES['categoryImage'];

			$errors = false; $image = $category->categoryImage;

			if( $model->validate() ) {
				if( $model->categoryImage['name'] ) {
					$allow = [ 'image/jpg', 'image/png', 'image/gif', 'image/jpeg' ];
					$upload = File::upload( $model->categoryImage, BASEPATH . $this->upload_path, $allow );

					if( $model->categoryImage['error'] > 0 ) {
						$session->setFlash( 'danger', $this->lang->getIndex( 'category', 'uploadError' ) );
						$errors = true;
					} else if( $upload == 'pathNotValid' ) {
						$session->setFlash( 'danger', $this->lang->getIndex( 'category', 'uploadPathNotValid' ) );
						$errors = true;
					} else if( $upload == 'typeNotValid' ) {
						$session->setFlash( 'danger', $this->lang->getIndex( 'category', 'uploadTypeNotValid' ) );
						$errors = true;
					} else {
						if( $image ) {
							File::delete( BASEPATH . $image );
						}
						$image = $this->upload_path . $upload['name'];
					}
				}

				if( !$errors ) {
					Category::model()->updateData( $categoryId, $model->categoryName, $image, $model->categoryDescription, $model->categoryOrder, $model->categoryStatus );
					$session->setFlash( 'success', $this->lang->getIndex( 'category', 'update' ) );
					$this->refresh();
				}
			}
		}

		$this->render( 'category/edit', [ 'model' => $model, 'category' => $category ] );
	}
}