<?php

namespace app\controllers\admin;

use framework\pagination\Pagination;
use framework\request\Request;
use framework\security\Csrf;
use framework\session\Session;
use app\components\AdminController;
use app\components\UserControl;
use app\models\User;
use app\models\forms\UserForm;
use app\models\forms\UserSearchForm;

class UserController extends AdminController
{
	/**
	 * user index page (list of all users) (delete)
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
				User::model()->delete( [ 'conditions' => 'userId = :id', 'params' => [ ':id' => $id ] ] );
			}
			
			Session::instance()->setFlash( 'success', $this->lang->getIndex( 'user', 'delete' ) );
		}

		$pagination = new Pagination();
		$config['fullRows'] = User::model()->find( [ 'select' => 'COUNT(*) as total' ] )->total;
		$config['itemLimit'] = $this->per_page;
		$pagination->initialize( $config );

		$users = User::model()->findAll( [ 'orderBy' => 'userId DESC', 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );
		
		$this->render( 'user/index', [ 'pagination' => $pagination, 'users' => $users ] );
	}

	/**
	 * create new user
	 *
	 * @access public
	 * @return void
	 */
	public function actionNew()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$model = new UserForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			$session = Session::instance();

			if( $model->validate() ) 
			{
				if( User::model()->find( ['conditions' => [ 'userName = :name' ], 'params' => [ ':name' => $model->userName ] ] ) ) {
					$session->setFlash( 'danger', $this->lang->getIndex( 'user', 'uniqueUserName' ) );
				} else if( User::model()->find( [ 'conditions' => [ 'userEmail = :email' ], 'params' => [ ':email' => $model->userEmail ] ] ) ) {
					$session->setFlash( 'danger', $this->lang->getIndex( 'user', 'uniqueEmail' ) );
				} else {
					$salt = UserControl::createSalt();
					$password = UserControl::createPassword( $salt, $model->userPassword );

					User::model()->insertData( $model->userName, $model->userEmail, $password, $salt );
					$session->setFlash( 'success', $this->lang->getIndex( 'user', 'success' ) );
					$this->refresh();
				}
			}
		}

		$this->render( 'user/new', [ 'model' => $model ] );
	}

	/**
	 * edit user information
	 *
	 * @param integer $userId, user id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionEdit( $userId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$model = new UserForm;
		$session = Session::instance();

		$user = User::model()->find( [ 'conditions' => [ 'userId = :id' ], 'params' => [ ':id' => $userId ] ] );

		if(!$user) {
			$session->setFlash( 'danger', $this->lang->getIndex( 'user', 'notFound' ) );
		}
		
		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				$salt = UserControl::createSalt();
				$password = UserControl::createPassword( $salt, $model->userPassword );

				User::model()->updateData( $userId, $salt, $password );
				$session->setFlash( 'success', $this->lang->getIndex( 'user', 'update' ) );
				$this->refresh();
			}
		}
		
		$this->render( 'user/edit', [ 'model' => $model, 'user' => $user ] );
	}

	/**
	 * search users with (username and email)
	 *
	 * @access public
	 * @return void
	 */
	public function actionSearch()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		$model = new UserSearchForm;

		if( Request::isGetRequest() and Request::getQueries() ) {
			$model->setAttributes( Request::getQueries() );

			if( $model->validate() ) {
				$conditions = []; $params = [];

				if( !empty( $model->userName ) ) {
					$conditions[] = 'userName LIKE :name';
					$params[':name'] = "%{$model->userName}%";
				}

				if( !empty( $model->userEmail ) ) {
					$conditions[] = 'userEmail = :email';
					$params[':email'] = $model->userEmail;
				}

				$pagination = new Pagination();
				$config['fullRows'] = User::model()->find( [ 'select' => 'COUNT(*) as total', 'conditions' => $conditions, 'params' => $params ] )->total;
				$config['itemLimit'] = $this->per_page;
				$config['pageVar'] = '&page=';
				$pagination->initialize( $config );

				$users = User::model()->findAll( [  'orderBy' => 'userId DESC','conditions' => $conditions,'params' => $params, 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );
				
				if( !$users ) {
					Session::instance()->setFlash( 'danger', $this->lang->getIndex( 'user', 'notFound' ) );
				}
			}
		}

		$this->render( 'user/search', [ 'model' => $model, 'users' => ( isset( $users ) ? $users : [] ), 'pagination' => ( isset( $pagination ) ? $pagination : [] ) ] );
	}
}