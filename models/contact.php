<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class Contact extends Model
{
	protected $table = 'contact';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $name, $email, $subject, $content, $status = 0 )
	{
		$fields = [
			'contactName' 		=> ':name',
			'contactEmail'		=> ':email',
			'contactSubject' 	 	=> ':subject',
			'contactContent' 	 	=> ':content',
			'contactCreatedDate' 	=> ':date',
			'contactStatus' 		=> ':status',
		];
		
		$params = [
			':name' 		=> $name,
			':email' 		=> $email,
			':subject' 	=> $subject,
			':content' 	=> $content,
			':date' 		=> time(),
			':status' 		=> $status,
		];

		return Database::queryBuilder()->insert( $this->table, $fields, $params );
	}
}