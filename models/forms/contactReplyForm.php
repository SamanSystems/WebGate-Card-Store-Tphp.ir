<?php

namespace app\models\forms;

use framework\core\Model;

class ContactReplyForm extends Model
{
	public $subject;
	public $content;

	public function rules()
	{
		$lang = \Framework::instance()->controller->lang;

		return [
			'required' => [
				'subject' => [
					'value' => $this->subject,
					'message' => $lang->getIndex('contact','requiredReplaySubject')
				],
				'content' => [
					'value' => $this->content,
					'message' => $lang->getIndex('contact','requiredReplayContent')
				]
			]
		];
	}
}