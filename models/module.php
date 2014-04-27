<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class Module extends Model
{
	protected $table = 'module';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $moduleName, $moduleFile, $moduleData, $moduleType = 0, $moduleStatus = 1 )
	{
		$fields = [
			'moduleName' 		=> ':name',
			'moduleFileName' 	=> ':file',
			'moduleData' 		=> ':data',
			'moduleType' 		=> ':type',
			'moduleStatus' 		=> ':status'
		];
		
		$params = [
			':name' 	=> $moduleName,
			':file' 	=> $moduleFile,
			':data' 	=> $moduleData,
			':type' 	=> $moduleType,
			':status' 	=> $moduleStatus
		];
		
		return Database::queryBuilder()->insert( $this->table, $fields, $params );
	}

	public function updateData( $moduleId, $moduleName, $moduleFile, $moduleType = 0, $moduleStatus = 1 )
	{
		$fields = [
			'moduleName' 		=> ':name',
			'moduleFileName' 	=> ':file',
			'moduleType' 		=> ':type',
			'moduleStatus' 		=> ':status'
		];
		
		$params = [
			':name' 	=> $moduleName,
			':file' 	=> $moduleFile,
			':type' 	=> $moduleType,
			':status' 	=> $moduleStatus,
			':id' 		=> $moduleId
		];
		
		return Database::queryBuilder()->update( $this->table, $fields, 'moduleId = :id', $params );
	}
}