<?php










namespace Symfony\Component\Process;

use Symfony\Component\Process\Exception\InvalidArgumentException;

/**
@author




*/
class ProcessUtils
{



private function __construct()
{
}

/**
@param
@return
@deprecated




*/
public static function escapeArgument($argument)
{
@trigger_error('The '.__METHOD__.'() method is deprecated since Symfony 3.3 and will be removed in 4.0. Use a command line array or give env vars to the Process::start/run() method instead.', \E_USER_DEPRECATED);


 
 
 
 if ('\\' === \DIRECTORY_SEPARATOR) {
if ('' === $argument) {
return escapeshellarg($argument);
}

$escapedArgument = '';
$quote = false;
foreach (preg_split('/(")/', $argument, -1, \PREG_SPLIT_NO_EMPTY | \PREG_SPLIT_DELIM_CAPTURE) as $part) {
if ('"' === $part) {
$escapedArgument .= '\\"';
} elseif (self::isSurroundedBy($part, '%')) {

 $escapedArgument .= '^%"'.substr($part, 1, -1).'"^%';
} else {

 if ('\\' === substr($part, -1)) {
$part .= '\\';
}
$quote = true;
$escapedArgument .= $part;
}
}
if ($quote) {
$escapedArgument = '"'.$escapedArgument.'"';
}

return $escapedArgument;
}

return "'".str_replace("'", "'\\''", $argument)."'";
}

/**
@param
@param
@return
@throws




*/
public static function validateInput($caller, $input)
{
if (null !== $input) {
if (\is_resource($input)) {
return $input;
}
if (\is_string($input)) {
return $input;
}
if (is_scalar($input)) {
return (string) $input;
}
if ($input instanceof Process) {
return $input->getIterator($input::ITER_SKIP_ERR);
}
if ($input instanceof \Iterator) {
return $input;
}
if ($input instanceof \Traversable) {
return new \IteratorIterator($input);
}

throw new InvalidArgumentException(sprintf('"%s" only accepts strings, Traversable objects or stream resources.', $caller));
}

return $input;
}

private static function isSurroundedBy($arg, $char)
{
return 2 < \strlen($arg) && $char === $arg[0] && $char === $arg[\strlen($arg) - 1];
}
}
