<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Terminal;

/**
@author


*/
class QuestionHelper extends Helper
{
private $inputStream;
private static $shell;
private static $stty = true;

/**
@return
@throws



*/
public function ask(InputInterface $input, OutputInterface $output, Question $question)
{
if ($output instanceof ConsoleOutputInterface) {
$output = $output->getErrorOutput();
}

if (!$input->isInteractive()) {
$default = $question->getDefault();

if (null === $default) {
return $default;
}

if ($validator = $question->getValidator()) {
return \call_user_func($question->getValidator(), $default);
} elseif ($question instanceof ChoiceQuestion) {
$choices = $question->getChoices();

if (!$question->isMultiselect()) {
return isset($choices[$default]) ? $choices[$default] : $default;
}

$default = explode(',', $default);
foreach ($default as $k => $v) {
$v = trim($v);
$default[$k] = isset($choices[$v]) ? $choices[$v] : $v;
}
}

return $default;
}

if ($input instanceof StreamableInputInterface && $stream = $input->getStream()) {
$this->inputStream = $stream;
}

if (!$question->getValidator()) {
return $this->doAsk($output, $question);
}

$interviewer = function () use ($output, $question) {
return $this->doAsk($output, $question);
};

return $this->validateAttempts($interviewer, $output, $question);
}

/**
@deprecated
@param
@throws







*/
public function setInputStream($stream)
{
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.2 and will be removed in 4.0. Use %s::setStream() instead.', __METHOD__, StreamableInputInterface::class), \E_USER_DEPRECATED);

if (!\is_resource($stream)) {
throw new InvalidArgumentException('Input stream must be a valid resource.');
}

$this->inputStream = $stream;
}

/**
@deprecated
@return




*/
public function getInputStream()
{
if (0 === \func_num_args() || func_get_arg(0)) {
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.2 and will be removed in 4.0. Use %s::getStream() instead.', __METHOD__, StreamableInputInterface::class), \E_USER_DEPRECATED);
}

return $this->inputStream;
}




public function getName()
{
return 'question';
}




public static function disableStty()
{
self::$stty = false;
}

/**
@return
@throws



*/
private function doAsk(OutputInterface $output, Question $question)
{
$this->writePrompt($output, $question);

$inputStream = $this->inputStream ?: \STDIN;
$autocomplete = $question->getAutocompleterValues();

if (\function_exists('sapi_windows_cp_set')) {

 @sapi_windows_cp_set(1252);
}

if (null === $autocomplete || !self::$stty || !Terminal::hasSttyAvailable()) {
$ret = false;
if ($question->isHidden()) {
try {
$ret = trim($this->getHiddenResponse($output, $inputStream));
} catch (RuntimeException $e) {
if (!$question->isHiddenFallback()) {
throw $e;
}
}
}

if (false === $ret) {
$ret = fgets($inputStream, 4096);
if (false === $ret) {
throw new RuntimeException('Aborted.');
}
$ret = trim($ret);
}
} else {
$ret = trim($this->autocomplete($output, $question, $inputStream, \is_array($autocomplete) ? $autocomplete : iterator_to_array($autocomplete, false)));
}

$ret = \strlen($ret) > 0 ? $ret : $question->getDefault();

if ($normalizer = $question->getNormalizer()) {
return $normalizer($ret);
}

return $ret;
}




protected function writePrompt(OutputInterface $output, Question $question)
{
$message = $question->getQuestion();

if ($question instanceof ChoiceQuestion) {
$output->writeln(array_merge([
$question->getQuestion(),
], $this->formatChoiceQuestionChoices($question, 'info')));

$message = $question->getPrompt();
}

$output->write($message);
}

/**
@param
@return

*/
protected function formatChoiceQuestionChoices(ChoiceQuestion $question, $tag)
{
$messages = [];

$maxWidth = max(array_map('self::strlen', array_keys($choices = $question->getChoices())));

foreach ($choices as $key => $value) {
$padding = str_repeat(' ', $maxWidth - self::strlen($key));

$messages[] = sprintf("  [<$tag>%s$padding</$tag>] %s", $key, $value);
}

return $messages;
}




protected function writeError(OutputInterface $output, \Exception $error)
{
if (null !== $this->getHelperSet() && $this->getHelperSet()->has('formatter')) {
$message = $this->getHelperSet()->get('formatter')->formatBlock($error->getMessage(), 'error');
} else {
$message = '<error>'.$error->getMessage().'</error>';
}

$output->writeln($message);
}

/**
@param
@return



*/
private function autocomplete(OutputInterface $output, Question $question, $inputStream, array $autocomplete)
{
$fullChoice = '';
$ret = '';

$i = 0;
$ofs = -1;
$matches = $autocomplete;
$numMatches = \count($matches);

$sttyMode = shell_exec('stty -g');


 shell_exec('stty -icanon -echo');


 $output->getFormatter()->setStyle('hl', new OutputFormatterStyle('black', 'white'));


 while (!feof($inputStream)) {
$c = fread($inputStream, 1);


 if (false === $c || ('' === $ret && '' === $c && null === $question->getDefault())) {
shell_exec(sprintf('stty %s', $sttyMode));
throw new RuntimeException('Aborted.');
} elseif ("\177" === $c) { 
 if (0 === $numMatches && 0 !== $i) {
--$i;
$fullChoice = self::substr($fullChoice, 0, $i);

 $output->write("\033[1D");
}

if (0 === $i) {
$ofs = -1;
$matches = $autocomplete;
$numMatches = \count($matches);
} else {
$numMatches = 0;
}


 $ret = self::substr($ret, 0, $i);
} elseif ("\033" === $c) {

 $c .= fread($inputStream, 2);


 if (isset($c[2]) && ('A' === $c[2] || 'B' === $c[2])) {
if ('A' === $c[2] && -1 === $ofs) {
$ofs = 0;
}

if (0 === $numMatches) {
continue;
}

$ofs += ('A' === $c[2]) ? -1 : 1;
$ofs = ($numMatches + $ofs) % $numMatches;
}
} elseif (\ord($c) < 32) {
if ("\t" === $c || "\n" === $c) {
if ($numMatches > 0 && -1 !== $ofs) {
$ret = $matches[$ofs];

 $remainingCharacters = substr($ret, \strlen(trim($this->mostRecentlyEnteredValue($fullChoice))));
$output->write($remainingCharacters);
$fullChoice .= $remainingCharacters;
$i = self::strlen($fullChoice);
}

if ("\n" === $c) {
$output->write($c);
break;
}

$numMatches = 0;
}

continue;
} else {
if ("\x80" <= $c) {
$c .= fread($inputStream, ["\xC0" => 1, "\xD0" => 1, "\xE0" => 2, "\xF0" => 3][$c & "\xF0"]);
}

$output->write($c);
$ret .= $c;
$fullChoice .= $c;
++$i;

$tempRet = $ret;

if ($question instanceof ChoiceQuestion && $question->isMultiselect()) {
$tempRet = $this->mostRecentlyEnteredValue($fullChoice);
}

$numMatches = 0;
$ofs = 0;

foreach ($autocomplete as $value) {

 if (0 === strpos($value, $tempRet)) {
$matches[$numMatches++] = $value;
}
}
}


 $output->write("\033[K");

if ($numMatches > 0 && -1 !== $ofs) {

 $output->write("\0337");

 $charactersEntered = \strlen(trim($this->mostRecentlyEnteredValue($fullChoice)));
$output->write('<hl>'.OutputFormatter::escapeTrailingBackslash(substr($matches[$ofs], $charactersEntered)).'</hl>');

 $output->write("\0338");
}
}


 shell_exec(sprintf('stty %s', $sttyMode));

return $fullChoice;
}

private function mostRecentlyEnteredValue($entered)
{

 if (false === strpos($entered, ',')) {
return $entered;
}

$choices = explode(',', $entered);
if (\strlen($lastChoice = trim($choices[\count($choices) - 1])) > 0) {
return $lastChoice;
}

return $entered;
}

/**
@param
@param
@return
@throws




*/
private function getHiddenResponse(OutputInterface $output, $inputStream)
{
if ('\\' === \DIRECTORY_SEPARATOR) {
$exe = __DIR__.'/../Resources/bin/hiddeninput.exe';


 if ('phar:' === substr(__FILE__, 0, 5)) {
$tmpExe = sys_get_temp_dir().'/hiddeninput.exe';
copy($exe, $tmpExe);
$exe = $tmpExe;
}

$value = rtrim(shell_exec($exe));
$output->writeln('');

if (isset($tmpExe)) {
unlink($tmpExe);
}

return $value;
}

if (self::$stty && Terminal::hasSttyAvailable()) {
$sttyMode = shell_exec('stty -g');

shell_exec('stty -echo');
$value = fgets($inputStream, 4096);
shell_exec(sprintf('stty %s', $sttyMode));

if (false === $value) {
throw new RuntimeException('Aborted.');
}

$value = trim($value);
$output->writeln('');

return $value;
}

if (false !== $shell = $this->getShell()) {
$readCmd = 'csh' === $shell ? 'set mypassword = $<' : 'read -r mypassword';
$command = sprintf("/usr/bin/env %s -c 'stty -echo; %s; stty echo; echo \$mypassword'", $shell, $readCmd);
$value = rtrim(shell_exec($command));
$output->writeln('');

return $value;
}

throw new RuntimeException('Unable to hide the response.');
}

/**
@param
@param
@param
@return
@throws




*/
private function validateAttempts(callable $interviewer, OutputInterface $output, Question $question)
{
$error = null;
$attempts = $question->getMaxAttempts();
while (null === $attempts || $attempts--) {
if (null !== $error) {
$this->writeError($output, $error);
}

try {
return \call_user_func($question->getValidator(), $interviewer());
} catch (RuntimeException $e) {
throw $e;
} catch (\Exception $error) {
}
}

throw $error;
}

/**
@return


*/
private function getShell()
{
if (null !== self::$shell) {
return self::$shell;
}

self::$shell = false;

if (file_exists('/usr/bin/env')) {

 $test = "/usr/bin/env %s -c 'echo OK' 2> /dev/null";
foreach (['bash', 'zsh', 'ksh', 'csh'] as $sh) {
if ('OK' === rtrim(shell_exec(sprintf($test, $sh)))) {
self::$shell = $sh;
break;
}
}
}

return self::$shell;
}
}
