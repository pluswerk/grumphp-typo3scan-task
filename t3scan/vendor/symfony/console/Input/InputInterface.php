<?php










namespace Symfony\Component\Console\Input;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;

/**
@author


*/
interface InputInterface
{
/**
@return


*/
public function getFirstArgument();

/**
@param
@param
@return








*/
public function hasParameterOption($values, $onlyParams = false);

/**
@param
@param
@param
@return








*/
public function getParameterOption($values, $default = false, $onlyParams = false);

/**
@throws


*/
public function bind(InputDefinition $definition);

/**
@throws


*/
public function validate();

/**
@return


*/
public function getArguments();

/**
@param
@return
@throws




*/
public function getArgument($name);

/**
@param
@param
@throws



*/
public function setArgument($name, $value);

/**
@param
@return



*/
public function hasArgument($name);

/**
@return


*/
public function getOptions();

/**
@param
@return
@throws




*/
public function getOption($name);

/**
@param
@param
@throws



*/
public function setOption($name, $value);

/**
@param
@return



*/
public function hasOption($name);

/**
@return


*/
public function isInteractive();

/**
@param


*/
public function setInteractive($interactive);
}
