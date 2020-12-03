<?php










namespace Symfony\Component\Debug\Exception;

/**
@author
@deprecated



*/
class ContextErrorException extends \ErrorException
{
private $context = [];

public function __construct($message, $code, $severity, $filename, $lineno, $context = [])
{
parent::__construct($message, $code, $severity, $filename, $lineno);
$this->context = $context;
}

/**
@return
*/
public function getContext()
{
@trigger_error(sprintf('The %s class is deprecated since Symfony 3.3 and will be removed in 4.0.', __CLASS__), \E_USER_DEPRECATED);

return $this->context;
}
}
