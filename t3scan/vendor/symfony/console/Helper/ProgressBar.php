<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Terminal;

/**
@author
@author


*/
final class ProgressBar
{
private $barWidth = 28;
private $barChar;
private $emptyBarChar = '-';
private $progressChar = '>';
private $format;
private $internalFormat;
private $redrawFreq = 1;
private $output;
private $step = 0;
private $max;
private $startTime;
private $stepWidth;
private $percent = 0.0;
private $formatLineCount;
private $messages = [];
private $overwrite = true;
private $terminal;
private $firstRun = true;

private static $formatters;
private static $formats;

/**
@param
@param
*/
public function __construct(OutputInterface $output, $max = 0)
{
if ($output instanceof ConsoleOutputInterface) {
$output = $output->getErrorOutput();
}

$this->output = $output;
$this->setMaxSteps($max);
$this->terminal = new Terminal();

if (!$this->output->isDecorated()) {

 $this->overwrite = false;


 $this->setRedrawFrequency($max / 10);
}

$this->startTime = time();
}

/**
@param
@param




*/
public static function setPlaceholderFormatterDefinition($name, callable $callable)
{
if (!self::$formatters) {
self::$formatters = self::initPlaceholderFormatters();
}

self::$formatters[$name] = $callable;
}

/**
@param
@return



*/
public static function getPlaceholderFormatterDefinition($name)
{
if (!self::$formatters) {
self::$formatters = self::initPlaceholderFormatters();
}

return isset(self::$formatters[$name]) ? self::$formatters[$name] : null;
}

/**
@param
@param




*/
public static function setFormatDefinition($name, $format)
{
if (!self::$formats) {
self::$formats = self::initFormats();
}

self::$formats[$name] = $format;
}

/**
@param
@return



*/
public static function getFormatDefinition($name)
{
if (!self::$formats) {
self::$formats = self::initFormats();
}

return isset(self::$formats[$name]) ? self::$formats[$name] : null;
}

/**
@param
@param






*/
public function setMessage($message, $name = 'message')
{
$this->messages[$name] = $message;
}

public function getMessage($name = 'message')
{
return $this->messages[$name];
}

/**
@return


*/
public function getStartTime()
{
return $this->startTime;
}

/**
@return


*/
public function getMaxSteps()
{
return $this->max;
}

/**
@return


*/
public function getProgress()
{
return $this->step;
}

/**
@return


*/
private function getStepWidth()
{
return $this->stepWidth;
}

/**
@return


*/
public function getProgressPercent()
{
return $this->percent;
}

/**
@param


*/
public function setBarWidth($size)
{
$this->barWidth = max(1, (int) $size);
}

/**
@return


*/
public function getBarWidth()
{
return $this->barWidth;
}

/**
@param


*/
public function setBarCharacter($char)
{
$this->barChar = $char;
}

/**
@return


*/
public function getBarCharacter()
{
if (null === $this->barChar) {
return $this->max ? '=' : $this->emptyBarChar;
}

return $this->barChar;
}

/**
@param


*/
public function setEmptyBarCharacter($char)
{
$this->emptyBarChar = $char;
}

/**
@return


*/
public function getEmptyBarCharacter()
{
return $this->emptyBarChar;
}

/**
@param


*/
public function setProgressCharacter($char)
{
$this->progressChar = $char;
}

/**
@return


*/
public function getProgressCharacter()
{
return $this->progressChar;
}

/**
@param


*/
public function setFormat($format)
{
$this->format = null;
$this->internalFormat = $format;
}

/**
@param


*/
public function setRedrawFrequency($freq)
{
$this->redrawFreq = max((int) $freq, 1);
}

/**
@param


*/
public function start($max = null)
{
$this->startTime = time();
$this->step = 0;
$this->percent = 0.0;

if (null !== $max) {
$this->setMaxSteps($max);
}

$this->display();
}

/**
@param


*/
public function advance($step = 1)
{
$this->setProgress($this->step + $step);
}

/**
@param


*/
public function setOverwrite($overwrite)
{
$this->overwrite = (bool) $overwrite;
}

/**
@param


*/
public function setProgress($step)
{
$step = (int) $step;

if ($this->max && $step > $this->max) {
$this->max = $step;
} elseif ($step < 0) {
$step = 0;
}

$prevPeriod = (int) ($this->step / $this->redrawFreq);
$currPeriod = (int) ($step / $this->redrawFreq);
$this->step = $step;
$this->percent = $this->max ? (float) $this->step / $this->max : 0;
if ($prevPeriod !== $currPeriod || $this->max === $step) {
$this->display();
}
}




public function finish()
{
if (!$this->max) {
$this->max = $this->step;
}

if ($this->step === $this->max && !$this->overwrite) {

 return;
}

$this->setProgress($this->max);
}




public function display()
{
if (OutputInterface::VERBOSITY_QUIET === $this->output->getVerbosity()) {
return;
}

if (null === $this->format) {
$this->setRealFormat($this->internalFormat ?: $this->determineBestFormat());
}

$this->overwrite($this->buildLine());
}








public function clear()
{
if (!$this->overwrite) {
return;
}

if (null === $this->format) {
$this->setRealFormat($this->internalFormat ?: $this->determineBestFormat());
}

$this->overwrite('');
}

/**
@param


*/
private function setRealFormat($format)
{

 if (!$this->max && null !== self::getFormatDefinition($format.'_nomax')) {
$this->format = self::getFormatDefinition($format.'_nomax');
} elseif (null !== self::getFormatDefinition($format)) {
$this->format = self::getFormatDefinition($format);
} else {
$this->format = $format;
}

$this->formatLineCount = substr_count($this->format, "\n");
}

/**
@param


*/
private function setMaxSteps($max)
{
$this->max = max(0, (int) $max);
$this->stepWidth = $this->max ? Helper::strlen($this->max) : 4;
}

/**
@param


*/
private function overwrite($message)
{
if ($this->overwrite) {
if (!$this->firstRun) {

 if ($this->formatLineCount > 0) {
$message = str_repeat("\x1B[1A\x1B[2K", $this->formatLineCount).$message;
}


 $message = "\x0D\x1B[2K$message";
}
} elseif ($this->step > 0) {
$message = \PHP_EOL.$message;
}

$this->firstRun = false;

$this->output->write($message);
}

private function determineBestFormat()
{
switch ($this->output->getVerbosity()) {

 case OutputInterface::VERBOSITY_VERBOSE:
return $this->max ? 'verbose' : 'verbose_nomax';
case OutputInterface::VERBOSITY_VERY_VERBOSE:
return $this->max ? 'very_verbose' : 'very_verbose_nomax';
case OutputInterface::VERBOSITY_DEBUG:
return $this->max ? 'debug' : 'debug_nomax';
default:
return $this->max ? 'normal' : 'normal_nomax';
}
}

private static function initPlaceholderFormatters()
{
return [
'bar' => function (self $bar, OutputInterface $output) {
$completeBars = floor($bar->getMaxSteps() > 0 ? $bar->getProgressPercent() * $bar->getBarWidth() : $bar->getProgress() % $bar->getBarWidth());
$display = str_repeat($bar->getBarCharacter(), $completeBars);
if ($completeBars < $bar->getBarWidth()) {
$emptyBars = $bar->getBarWidth() - $completeBars - Helper::strlenWithoutDecoration($output->getFormatter(), $bar->getProgressCharacter());
$display .= $bar->getProgressCharacter().str_repeat($bar->getEmptyBarCharacter(), $emptyBars);
}

return $display;
},
'elapsed' => function (self $bar) {
return Helper::formatTime(time() - $bar->getStartTime());
},
'remaining' => function (self $bar) {
if (!$bar->getMaxSteps()) {
throw new LogicException('Unable to display the remaining time if the maximum number of steps is not set.');
}

if (!$bar->getProgress()) {
$remaining = 0;
} else {
$remaining = round((time() - $bar->getStartTime()) / $bar->getProgress() * ($bar->getMaxSteps() - $bar->getProgress()));
}

return Helper::formatTime($remaining);
},
'estimated' => function (self $bar) {
if (!$bar->getMaxSteps()) {
throw new LogicException('Unable to display the estimated time if the maximum number of steps is not set.');
}

if (!$bar->getProgress()) {
$estimated = 0;
} else {
$estimated = round((time() - $bar->getStartTime()) / $bar->getProgress() * $bar->getMaxSteps());
}

return Helper::formatTime($estimated);
},
'memory' => function (self $bar) {
return Helper::formatMemory(memory_get_usage(true));
},
'current' => function (self $bar) {
return str_pad($bar->getProgress(), $bar->getStepWidth(), ' ', \STR_PAD_LEFT);
},
'max' => function (self $bar) {
return $bar->getMaxSteps();
},
'percent' => function (self $bar) {
return floor($bar->getProgressPercent() * 100);
},
];
}

private static function initFormats()
{
return [
'normal' => ' %current%/%max% [%bar%] %percent:3s%%',
'normal_nomax' => ' %current% [%bar%]',

'verbose' => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%',
'verbose_nomax' => ' %current% [%bar%] %elapsed:6s%',

'very_verbose' => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s%',
'very_verbose_nomax' => ' %current% [%bar%] %elapsed:6s%',

'debug' => ' %current%/%max% [%bar%] %percent:3s%% %elapsed:6s%/%estimated:-6s% %memory:6s%',
'debug_nomax' => ' %current% [%bar%] %elapsed:6s% %memory:6s%',
];
}

/**
@return
*/
private function buildLine()
{
$regex = "{%([a-z\-_]+)(?:\:([^%]+))?%}i";
$callback = function ($matches) {
if ($formatter = $this::getPlaceholderFormatterDefinition($matches[1])) {
$text = \call_user_func($formatter, $this, $this->output);
} elseif (isset($this->messages[$matches[1]])) {
$text = $this->messages[$matches[1]];
} else {
return $matches[0];
}

if (isset($matches[2])) {
$text = sprintf('%'.$matches[2], $text);
}

return $text;
};
$line = preg_replace_callback($regex, $callback, $this->format);


 $linesLength = array_map(function ($subLine) {
return Helper::strlenWithoutDecoration($this->output->getFormatter(), rtrim($subLine, "\r"));
}, explode("\n", $line));

$linesWidth = max($linesLength);

$terminalWidth = $this->terminal->getWidth();
if ($linesWidth <= $terminalWidth) {
return $line;
}

$this->setBarWidth($this->barWidth - $linesWidth + $terminalWidth);

return preg_replace_callback($regex, $callback, $this->format);
}
}
