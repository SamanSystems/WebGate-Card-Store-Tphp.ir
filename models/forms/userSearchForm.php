<?php

namespace app\models\forms;

use framework\core\Model;

class UserSearchForm extends Model
{
	public $userName;
	public $userEmail;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'email' => [
				'userEmail' => [
					'value' => $this->userEmail,
					'message' => $lang->getIndex('user','emailNotValid')
				]
			]
		];
	}
}