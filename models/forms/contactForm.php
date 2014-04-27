<?php

namespace app\models\forms;

use framework\core\Model;

class ContactForm extends Model
{
	public $name;
	public $email;
	public $subject;
	public $content;
	public $captcha;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'name' => [
					'value' => $this->name,
					'message' => $lang->getIndex('contact','requiredName')
				],
				'email' => [
					'value' => $this->email,
					'message' => $lang->getIndex('contact','requiredEmail')
				],
				'subject' => [
					'value' => $this->subject,
					'message' => $lang->getIndex('contact','requiredSubject')
				],
				'content' => [
					'value' => $this->content,
					'message' => $lang->getIndex('contact','requiredContent')
				],
				'captcha' => [
					'value' => $this->captcha,
					'message' => $lang->getIndex('contact','requiredCaptcha')
				]
			],
			'email' => [
				'email' => [
					'value' => $this->email,
					'message' => $lang->getIndex('contact','emailNotValid')
				]
			],
			'captcha' => [
				'captcha' => [
					'value' => $this->captcha,
					'message' => $lang->getIndex('contact','captchaNotValid')
				]
			]
		];
	}
}