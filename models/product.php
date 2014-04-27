<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class Product extends Model
{
	protected $table = 'product';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $productName, $productCategory, $productTag, $productPrice, $productOrder, $productStatus = 1 )
	{
		$fields = [
			'productName' 		=> ':name',
			'productCategoryId' 	=> ':categoryId',
			'productTag'	 		=> ':tag',
			'productPrice' 		=> ':price',
			'productOrder' 		=> ':order',
			'productCreatedDate' 	=> ':date',
			'productStatus' 		=> ':status'
		];
		
		$params = [
			':name' 		=> $productName,
			':categoryId' 	=> $productCategory,
			':tag' 		=> $productTag,
			':price' 		=> $productPrice,
			':order' 		=> $productOrder,
			':date' 		=> time(),
			':status' 		=> $productStatus
		];
		
		return Database::queryBuilder()->insert( $this->table, $fields, $params );
	}

	public function updateData( $productId, $productName, $productCategory, $productTag, $productPrice, $productOrder, $productStatus = 1 )
	{
		$fields = [
			'productName' 		=> ':name',
			'productCategoryId' 	=> ':categoryId',
			'productTag' 			=> ':tag',
			'productPrice' 		=> ':price',
			'productOrder' 		=> ':order',
			'productStatus' 		=> ':status'
		];
		
		$params = [
			':name' 		=> $productName,
			':categoryId' 	=> $productCategory,
			':tag' 		=> $productTag,
			':price'	 	=> $productPrice,
			':order' 		=> $productOrder,
			':status' 		=> $productStatus,
			':id' 			=> $productId
		];
		
		return Database::queryBuilder()->update( $this->table, $fields, 'productId = :id', $params );
	}

	public function getProduct( $productId )
	{
		return Database::queryBuilder()
				->select( '{prefix}product.*' )
				->from( $this->table )
				->join( '{prefix}category ON categoryId = productCategoryId AND categoryStatus = 1', 'INNER JOIN' )
				->where( 'productId = :id AND productStatus = 1' )
				->fetch( [ ':id' => $productId ] );
	}

	public function getProducts()
	{
		return Database::queryBuilder()
				->select( '{prefix}product.*' )
				->from( $this->table )
				->join( '{prefix}category ON categoryId = productCategoryId AND categoryStatus = 1', 'INNER JOIN' )
				->where( 'productStatus = 1' )
				->orderBy( 'productOrder ASC' )
				->fetchAll();
	}
}