<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class Option extends Model
{
	protected $table = 'option';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}
}