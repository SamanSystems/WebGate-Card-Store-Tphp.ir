<?php

namespace app\models\forms;

use framework\core\Model;

class TransSearchForm extends Model
{
	public $transAu;
	public $transGatewayAu;
	public $transEmail;
	public $transStatus;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'transStatus' => [
					'value' => $this->transStatus,
					'message' => $lang->getIndex('trans','requiredTransStatus')
				]
			],
			'email' => [
				'transEmail' => [
					'value' => $this->transEmail,
					'message' => $lang->getIndex('trans','emailNotValid')
				]
			],
			'numerical' => [
				'transStatus' => [
					'value' => $this->transStatus,
					'message' => $lang->getIndex('trans','numericalTransStatus')
				]
			]
		];
	}
}