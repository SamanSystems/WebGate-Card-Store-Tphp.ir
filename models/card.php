<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class Card extends Model
{
	protected $table = 'card';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $cardName, $cardProduct, $cardValue, $cardOrder, $cardStatus = 0 )
	{
		$fields = [
			'cardName' 		=> ':name',
			'cardProductId' 	=> ':product',
			'cardValue' 		=> ':value',
			'cardOrder' 		=> ':order',
			'cardCreatedDate'   => ':date',
			'cardStatus' 		=> ':status'
		];

		$params = [
			':name' 		=> $cardName,
			':product' 	=> $cardProduct,
			':value' 		=> $cardValue,
			':order' 		=> $cardOrder,
			':date' 		=> time(),
			':status' 		=> $cardStatus
		];
		
		return Database::queryBuilder()->insert( $this->table, $fields, $params );
	}

	public function updateData( $cardId, $cardName, $cardProduct, $cardValue, $cardOrder, $cardStatus = 0 )
	{
		$fields = [
			'cardName' 		=> ':name',
			'cardProductId' 	=> ':product',
			'cardValue' 		=> ':value',
			'cardOrder'		=> ':order',
			'cardStatus' 		=> ':status'
		];
		
		$params = [
			':name' 		=> $cardName,
			':product' 	=> $cardProduct,
			':value' 		=> $cardValue,
			':order' 		=> $cardOrder,
			':status' 		=> $cardStatus,
			':id' 			=> $cardId
		];
		
		return Database::queryBuilder()->update( $this->table, $fields, 'cardId = :id', $params );
	}

	public function getTotalProductCards( $productId )
	{
		return Database::queryBuilder()
				->select( 'COUNT(*) as total' )
				->from( $this->table )
				->where( 'cardProductId = :id' )
				->where( 'cardStatus = 0 AND (cardReserveDate IS NULL OR cardReserveDate < :nowTime)', 'AND' )
				->fetch( [ ':id' => $productId, ':nowTime' => time() ] );
	}

	public function getProductCards( $productId, $limit = 1 )
	{
		return Database::queryBuilder()
				->select( '*' )
				->from( $this->table )
				->where( 'cardProductId = :id' )
				->where( 'cardStatus = 0 AND (cardReserveDate IS NULL OR cardReserveDate < :nowTime)', 'AND' )
				->orderBy( 'cardId ASC' )
				->limit( $limit )
				->fetchAll( [ ':id' => $productId, ':nowTime' => time() ] );
	}

	public function getCardsByTransId( $transId, $status = 0 )
	{
		return Database::queryBuilder()
				->select( '*' )
				->from( $this->table )
				->join( '{prefix}trans_card ON transCardTransId = :id AND transCardCardId = cardId' )
				->where( 'cardStatus = :status' )
				->fetchAll( [ ':id' => $transId, ':status' => $status ] );
	}
}