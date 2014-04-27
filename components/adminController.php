<?php

namespace app\components;

use app\components\Controller;

class AdminController extends Controller
{
	public function init()
	{
		parent::init();
		$this->siteTheme = $this->theme;
		$this->theme = 'admin';
	}
}