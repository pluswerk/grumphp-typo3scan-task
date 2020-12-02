<?php










namespace Symfony\Component\Console\Formatter;

use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
@author


*/
class OutputFormatter implements OutputFormatterInterface
{
private $decorated;
private $styles = [];
private $styleStack;

/**
@param
@return



*/
public static function escape($text)
{
$text = preg_replace('/([^\\\\]?)</', '$1\\<', $text);

return self::escapeTrailingBackslash($text);
}

/**
@param
@return
@internal




*/
public static function escapeTrailingBackslash($text)
{
if ('\\' === substr($text, -1)) {
$len = \strlen($text);
$text = rtrim($text, '\\');
$text = str_replace("\0", '', $text);
$text .= str_repeat("\0", $len - \strlen($text));
}

return $text;
}

/**
@param
@param


*/
public function __construct($decorated = false, array $styles = [])
{
$this->decorated = (bool) $decorated;

$this->setStyle('error', new OutputFormatterStyle('white', 'red'));
$this->setStyle('info', new OutputFormatterStyle('green'));
$this->setStyle('comment', new OutputFormatterStyle('yellow'));
$this->setStyle('question', new OutputFormatterStyle('black', 'cyan'));

foreach ($styles as $name => $style) {
$this->setStyle($name, $style);
}

$this->styleStack = new OutputFormatterStyleStack();
}




public function setDecorated($decorated)
{
$this->decorated = (bool) $decorated;
}




public function isDecorated()
{
return $this->decorated;
}




public function setStyle($name, OutputFormatterStyleInterface $style)
{
$this->styles[strtolower($name)] = $style;
}




public function hasStyle($name)
{
return isset($this->styles[strtolower($name)]);
}




public function getStyle($name)
{
if (!$this->hasStyle($name)) {
throw new InvalidArgumentException(sprintf('Undefined style: "%s".', $name));
}

return $this->styles[strtolower($name)];
}




public function format($message)
{
$message = (string) $message;
$offset = 0;
$output = '';
$tagRegex = '[a-z][a-z0-9,_=;-]*+';
preg_match_all("#<(($tagRegex) | /($tagRegex)?)>#ix", $message, $matches, \PREG_OFFSET_CAPTURE);
foreach ($matches[0] as $i => $match) {
$pos = $match[1];
$text = $match[0];

if (0 != $pos && '\\' == $message[$pos - 1]) {
continue;
}


 $output .= $this->applyCurrentStyle(substr($message, $offset, $pos - $offset));
$offset = $pos + \strlen($text);


 if ($open = '/' != $text[1]) {
$tag = $matches[1][$i][0];
} else {
$tag = isset($matches[3][$i][0]) ? $matches[3][$i][0] : '';
}

if (!$open && !$tag) {

 $this->styleStack->pop();
} elseif (false === $style = $this->createStyleFromString($tag)) {
$output .= $this->applyCurrentStyle($text);
} elseif ($open) {
$this->styleStack->push($style);
} else {
$this->styleStack->pop($style);
}
}

$output .= $this->applyCurrentStyle(substr($message, $offset));

if (false !== strpos($output, "\0")) {
return strtr($output, ["\0" => '\\', '\\<' => '<']);
}

return str_replace('\\<', '<', $output);
}

/**
@return
*/
public function getStyleStack()
{
return $this->styleStack;
}

/**
@param
@return



*/
private function createStyleFromString($string)
{
if (isset($this->styles[$string])) {
return $this->styles[$string];
}

if (!preg_match_all('/([^=]+)=([^;]+)(;|$)/', $string, $matches, \PREG_SET_ORDER)) {
return false;
}

$style = new OutputFormatterStyle();
foreach ($matches as $match) {
array_shift($match);
$match[0] = strtolower($match[0]);

if ('fg' == $match[0]) {
$style->setForeground(strtolower($match[1]));
} elseif ('bg' == $match[0]) {
$style->setBackground(strtolower($match[1]));
} elseif ('options' === $match[0]) {
preg_match_all('([^,;]+)', strtolower($match[1]), $options);
$options = array_shift($options);
foreach ($options as $option) {
try {
$style->setOption($option);
} catch (\InvalidArgumentException $e) {
@trigger_error(sprintf('Unknown style options are deprecated since Symfony 3.2 and will be removed in 4.0. Exception "%s".', $e->getMessage()), \E_USER_DEPRECATED);

return false;
}
}
} else {
return false;
}
}

return $style;
}

/**
@param
@return



*/
private function applyCurrentStyle($text)
{
return $this->isDecorated() && \strlen($text) > 0 ? $this->styleStack->getCurrent()->apply($text) : $text;
}
}
