<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
@author
*/
class TableCell
{
private $value;
private $options = [
'rowspan' => 1,
'colspan' => 1,
];

/**
@param
*/
public function __construct($value = '', array $options = [])
{
if (is_numeric($value) && !\is_string($value)) {
$value = (string) $value;
}

$this->value = $value;


 if ($diff = array_diff(array_keys($options), array_keys($this->options))) {
throw new InvalidArgumentException(sprintf('The TableCell does not support the following options: \'%s\'.', implode('\', \'', $diff)));
}

$this->options = array_merge($this->options, $options);
}

/**
@return


*/
public function __toString()
{
return $this->value;
}

/**
@return


*/
public function getColspan()
{
return (int) $this->options['colspan'];
}

/**
@return


*/
public function getRowspan()
{
return (int) $this->options['rowspan'];
}
}
