<?php










namespace Symfony\Component\Process;

@trigger_error(sprintf('The %s class is deprecated since Symfony 3.4 and will be removed in 4.0. Use the Process class instead.', ProcessBuilder::class), \E_USER_DEPRECATED);

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;

/**
@author
@deprecated

*/
class ProcessBuilder
{
private $arguments;
private $cwd;
private $env = [];
private $input;
private $timeout = 60;
private $options;
private $inheritEnv = true;
private $prefix = [];
private $outputDisabled = false;

/**
@param
*/
public function __construct(array $arguments = [])
{
$this->arguments = $arguments;
}

/**
@param
@return



*/
public static function create(array $arguments = [])
{
return new static($arguments);
}

/**
@param
@return



*/
public function add($argument)
{
$this->arguments[] = $argument;

return $this;
}

/**
@param
@return





*/
public function setPrefix($prefix)
{
$this->prefix = \is_array($prefix) ? $prefix : [$prefix];

return $this;
}

/**
@param
@return






*/
public function setArguments(array $arguments)
{
$this->arguments = $arguments;

return $this;
}

/**
@param
@return



*/
public function setWorkingDirectory($cwd)
{
$this->cwd = $cwd;

return $this;
}

/**
@param
@return



*/
public function inheritEnvironmentVariables($inheritEnv = true)
{
$this->inheritEnv = $inheritEnv;

return $this;
}

/**
@param
@param
@return






*/
public function setEnv($name, $value)
{
$this->env[$name] = $value;

return $this;
}

/**
@param
@return







*/
public function addEnvironmentVariables(array $variables)
{
$this->env = array_replace($this->env, $variables);

return $this;
}

/**
@param
@return
@throws




*/
public function setInput($input)
{
$this->input = ProcessUtils::validateInput(__METHOD__, $input);

return $this;
}

/**
@param
@return
@throws






*/
public function setTimeout($timeout)
{
if (null === $timeout) {
$this->timeout = null;

return $this;
}

$timeout = (float) $timeout;

if ($timeout < 0) {
throw new InvalidArgumentException('The timeout value must be a valid positive integer or float number.');
}

$this->timeout = $timeout;

return $this;
}

/**
@param
@param
@return



*/
public function setOption($name, $value)
{
$this->options[$name] = $value;

return $this;
}

/**
@return


*/
public function disableOutput()
{
$this->outputDisabled = true;

return $this;
}

/**
@return


*/
public function enableOutput()
{
$this->outputDisabled = false;

return $this;
}

/**
@return
@throws



*/
public function getProcess()
{
if (0 === \count($this->prefix) && 0 === \count($this->arguments)) {
throw new LogicException('You must add() command arguments before calling getProcess().');
}

$arguments = array_merge($this->prefix, $this->arguments);
$process = new Process($arguments, $this->cwd, $this->env, $this->input, $this->timeout, $this->options);

 
 $process->setCommandLine($process->getCommandLine());

if ($this->inheritEnv) {
$process->inheritEnvironmentVariables();
}
if ($this->outputDisabled) {
$process->disableOutput();
}

return $process;
}
}
