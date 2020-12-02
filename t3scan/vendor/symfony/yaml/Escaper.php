<?php










namespace Symfony\Component\Yaml;

/**
@author
@internal




*/
class Escaper
{

 const REGEX_CHARACTER_TO_ESCAPE = "[\\x00-\\x1f]|\x7f|\xc2\x85|\xc2\xa0|\xe2\x80\xa8|\xe2\x80\xa9";


 
 
 
 private static $escapees = ['\\', '\\\\', '\\"', '"',
"\x00", "\x01", "\x02", "\x03", "\x04", "\x05", "\x06", "\x07",
"\x08", "\x09", "\x0a", "\x0b", "\x0c", "\x0d", "\x0e", "\x0f",
"\x10", "\x11", "\x12", "\x13", "\x14", "\x15", "\x16", "\x17",
"\x18", "\x19", "\x1a", "\x1b", "\x1c", "\x1d", "\x1e", "\x1f",
"\x7f",
"\xc2\x85", "\xc2\xa0", "\xe2\x80\xa8", "\xe2\x80\xa9",
];
private static $escaped = ['\\\\', '\\"', '\\\\', '\\"',
'\\0', '\\x01', '\\x02', '\\x03', '\\x04', '\\x05', '\\x06', '\\a',
'\\b', '\\t', '\\n', '\\v', '\\f', '\\r', '\\x0e', '\\x0f',
'\\x10', '\\x11', '\\x12', '\\x13', '\\x14', '\\x15', '\\x16', '\\x17',
'\\x18', '\\x19', '\\x1a', '\\e', '\\x1c', '\\x1d', '\\x1e', '\\x1f',
'\\x7f',
'\\N', '\\_', '\\L', '\\P',
];

/**
@param
@return



*/
public static function requiresDoubleQuoting($value)
{
return 0 < preg_match('/'.self::REGEX_CHARACTER_TO_ESCAPE.'/u', $value);
}

/**
@param
@return



*/
public static function escapeWithDoubleQuotes($value)
{
return sprintf('"%s"', str_replace(self::$escapees, self::$escaped, $value));
}

/**
@param
@return



*/
public static function requiresSingleQuoting($value)
{

 
 if (\in_array(strtolower($value), ['null', '~', 'true', 'false', 'y', 'n', 'yes', 'no', 'on', 'off'])) {
return true;
}


 
 return 0 < preg_match('/[ \s \' " \: \{ \} \[ \] , & \* \# \?] | \A[ \- ? | < > = ! % @ ` ]/x', $value);
}

/**
@param
@return



*/
public static function escapeWithSingleQuotes($value)
{
return sprintf("'%s'", str_replace('\'', '\'\'', $value));
}
}
