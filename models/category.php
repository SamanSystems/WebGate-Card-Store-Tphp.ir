<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class Category extends Model
{
	protected $table = 'category';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $categoryName, $categoryImage, $categoryDescription, $categoryOrder, $categoryStatus = 1 )
	{
		$fields = [
			'categoryName' 			=> ':name',
			'categoryImage' 			=> ':image',
			'categoryDescription' 		=> ':desc',
			'categoryOrder' 			=> ':order',
			'categoryCreatedDate' 	=> ':date',
			'categoryStatus' 			=> ':status'
		];
		
		$params = [
			':name' 		=> $categoryName,
			':image' 		=> $categoryImage,
			':desc' 		=> $categoryDescription,
			':order' 		=> $categoryOrder,
			':date' 		=> time(),
			':status' 		=> $categoryStatus
		];
		
		return Database::queryBuilder()->insert( $this->table, $fields, $params );
	}

	public function updateData( $categoryId, $categoryName, $categoryImage, $categoryDescription, $categoryOrder, $categoryStatus = 1 )
	{
		$fields = [
			'categoryName' 		=> ':name',
			'categoryImage' 		=> ':image',
			'categoryDescription' 	=> ':desc',
			'categoryOrder' 		=> ':order',
			'categoryStatus' 		=> ':status'
		];
		
		$params = [
			':name' 		=> $categoryName,
			':image' 		=> $categoryImage,
			':desc' 		=> $categoryDescription,
			':order' 		=> $categoryOrder,
			':status' 		=> $categoryStatus,
			':id' 			=> $categoryId
		];
		
		return Database::queryBuilder()->update( $this->table, $fields, 'categoryId = :id', $params );
	}
}