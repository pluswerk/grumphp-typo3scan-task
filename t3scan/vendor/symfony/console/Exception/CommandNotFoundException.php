<?php










namespace Symfony\Component\Console\Exception;

/**
@author


*/
class CommandNotFoundException extends \InvalidArgumentException implements ExceptionInterface
{
private $alternatives;

/**
@param
@param
@param
@param
*/
public function __construct($message, array $alternatives = [], $code = 0, \Exception $previous = null)
{
parent::__construct($message, $code, $previous);

$this->alternatives = $alternatives;
}

/**
@return
*/
public function getAlternatives()
{
return $this->alternatives;
}
}
