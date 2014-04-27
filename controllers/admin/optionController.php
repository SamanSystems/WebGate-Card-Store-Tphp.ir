<?php

namespace app\controllers\admin;

use framework\request\Request;
use framework\security\Csrf;
use framework\session\Session;
use app\components\AdminController;
use app\components\UserControl;
use app\models\forms\OptionForm;
use app\models\Option;

class OptionController extends AdminController
{
	/**
	 * update options
	 *
	 * @access public
	 * @return void
	 */
	public function actionIndex()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$model = new OptionForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				foreach( Request::getPosts() as $name => $value ) {
					if( $name == 'smtp_password' ) {
						if( empty( $model->{$name} ) ) {
							$model->{$name} = $this->smtp_password;
						}
					}
					if( $this->{$name} != $model->{$name} ) {
						Option::model()->update( [ 'fields' => [ 'optionValue' => ':value' ], 'conditions' => 'optionName = :name', 'params' => [ ':value' => $value, ':name' => $name ] ] );
					}
				}

				Session::instance()->setFlash( 'success', $this->lang->getIndex( 'option', 'update' ) );
				$this->refresh();
			}
		}

		$themes = scandir( BASEPATH . 'themes/' );

		$filters = [ '.', '..', 'handler', 'admin' ];
		foreach( $themes as $index => $value ) {
			if( in_array( $value, $filters ) ) {
				unset( $themes[$index] );
			}
		}

		$themes = array_combine( $themes, $themes );

		$this->render( 'option/index', [ 'model' => $model, 'themes' => $themes ] );
	}
}