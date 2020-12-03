<?php










namespace Twig\Extension {
use Twig\FileExtensionEscapingStrategy;
use Twig\NodeVisitor\EscaperNodeVisitor;
use Twig\TokenParser\AutoEscapeTokenParser;
use Twig\TwigFilter;

final class EscaperExtension extends AbstractExtension
{
private $defaultStrategy;
private $escapers = [];

/**
@internal */
public $safeClasses = [];

/**
@internal */
public $safeLookup = [];

/**
@param
@see

*/
public function __construct($defaultStrategy = 'html')
{
$this->setDefaultStrategy($defaultStrategy);
}

public function getTokenParsers()
{
return [new AutoEscapeTokenParser()];
}

public function getNodeVisitors()
{
return [new EscaperNodeVisitor()];
}

public function getFilters()
{
return [
new TwigFilter('escape', 'twig_escape_filter', ['needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe']),
new TwigFilter('e', 'twig_escape_filter', ['needs_environment' => true, 'is_safe_callback' => 'twig_escape_filter_is_safe']),
new TwigFilter('raw', 'twig_raw_filter', ['is_safe' => ['all']]),
];
}

/**
@param





*/
public function setDefaultStrategy($defaultStrategy)
{
if ('name' === $defaultStrategy) {
$defaultStrategy = [FileExtensionEscapingStrategy::class, 'guess'];
}

$this->defaultStrategy = $defaultStrategy;
}

/**
@param
@return



*/
public function getDefaultStrategy($name)
{

 
 if (!\is_string($this->defaultStrategy) && false !== $this->defaultStrategy) {
return \call_user_func($this->defaultStrategy, $name);
}

return $this->defaultStrategy;
}

/**
@param
@param


*/
public function setEscaper($strategy, callable $callable)
{
$this->escapers[$strategy] = $callable;
}

/**
@return


*/
public function getEscapers()
{
return $this->escapers;
}

public function setSafeClasses(array $safeClasses = [])
{
$this->safeClasses = [];
$this->safeLookup = [];
foreach ($safeClasses as $class => $strategies) {
$this->addSafeClass($class, $strategies);
}
}

public function addSafeClass(string $class, array $strategies)
{
$class = ltrim($class, '\\');
if (!isset($this->safeClasses[$class])) {
$this->safeClasses[$class] = [];
}
$this->safeClasses[$class] = array_merge($this->safeClasses[$class], $strategies);

foreach ($strategies as $strategy) {
$this->safeLookup[$strategy][$class] = true;
}
}
}

class_alias('Twig\Extension\EscaperExtension', 'Twig_Extension_Escaper');
}

namespace {
use Twig\Environment;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\EscaperExtension;
use Twig\Markup;
use Twig\Node\Expression\ConstantExpression;
use Twig\Node\Node;

/**
@param
@return



*/
function twig_raw_filter($string)
{
return $string;
}

/**
@param
@param
@param
@param
@return



*/
function twig_escape_filter(Environment $env, $string, $strategy = 'html', $charset = null, $autoescape = false)
{
if ($autoescape && $string instanceof Markup) {
return $string;
}

if (!\is_string($string)) {
if (\is_object($string) && method_exists($string, '__toString')) {
if ($autoescape) {
$c = \get_class($string);
$ext = $env->getExtension(EscaperExtension::class);
if (!isset($ext->safeClasses[$c])) {
$ext->safeClasses[$c] = [];
foreach (class_parents($string) + class_implements($string) as $class) {
if (isset($ext->safeClasses[$class])) {
$ext->safeClasses[$c] = array_unique(array_merge($ext->safeClasses[$c], $ext->safeClasses[$class]));
foreach ($ext->safeClasses[$class] as $s) {
$ext->safeLookup[$s][$c] = true;
}
}
}
}
if (isset($ext->safeLookup[$strategy][$c]) || isset($ext->safeLookup['all'][$c])) {
return (string) $string;
}
}

$string = (string) $string;
} elseif (\in_array($strategy, ['html', 'js', 'css', 'html_attr', 'url'])) {
return $string;
}
}

if ('' === $string) {
return '';
}

if (null === $charset) {
$charset = $env->getCharset();
}

switch ($strategy) {
case 'html':



 
 
 static $htmlspecialcharsCharsets = [
'ISO-8859-1' => true, 'ISO8859-1' => true,
'ISO-8859-15' => true, 'ISO8859-15' => true,
'utf-8' => true, 'UTF-8' => true,
'CP866' => true, 'IBM866' => true, '866' => true,
'CP1251' => true, 'WINDOWS-1251' => true, 'WIN-1251' => true,
'1251' => true,
'CP1252' => true, 'WINDOWS-1252' => true, '1252' => true,
'KOI8-R' => true, 'KOI8-RU' => true, 'KOI8R' => true,
'BIG5' => true, '950' => true,
'GB2312' => true, '936' => true,
'BIG5-HKSCS' => true,
'SHIFT_JIS' => true, 'SJIS' => true, '932' => true,
'EUC-JP' => true, 'EUCJP' => true,
'ISO8859-5' => true, 'ISO-8859-5' => true, 'MACROMAN' => true,
];

if (isset($htmlspecialcharsCharsets[$charset])) {
return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
}

if (isset($htmlspecialcharsCharsets[strtoupper($charset)])) {

 $htmlspecialcharsCharsets[$charset] = true;

return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
}

$string = twig_convert_encoding($string, 'UTF-8', $charset);
$string = htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');

return iconv('UTF-8', $charset, $string);

case 'js':

 
 if ('UTF-8' !== $charset) {
$string = twig_convert_encoding($string, 'UTF-8', $charset);
}

if (!preg_match('//u', $string)) {
throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
}

$string = preg_replace_callback('#[^a-zA-Z0-9,\._]#Su', function ($matches) {
$char = $matches[0];






static $shortMap = [
'\\' => '\\\\',
'/' => '\\/',
"\x08" => '\b',
"\x0C" => '\f',
"\x0A" => '\n',
"\x0D" => '\r',
"\x09" => '\t',
];

if (isset($shortMap[$char])) {
return $shortMap[$char];
}


 $char = twig_convert_encoding($char, 'UTF-16BE', 'UTF-8');
$char = strtoupper(bin2hex($char));

if (4 >= \strlen($char)) {
return sprintf('\u%04s', $char);
}

return sprintf('\u%04s\u%04s', substr($char, 0, -4), substr($char, -4));
}, $string);

if ('UTF-8' !== $charset) {
$string = iconv('UTF-8', $charset, $string);
}

return $string;

case 'css':
if ('UTF-8' !== $charset) {
$string = twig_convert_encoding($string, 'UTF-8', $charset);
}

if (!preg_match('//u', $string)) {
throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
}

$string = preg_replace_callback('#[^a-zA-Z0-9]#Su', function ($matches) {
$char = $matches[0];

return sprintf('\\%X ', 1 === \strlen($char) ? \ord($char) : mb_ord($char, 'UTF-8'));
}, $string);

if ('UTF-8' !== $charset) {
$string = iconv('UTF-8', $charset, $string);
}

return $string;

case 'html_attr':
if ('UTF-8' !== $charset) {
$string = twig_convert_encoding($string, 'UTF-8', $charset);
}

if (!preg_match('//u', $string)) {
throw new RuntimeError('The string to escape is not a valid UTF-8 string.');
}

$string = preg_replace_callback('#[^a-zA-Z0-9,\.\-_]#Su', function ($matches) {
/**
@copyright
@license


*/
$chr = $matches[0];
$ord = \ord($chr);





if (($ord <= 0x1f && "\t" != $chr && "\n" != $chr && "\r" != $chr) || ($ord >= 0x7f && $ord <= 0x9f)) {
return '&#xFFFD;';
}





if (1 === \strlen($chr)) {






static $entityMap = [
34 => '&quot;', 
38 => '&amp;', 
60 => '&lt;', 
62 => '&gt;', 
];

if (isset($entityMap[$ord])) {
return $entityMap[$ord];
}

return sprintf('&#x%02X;', $ord);
}





return sprintf('&#x%04X;', mb_ord($chr, 'UTF-8'));
}, $string);

if ('UTF-8' !== $charset) {
$string = iconv('UTF-8', $charset, $string);
}

return $string;

case 'url':
return rawurlencode($string);

default:
static $escapers;

if (null === $escapers) {

 $escapers = array_merge(
$env->getExtension(CoreExtension::class)->getEscapers(false),
$env->getExtension(EscaperExtension::class)->getEscapers()
);
}

if (isset($escapers[$strategy])) {
return $escapers[$strategy]($env, $string, $charset);
}

$validStrategies = implode(', ', array_merge(['html', 'js', 'url', 'css', 'html_attr'], array_keys($escapers)));

throw new RuntimeError(sprintf('Invalid escaping strategy "%s" (valid ones: %s).', $strategy, $validStrategies));
}
}

/**
@internal
*/
function twig_escape_filter_is_safe(Node $filterArgs)
{
foreach ($filterArgs as $arg) {
if ($arg instanceof ConstantExpression) {
return [$arg->getAttribute('value')];
}

return [];
}

return ['html'];
}
}
