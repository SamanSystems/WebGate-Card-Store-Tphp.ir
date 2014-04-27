<?php

namespace app\models\forms;

use framework\core\Model;

class ModuleForm extends Model
{
	public $moduleName;
	public $moduleFileName;
	public $moduleType;
	public $moduleStatus;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'moduleName' => [
					'value' => $this->moduleName,
					'message' => $lang->getIndex('module','requiredModuleName')
				],
				'moduleFileName' => [
					'value' => $this->moduleFileName,
					'message' => $lang->getIndex('module','requiredModuleFileName')
				],
				'moduleType' => [
					'value' => $this->moduleType,
					'message' => $lang->getIndex('module','requiredModuleType')
				],
				'moduleStatus' => [
					'value' => $this->moduleStatus,
					'message' => $lang->getIndex('module','requiredModuleStatus')
				]
			],
			'numerical' => [
				'moduleType' => [
					'value' => $this->moduleType,
					'message' => $lang->getIndex('module','numericalModuleType')
				],
				'moduleStatus' => [
					'value' => $this->moduleStatus,
					'message' => $lang->getIndex('module','numericalModuleStatus')
				]
			]
		];
	}
}