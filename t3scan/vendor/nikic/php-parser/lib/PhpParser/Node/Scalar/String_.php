<?php declare(strict_types=1);

namespace PhpParser\Node\Scalar;

use PhpParser\Error;
use PhpParser\Node\Scalar;

class String_ extends Scalar
{

const KIND_SINGLE_QUOTED = 1;
const KIND_DOUBLE_QUOTED = 2;
const KIND_HEREDOC = 3;
const KIND_NOWDOC = 4;

/**
@var */
public $value;

protected static $replacements = [
'\\' => '\\',
'$' => '$',
'n' => "\n",
'r' => "\r",
't' => "\t",
'f' => "\f",
'v' => "\v",
'e' => "\x1B",
];

/**
@param
@param


*/
public function __construct(string $value, array $attributes = []) {
$this->attributes = $attributes;
$this->value = $value;
}

public function getSubNodeNames() : array {
return ['value'];
}

/**
@internal
@param
@param
@return




*/
public static function parse(string $str, bool $parseUnicodeEscape = true) : string {
$bLength = 0;
if ('b' === $str[0] || 'B' === $str[0]) {
$bLength = 1;
}

if ('\'' === $str[$bLength]) {
return str_replace(
['\\\\', '\\\''],
['\\', '\''],
substr($str, $bLength + 1, -1)
);
} else {
return self::parseEscapeSequences(
substr($str, $bLength + 1, -1), '"', $parseUnicodeEscape
);
}
}

/**
@internal
@param
@param
@param
@return




*/
public static function parseEscapeSequences(string $str, $quote, bool $parseUnicodeEscape = true) : string {
if (null !== $quote) {
$str = str_replace('\\' . $quote, $quote, $str);
}

$extra = '';
if ($parseUnicodeEscape) {
$extra = '|u\{([0-9a-fA-F]+)\}';
}

return preg_replace_callback(
'~\\\\([\\\\$nrtfve]|[xX][0-9a-fA-F]{1,2}|[0-7]{1,3}' . $extra . ')~',
function($matches) {
$str = $matches[1];

if (isset(self::$replacements[$str])) {
return self::$replacements[$str];
} elseif ('x' === $str[0] || 'X' === $str[0]) {
return chr(hexdec(substr($str, 1)));
} elseif ('u' === $str[0]) {
return self::codePointToUtf8(hexdec($matches[2]));
} else {
return chr(octdec($str));
}
},
$str
);
}

/**
@param
@return



*/
private static function codePointToUtf8(int $num) : string {
if ($num <= 0x7F) {
return chr($num);
}
if ($num <= 0x7FF) {
return chr(($num>>6) + 0xC0) . chr(($num&0x3F) + 0x80);
}
if ($num <= 0xFFFF) {
return chr(($num>>12) + 0xE0) . chr((($num>>6)&0x3F) + 0x80) . chr(($num&0x3F) + 0x80);
}
if ($num <= 0x1FFFFF) {
return chr(($num>>18) + 0xF0) . chr((($num>>12)&0x3F) + 0x80)
. chr((($num>>6)&0x3F) + 0x80) . chr(($num&0x3F) + 0x80);
}
throw new Error('Invalid UTF-8 codepoint escape sequence: Codepoint too large');
}

public function getType() : string {
return 'Scalar_String';
}
}
