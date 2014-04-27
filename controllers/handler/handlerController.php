<?php

namespace app\controllers\handler;

use framework\core\Controller;

class HandlerController extends Controller
{
	public $theme = 'handler';

	public function exception( $exception, $mode )
	{
		$this->render( 'handler/exception', [ 'exception' => $exception, 'mode' => $mode ] );
	}
}