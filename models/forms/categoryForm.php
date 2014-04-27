<?php

namespace app\models\forms;

use framework\core\Model;

class CategoryForm extends Model
{
	public $categoryName;
	public $categoryImage;
	public $categoryDescription;
	public $categoryOrder;
	public $categoryStatus;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'categoryName' => [
					'value' => $this->categoryName,
					'message' => $lang->getIndex('category','requiredCategoryName')
				],
				'categoryDescription' => [
					'value' => $this->categoryDescription,
					'message' => $lang->getIndex('category','requiredCategoryDescription')
				],
				'categoryOrder' => [
					'value' => $this->categoryOrder,
					'message' => $lang->getIndex('category','requiredCategoryOrder')
				],
				'categoryStatus' => [
					'value' => $this->categoryStatus,
					'message' => $lang->getIndex('category','requiredCategoryStatus')
				]
			],
			'numerical' => [
				'categoryOrder' => [
					'value' => $this->categoryOrder,
					'message' => $lang->getIndex('category','numericalCategoryOrder')
				],
				'categoryStatus' => [
					'value' => $this->categoryStatus,
					'message' => $lang->getIndex('category','numericalCategoryStatus')
				]
			]
		];
	}
}