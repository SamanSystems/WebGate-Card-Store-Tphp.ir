<?php

namespace app\models\forms;

use framework\core\Model;

class LoginForm extends Model
{
	public $userName;
	public $password;
	public $captcha;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'userName' => [
					'value' => $this->userName,
					'message' => $lang->getIndex('login','requiredUserName')
				],
				'password' => [
					'value' => $this->password,
					'message' => $lang->getIndex('login','requiredPassword')
				],
				'captcha' => [
					'value' => $this->captcha,
					'message' => $lang->getIndex('login','requiredCaptcha')
				]
			],
			'captcha' => [
				'captcha' => [
					'value' => $this->captcha,
					'message' => $lang->getIndex('login','invalidCaptcha')
				]
			],
		];
	}
}