<?php










namespace Twig\Extension {
use Twig\ExpressionParser;
use Twig\Node\Expression\Binary\AddBinary;
use Twig\Node\Expression\Binary\AndBinary;
use Twig\Node\Expression\Binary\BitwiseAndBinary;
use Twig\Node\Expression\Binary\BitwiseOrBinary;
use Twig\Node\Expression\Binary\BitwiseXorBinary;
use Twig\Node\Expression\Binary\ConcatBinary;
use Twig\Node\Expression\Binary\DivBinary;
use Twig\Node\Expression\Binary\EndsWithBinary;
use Twig\Node\Expression\Binary\EqualBinary;
use Twig\Node\Expression\Binary\FloorDivBinary;
use Twig\Node\Expression\Binary\GreaterBinary;
use Twig\Node\Expression\Binary\GreaterEqualBinary;
use Twig\Node\Expression\Binary\InBinary;
use Twig\Node\Expression\Binary\LessBinary;
use Twig\Node\Expression\Binary\LessEqualBinary;
use Twig\Node\Expression\Binary\MatchesBinary;
use Twig\Node\Expression\Binary\ModBinary;
use Twig\Node\Expression\Binary\MulBinary;
use Twig\Node\Expression\Binary\NotEqualBinary;
use Twig\Node\Expression\Binary\NotInBinary;
use Twig\Node\Expression\Binary\OrBinary;
use Twig\Node\Expression\Binary\PowerBinary;
use Twig\Node\Expression\Binary\RangeBinary;
use Twig\Node\Expression\Binary\SpaceshipBinary;
use Twig\Node\Expression\Binary\StartsWithBinary;
use Twig\Node\Expression\Binary\SubBinary;
use Twig\Node\Expression\Filter\DefaultFilter;
use Twig\Node\Expression\NullCoalesceExpression;
use Twig\Node\Expression\Test\ConstantTest;
use Twig\Node\Expression\Test\DefinedTest;
use Twig\Node\Expression\Test\DivisiblebyTest;
use Twig\Node\Expression\Test\EvenTest;
use Twig\Node\Expression\Test\NullTest;
use Twig\Node\Expression\Test\OddTest;
use Twig\Node\Expression\Test\SameasTest;
use Twig\Node\Expression\Unary\NegUnary;
use Twig\Node\Expression\Unary\NotUnary;
use Twig\Node\Expression\Unary\PosUnary;
use Twig\NodeVisitor\MacroAutoImportNodeVisitor;
use Twig\TokenParser\ApplyTokenParser;
use Twig\TokenParser\BlockTokenParser;
use Twig\TokenParser\DeprecatedTokenParser;
use Twig\TokenParser\DoTokenParser;
use Twig\TokenParser\EmbedTokenParser;
use Twig\TokenParser\ExtendsTokenParser;
use Twig\TokenParser\FilterTokenParser;
use Twig\TokenParser\FlushTokenParser;
use Twig\TokenParser\ForTokenParser;
use Twig\TokenParser\FromTokenParser;
use Twig\TokenParser\IfTokenParser;
use Twig\TokenParser\ImportTokenParser;
use Twig\TokenParser\IncludeTokenParser;
use Twig\TokenParser\MacroTokenParser;
use Twig\TokenParser\SetTokenParser;
use Twig\TokenParser\SpacelessTokenParser;
use Twig\TokenParser\UseTokenParser;
use Twig\TokenParser\WithTokenParser;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

final class CoreExtension extends AbstractExtension
{
private $dateFormats = ['F j, Y H:i', '%d days'];
private $numberFormat = [0, '.', ','];
private $timezone = null;
private $escapers = [];

/**
@param
@param
@deprecated



*/
public function setEscaper($strategy, callable $callable)
{
@trigger_error(sprintf('The "%s" method is deprecated since Twig 2.11; use "%s::setEscaper" instead.', __METHOD__, EscaperExtension::class), E_USER_DEPRECATED);

$this->escapers[$strategy] = $callable;
}

/**
@return
@deprecated



*/
public function getEscapers()
{
if (0 === \func_num_args() || func_get_arg(0)) {
@trigger_error(sprintf('The "%s" method is deprecated since Twig 2.11; use "%s::getEscapers" instead.', __METHOD__, EscaperExtension::class), E_USER_DEPRECATED);
}

return $this->escapers;
}

/**
@param
@param


*/
public function setDateFormat($format = null, $dateIntervalFormat = null)
{
if (null !== $format) {
$this->dateFormats[0] = $format;
}

if (null !== $dateIntervalFormat) {
$this->dateFormats[1] = $dateIntervalFormat;
}
}

/**
@return


*/
public function getDateFormat()
{
return $this->dateFormats;
}

/**
@param


*/
public function setTimezone($timezone)
{
$this->timezone = $timezone instanceof \DateTimeZone ? $timezone : new \DateTimeZone($timezone);
}

/**
@return


*/
public function getTimezone()
{
if (null === $this->timezone) {
$this->timezone = new \DateTimeZone(date_default_timezone_get());
}

return $this->timezone;
}

/**
@param
@param
@param


*/
public function setNumberFormat($decimal, $decimalPoint, $thousandSep)
{
$this->numberFormat = [$decimal, $decimalPoint, $thousandSep];
}

/**
@return


*/
public function getNumberFormat()
{
return $this->numberFormat;
}

public function getTokenParsers()
{
return [
new ApplyTokenParser(),
new ForTokenParser(),
new IfTokenParser(),
new ExtendsTokenParser(),
new IncludeTokenParser(),
new BlockTokenParser(),
new UseTokenParser(),
new FilterTokenParser(),
new MacroTokenParser(),
new ImportTokenParser(),
new FromTokenParser(),
new SetTokenParser(),
new SpacelessTokenParser(),
new FlushTokenParser(),
new DoTokenParser(),
new EmbedTokenParser(),
new WithTokenParser(),
new DeprecatedTokenParser(),
];
}

public function getFilters()
{
return [

 new TwigFilter('date', 'twig_date_format_filter', ['needs_environment' => true]),
new TwigFilter('date_modify', 'twig_date_modify_filter', ['needs_environment' => true]),
new TwigFilter('format', 'sprintf'),
new TwigFilter('replace', 'twig_replace_filter'),
new TwigFilter('number_format', 'twig_number_format_filter', ['needs_environment' => true]),
new TwigFilter('abs', 'abs'),
new TwigFilter('round', 'twig_round'),


 new TwigFilter('url_encode', 'twig_urlencode_filter'),
new TwigFilter('json_encode', 'json_encode'),
new TwigFilter('convert_encoding', 'twig_convert_encoding'),


 new TwigFilter('title', 'twig_title_string_filter', ['needs_environment' => true]),
new TwigFilter('capitalize', 'twig_capitalize_string_filter', ['needs_environment' => true]),
new TwigFilter('upper', 'twig_upper_filter', ['needs_environment' => true]),
new TwigFilter('lower', 'twig_lower_filter', ['needs_environment' => true]),
new TwigFilter('striptags', 'strip_tags'),
new TwigFilter('trim', 'twig_trim_filter'),
new TwigFilter('nl2br', 'nl2br', ['pre_escape' => 'html', 'is_safe' => ['html']]),
new TwigFilter('spaceless', 'twig_spaceless', ['is_safe' => ['html']]),


 new TwigFilter('join', 'twig_join_filter'),
new TwigFilter('split', 'twig_split_filter', ['needs_environment' => true]),
new TwigFilter('sort', 'twig_sort_filter'),
new TwigFilter('merge', 'twig_array_merge'),
new TwigFilter('batch', 'twig_array_batch'),
new TwigFilter('column', 'twig_array_column'),
new TwigFilter('filter', 'twig_array_filter', ['needs_environment' => true]),
new TwigFilter('map', 'twig_array_map', ['needs_environment' => true]),
new TwigFilter('reduce', 'twig_array_reduce', ['needs_environment' => true]),


 new TwigFilter('reverse', 'twig_reverse_filter', ['needs_environment' => true]),
new TwigFilter('length', 'twig_length_filter', ['needs_environment' => true]),
new TwigFilter('slice', 'twig_slice', ['needs_environment' => true]),
new TwigFilter('first', 'twig_first', ['needs_environment' => true]),
new TwigFilter('last', 'twig_last', ['needs_environment' => true]),


 new TwigFilter('default', '_twig_default_filter', ['node_class' => DefaultFilter::class]),
new TwigFilter('keys', 'twig_get_array_keys_filter'),
];
}

public function getFunctions()
{
return [
new TwigFunction('max', 'max'),
new TwigFunction('min', 'min'),
new TwigFunction('range', 'range'),
new TwigFunction('constant', 'twig_constant'),
new TwigFunction('cycle', 'twig_cycle'),
new TwigFunction('random', 'twig_random', ['needs_environment' => true]),
new TwigFunction('date', 'twig_date_converter', ['needs_environment' => true]),
new TwigFunction('include', 'twig_include', ['needs_environment' => true, 'needs_context' => true, 'is_safe' => ['all']]),
new TwigFunction('source', 'twig_source', ['needs_environment' => true, 'is_safe' => ['all']]),
];
}

public function getTests()
{
return [
new TwigTest('even', null, ['node_class' => EvenTest::class]),
new TwigTest('odd', null, ['node_class' => OddTest::class]),
new TwigTest('defined', null, ['node_class' => DefinedTest::class]),
new TwigTest('same as', null, ['node_class' => SameasTest::class]),
new TwigTest('none', null, ['node_class' => NullTest::class]),
new TwigTest('null', null, ['node_class' => NullTest::class]),
new TwigTest('divisible by', null, ['node_class' => DivisiblebyTest::class]),
new TwigTest('constant', null, ['node_class' => ConstantTest::class]),
new TwigTest('empty', 'twig_test_empty'),
new TwigTest('iterable', 'twig_test_iterable'),
];
}

public function getNodeVisitors()
{
return [new MacroAutoImportNodeVisitor()];
}

public function getOperators()
{
return [
[
'not' => ['precedence' => 50, 'class' => NotUnary::class],
'-' => ['precedence' => 500, 'class' => NegUnary::class],
'+' => ['precedence' => 500, 'class' => PosUnary::class],
],
[
'or' => ['precedence' => 10, 'class' => OrBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'and' => ['precedence' => 15, 'class' => AndBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'b-or' => ['precedence' => 16, 'class' => BitwiseOrBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'b-xor' => ['precedence' => 17, 'class' => BitwiseXorBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'b-and' => ['precedence' => 18, 'class' => BitwiseAndBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'==' => ['precedence' => 20, 'class' => EqualBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'!=' => ['precedence' => 20, 'class' => NotEqualBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'<=>' => ['precedence' => 20, 'class' => SpaceshipBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'<' => ['precedence' => 20, 'class' => LessBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'>' => ['precedence' => 20, 'class' => GreaterBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'>=' => ['precedence' => 20, 'class' => GreaterEqualBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'<=' => ['precedence' => 20, 'class' => LessEqualBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'not in' => ['precedence' => 20, 'class' => NotInBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'in' => ['precedence' => 20, 'class' => InBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'matches' => ['precedence' => 20, 'class' => MatchesBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'starts with' => ['precedence' => 20, 'class' => StartsWithBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'ends with' => ['precedence' => 20, 'class' => EndsWithBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'..' => ['precedence' => 25, 'class' => RangeBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'+' => ['precedence' => 30, 'class' => AddBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'-' => ['precedence' => 30, 'class' => SubBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'~' => ['precedence' => 40, 'class' => ConcatBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'*' => ['precedence' => 60, 'class' => MulBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'/' => ['precedence' => 60, 'class' => DivBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'//' => ['precedence' => 60, 'class' => FloorDivBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'%' => ['precedence' => 60, 'class' => ModBinary::class, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'is' => ['precedence' => 100, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'is not' => ['precedence' => 100, 'associativity' => ExpressionParser::OPERATOR_LEFT],
'**' => ['precedence' => 200, 'class' => PowerBinary::class, 'associativity' => ExpressionParser::OPERATOR_RIGHT],
'??' => ['precedence' => 300, 'class' => NullCoalesceExpression::class, 'associativity' => ExpressionParser::OPERATOR_RIGHT],
],
];
}
}

class_alias('Twig\Extension\CoreExtension', 'Twig_Extension_Core');
}

namespace {
use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Source;
use Twig\Template;

/**
@param
@param
@return



*/
function twig_cycle($values, $position)
{
if (!\is_array($values) && !$values instanceof \ArrayAccess) {
return $values;
}

return $values[$position % \count($values)];
}

/**
@param
@param
@throws
@return







*/
function twig_random(Environment $env, $values = null, $max = null)
{
if (null === $values) {
return null === $max ? mt_rand() : mt_rand(0, $max);
}

if (\is_int($values) || \is_float($values)) {
if (null === $max) {
if ($values < 0) {
$max = 0;
$min = $values;
} else {
$max = $values;
$min = 0;
}
} else {
$min = $values;
$max = $max;
}

return mt_rand($min, $max);
}

if (\is_string($values)) {
if ('' === $values) {
return '';
}

$charset = $env->getCharset();

if ('UTF-8' !== $charset) {
$values = twig_convert_encoding($values, 'UTF-8', $charset);
}


 
 $values = preg_split('/(?<!^)(?!$)/u', $values);

if ('UTF-8' !== $charset) {
foreach ($values as $i => $value) {
$values[$i] = twig_convert_encoding($value, $charset, 'UTF-8');
}
}
}

if (!twig_test_iterable($values)) {
return $values;
}

$values = twig_to_array($values);

if (0 === \count($values)) {
throw new RuntimeError('The random function cannot pick from an empty array.');
}

return $values[array_rand($values, 1)];
}

/**
@param
@param
@param
@return





*/
function twig_date_format_filter(Environment $env, $date, $format = null, $timezone = null)
{
if (null === $format) {
$formats = $env->getExtension(CoreExtension::class)->getDateFormat();
$format = $date instanceof \DateInterval ? $formats[1] : $formats[0];
}

if ($date instanceof \DateInterval) {
return $date->format($format);
}

return twig_date_converter($env, $date, $timezone)->format($format);
}

/**
@param
@param
@return





*/
function twig_date_modify_filter(Environment $env, $date, $modifier)
{
$date = twig_date_converter($env, $date, false);

return $date->modify($modifier);
}

/**
@param
@param
@return







*/
function twig_date_converter(Environment $env, $date = null, $timezone = null)
{

 if (false !== $timezone) {
if (null === $timezone) {
$timezone = $env->getExtension(CoreExtension::class)->getTimezone();
} elseif (!$timezone instanceof \DateTimeZone) {
$timezone = new \DateTimeZone($timezone);
}
}


 if ($date instanceof \DateTimeImmutable) {
return false !== $timezone ? $date->setTimezone($timezone) : $date;
}

if ($date instanceof \DateTimeInterface) {
$date = clone $date;
if (false !== $timezone) {
$date->setTimezone($timezone);
}

return $date;
}

if (null === $date || 'now' === $date) {
return new \DateTime($date, false !== $timezone ? $timezone : $env->getExtension(CoreExtension::class)->getTimezone());
}

$asString = (string) $date;
if (ctype_digit($asString) || (!empty($asString) && '-' === $asString[0] && ctype_digit(substr($asString, 1)))) {
$date = new \DateTime('@'.$date);
} else {
$date = new \DateTime($date, $env->getExtension(CoreExtension::class)->getTimezone());
}

if (false !== $timezone) {
$date->setTimezone($timezone);
}

return $date;
}

/**
@param
@param
@return



*/
function twig_replace_filter($str, $from)
{
if (!twig_test_iterable($from)) {
throw new RuntimeError(sprintf('The "replace" filter expects an array or "Traversable" as replace values, got "%s".', \is_object($from) ? \get_class($from) : \gettype($from)));
}

return strtr($str, twig_to_array($from));
}

/**
@param
@param
@param
@return



*/
function twig_round($value, $precision = 0, $method = 'common')
{
if ('common' === $method) {
return round($value, $precision);
}

if ('ceil' !== $method && 'floor' !== $method) {
throw new RuntimeError('The round filter only supports the "common", "ceil", and "floor" methods.');
}

return $method($value * pow(10, $precision)) / pow(10, $precision);
}

/**
@param
@param
@param
@param
@return







*/
function twig_number_format_filter(Environment $env, $number, $decimal = null, $decimalPoint = null, $thousandSep = null)
{
$defaults = $env->getExtension(CoreExtension::class)->getNumberFormat();
if (null === $decimal) {
$decimal = $defaults[0];
}

if (null === $decimalPoint) {
$decimalPoint = $defaults[1];
}

if (null === $thousandSep) {
$thousandSep = $defaults[2];
}

return number_format((float) $number, $decimal, $decimalPoint, $thousandSep);
}

/**
@param
@return



*/
function twig_urlencode_filter($url)
{
if (\is_array($url)) {
return http_build_query($url, '', '&', PHP_QUERY_RFC3986);
}

return rawurlencode($url);
}

/**
@param
@param
@return









*/
function twig_array_merge($arr1, $arr2)
{
if (!twig_test_iterable($arr1)) {
throw new RuntimeError(sprintf('The merge filter only works with arrays or "Traversable", got "%s" as first argument.', \gettype($arr1)));
}

if (!twig_test_iterable($arr2)) {
throw new RuntimeError(sprintf('The merge filter only works with arrays or "Traversable", got "%s" as second argument.', \gettype($arr2)));
}

return array_merge(twig_to_array($arr1), twig_to_array($arr2));
}

/**
@param
@param
@param
@param
@return



*/
function twig_slice(Environment $env, $item, $start, $length = null, $preserveKeys = false)
{
if ($item instanceof \Traversable) {
while ($item instanceof \IteratorAggregate) {
$item = $item->getIterator();
}

if ($start >= 0 && $length >= 0 && $item instanceof \Iterator) {
try {
return iterator_to_array(new \LimitIterator($item, $start, null === $length ? -1 : $length), $preserveKeys);
} catch (\OutOfBoundsException $e) {
return [];
}
}

$item = iterator_to_array($item, $preserveKeys);
}

if (\is_array($item)) {
return \array_slice($item, $start, $length, $preserveKeys);
}

$item = (string) $item;

return (string) mb_substr($item, $start, $length, $env->getCharset());
}

/**
@param
@return



*/
function twig_first(Environment $env, $item)
{
$elements = twig_slice($env, $item, 0, 1, false);

return \is_string($elements) ? $elements : current($elements);
}

/**
@param
@return



*/
function twig_last(Environment $env, $item)
{
$elements = twig_slice($env, $item, -1, 1, false);

return \is_string($elements) ? $elements : current($elements);
}

/**
@param
@param
@param
@return














*/
function twig_join_filter($value, $glue = '', $and = null)
{
if (!twig_test_iterable($value)) {
$value = (array) $value;
}

$value = twig_to_array($value, false);

if (0 === \count($value)) {
return '';
}

if (null === $and || $and === $glue) {
return implode($glue, $value);
}

if (1 === \count($value)) {
return $value[0];
}

return implode($glue, \array_slice($value, 0, -1)).$and.$value[\count($value) - 1];
}

/**
@param
@param
@param
@return















*/
function twig_split_filter(Environment $env, $value, $delimiter, $limit = null)
{
if (\strlen($delimiter) > 0) {
return null === $limit ? explode($delimiter, $value) : explode($delimiter, $value, $limit);
}

if ($limit <= 1) {
return preg_split('/(?<!^)(?!$)/u', $value);
}

$length = mb_strlen($value, $env->getCharset());
if ($length < $limit) {
return [$value];
}

$r = [];
for ($i = 0; $i < $length; $i += $limit) {
$r[] = mb_substr($value, $i, $limit, $env->getCharset());
}

return $r;
}




/**
@internal
*/
function _twig_default_filter($value, $default = '')
{
if (twig_test_empty($value)) {
return $default;
}

return $value;
}

/**
@param
@return









*/
function twig_get_array_keys_filter($array)
{
if ($array instanceof \Traversable) {
while ($array instanceof \IteratorAggregate) {
$array = $array->getIterator();
}

if ($array instanceof \Iterator) {
$keys = [];
$array->rewind();
while ($array->valid()) {
$keys[] = $array->key();
$array->next();
}

return $keys;
}

$keys = [];
foreach ($array as $key => $item) {
$keys[] = $key;
}

return $keys;
}

if (!\is_array($array)) {
return [];
}

return array_keys($array);
}

/**
@param
@param
@return



*/
function twig_reverse_filter(Environment $env, $item, $preserveKeys = false)
{
if ($item instanceof \Traversable) {
return array_reverse(iterator_to_array($item), $preserveKeys);
}

if (\is_array($item)) {
return array_reverse($item, $preserveKeys);
}

$string = (string) $item;

$charset = $env->getCharset();

if ('UTF-8' !== $charset) {
$item = twig_convert_encoding($string, 'UTF-8', $charset);
}

preg_match_all('/./us', $item, $matches);

$string = implode('', array_reverse($matches[0]));

if ('UTF-8' !== $charset) {
$string = twig_convert_encoding($string, $charset, 'UTF-8');
}

return $string;
}

/**
@param
@return



*/
function twig_sort_filter($array, $arrow = null)
{
if ($array instanceof \Traversable) {
$array = iterator_to_array($array);
} elseif (!\is_array($array)) {
throw new RuntimeError(sprintf('The sort filter only works with arrays or "Traversable", got "%s".', \gettype($array)));
}

if (null !== $arrow) {
uasort($array, $arrow);
} else {
asort($array);
}

return $array;
}

/**
@internal
*/
function twig_in_filter($value, $compare)
{
if ($value instanceof Markup) {
$value = (string) $value;
}
if ($compare instanceof Markup) {
$compare = (string) $compare;
}

if (\is_array($compare)) {
return \in_array($value, $compare, \is_object($value) || \is_resource($value));
} elseif (\is_string($compare) && (\is_string($value) || \is_int($value) || \is_float($value))) {
return '' === $value || false !== strpos($compare, (string) $value);
} elseif ($compare instanceof \Traversable) {
if (\is_object($value) || \is_resource($value)) {
foreach ($compare as $item) {
if ($item === $value) {
return true;
}
}
} else {
foreach ($compare as $item) {
if ($item == $value) {
return true;
}
}
}

return false;
}

return false;
}

/**
@return
@throws



*/
function twig_trim_filter($string, $characterMask = null, $side = 'both')
{
if (null === $characterMask) {
$characterMask = " \t\n\r\0\x0B";
}

switch ($side) {
case 'both':
return trim($string, $characterMask);
case 'left':
return ltrim($string, $characterMask);
case 'right':
return rtrim($string, $characterMask);
default:
throw new RuntimeError('Trimming side must be "left", "right" or "both".');
}
}

/**
@return


*/
function twig_spaceless($content)
{
return trim(preg_replace('/>\s+</', '><', $content));
}

function twig_convert_encoding($string, $to, $from)
{
if (!\function_exists('iconv')) {
throw new RuntimeError('Unable to convert encoding: required function iconv() does not exist. You should install ext-iconv or symfony/polyfill-iconv.');
}

return iconv($from, $to, $string);
}

/**
@param
@return



*/
function twig_length_filter(Environment $env, $thing)
{
if (null === $thing) {
return 0;
}

if (is_scalar($thing)) {
return mb_strlen($thing, $env->getCharset());
}

if ($thing instanceof \Countable || \is_array($thing) || $thing instanceof \SimpleXMLElement) {
return \count($thing);
}

if ($thing instanceof \Traversable) {
return iterator_count($thing);
}

if (method_exists($thing, '__toString') && !$thing instanceof \Countable) {
return mb_strlen((string) $thing, $env->getCharset());
}

return 1;
}

/**
@param
@return



*/
function twig_upper_filter(Environment $env, $string)
{
return mb_strtoupper($string, $env->getCharset());
}

/**
@param
@return



*/
function twig_lower_filter(Environment $env, $string)
{
return mb_strtolower($string, $env->getCharset());
}

/**
@param
@return



*/
function twig_title_string_filter(Environment $env, $string)
{
if (null !== $charset = $env->getCharset()) {
return mb_convert_case($string, MB_CASE_TITLE, $charset);
}

return ucwords(strtolower($string));
}

/**
@param
@return



*/
function twig_capitalize_string_filter(Environment $env, $string)
{
$charset = $env->getCharset();

return mb_strtoupper(mb_substr($string, 0, 1, $charset), $charset).mb_strtolower(mb_substr($string, 1, null, $charset), $charset);
}

/**
@internal
*/
function twig_call_macro(Template $template, string $method, array $args, int $lineno, array $context, Source $source)
{
if (!method_exists($template, $method)) {
$parent = $template;
while ($parent = $parent->getParent($context)) {
if (method_exists($parent, $method)) {
return $parent->$method(...$args);
}
}

throw new RuntimeError(sprintf('Macro "%s" is not defined in template "%s".', substr($method, \strlen('macro_')), $template->getTemplateName()), $lineno, $source);
}

return $template->$method(...$args);
}

/**
@internal
*/
function twig_ensure_traversable($seq)
{
if ($seq instanceof \Traversable || \is_array($seq)) {
return $seq;
}

return [];
}

/**
@internal
*/
function twig_to_array($seq, $preserveKeys = true)
{
if ($seq instanceof \Traversable) {
return iterator_to_array($seq, $preserveKeys);
}

if (!\is_array($seq)) {
return $seq;
}

return $preserveKeys ? $seq : array_values($seq);
}

/**
@param
@return








*/
function twig_test_empty($value)
{
if ($value instanceof \Countable) {
return 0 === \count($value);
}

if ($value instanceof \Traversable) {
return !iterator_count($value);
}

if (\is_object($value) && method_exists($value, '__toString')) {
return '' === (string) $value;
}

return '' === $value || false === $value || null === $value || [] === $value;
}

/**
@param
@return








*/
function twig_test_iterable($value)
{
return $value instanceof \Traversable || \is_array($value);
}

/**
@param
@param
@param
@param
@param
@param
@return



*/
function twig_include(Environment $env, $context, $template, $variables = [], $withContext = true, $ignoreMissing = false, $sandboxed = false)
{
$alreadySandboxed = false;
$sandbox = null;
if ($withContext) {
$variables = array_merge($context, $variables);
}

if ($isSandboxed = $sandboxed && $env->hasExtension(SandboxExtension::class)) {
$sandbox = $env->getExtension(SandboxExtension::class);
if (!$alreadySandboxed = $sandbox->isSandboxed()) {
$sandbox->enableSandbox();
}
}

try {
$loaded = null;
try {
$loaded = $env->resolveTemplate($template);
} catch (LoaderError $e) {
if (!$ignoreMissing) {
throw $e;
}
}

return $loaded ? $loaded->render($variables) : '';
} finally {
if ($isSandboxed && !$alreadySandboxed) {
$sandbox->disableSandbox();
}
}
}

/**
@param
@param
@return



*/
function twig_source(Environment $env, $name, $ignoreMissing = false)
{
$loader = $env->getLoader();
try {
return $loader->getSourceContext($name)->getCode();
} catch (LoaderError $e) {
if (!$ignoreMissing) {
throw $e;
}
}
}

/**
@param
@param
@return



*/
function twig_constant($constant, $object = null)
{
if (null !== $object) {
$constant = \get_class($object).'::'.$constant;
}

return \constant($constant);
}

/**
@param
@param
@return



*/
function twig_constant_is_defined($constant, $object = null)
{
if (null !== $object) {
$constant = \get_class($object).'::'.$constant;
}

return \defined($constant);
}

/**
@param
@param
@param
@return



*/
function twig_array_batch($items, $size, $fill = null, $preserveKeys = true)
{
if (!twig_test_iterable($items)) {
throw new RuntimeError(sprintf('The "batch" filter expects an array or "Traversable", got "%s".', \is_object($items) ? \get_class($items) : \gettype($items)));
}

$size = ceil($size);

$result = array_chunk(twig_to_array($items, $preserveKeys), $size, $preserveKeys);

if (null !== $fill && $result) {
$last = \count($result) - 1;
if ($fillCount = $size - \count($result[$last])) {
for ($i = 0; $i < $fillCount; ++$i) {
$result[$last][] = $fill;
}
}
}

return $result;
}

/**
@param
@param
@param
@param
@param
@param
@param
@return
@throws
@internal





*/
function twig_get_attribute(Environment $env, Source $source, $object, $item, array $arguments = [], $type =  'any', $isDefinedTest = false, $ignoreStrictCheck = false, $sandboxed = false, int $lineno = -1)
{

 if ( 'method' !== $type) {
$arrayItem = \is_bool($item) || \is_float($item) ? (int) $item : $item;

if (((\is_array($object) || $object instanceof \ArrayObject) && (isset($object[$arrayItem]) || \array_key_exists($arrayItem, (array) $object)))
|| ($object instanceof ArrayAccess && isset($object[$arrayItem]))
) {
if ($isDefinedTest) {
return true;
}

return $object[$arrayItem];
}

if ( 'array' === $type || !\is_object($object)) {
if ($isDefinedTest) {
return false;
}

if ($ignoreStrictCheck || !$env->isStrictVariables()) {
return;
}

if ($object instanceof ArrayAccess) {
$message = sprintf('Key "%s" in object with ArrayAccess of class "%s" does not exist.', $arrayItem, \get_class($object));
} elseif (\is_object($object)) {
$message = sprintf('Impossible to access a key "%s" on an object of class "%s" that does not implement ArrayAccess interface.', $item, \get_class($object));
} elseif (\is_array($object)) {
if (empty($object)) {
$message = sprintf('Key "%s" does not exist as the array is empty.', $arrayItem);
} else {
$message = sprintf('Key "%s" for array with keys "%s" does not exist.', $arrayItem, implode(', ', array_keys($object)));
}
} elseif ( 'array' === $type) {
if (null === $object) {
$message = sprintf('Impossible to access a key ("%s") on a null variable.', $item);
} else {
$message = sprintf('Impossible to access a key ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
}
} elseif (null === $object) {
$message = sprintf('Impossible to access an attribute ("%s") on a null variable.', $item);
} else {
$message = sprintf('Impossible to access an attribute ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
}

throw new RuntimeError($message, $lineno, $source);
}
}

if (!\is_object($object)) {
if ($isDefinedTest) {
return false;
}

if ($ignoreStrictCheck || !$env->isStrictVariables()) {
return;
}

if (null === $object) {
$message = sprintf('Impossible to invoke a method ("%s") on a null variable.', $item);
} elseif (\is_array($object)) {
$message = sprintf('Impossible to invoke a method ("%s") on an array.', $item);
} else {
$message = sprintf('Impossible to invoke a method ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
}

throw new RuntimeError($message, $lineno, $source);
}

if ($object instanceof Template) {
throw new RuntimeError('Accessing \Twig\Template attributes is forbidden.', $lineno, $source);
}


 if ( 'method' !== $type) {
if (isset($object->$item) || \array_key_exists((string) $item, (array) $object)) {
if ($isDefinedTest) {
return true;
}

if ($sandboxed) {
$env->getExtension(SandboxExtension::class)->checkPropertyAllowed($object, $item, $lineno, $source);
}

return $object->$item;
}
}

static $cache = [];

$class = \get_class($object);


 
 if (!isset($cache[$class])) {
$methods = get_class_methods($object);
sort($methods);
$lcMethods = array_map(function ($value) { return strtr($value, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz'); }, $methods);
$classCache = [];
foreach ($methods as $i => $method) {
$classCache[$method] = $method;
$classCache[$lcName = $lcMethods[$i]] = $method;

if ('g' === $lcName[0] && 0 === strpos($lcName, 'get')) {
$name = substr($method, 3);
$lcName = substr($lcName, 3);
} elseif ('i' === $lcName[0] && 0 === strpos($lcName, 'is')) {
$name = substr($method, 2);
$lcName = substr($lcName, 2);
} elseif ('h' === $lcName[0] && 0 === strpos($lcName, 'has')) {
$name = substr($method, 3);
$lcName = substr($lcName, 3);
if (\in_array('is'.$lcName, $lcMethods)) {
continue;
}
} else {
continue;
}


 if ($name) {
if (!isset($classCache[$name])) {
$classCache[$name] = $method;
}

if (!isset($classCache[$lcName])) {
$classCache[$lcName] = $method;
}
}
}
$cache[$class] = $classCache;
}

$call = false;
if (isset($cache[$class][$item])) {
$method = $cache[$class][$item];
} elseif (isset($cache[$class][$lcItem = strtr($item, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')])) {
$method = $cache[$class][$lcItem];
} elseif (isset($cache[$class]['__call'])) {
$method = $item;
$call = true;
} else {
if ($isDefinedTest) {
return false;
}

if ($ignoreStrictCheck || !$env->isStrictVariables()) {
return;
}

throw new RuntimeError(sprintf('Neither the property "%1$s" nor one of the methods "%1$s()", "get%1$s()"/"is%1$s()"/"has%1$s()" or "__call()" exist and have public access in class "%2$s".', $item, $class), $lineno, $source);
}

if ($isDefinedTest) {
return true;
}

if ($sandboxed) {
$env->getExtension(SandboxExtension::class)->checkMethodAllowed($object, $method, $lineno, $source);
}


 
 try {
$ret = $object->$method(...$arguments);
} catch (\BadMethodCallException $e) {
if ($call && ($ignoreStrictCheck || !$env->isStrictVariables())) {
return;
}
throw $e;
}

return $ret;
}

/**
@param
@param
@param
@return











*/
function twig_array_column($array, $name, $index = null): array
{
if ($array instanceof Traversable) {
$array = iterator_to_array($array);
} elseif (!\is_array($array)) {
throw new RuntimeError(sprintf('The column filter only works with arrays or "Traversable", got "%s" as first argument.', \gettype($array)));
}

return array_column($array, $name, $index);
}

function twig_array_filter(Environment $env, $array, $arrow)
{
if (!twig_test_iterable($array)) {
throw new RuntimeError(sprintf('The "filter" filter expects an array or "Traversable", got "%s".', \is_object($array) ? \get_class($array) : \gettype($array)));
}

if (!$arrow instanceof Closure && $env->hasExtension('\Twig\Extension\SandboxExtension') && $env->getExtension('\Twig\Extension\SandboxExtension')->isSandboxed()) {
throw new RuntimeError('The callable passed to "filter" filter must be a Closure in sandbox mode.');
}

if (\is_array($array)) {
return array_filter($array, $arrow, \ARRAY_FILTER_USE_BOTH);
}


 return new \CallbackFilterIterator(new \IteratorIterator($array), $arrow);
}

function twig_array_map(Environment $env, $array, $arrow)
{
if (!$arrow instanceof Closure && $env->hasExtension('\Twig\Extension\SandboxExtension') && $env->getExtension('\Twig\Extension\SandboxExtension')->isSandboxed()) {
throw new RuntimeError('The callable passed to the "map" filter must be a Closure in sandbox mode.');
}

$r = [];
foreach ($array as $k => $v) {
$r[$k] = $arrow($v, $k);
}

return $r;
}

function twig_array_reduce(Environment $env, $array, $arrow, $initial = null)
{
if (!$arrow instanceof Closure && $env->hasExtension('\Twig\Extension\SandboxExtension') && $env->getExtension('\Twig\Extension\SandboxExtension')->isSandboxed()) {
throw new RuntimeError('The callable passed to the "reduce" filter must be a Closure in sandbox mode.');
}

if (!\is_array($array)) {
$array = iterator_to_array($array);
}

return array_reduce($array, $arrow, $initial);
}
}
