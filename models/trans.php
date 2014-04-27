<?php

namespace app\models;

use framework\core\Model;
use framework\database\Database;

class Trans extends Model
{
	protected $table = 'trans';

	public static function model( $class = __CLASS__ )
	{
		return parent::model( $class );
	}

	public function insertData( $au, $reserveDate, $product, $price, $total, $email, $mobile, $content, $gateway )
	{
		$fields = [
			'transAu' 			=> ':au',
			'transReserveDate' 	=> ':rdate',
			'transProductId' 		=> ':product',
			'transPrice' 			=> ':price',
			'transTotalCard' 		=> ':total',
			'transEmail' 			=> ':email',
			'transMobile' 		=> ':mobile',
			'transContent' 		=> ':content',
			'transModuleId' 		=> ':gateway',
			'transCreatedDate' 	=> ':date',
		];
		
		$params = [
			':au' 		=> $au,
			':rdate' 		=> $reserveDate,
			':product' 	=> $product,
			':price' 		=> $price,
			':total' 		=> $total,
			':email' 		=> $email,
			':mobile'	=> ($mobile ? $mobile : NULL),
			':content' 	=> ($content ? $content : NULL),
			':gateway' 	=> $gateway,
			':date' 		=> time(),
		];
		
		Database::queryBuilder()->insert( $this->table, $fields, $params );
		
		return Database::connection()->lastInsertId();
	}

	public function getTrans( $au )
	{
		return Database::queryBuilder()
				->select( '*' )
				->from( $this->table )
				->join( '{prefix}module ON moduleId = transModuleId AND moduleStatus = 1', 'LEFT JOIN' )
				->join( '{prefix}product ON productId = transProductId AND productStatus = 1', 'LEFT JOIN' )
				->where( 'transAu = :au' )
				->fetch( [ ':au' => $au ] );
	}

	public function getTransFactor( $transId )
	{
		return Database::queryBuilder()
				->select( '*' )
				->from( $this->table )
				->join( '{prefix}module ON moduleId = transModuleId AND moduleStatus = 1', 'LEFT JOIN' )
				->join( '{prefix}product ON productId = transProductId AND productStatus = 1', 'LEFT JOIN' )
				->join( '{prefix}trans_info ON transInfoTransId = transId', 'LEFT JOIN' )
				->where( 'transId = :id' )
				->fetch( [ ':id' => $transId ] );
	}
}