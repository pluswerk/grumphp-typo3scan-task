<?php










namespace Symfony\Component\Yaml;

use Symfony\Component\Yaml\Exception\ParseException;

/**
@author
@internal




*/
class Unescaper
{



const REGEX_ESCAPED_CHARACTER = '\\\\(x[0-9a-fA-F]{2}|u[0-9a-fA-F]{4}|U[0-9a-fA-F]{8}|.)';

/**
@param
@return



*/
public function unescapeSingleQuotedString($value)
{
return str_replace('\'\'', '\'', $value);
}

/**
@param
@return



*/
public function unescapeDoubleQuotedString($value)
{
$callback = function ($match) {
return $this->unescapeCharacter($match[0]);
};


 return preg_replace_callback('/'.self::REGEX_ESCAPED_CHARACTER.'/u', $callback, $value);
}

/**
@param
@return



*/
private function unescapeCharacter($value)
{
switch ($value[1]) {
case '0':
return "\x0";
case 'a':
return "\x7";
case 'b':
return "\x8";
case 't':
return "\t";
case "\t":
return "\t";
case 'n':
return "\n";
case 'v':
return "\xB";
case 'f':
return "\xC";
case 'r':
return "\r";
case 'e':
return "\x1B";
case ' ':
return ' ';
case '"':
return '"';
case '/':
return '/';
case '\\':
return '\\';
case 'N':

 return "\xC2\x85";
case '_':

 return "\xC2\xA0";
case 'L':

 return "\xE2\x80\xA8";
case 'P':

 return "\xE2\x80\xA9";
case 'x':
return self::utf8chr(hexdec(substr($value, 2, 2)));
case 'u':
return self::utf8chr(hexdec(substr($value, 2, 4)));
case 'U':
return self::utf8chr(hexdec(substr($value, 2, 8)));
default:
throw new ParseException(sprintf('Found unknown escape character "%s".', $value));
}
}

/**
@param
@return



*/
private static function utf8chr($c)
{
if (0x80 > $c %= 0x200000) {
return \chr($c);
}
if (0x800 > $c) {
return \chr(0xC0 | $c >> 6).\chr(0x80 | $c & 0x3F);
}
if (0x10000 > $c) {
return \chr(0xE0 | $c >> 12).\chr(0x80 | $c >> 6 & 0x3F).\chr(0x80 | $c & 0x3F);
}

return \chr(0xF0 | $c >> 18).\chr(0x80 | $c >> 12 & 0x3F).\chr(0x80 | $c >> 6 & 0x3F).\chr(0x80 | $c & 0x3F);
}
}
