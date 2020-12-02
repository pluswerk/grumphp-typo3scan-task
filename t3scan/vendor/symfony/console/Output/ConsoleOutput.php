<?php










namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
@author











*/
class ConsoleOutput extends StreamOutput implements ConsoleOutputInterface
{
private $stderr;

/**
@param
@param
@param
*/
public function __construct($verbosity = self::VERBOSITY_NORMAL, $decorated = null, OutputFormatterInterface $formatter = null)
{
parent::__construct($this->openOutputStream(), $verbosity, $decorated, $formatter);

$actualDecorated = $this->isDecorated();
$this->stderr = new StreamOutput($this->openErrorStream(), $verbosity, $decorated, $this->getFormatter());

if (null === $decorated) {
$this->setDecorated($actualDecorated && $this->stderr->isDecorated());
}
}




public function setDecorated($decorated)
{
parent::setDecorated($decorated);
$this->stderr->setDecorated($decorated);
}




public function setFormatter(OutputFormatterInterface $formatter)
{
parent::setFormatter($formatter);
$this->stderr->setFormatter($formatter);
}




public function setVerbosity($level)
{
parent::setVerbosity($level);
$this->stderr->setVerbosity($level);
}




public function getErrorOutput()
{
return $this->stderr;
}




public function setErrorOutput(OutputInterface $error)
{
$this->stderr = $error;
}

/**
@return



*/
protected function hasStdoutSupport()
{
return false === $this->isRunningOS400();
}

/**
@return



*/
protected function hasStderrSupport()
{
return false === $this->isRunningOS400();
}

/**
@return



*/
private function isRunningOS400()
{
$checks = [
\function_exists('php_uname') ? php_uname('s') : '',
getenv('OSTYPE'),
\PHP_OS,
];

return false !== stripos(implode(';', $checks), 'OS400');
}

/**
@return
*/
private function openOutputStream()
{
if (!$this->hasStdoutSupport()) {
return fopen('php://output', 'w');
}

return @fopen('php://stdout', 'w') ?: fopen('php://output', 'w');
}

/**
@return
*/
private function openErrorStream()
{
return fopen($this->hasStderrSupport() ? 'php://stderr' : 'php://output', 'w');
}
}
