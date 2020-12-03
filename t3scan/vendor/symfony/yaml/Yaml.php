<?php










namespace Symfony\Component\Yaml;

use Symfony\Component\Yaml\Exception\ParseException;

/**
@author
@final



*/
class Yaml
{
const DUMP_OBJECT = 1;
const PARSE_EXCEPTION_ON_INVALID_TYPE = 2;
const PARSE_OBJECT = 4;
const PARSE_OBJECT_FOR_MAP = 8;
const DUMP_EXCEPTION_ON_INVALID_TYPE = 16;
const PARSE_DATETIME = 32;
const DUMP_OBJECT_AS_MAP = 64;
const DUMP_MULTI_LINE_LITERAL_BLOCK = 128;
const PARSE_CONSTANT = 256;
const PARSE_CUSTOM_TAGS = 512;
const DUMP_EMPTY_ARRAY_AS_SEQUENCE = 1024;

/**
@deprecated
*/
const PARSE_KEYS_AS_STRINGS = 2048;

/**
@param
@param
@return
@throws









*/
public static function parseFile($filename, $flags = 0)
{
$yaml = new Parser();

return $yaml->parseFile($filename, $flags);
}

/**
@param
@param
@return
@throws










*/
public static function parse($input, $flags = 0)
{
if (\is_bool($flags)) {
@trigger_error('Passing a boolean flag to toggle exception handling is deprecated since Symfony 3.1 and will be removed in 4.0. Use the PARSE_EXCEPTION_ON_INVALID_TYPE flag instead.', \E_USER_DEPRECATED);

if ($flags) {
$flags = self::PARSE_EXCEPTION_ON_INVALID_TYPE;
} else {
$flags = 0;
}
}

if (\func_num_args() >= 3) {
@trigger_error('Passing a boolean flag to toggle object support is deprecated since Symfony 3.1 and will be removed in 4.0. Use the PARSE_OBJECT flag instead.', \E_USER_DEPRECATED);

if (func_get_arg(2)) {
$flags |= self::PARSE_OBJECT;
}
}

if (\func_num_args() >= 4) {
@trigger_error('Passing a boolean flag to toggle object for map support is deprecated since Symfony 3.1 and will be removed in 4.0. Use the Yaml::PARSE_OBJECT_FOR_MAP flag instead.', \E_USER_DEPRECATED);

if (func_get_arg(3)) {
$flags |= self::PARSE_OBJECT_FOR_MAP;
}
}

$yaml = new Parser();

return $yaml->parse($input, $flags);
}

/**
@param
@param
@param
@param
@return






*/
public static function dump($input, $inline = 2, $indent = 4, $flags = 0)
{
if (\is_bool($flags)) {
@trigger_error('Passing a boolean flag to toggle exception handling is deprecated since Symfony 3.1 and will be removed in 4.0. Use the DUMP_EXCEPTION_ON_INVALID_TYPE flag instead.', \E_USER_DEPRECATED);

if ($flags) {
$flags = self::DUMP_EXCEPTION_ON_INVALID_TYPE;
} else {
$flags = 0;
}
}

if (\func_num_args() >= 5) {
@trigger_error('Passing a boolean flag to toggle object support is deprecated since Symfony 3.1 and will be removed in 4.0. Use the DUMP_OBJECT flag instead.', \E_USER_DEPRECATED);

if (func_get_arg(4)) {
$flags |= self::DUMP_OBJECT;
}
}

$yaml = new Dumper($indent);

return $yaml->dump($input, $inline, 0, $flags);
}
}
