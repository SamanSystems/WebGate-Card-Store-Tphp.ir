<?php

namespace app\models\forms;

use framework\core\Model;

class UserForm extends Model
{
	public $userName;
	public $userEmail;
	public $userPassword;
	public $userRePassword;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'userName' => [
					'value' => $this->userName,
					'message' => $lang->getIndex('user','requiredUserName')
				],
				'userEmail' => [
					'value' => $this->userEmail,
					'message' => $lang->getIndex('user','requiredEmail')
				],
				'userPassword' => [
					'value' => $this->userPassword,
					'message' => $lang->getIndex('user','requiredPassword')
				],
				'userRePassword' => [
					'value' => $this->userRePassword,
					'message' => $lang->getIndex('user','requiredRePassword')
				]
			],
			'email' => [
				'email' => [
					'value' => $this->userEmail,
					'message' => $lang->getIndex('user','emailNotValid')
				]
			],
			'compare' => [
				'userRePassword' => [
					'one' => $this->userPassword,
					'two' => $this->userRePassword,
					'message' => $lang->getIndex('user','compareNotValid')
				]
			]
		];
	}
}