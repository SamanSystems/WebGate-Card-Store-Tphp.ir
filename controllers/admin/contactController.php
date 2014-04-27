<?php

namespace app\controllers\admin;

use framework\pagination\Pagination;
use framework\request\Request;
use framework\security\Csrf;
use framework\session\Session;
use framework\mailer\Mailer;
use app\components\AdminController;
use app\components\UserControl;
use app\models\Contact;
use app\models\forms\ContactReplyForm;

class ContactController extends AdminController
{
	/**
	 * list contacts and (delete)
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
				Contact::model()->delete( [ 'conditions' => 'contactId = :id', 'params' => [ ':id' => $id ] ] );
			}

			Session::instance()->setFlash( 'success', $this->lang->getIndex( 'contact', 'delete' ) );
		}

		$pagination = new Pagination();
		$config['fullRows'] = Contact::model()->find( [ 'select' => 'COUNT(*) as total' ] )->total;
		$config['itemLimit'] = $this->per_page;
		$pagination->initialize( $config );

		$contacts = Contact::model()->findAll( [ 'orderBy' => 'contactId DESC', 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );

		$this->render( 'contact/index', [ 'pagination' => $pagination, 'contacts' => $contacts ] );
	}

	/**
	 * show contact
	 *
	 * @param integer $contactId, contact id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionShow( $contactId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		$contact = Contact::model()->find( [ 'conditions' => [ 'contactId = :id' ], 'params' => [ ':id' => $contactId ] ] );

		if( $contact ) {
			Contact::model()->update( [ 'fields' => [ 'contactStatus' => 1 ], 'conditions' => 'contactId = :id', 'params' => [ ':id' => $contactId ] ] );
		} else {
			Session::instance()->setFlash( 'danger', $this->lang->getIndex( 'contact', 'notFound' ) );
		}

		$this->render( 'contact/show', [ 'contact' => $contact ] );
	}

	/**
	 * send reply email to contact email
	 *
	 * @param integer $contactId , contact id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionReply( $contactId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		$session = Session::instance();
		
		$model = new ContactReplyForm;

		$contact = Contact::model()->find( [ 'conditions' => [ 'contactId = :id' ], 'params' => [ ':id' => $contactId ] ] );

		if( !$contact ) {
			$session->setFlash( 'danger', $this->lang->getIndex( 'contact', 'notFound' ) );
		}

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {

				ob_start();
				$mailer = new Mailer( [ 'subject' => $model->subject, 'body' => $model->content ] );
				$mailer->isSMTP();
				$mailer->SMTPAuth = true;
				$mailer->Host = $this->smtp_host;
				$mailer->Username = $this->smtp_username;
				$mailer->Password = $this->smtp_password;
				$mailer->Port = $this->smtp_port;
				$mailer->SMTPSecure = $this->smtp_secure;
				$mailer->setFrom( $this->admin_email );
				$mailer->setTo( [ $contact->contactEmail => $contact->contactName ] );
				$send = $mailer->send( false );
				ob_end_clean();

				if( $send ) {
					$session->setFlash( 'success', $this->lang->getIndex( 'contact', 'reply' ) );
					$this->refresh();
				} else {
					$session->setFlash( 'danger', $mailer->ErrorInfo );
				}
			}
		}

		$this->render( 'contact/reply', [ 'model' => $model, 'contact' => $contact ] );
	}
}