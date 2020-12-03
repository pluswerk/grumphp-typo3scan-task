<?php










namespace Symfony\Component\Console\Input;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;

/**
@author


*/
class InputArgument
{
const REQUIRED = 1;
const OPTIONAL = 2;
const IS_ARRAY = 4;

private $name;
private $mode;
private $default;
private $description;

/**
@param
@param
@param
@param
@throws

*/
public function __construct($name, $mode = null, $description = '', $default = null)
{
if (null === $mode) {
$mode = self::OPTIONAL;
} elseif (!\is_int($mode) || $mode > 7 || $mode < 1) {
throw new InvalidArgumentException(sprintf('Argument mode "%s" is not valid.', $mode));
}

$this->name = $name;
$this->mode = $mode;
$this->description = $description;

$this->setDefault($default);
}

/**
@return


*/
public function getName()
{
return $this->name;
}

/**
@return


*/
public function isRequired()
{
return self::REQUIRED === (self::REQUIRED & $this->mode);
}

/**
@return


*/
public function isArray()
{
return self::IS_ARRAY === (self::IS_ARRAY & $this->mode);
}

/**
@param
@throws



*/
public function setDefault($default = null)
{
if (self::REQUIRED === $this->mode && null !== $default) {
throw new LogicException('Cannot set a default value except for InputArgument::OPTIONAL mode.');
}

if ($this->isArray()) {
if (null === $default) {
$default = [];
} elseif (!\is_array($default)) {
throw new LogicException('A default value for an array argument must be an array.');
}
}

$this->default = $default;
}

/**
@return


*/
public function getDefault()
{
return $this->default;
}

/**
@return


*/
public function getDescription()
{
return $this->description;
}
}
