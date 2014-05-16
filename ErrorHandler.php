<?php
/**
 * Created by PhpStorm.
 * User: prgtw
 * Date: 5/20/14
 * Time: 2:40 PM
 */

namespace prgTW\ErrorHandlerBundle;

use prgTW\ErrorHandler\ErrorHandler as BaseErrorHandler;

class ErrorHandler extends BaseErrorHandler
{
	/** {@inheritdoc} */
	public function handleError($projectName, $errNo, $errStr, $errFile, $errLine, $errContext = array())
	{

	}

	/** {@inheritdoc} */
	public function handleException($projectName, \Exception $exception)
	{

	}
}
