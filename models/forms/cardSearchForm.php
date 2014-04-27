<?php

namespace app\models\forms;

use framework\core\Model;

class CardSearchForm extends Model
{
	public $cardName;
	public $cardProduct;
	public $cardStatus;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'cardProduct' => [
					'value' => $this->cardProduct,
					'message' => $lang->getIndex('card','requiredCardProduct')
				],
				'cardStatus' => [
					'value' => $this->cardStatus,
					'message' => $lang->getIndex('card','requiredCardStatus')
				]
			],
			'numerical' => [
				'cardProduct' => [
					'value' => $this->cardProduct,
					'message' => $lang->getIndex('card','numericalCardProduct')
				],
				'cardStatus' => [
					'value' => $this->cardStatus,
					'message' => $lang->getIndex('card','numericalCardStatus')
				]
			]
		];
	}
}