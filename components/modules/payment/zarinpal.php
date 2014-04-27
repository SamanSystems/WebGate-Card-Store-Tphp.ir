<?php

namespace app\components\modules\payment;

use framework\request\Request;

/**
 * payment with zarinpal
 *
 * @author		Saeed Johari <sjohari74@gmail.com>
 * @since		1.0
 * @package		payment module
 * @copyright   (c) 2014 all rights reserved
 */
class Zarinpal extends Payment
{
	/**
	 * request to zarinpal gateway
	 *
	 * @param integer $id, trans id (primary key)
	 * @param integer $au, trans authority code
	 * @param integer $price, trans price
	 * @param array $module, information of this module
	 * @param integer $product, product id
	 *
	 * @access public
	 * @return void
	 */
	public function request( $id, $au, $price, $module, $product )
	{
		\Framework::import( BASEPATH . 'app/extensions/nusoap', true );

		$params = [
			'MerchantID' => $module['merchantId']['value'],
			'Amount' => $price,
			'Description' => $module['description']['value'],
			'CallbackURL' => $this->getCallbackUrl( $au )
		];

		$client = new \nusoap_client( $module['webserviceUrl']['value'], 'wsdl' );
		$client->soap_defencoding = 'UTF-8';
		$result = $client->call( 'PaymentRequest', [ $params ] );

		if( $error = $client->getError() ) {
			$this->setFlash( 'danger', $error );
		} elseif( $client->fault ) {
			$this->setFlash( 'danger', $client->faultcode . ':' . $client->faultstring );
		} elseif( isset( $result['Status'], $result['Authority'] ) and $result['Status'] == 100 ) {
			$this->updateAu( $id, $result['Authority'] );
			$url = $module['redirectUrl']['value'] . $result['Authority'];
			if( $module['type']['value'] != 'webgate' ) {
				$url .= '/ZarinGate';
			}
			$this->redirect( $url );
		} else {
			$this->setFlash( 'danger', $this->lang()->getIndex( 'zarinpal', 'error' ) . $result['Status'] );
		}
	}

	/**
	 * request to zarinpal for verify transaction
	 *
	 * @param integer $id, trans id (primary key)
	 * @param integer $au, trans authority code
	 * @param integer $price, trans price
	 * @param array $module, information of this module
	 * @param integer $product, product id
	 *
	 * @access public
	 * @return array|boolean
	 */
	public function verify( $id, $au, $price, $module, $product )
	{
		if( !Request::isQuery( 'Authority' ) OR Request::getQuery( 'Status' ) != 'OK' ) {
			$this->setFlash( 'danger', $this->lang()->getIndex( 'zarinpal', 'inputNotValid' ) );
			return false;
		}

		\Framework::import( BASEPATH . 'app/extensions/nusoap', true );

		$params = [
			'MerchantID' => $module['merchantId']['value'],
			'Authority' => Request::getQuery( 'Authority' ),
			'Amount' => $price
		];

		$client = new \nusoap_client( $module['webserviceUrl']['value'], 'wsdl' );
		$client->soap_defencoding = 'UTF-8';

		$result = $client->call( 'PaymentVerification',[ $params ] );
		
		if( $error = $client->getError() ) {
			$this->setFlash( 'danger', $error );
		} elseif( $client->fault ) {
			$this->setFlash( 'danger', $client->faultcode . ':' . $client->faultstring );
		} elseif( isset( $result['Status'] ) and $result['Status'] == 100 ) {
			return [ 'au' => $result['RefId'] ];
		} else {
			$this->setFlash( 'danger', $this->lang()->getIndex( 'zarinpal', 'error' ) . $result['Status'] );
		}
	}

	/**
	 * module fields for install this
	 *
	 * @access public
	 * @return array
	 */
	public function fields()
	{
		return [
			'merchantId' => [
				'label' => $this->lang()->getIndex( 'zarinpal', 'merchantId' ),
				'value' => '',
			],
			'webserviceUrl' => [
				'label' => $this->lang()->getIndex( 'zarinpal', 'webserviceUrl' ),
				'value' => 'https://de.zarinpal.com/pg/services/WebGate/wsdl',
			],
			'redirectUrl' => [
				'label' => $this->lang()->getIndex( 'zarinpal', 'redirectUrl' ),
				'value' => 'https://www.zarinpal.com/pg/StartPay/',
			],
			'description' => [
				'label' => $this->lang()->getIndex( 'zarinpal', 'description' ),
				'value' => $this->lang()->getIndex( 'zarinpal', 'descriptionValue' ),
			],
			'type' => [
				'label' => $this->lang()->getIndex('zarinpal','type'),
				'value' => 'webgate',
			],
		];
	}
}