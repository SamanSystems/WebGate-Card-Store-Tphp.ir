<?php

namespace app\components\modules\notification;

/**
 * notification with sms
 *
 * @author		Saeed Johari <sjohari74@gmail.com>
 * @since		1.0
 * @package		notification module
 * @copyright   (c) 2014 all rights reserved
 */
class Novinpayamak extends Notification
{
	/**
	 * run module
	 *
	 * @param object $trans, trans informations
	 * @param array $module, module data
	 * @param object $cards, buyed cards
	 * @access public
	 * @return void
	 */
	public function run( $trans, $module, $cards )
	{
		\Framework::import( BASEPATH . 'app/extensions/nusoap', true );

		$client = new \nusoap_client( $module['webserviceUrl']['value'], 'wsdl' );
		$client->soap_defencoding = 'UTF-8';

		$recipients[0] = $module['adminMobile']['value'];
		if( !empty( $trans->transMobile ) ) {
			$recipients[1] = $trans->transMobile;
		}

		$find = [ '{au}', '{price}', '{cards}' ];

		$cardOutput = '';
		foreach( $cards as $card ) {
			$cardOutput .= PHP_EOL . $card->cardName . PHP_EOL . $card->cardValue . '--------------------';
		}

		$content = str_replace( $find, [ $trans->transAu, $trans->transPrice, $cardOutput ], $module['content']['value'] );

		$result = $client->call( 'Send',
			[
				[
					'Auth' => [
						'number' => $module['number']['value'],
						'pass' => $module['password']['value'],
					],
					'Recipients' => [
						'string' => $recipients,
					],
					'Message' => [
						'string' => [ $content ],
					],
					'Flash' => false,
				]
			]
		);
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
			'webserviceUrl' => [
				'label' => $this->lang()->getIndex( 'novinpayamak', 'webserviceUrl' ),
				'value' => 'http://www.novinpayamak.com/services/SMSBox/wsdl',
			],
			'adminMobile' => [
				'label' => $this->lang()->getIndex( 'novinpayamak', 'adminMobile' ),
				'value' => '',
			],
			'number' => [
				'label' => $this->lang()->getIndex( 'novinpayamak', 'number' ),
				'value' => '',
			],
			'password' => [
				'label' => $this->lang()->getIndex( 'novinpayamak', 'password' ),
				'value' => '',
			],
			'content' => [
				'label' => $this->lang()->getIndex( 'novinpayamak', 'content' ),
				'value' => '{au}<br>{price}<br>{cards}',
			],
		];
	}
}