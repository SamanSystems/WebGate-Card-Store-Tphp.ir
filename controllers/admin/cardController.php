<?php

namespace app\controllers\admin;

use framework\pagination\Pagination;
use framework\security\Csrf;
use framework\session\Session;
use framework\request\Request;
use framework\helpers\File;
use app\components\AdminController;
use app\components\UserControl;
use app\models\Product;
use app\models\Card;
use app\models\forms\CardForm;
use app\models\forms\CardSearchForm;

class CardController extends AdminController
{
	/**
	 * card lists and (delete)
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
				Card::model()->delete( [ 'conditions' => 'cardId = :id', 'params' => [ ':id' => $id ] ] );
			}
			
			Session::instance()->setFlash( 'success', $this->lang->getIndex( 'card', 'delete' ) );
		}

		$pagination = new Pagination();
		$config['fullRows'] = Card::model()->find( [ 'select' => 'COUNT(*) as total' ] )->total;
		$config['itemLimit'] = $this->per_page;
		$pagination->initialize( $config );

		$cards = Card::model()->findAll( [ 'orderBy' => 'cardId DESC', 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );

		$this->render( 'card/index', [ 'pagination' => $pagination, 'cards' => $cards ] );
	}

	/**
	 * create new card
	 *
	 * @access public
	 * @return void
	 */
	public function actionNew()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$model = new CardForm;

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				Card::model()->insertData( $model->cardName, $model->cardProduct, $model->cardValue, $model->cardOrder, $model->cardStatus );

				Session::instance()->setFlash( 'success', $this->lang->getIndex( 'card', 'success' ) );
				$this->refresh();
			}
		}

		$products = Product::model()->findAll( [ 'conditions' => [ 'productStatus = 1' ], 'orderBy' => 'productId DESC' ] );
		
		$this->render( 'card/new', [ 'model' => $model, 'products' => $products ] );
	}

	/**
	 * edit card
	 *
	 * @param integer $cardId , card id (primary key)
	 * @access public
	 * @return void
	 */
	public function actionEdit( $cardId = 0 )
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}
		
		$model = new CardForm;

		$card = Card::model()->find( [ 'conditions' => [ 'cardId = :id' ], 'params' => [ ':id' => $cardId ] ] );

		$session = Session::instance();

		if( $card ) {
			$products = Product::model()->findAll( [ 'conditions' => [ 'productStatus = 1' ], 'orderBy' => 'productId DESC' ] );
		} else {
			$session->setFlash( 'danger', $this->lang->getIndex( 'card', 'notFound' ) );
		}

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$model->setAttributes( Request::getPosts() );

			if( $model->validate() ) {
				Card::model()->updateData( $cardId, $model->cardName, $model->cardProduct, $model->cardValue, $model->cardOrder, $model->cardStatus );

				$session->setFlash( 'success', $this->lang->getIndex( 'card', 'update' ) );
				$this->refresh();
			}
		}

		$this->render( 'card/edit' ,[ 'model' => $model, 'products' => ( isset( $products ) ? $products : [ ] ), 'card' => $card ] );
	}

	/**
	 * search cards with (card name, card product, card status)
	 *
	 * @access public
	 * @return void
	 */
	public function actionSearch()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		$model = new CardSearchForm;

		$cards = [];

		if( Request::isGetRequest() and Request::getQueries() ) {
			$model->setAttributes( Request::getQueries() );

			if( $model->validate() ) {
				$conditions[0] = ''; $params = [];

				if( !empty( $model->cardName ) ) {
					$conditions[0] = 'cardName LIKE :name AND ';
					$params[':name'] = "%{$model->cardName}%";
				}
				
				$conditions[0] .= 'cardProductId = :id AND cardStatus = :status';
				$params[':id'] = $model->cardProduct;
				$params[':status'] = $model->cardStatus;
				
				$pagination = new Pagination();
				$config['fullRows'] = Card::model()->find( [ 'select' => 'COUNT(*) as total', 'conditions' => $conditions, 'params' => $params ] )->total;
				$config['itemLimit'] = $this->per_page;
				$config['pageVar'] = '&page=';
				$pagination->initialize( $config );

				$cards = Card::model()->findAll( [ 'orderBy' => 'cardId DESC', 'conditions' => $conditions, 'params' => $params, 'limit' => $pagination->applyLimit(), 'offset' => $pagination->applyOffset() ] );

				if( !$cards ) {
					Session::instance()->setFlash( 'danger', $this->lang->getIndex( 'card', 'notFound' ) );
				}
			}
		}

		if( !$cards ) {
			$products = Product::model()->findAll( [ 'conditions' => [ 'productStatus = 1' ], 'orderBy' => 'productOrder ASC' ] );
		}

		$this->render( 'card/search', [ 'model' => $model, 'products' => ( isset( $products ) ? $products : [] ),'cards' => ( isset( $cards ) ? $cards : [] ), 'pagination' => ( isset( $pagination ) ? $pagination : [] ) ] );
	}

	/**
	 * upload cards to database
	 *
	 * @access public
	 * @return void
	 */
	public function actionUpload()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		} 

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$session = Session::instance();
			if( empty( $_FILES['file']['name'] ) ) {
				$session->setFlash( 'danger', $this->lang->getIndex( 'card', 'fileNotSelected' ) );
			} else {
				if( $_FILES['file']['type'] != 'text/plain' ) {
					$session->setFlash( 'danger', $this->lang->getIndex( 'card', 'formatNotSupported' ) );
				} else {
					$contents = file_get_contents( $_FILES['file']['tmp_name'] );
					preg_match_all( '/\{(.*?)\}/is', $contents, $cards );

					if( isset( $cards[1] ) and count( $cards[1] ) > 0 ) {
						set_time_limit( 0 );
						foreach( $cards[1] as $card ) {
							list( $name, $productId, $value, $status) = explode( '#', $card );
							Card::model()->insertData( $name, $productId, $value, 1, $status );
						}
						$session->setFlash( 'success', $this->lang->getIndex( 'card', 'upload' ) );
					} else {
						$session->setFlash( 'danger', $this->lang->getIndex( 'card', 'notFound' ) );
					}
				}
			}
		}

		$this->render( 'card/upload' );
	}

	/**
	 * download cards by status
	 *
	 * @access public
	 * @return string
	 */
	public function actionDownload()
	{
		if( !UserControl::isLogged() ) {
			Request::redirect( \Framework::createUrl( 'admin/common/login' ) );
		}

		if( Request::isPostRequest() and Csrf::validate( Request::getPost( 'csrf' ) ) ) {
			$conditions = []; $params = [];
			if( Request::getPost( 'cardStatus') != 'all' ) {
				$conditions[] = 'cardStatus = :status';
				$params[':status'] = Request::getPost( 'cardStatus' );
			}
			$cards = Card::model()->findAll( [ 'conditions' => $conditions, 'params' => $params ] );

			$result = '';
			set_time_limit( 0 );
			foreach( $cards as $card ) {
				$result .= "{{$card->cardName}#{$card->cardProductId}#{$card->cardValue}#{$card->cardStatus}}" . PHP_EOL;
			}

			header( 'Content-Disposition:attachment;filename=cards_' . $this->dateFormat(time()) . '.txt' );
			echo $result;
			\Framework::end();
		}

		$this->render( 'card/download' );
	}
}