<?php

namespace app\models\forms;

use framework\core\Model;

class OptionForm extends Model
{
	public $title;
	public $meta_description;
	public $meta_keywords;
	public $logo_title;
	public $reserve_second;
	public $theme;
	public $contact_category;
	public $per_page;
	public $upload_path;
	public $admin_email;
	public $smtp_host;
	public $smtp_username;
	public $smtp_password;
	public $smtp_secure;
	public $smtp_port;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'title' => [
					'value' => $this->title,
					'message' => $lang->getIndex('option','requiredTitle')
				],
				'meta_description' => [
					'value' => $this->meta_description,
					'message' => $lang->getIndex('option','requiredMetaDescription')
				],
				'meta_keywords' => [
					'value' => $this->meta_keywords,
					'message' => $lang->getIndex('option','requiredMetaKeywords')
				],
				'logo_title' => [
					'value' => $this->logo_title,
					'message' => $lang->getIndex('option','requiredLogoTitle')
				],
				'reserve_second' => [
					'value' => $this->reserve_second,
					'message' => $lang->getIndex('option','requiredReserveSecond')
				],
				'theme' => [
					'value' => $this->theme,
					'message' => $lang->getIndex('option','requiredTheme')
				],
				'contact_category' => [
					'value' => $this->contact_category,
					'message' => $lang->getIndex('option','requiredContactCategory')
				],
				'per_page' => [
					'value' => $this->per_page,
					'message' => $lang->getIndex('option','requiredPerPage')
				],
				'upload_path' => [
					'value' => $this->upload_path,
					'message' => $lang->getIndex('option','requiredUploadPath')
				],
			],
			'email' => [
				'admin_email' => [
					'value' => $this->admin_email,
					'message' => $lang->getIndex('option','emailNotValid')
				]
			],
			'numerical' => [
				'reserve_second' => [
					'value' => $this->reserve_second,
					'message' => $lang->getIndex('option','numericalReserveSecond')
				],
				'per_page' => [
					'value' => $this->per_page,
					'message' => $lang->getIndex('option','numericalPerPage')
				],
				'smtp_port' => [
					'value' => $this->smtp_port,
					'message' => $lang->getIndex('option','numericalSmtpPort')
				],
			],
		];
	}
}