<?php

namespace app\components\modules\notification;

abstract class Notification
{
	abstract public function fields();

	abstract public function run( $trans, $module, $cards );

	public function getController()
	{
		return \Framework::instance()->controller;
	}

	public function lang()
	{
		return $this->getController()->lang;
	}
}