<?php










namespace Symfony\Component\Console\Event;

@trigger_error(sprintf('The "%s" class is deprecated since Symfony 3.3 and will be removed in 4.0. Use the ConsoleErrorEvent instead.', ConsoleExceptionEvent::class), \E_USER_DEPRECATED);

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
@author
@deprecated



*/
class ConsoleExceptionEvent extends ConsoleEvent
{
private $exception;
private $exitCode;

public function __construct(Command $command, InputInterface $input, OutputInterface $output, \Exception $exception, $exitCode)
{
parent::__construct($command, $input, $output);

$this->setException($exception);
$this->exitCode = (int) $exitCode;
}

/**
@return


*/
public function getException()
{
return $this->exception;
}

/**
@param




*/
public function setException(\Exception $exception)
{
$this->exception = $exception;
}

/**
@return


*/
public function getExitCode()
{
return $this->exitCode;
}
}
