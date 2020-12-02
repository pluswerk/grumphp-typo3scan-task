<?php










namespace Symfony\Component\Finder\Comparator;

/**
@author


*/
class Comparator
{
private $target;
private $operator = '==';

/**
@return


*/
public function getTarget()
{
return $this->target;
}

/**
@param


*/
public function setTarget($target)
{
$this->target = $target;
}

/**
@return


*/
public function getOperator()
{
return $this->operator;
}

/**
@param
@throws



*/
public function setOperator($operator)
{
if (!$operator) {
$operator = '==';
}

if (!\in_array($operator, ['>', '<', '>=', '<=', '==', '!='])) {
throw new \InvalidArgumentException(sprintf('Invalid operator "%s".', $operator));
}

$this->operator = $operator;
}

/**
@param
@return



*/
public function test($test)
{
switch ($this->operator) {
case '>':
return $test > $this->target;
case '>=':
return $test >= $this->target;
case '<':
return $test < $this->target;
case '<=':
return $test <= $this->target;
case '!=':
return $test != $this->target;
}

return $test == $this->target;
}
}
