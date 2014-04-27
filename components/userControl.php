<?php

namespace app\components;

use framework\session\Session;
use framework\request\Request;

class UserControl
{
	const SALT = '!@#$%^&*()';

	public static function login( $userName )
	{
		$session = Session::instance();
		$session->set( 'user.name', $userName );
		$session->set( 'user.key', static::key( $userName ) );
	}

	public static function logout()
	{
		$session = Session::instance();
		$session->delete( 'user' );
		$session->destroy();
	}

	public static function isLogged()
	{
		$session = Session::instance();
		
		if( !$session->get( 'user.name' ) or !$session->get( 'user.key' ) )
			return false;
		
		$key = static::key( $session->get( 'user.name' ) );

		if( $key === $session->get( 'user.key' ) )
		    return true;

		if( $session->has( 'user.key' ) )
			static::logout();

		return false;
	}

	public static function createSalt()
	{
		return sha1( rand() . static::SALT );
	}

	public static function createPassword( $salt, $password )
	{
		return sha1( $salt . $password );
	}

	public static function getUserName()
	{
		return Session::instance()->get( 'user.name' );
	}

	private static function key( $userName )
	{
		return sha1( $userName . Request::getRemoteAddr() . Request::getServer( 'HTTP_USER_AGENT' ));
	}
}