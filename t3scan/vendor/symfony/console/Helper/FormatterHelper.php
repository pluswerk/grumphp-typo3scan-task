<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Formatter\OutputFormatter;

/**
@author


*/
class FormatterHelper extends Helper
{
/**
@param
@param
@param
@return



*/
public function formatSection($section, $message, $style = 'info')
{
return sprintf('<%s>[%s]</%s> %s', $style, $section, $style, $message);
}

/**
@param
@param
@param
@return



*/
public function formatBlock($messages, $style, $large = false)
{
if (!\is_array($messages)) {
$messages = [$messages];
}

$len = 0;
$lines = [];
foreach ($messages as $message) {
$message = OutputFormatter::escape($message);
$lines[] = sprintf($large ? '  %s  ' : ' %s ', $message);
$len = max(self::strlen($message) + ($large ? 4 : 2), $len);
}

$messages = $large ? [str_repeat(' ', $len)] : [];
for ($i = 0; isset($lines[$i]); ++$i) {
$messages[] = $lines[$i].str_repeat(' ', $len - self::strlen($lines[$i]));
}
if ($large) {
$messages[] = str_repeat(' ', $len);
}

for ($i = 0; isset($messages[$i]); ++$i) {
$messages[$i] = sprintf('<%s>%s</%s>', $style, $messages[$i], $style);
}

return implode("\n", $messages);
}

/**
@param
@param
@param
@return



*/
public function truncate($message, $length, $suffix = '...')
{
$computedLength = $length - self::strlen($suffix);

if ($computedLength > self::strlen($message)) {
return $message;
}

return self::substr($message, 0, $length).$suffix;
}




public function getName()
{
return 'formatter';
}
}
