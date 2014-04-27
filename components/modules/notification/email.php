<?php

namespace app\components\modules\notification;

use framework\mailer\Mailer;

/**
 * notification with email
 *
 * @author		Saeed Johari <sjohari74@gmail.com>
 * @since		1.0
 * @package		notification module
 * @copyright   (c) 2014 all rights reserved
 */
class Email extends Notification
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
		$find = [ '{price}', '{au}', '{email}', '{date}', '{cards}' ];

		$cardOutput = '';
		foreach( $cards as $card ) {
			$cardOutput .= '<br>' . $card->cardName . '<br>' . $card->cardValue . '-------------------------------------';
		}

		$date = $this->getController()->dateFormat( $trans->transCreatedDate );
		$content = str_replace( $find, [ $trans->transPrice, $trans->transAu, $trans->transEmail, $date, $cardOutput ], $module['content']['value'] );

		ob_start();
		$mailer = new Mailer( ['subject' => $module['subject']['value'], 'body' => $content ] );
		$mailer->isSMTP();
		$mailer->SMTPAuth = true;
		$mailer->Host = $this->getController()->smtp_host;
		$mailer->Username = $this->getController()->smtp_username;
		$mailer->Password = $this->getController()->smtp_password;
		$mailer->Port = $this->getController()->smtp_port;
		$mailer->SMTPSecure = $this->getController()->smtp_secure;
		$mailer->setFrom( $this->getController()->admin_email );
		$mailer->setBcc( [ $trans->transEmail => '', $this->getController()->admin_email => '' ] );
		$mailer->send( false );
		ob_end_clean();
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
			'subject' => [
				'label' => $this->lang()->getIndex( 'email', 'subject' ),
				'value' => $this->lang()->getIndex( 'email', 'subjectValue' ),
			],
			'content' => [
				'label' => $this->lang()->getIndex( 'email', 'content' ),
				'value' => '{price}<br>{au}<br>{email}<br>{date}<br>{cards}',
			],
		];
	}
}