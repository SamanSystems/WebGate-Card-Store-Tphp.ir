<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class TransInfo extends Model
{
	protected $table = 'trans_info';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $transId, $content )
	{
		$fields = [
			'transInfoTransId' 	=> ':id',
			'transInfoContent' 	=> ':content',
		];
		
		$params = [
			':id' 			=> $transId,
			':content' 	=> $content
		];
		
		return Database::queryBuilder()->insert( $this->table, $fields, $params );
	}
}