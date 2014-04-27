<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class User extends Model
{
	protected $table = 'user';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $userName, $userEmail, $userPassword, $userSalt )
	{
		$fields = [
			'userName' 			=> ':name',
			'userEmail' 			=> ':email',
			'userPassword' 		=> ':pass',
			'userSalt' 			=> ':salt',
			'userCreatedDate' 	=> ':date'
		];
		
		$params = [
			':name' 		=> $userName,
			':email' 		=> $userEmail,
			':pass' 		=> $userPassword,
			':salt' 		=> $userSalt,
			':date' 		=> time()
		];
		
		return Database::queryBuilder()->insert( $this->table, $fields, $params );
	}

	public function updateData( $userId, $userSalt, $userPassword )
	{
		$fields = [
			'userSalt' 		=> ':salt',
			'userPassword' 	=> ':pass'
		];
		
		$params = [
			':salt' 	=> $userSalt,
			':pass' 	=> $userPassword,
			':id' 		=> $userId
		];
		
		return Database::queryBuilder()->update( $this->table, $fields, 'userId = :id', $params );
	}
}