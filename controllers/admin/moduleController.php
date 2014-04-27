<?php

namespace app\controllers\admin;

use framework\pagination\Pagination;
use framework\request\Request;
use framework\security\Csrf;
use framework\session\Session;
use app\components\AdminController;
use app\components\UserControl;
use app\models\Module;
use app\models\forms\ModuleForm;

class ModuleController extends AdminController
{
	/**
	 * module list and (delete)
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
				Module::model()->delete( [ 'conditions' => 'moduleId = :id', 'params' => [ ':id' => $id ] ] );
			}

			Session::instance()->setFlash( 'success', $this->lang->getIndex( 'module', 'delete' ) );
		}

		$pagination = new Pagination();
		$config['fullRows'] = Module::model()->find( [ 'select' => 'COUNT(*) as total' ] )->total;
		$config['itemLimit'] = $this->per_page;
		$pagination->initialize( $config );

		$modules = Module::model()->findAll( [ 'orderBy' => 'moduleId DESC', 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );

		$this->render( 'module/index', [ 'pagination' => $pagination, 'modules' => $modules ] );
	}

	/**
	 * create new module
	 *
	 * @access public
	 * @return void
	 */
	public function actionNew()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$model = new ModuleForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				if( $model->moduleType == 0 ) {
					$path = 'app/components/modules/payment/';
				} else {
					$path = 'app/components/modules/notification/';
				}

				\Framework::import( BASEPATH . $path . $model->moduleFileName, true );
				$namespace = str_replace( '/', '\\', $path ) . ucfirst( $model->moduleFileName );
				$class = new $namespace();
				$data = serialize( $class->fields() );

				Module::model()->insertData( $model->moduleName, $model->moduleFileName, $data, $model->moduleType, $model->moduleStatus );
				Session::instance()->setFlash( 'success', $this->lang->getIndex( 'module', 'success' ) );
				$this->refresh();
			}
		}

		$this->render( 'module/new', [ 'model' => $model ] );
	}

	/**
	 * edit module
	 *
	 * @param integer $moduleId , module id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionEdit( $moduleId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$model = new ModuleForm;

		$module = Module::model()->find( [ 'conditions' => [ 'moduleId = :id' ], 'params' => [ ':id' => $moduleId ] ] );

		$session = Session::instance();

		if( !$module ) {
			$session->setFlash( 'danger', $this->lang->getIndex( 'module', 'notFound' ) );
		}

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				Module::model()->updateData( $moduleId, $model->moduleName, $model->moduleFileName, $model->moduleType, $model->moduleStatus );
				$session->setFlash( 'success', $this->lang->getIndex( 'module', 'update' ) );
				$this->refresh();
			}
		}
 
		$this->render( 'module/edit', [ 'model' => $model, 'module' => $module ] );
	}

	/**
	 * edit module options
	 *
	 * @param integer $moduleId , module id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionOption( $moduleId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$module = Module::model()->find( [ 'conditions' => [ 'moduleId = :id' ], 'params' => [ ':id' => $moduleId ] ] );

		$session = Session::instance();

		if( $module ) {
			$module->moduleData = @unserialize( $module->moduleData );
			
			if( is_array( $module->moduleData ) and count( $module->moduleData ) > 0 ) {
				$haveOptions = true;
			} else {
				$session->setFlash( 'danger', $this->lang->getIndex( 'module', 'haveNotOption' ) );
			}
		} else {
			$session->setFlash( 'danger', $this->lang->getIndex( 'module', 'notFound' ) );
		}

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			foreach( $module->moduleData as $name => $value ) {
				$module->moduleData[$name]['value'] = Request::getPost( $name );
			}

			Module::model()->update( [ 'fields' => [ 'moduleData' => ':data' ], 'conditions' => 'moduleId = :id', 'params' => [ ':data' => @serialize( $module->moduleData ), ':id' => $module->moduleId ] ] );

			$session->setFlash( 'success', $this->lang->getIndex( 'module', 'updateOption' ) );
			$this->refresh();
		}

		$this->render( 'module/option', [ 'module' => $module, 'haveOptions' => (isset($haveOptions) ? true : false) ] );
	}
}