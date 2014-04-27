<?php

namespace app\models\forms;

use framework\core\Model;

class CardForm extends Model
{
	public $cardProduct;
	public $cardName;
	public $cardValue;
	public $cardOrder;
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
				'cardName' => [
					'value' => $this->cardName,
					'message' => $lang->getIndex('card','requiredCardName')
				],
				'cardValue' => [
					'value' => $this->cardValue,
					'message' => $lang->getIndex('card','requiredCardValue')
				],
				'cardOrder' => [
					'value' => $this->cardOrder,
					'message' => $lang->getIndex('card','requiredCardOrder')
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
				'cardOrder' => [
					'value' => $this->cardOrder,
					'message' => $lang->getIndex('card','numericalCardOrder')
				],
				'cardStatus' => [
					'value' => $this->cardStatus,
					'message' => $lang->getIndex('card','numericalCardStatus')
				]
			]
		];
	}
}