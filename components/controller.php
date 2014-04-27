<?php

namespace app\components;

use framework\core\Controller as BaseController;
use framework\language\Language;
use app\models\Option;

class Controller extends BaseController
{
	public $lang;

	public function __set( $key, $value )
	{
		$this->{$key} = $value;
	}

	public function __get( $key )
	{
		return $this->{$key};
	}

	public function init()
	{
		foreach( Option::model()->findAll() as $option )
			$this->{$option->optionName} = $option->optionValue;

		$this->lang = Language::instance()->setLanguage( 'fa' );
	}

	public function random( $length = 10, $append = '' )
	{
		$result = '';

		for( $i=0; $i<$length; $i++ ) {
			$result .= rand( 0, 9 );
		}

		return $result . $append;
	}

	public function priceFormat( $price )
	{
		return number_format( $price );
	}

	public function dateFormat( $date, $format = 'Y/m/d - H:i:s' )
	{
		\Framework::import( BASEPATH . 'app/extensions/jdf/jdf', true );

		return jdate( $format, $date, '', '', 'en' );
	}
}