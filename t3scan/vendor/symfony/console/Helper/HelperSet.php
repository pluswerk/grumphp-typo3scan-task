<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
@author


*/
class HelperSet implements \IteratorAggregate
{
/**
@var
*/
private $helpers = [];
private $command;

/**
@param
*/
public function __construct(array $helpers = [])
{
foreach ($helpers as $alias => $helper) {
$this->set($helper, \is_int($alias) ? null : $alias);
}
}

/**
@param
@param


*/
public function set(HelperInterface $helper, $alias = null)
{
$this->helpers[$helper->getName()] = $helper;
if (null !== $alias) {
$this->helpers[$alias] = $helper;
}

$helper->setHelperSet($this);
}

/**
@param
@return



*/
public function has($name)
{
return isset($this->helpers[$name]);
}

/**
@param
@return
@throws




*/
public function get($name)
{
if (!$this->has($name)) {
throw new InvalidArgumentException(sprintf('The helper "%s" is not defined.', $name));
}

return $this->helpers[$name];
}

public function setCommand(Command $command = null)
{
$this->command = $command;
}

/**
@return


*/
public function getCommand()
{
return $this->command;
}

/**
@return
*/
public function getIterator()
{
return new \ArrayIterator($this->helpers);
}
}
