<?php










namespace Symfony\Component\Console\CommandLoader;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Exception\CommandNotFoundException;

/**
@author
*/
interface CommandLoaderInterface
{
/**
@param
@return
@throws




*/
public function get($name);

/**
@param
@return



*/
public function has($name);

/**
@return
*/
public function getNames();
}
