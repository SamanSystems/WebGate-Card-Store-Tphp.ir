<?php

namespace app\components\modules\payment;

use framework\session\Session;
use framework\request\Request;
use app\models\Trans;

abstract class Payment
{
	abstract public function fields();

	abstract public function request( $id, $au, $price, $module, $product );

	abstract public function verify( $id, $au, $price, $module, $product );

	protected function getRial( $price )
	{
		return ( $price*10 );
	}

	protected function redirect( $url )
	{
		Request::redirect( $url );
	}

	public function getController()
	{
		return \Framework::instance()->controller;
	}

	public function lang()
	{
		return $this->getController()->lang;
	}

	public function setFlash( $key, $value )
	{
		Session::instance()->setFlash( $key, $value );
	}

	public function updateAu( $id, $au )
	{
		return Trans::model()->update( [ 'fields' => [ 'transGatewayAu' => ':au' ], 'conditions' => 'transId = :id', 'params' => [ ':au' => $au, ':id' => $id ] ] );
	}

	public function getCallbackUrl( $au, $encode = false )
	{
		$url = \Framework::createUrl( 'site/payment/verify', [ $au ] );

		return ( $encode ? urlencode( $url ) : $url );
	}

	public function render( $view, $vars = [], $terminate = false )
	{
		$this->getController()->render( $view, $vars, $terminate );
	}
}