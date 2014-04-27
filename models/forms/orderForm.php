<?php

namespace app\models\forms;

use framework\core\Model;

class OrderForm extends Model
{
	public $product;
	public $email;
	public $mobile;
	public $content;
	public $quantity;
	public $gateway;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'product' => [
					'value' => $this->product,
					'message' => $lang->getIndex('order','requiredProduct')
				],
				'email' => [
					'value' => $this->email,
					'message' => $lang->getIndex('order','requiredEmail')
				],
				'quantity' => [
					'value' => $this->quantity,
					'message' => $lang->getIndex('order','requiredQuantity')
				],
				'gateway' => [
					'value' => $this->gateway,
					'message' => $lang->getIndex('order','requiredGateway')
				],
			],
			'numerical' => [
				'product' => [
					'value' => $this->product,
					'message' => $lang->getIndex('order','numericalProduct')
				],
				'mobile' => [
					'value' => $this->mobile,
					'message' => $lang->getIndex('order','numericalMobile')
				],
				'quantity' => [
					'value' => $this->quantity,
					'message' => $lang->getIndex('order','numericalQuantity')
				],
				'gateway' => [
					'value' => $this->gateway,
					'message' => $lang->getIndex('order','numericalGateway')
				],
			],
			'email' => [
				'email' => [
					'value' => $this->email,
					'message' => $lang->getIndex('order','emailNotValid')
				],
			],
			'regex' => [
				'mobile' => [
					'value' => $this->mobile,
					'regex' => '/^09[0-9]{9}$/',
					'message' => $lang->getIndex('order','mobileNotValid')
				],
			],
			'number' => [
				'quantity' => [
					'value' => $this->quantity,
					'min' => 1,
					'max' => 5,
					'message' => $lang->getIndex('order','lengthQuantity')
				],
			]
		];
	}
}