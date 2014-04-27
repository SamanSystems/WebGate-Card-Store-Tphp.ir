<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class TransCard extends Model
{
	protected $table = 'trans_card';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $cardId, $transId )
	{
		$fields = [
			'transCardCardId' 		=> ':cardId',
			'transCardTransId' 	=> ':transId'
		];
		$params = [
			':cardId' 		=> $cardId,
			':transId' 	=> $transId
		];

		Database::queryBuilder()->insert( $this->table, $fields, $params );
		
		return Database::connection()->lastInsertId();
	}
}