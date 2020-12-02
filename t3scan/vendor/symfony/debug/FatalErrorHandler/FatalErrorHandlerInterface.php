<?php










namespace Symfony\Component\Debug\FatalErrorHandler;

use Symfony\Component\Debug\Exception\FatalErrorException;

/**
@author


*/
interface FatalErrorHandlerInterface
{
/**
@param
@param
@return



*/
public function handleError(array $error, FatalErrorException $exception);
}
