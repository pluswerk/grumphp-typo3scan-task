<?php










namespace Symfony\Component\Yaml;

use Symfony\Component\Yaml\Tag\TaggedValue;

/**
@author
@final



*/
class Dumper
{
/**
@var


*/
protected $indentation;

/**
@param
*/
public function __construct($indentation = 4)
{
if ($indentation < 1) {
throw new \InvalidArgumentException('The indentation must be greater than zero.');
}

$this->indentation = $indentation;
}

/**
@param
@deprecated



*/
public function setIndentation($num)
{
@trigger_error('The '.__METHOD__.' method is deprecated since Symfony 3.1 and will be removed in 4.0. Pass the indentation to the constructor instead.', \E_USER_DEPRECATED);

$this->indentation = (int) $num;
}

/**
@param
@param
@param
@param
@return



*/
public function dump($input, $inline = 0, $indent = 0, $flags = 0)
{
if (\is_bool($flags)) {
@trigger_error('Passing a boolean flag to toggle exception handling is deprecated since Symfony 3.1 and will be removed in 4.0. Use the Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE flag instead.', \E_USER_DEPRECATED);

if ($flags) {
$flags = Yaml::DUMP_EXCEPTION_ON_INVALID_TYPE;
} else {
$flags = 0;
}
}

if (\func_num_args() >= 5) {
@trigger_error('Passing a boolean flag to toggle object support is deprecated since Symfony 3.1 and will be removed in 4.0. Use the Yaml::DUMP_OBJECT flag instead.', \E_USER_DEPRECATED);

if (func_get_arg(4)) {
$flags |= Yaml::DUMP_OBJECT;
}
}

$output = '';
$prefix = $indent ? str_repeat(' ', $indent) : '';
$dumpObjectAsInlineMap = true;

if (Yaml::DUMP_OBJECT_AS_MAP & $flags && ($input instanceof \ArrayObject || $input instanceof \stdClass)) {
$dumpObjectAsInlineMap = empty((array) $input);
}

if ($inline <= 0 || (!\is_array($input) && !$input instanceof TaggedValue && $dumpObjectAsInlineMap) || empty($input)) {
$output .= $prefix.Inline::dump($input, $flags);
} else {
$dumpAsMap = Inline::isHash($input);

foreach ($input as $key => $value) {
if ($inline >= 1 && Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK & $flags && \is_string($value) && false !== strpos($value, "\n") && false === strpos($value, "\r")) {

 
 $blockIndentationIndicator = (' ' === substr($value, 0, 1)) ? (string) $this->indentation : '';
$output .= sprintf("%s%s%s |%s\n", $prefix, $dumpAsMap ? Inline::dump($key, $flags).':' : '-', '', $blockIndentationIndicator);

foreach (explode("\n", $value) as $row) {
$output .= sprintf("%s%s%s\n", $prefix, str_repeat(' ', $this->indentation), $row);
}

continue;
}

if ($value instanceof TaggedValue) {
$output .= sprintf('%s%s !%s', $prefix, $dumpAsMap ? Inline::dump($key, $flags).':' : '-', $value->getTag());

if ($inline >= 1 && Yaml::DUMP_MULTI_LINE_LITERAL_BLOCK & $flags && \is_string($value->getValue()) && false !== strpos($value->getValue(), "\n") && false === strpos($value->getValue(), "\r\n")) {

 
 $blockIndentationIndicator = (' ' === substr($value->getValue(), 0, 1)) ? (string) $this->indentation : '';
$output .= sprintf(" |%s\n", $blockIndentationIndicator);

foreach (explode("\n", $value->getValue()) as $row) {
$output .= sprintf("%s%s%s\n", $prefix, str_repeat(' ', $this->indentation), $row);
}

continue;
}

if ($inline - 1 <= 0 || null === $value->getValue() || is_scalar($value->getValue())) {
$output .= ' '.$this->dump($value->getValue(), $inline - 1, 0, $flags)."\n";
} else {
$output .= "\n";
$output .= $this->dump($value->getValue(), $inline - 1, $dumpAsMap ? $indent + $this->indentation : $indent + 2, $flags);
}

continue;
}

$dumpObjectAsInlineMap = true;

if (Yaml::DUMP_OBJECT_AS_MAP & $flags && ($value instanceof \ArrayObject || $value instanceof \stdClass)) {
$dumpObjectAsInlineMap = empty((array) $value);
}

$willBeInlined = $inline - 1 <= 0 || !\is_array($value) && $dumpObjectAsInlineMap || empty($value);

$output .= sprintf('%s%s%s%s',
$prefix,
$dumpAsMap ? Inline::dump($key, $flags).':' : '-',
$willBeInlined ? ' ' : "\n",
$this->dump($value, $inline - 1, $willBeInlined ? 0 : $indent + $this->indentation, $flags)
).($willBeInlined ? "\n" : '');
}
}

return $output;
}
}
