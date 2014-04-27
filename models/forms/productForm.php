<?php

namespace app\models\forms;

use framework\core\Model;

class ProductForm extends Model
{
	public $productName;
	public $productCategory;
	public $productTag;
	public $productPrice;
	public $productOrder;
	public $productStatus;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'productName' => [
					'value' => $this->productName,
					'message' => $lang->getIndex('product','requiredProductName')
				],
				'productCategory' => [
					'value' => $this->productCategory,
					'message' => $lang->getIndex('product','requiredProductCategory')
				],
				'productTag' => [
					'value' => $this->productTag,
					'message' => $lang->getIndex('product','requiredProductTag')
				],
				'productPrice' => [
					'value' => $this->productPrice,
					'message' => $lang->getIndex('product','requiredProductPrice')
				],
				'productOrder' => [
					'value' => $this->productOrder,
					'message' => $lang->getIndex('product','requiredProductOrder')
				],
				'productStatus' => [
					'value' => $this->productStatus,
					'message' => $lang->getIndex('product','requiredProductStatus')
				]
			],
			'numerical' => [
				'productCategory' => [
					'value' => $this->productCategory,
					'message' => $lang->getIndex('product','numericalProductCategory')
				],
				'productPrice' => [
					'value' => $this->productPrice,
					'message' => $lang->getIndex('product','numericalProductPrice')
				],
				'productOrder' => [
					'value' => $this->productOrder,
					'message' => $lang->getIndex('product','numericalProductOrder')
				],
				'productStatus' => [
					'value' => $this->productStatus,
					'message' => $lang->getIndex('product','numericalProductStatus')
				]
			]
		];
	}
}