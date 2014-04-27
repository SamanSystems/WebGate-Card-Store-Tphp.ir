<?php

namespace app\controllers\admin;

use framework\session\Session;
use framework\request\Request;
use app\components\AdminController;
use app\components\UserControl;
use app\models\forms\LoginForm;
use app\models\Trans;
use app\models\Card;
use app\models\User;

class CommonController extends AdminController
{
	/**
	 * admin index page (dashboard)
	 *
	 * @access public
	 * @return void
	 */
	public function actionIndex()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		\Framework::import( BASEPATH . 'app/extensions/xml', true );

		$xml = new \Xml();

		$newsData = @file_get_contents( $this->script_site . '/news.xml' );
		if( !$newsData ) {
			$news = [];
		} else {
			$news = $xml->parse( $newsData );
		}
		
		$themesData = @file_get_contents( $this->script_site . '/themes.xml' );
		if( !$themesData ) {
			$themes = [];
		} else {
			$themes = $xml->parse( $themesData );
		}
		unset( $newsData, $themesData );

		$transSumPrice = Trans::model()->find( [ 'select' => 'SUM(transPrice) as total', 'conditions' => [ 'transStatus = 1' ] ] );
		$totalTrans = Trans::model()->find( [ 'select' => 'COUNT(*) as total' ] );
		$cardSold = Card::model()->find( [ 'select' => 'COUNT(*) as total', 'conditions' => [ 'cardStatus= 1' ] ] );
		$cardAvailable = Card::model()->find( [ 'select' => 'COUNT(*) as total', 'conditions'  => [ 'cardStatus = 0' ] ] );
		$transactions = Trans::model()->findAll( [ 'orderBy' => 'transId DESC', 'limit' => 10 ] );
		
		$this->render( 'common/index', [ 'transSumPrice' => $transSumPrice, 'totalTrans' => $totalTrans, 'cardSold' => $cardSold, 'cardAvailable' => $cardAvailable, 'transactions' => $transactions, 'news' => $news, 'themes' => $themes ] );
	}

	/**
	 * admin login page
	 *
	 * @access public
	 * @use http://example.com/index.php/admin/common/login
	 * @return void
	 */
	public function actionLogin()
	{
		if( UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/index' ) );
		}
		
		$model = new LoginForm;

		if( Request::isPostRequest() ) {
			$model->setAttributes( Request::getPosts() );

			$session = Session::instance();

			if( $model->validate() ) {
				$user = User::model()->find( [ 'conditions' => [ 'userName = :name' ], 'params' => [ ':name' => $model->userName ] ] );

				if( $user ) {
					$password = UserControl::createPassword( $user->userSalt, $model->password );

					if( (string)$password == $user->userPassword ) {
						UserControl::login( $user->userName );
						Request::redirect( \Framework::createUrl( 'admin/common/index' ) );
					}
				}
				$session->setFlash( 'danger', $this->lang->getIndex( 'login', 'invalidUserNameOrPassword' ) );
			}
		}

		$this->render( 'common/login', [ 'model' => $model ] );
	}

	/**
	 * admin logout page
	 *
	 * @access public
	 * @return void
	 */
	public function actionLogout()
	{
		if( UserControl::isLogged() ) {
			UserControl::logout();
		}

		Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
	}
}