<?php










namespace Symfony\Component\Console\Event;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
@author


*/
final class ConsoleErrorEvent extends ConsoleEvent
{
private $error;
private $exitCode;

public function __construct(InputInterface $input, OutputInterface $output, $error, Command $command = null)
{
parent::__construct($command, $input, $output);

$this->setError($error);
}

/**
@return


*/
public function getError()
{
return $this->error;
}

/**
@param


*/
public function setError($error)
{
if (!$error instanceof \Throwable && !$error instanceof \Exception) {
throw new InvalidArgumentException(sprintf('The error passed to ConsoleErrorEvent must be an instance of \Throwable or \Exception, "%s" was passed instead.', \is_object($error) ? \get_class($error) : \gettype($error)));
}

$this->error = $error;
}

/**
@param


*/
public function setExitCode($exitCode)
{
$this->exitCode = (int) $exitCode;

$r = new \ReflectionProperty($this->error, 'code');
$r->setAccessible(true);
$r->setValue($this->error, $this->exitCode);
}

/**
@return


*/
public function getExitCode()
{
return null !== $this->exitCode ? $this->exitCode : (\is_int($this->error->getCode()) && 0 !== $this->error->getCode() ? $this->error->getCode() : 1);
}
}
