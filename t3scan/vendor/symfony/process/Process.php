<?php










namespace Symfony\Component\Process;

use Symfony\Component\Process\Exception\InvalidArgumentException;
use Symfony\Component\Process\Exception\LogicException;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Exception\ProcessTimedOutException;
use Symfony\Component\Process\Exception\RuntimeException;
use Symfony\Component\Process\Pipes\PipesInterface;
use Symfony\Component\Process\Pipes\UnixPipes;
use Symfony\Component\Process\Pipes\WindowsPipes;

/**
@author
@author



*/
class Process implements \IteratorAggregate
{
const ERR = 'err';
const OUT = 'out';

const STATUS_READY = 'ready';
const STATUS_STARTED = 'started';
const STATUS_TERMINATED = 'terminated';

const STDIN = 0;
const STDOUT = 1;
const STDERR = 2;


 const TIMEOUT_PRECISION = 0.2;

const ITER_NON_BLOCKING = 1; 
 const ITER_KEEP_OUTPUT = 2; 
 const ITER_SKIP_OUT = 4; 
 const ITER_SKIP_ERR = 8; 

private $callback;
private $hasCallback = false;
private $commandline;
private $cwd;
private $env;
private $input;
private $starttime;
private $lastOutputTime;
private $timeout;
private $idleTimeout;
private $options = ['suppress_errors' => true];
private $exitcode;
private $fallbackStatus = [];
private $processInformation;
private $outputDisabled = false;
private $stdout;
private $stderr;
private $enhanceWindowsCompatibility = true;
private $enhanceSigchildCompatibility;
private $process;
private $status = self::STATUS_READY;
private $incrementalOutputOffset = 0;
private $incrementalErrorOutputOffset = 0;
private $tty = false;
private $pty;
private $inheritEnv = false;

private $useFileHandles = false;
/**
@var */
private $processPipes;

private $latestSignal;

private static $sigchild;






public static $exitCodes = [
0 => 'OK',
1 => 'General error',
2 => 'Misuse of shell builtins',

126 => 'Invoked command cannot execute',
127 => 'Command not found',
128 => 'Invalid exit argument',


 129 => 'Hangup',
130 => 'Interrupt',
131 => 'Quit and dump core',
132 => 'Illegal instruction',
133 => 'Trace/breakpoint trap',
134 => 'Process aborted',
135 => 'Bus error: "access to undefined portion of memory object"',
136 => 'Floating point exception: "erroneous arithmetic operation"',
137 => 'Kill (terminate immediately)',
138 => 'User-defined 1',
139 => 'Segmentation violation',
140 => 'User-defined 2',
141 => 'Write to pipe with no one reading',
142 => 'Signal raised by alarm',
143 => 'Termination (request to terminate)',

 145 => 'Child process terminated, stopped (or continued*)',
146 => 'Continue if stopped',
147 => 'Stop executing temporarily',
148 => 'Terminal stop signal',
149 => 'Background process attempting to read from tty ("in")',
150 => 'Background process attempting to write to tty ("out")',
151 => 'Urgent data available on socket',
152 => 'CPU time limit exceeded',
153 => 'File size limit exceeded',
154 => 'Signal raised by timer counting virtual time: "virtual timer expired"',
155 => 'Profiling timer expired',

 157 => 'Pollable event',

 159 => 'Bad syscall',
];

/**
@param
@param
@param
@param
@param
@param
@throws

*/
public function __construct($commandline, $cwd = null, array $env = null, $input = null, $timeout = 60, array $options = null)
{
if (!\function_exists('proc_open')) {
throw new RuntimeException('The Process class relies on proc_open, which is not available on your PHP installation.');
}

$this->commandline = $commandline;
$this->cwd = $cwd;


 
 
 
 if (null === $this->cwd && (\defined('ZEND_THREAD_SAFE') || '\\' === \DIRECTORY_SEPARATOR)) {
$this->cwd = getcwd();
}
if (null !== $env) {
$this->setEnv($env);
}

$this->setInput($input);
$this->setTimeout($timeout);
$this->useFileHandles = '\\' === \DIRECTORY_SEPARATOR;
$this->pty = false;
$this->enhanceSigchildCompatibility = '\\' !== \DIRECTORY_SEPARATOR && $this->isSigchildEnabled();
if (null !== $options) {
@trigger_error(sprintf('The $options parameter of the %s constructor is deprecated since Symfony 3.3 and will be removed in 4.0.', __CLASS__), \E_USER_DEPRECATED);
$this->options = array_replace($this->options, $options);
}
}

public function __destruct()
{
$this->stop(0);
}

public function __clone()
{
$this->resetProcessData();
}

/**
@param
@return
@throws
@throws
@throws
@final













*/
public function run($callback = null)
{
$env = 1 < \func_num_args() ? func_get_arg(1) : null;
$this->start($callback, $env);

return $this->wait();
}

/**
@return
@throws
@throws
@final







*/
public function mustRun(callable $callback = null)
{
if (!$this->enhanceSigchildCompatibility && $this->isSigchildEnabled()) {
throw new RuntimeException('This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.');
}
$env = 1 < \func_num_args() ? func_get_arg(1) : null;

if (0 !== $this->run($callback, $env)) {
throw new ProcessFailedException($this);
}

return $this;
}

/**
@param
@throws
@throws
@throws













*/
public function start(callable $callback = null)
{
if ($this->isRunning()) {
throw new RuntimeException('Process is already running.');
}
if (2 <= \func_num_args()) {
$env = func_get_arg(1);
} else {
if (__CLASS__ !== static::class) {
$r = new \ReflectionMethod($this, __FUNCTION__);
if (__CLASS__ !== $r->getDeclaringClass()->getName() && (2 > $r->getNumberOfParameters() || 'env' !== $r->getParameters()[1]->name)) {
@trigger_error(sprintf('The %s::start() method expects a second "$env" argument since Symfony 3.3. It will be made mandatory in 4.0.', static::class), \E_USER_DEPRECATED);
}
}
$env = null;
}

$this->resetProcessData();
$this->starttime = $this->lastOutputTime = microtime(true);
$this->callback = $this->buildCallback($callback);
$this->hasCallback = null !== $callback;
$descriptors = $this->getDescriptors();
$inheritEnv = $this->inheritEnv;

if (\is_array($commandline = $this->commandline)) {
$commandline = implode(' ', array_map([$this, 'escapeArgument'], $commandline));

if ('\\' !== \DIRECTORY_SEPARATOR) {

 $commandline = 'exec '.$commandline;
}
}

if (null === $env) {
$env = $this->env;
} else {
if ($this->env) {
$env += $this->env;
}
$inheritEnv = true;
}

if (null !== $env && $inheritEnv) {
$env += $this->getDefaultEnv();
} elseif (null !== $env) {
@trigger_error('Not inheriting environment variables is deprecated since Symfony 3.3 and will always happen in 4.0. Set "Process::inheritEnvironmentVariables()" to true instead.', \E_USER_DEPRECATED);
} else {
$env = $this->getDefaultEnv();
}
if ('\\' === \DIRECTORY_SEPARATOR && $this->enhanceWindowsCompatibility) {
$this->options['bypass_shell'] = true;
$commandline = $this->prepareWindowsCommandLine($commandline, $env);
} elseif (!$this->useFileHandles && $this->enhanceSigchildCompatibility && $this->isSigchildEnabled()) {

 $descriptors[3] = ['pipe', 'w'];


 $commandline = '{ ('.$commandline.') <&3 3<&- 3>/dev/null & } 3<&0;';
$commandline .= 'pid=$!; echo $pid >&3; wait $pid; code=$?; echo $code >&3; exit $code';


 
 $ptsWorkaround = fopen(__FILE__, 'r');
}
if (\defined('HHVM_VERSION')) {
$envPairs = $env;
} else {
$envPairs = [];
foreach ($env as $k => $v) {
if (false !== $v) {
$envPairs[] = $k.'='.$v;
}
}
}

if (!is_dir($this->cwd)) {
@trigger_error('The provided cwd does not exist. Command is currently ran against getcwd(). This behavior is deprecated since Symfony 3.4 and will be removed in 4.0.', \E_USER_DEPRECATED);
}

$this->process = @proc_open($commandline, $descriptors, $this->processPipes->pipes, $this->cwd, $envPairs, $this->options);

if (!\is_resource($this->process)) {
throw new RuntimeException('Unable to launch a new process.');
}
$this->status = self::STATUS_STARTED;

if (isset($descriptors[3])) {
$this->fallbackStatus['pid'] = (int) fgets($this->processPipes->pipes[3]);
}

if ($this->tty) {
return;
}

$this->updateStatus(false);
$this->checkTimeout();
}

/**
@param
@return
@throws
@throws
@see
@final









*/
public function restart(callable $callback = null)
{
if ($this->isRunning()) {
throw new RuntimeException('Process is already running.');
}
$env = 1 < \func_num_args() ? func_get_arg(1) : null;

$process = clone $this;
$process->start($callback, $env);

return $process;
}

/**
@param
@return
@throws
@throws
@throws








*/
public function wait(callable $callback = null)
{
$this->requireProcessIsStarted(__FUNCTION__);

$this->updateStatus(false);

if (null !== $callback) {
if (!$this->processPipes->haveReadSupport()) {
$this->stop(0);
throw new \LogicException('Pass the callback to the Process::start method or enableOutput to use a callback with Process::wait.');
}
$this->callback = $this->buildCallback($callback);
}

do {
$this->checkTimeout();
$running = '\\' === \DIRECTORY_SEPARATOR ? $this->isRunning() : $this->processPipes->areOpen();
$this->readPipes($running, '\\' !== \DIRECTORY_SEPARATOR || !$running);
} while ($running);

while ($this->isRunning()) {
$this->checkTimeout();
usleep(1000);
}

if ($this->processInformation['signaled'] && $this->processInformation['termsig'] !== $this->latestSignal) {
throw new RuntimeException(sprintf('The process has been signaled with signal "%s".', $this->processInformation['termsig']));
}

return $this->exitcode;
}

/**
@return


*/
public function getPid()
{
return $this->isRunning() ? $this->processInformation['pid'] : null;
}

/**
@param
@return
@throws
@throws
@throws




*/
public function signal($signal)
{
$this->doSignal($signal, true);

return $this;
}

/**
@return
@throws
@throws



*/
public function disableOutput()
{
if ($this->isRunning()) {
throw new RuntimeException('Disabling output while the process is running is not possible.');
}
if (null !== $this->idleTimeout) {
throw new LogicException('Output can not be disabled while an idle timeout is set.');
}

$this->outputDisabled = true;

return $this;
}

/**
@return
@throws



*/
public function enableOutput()
{
if ($this->isRunning()) {
throw new RuntimeException('Enabling output while the process is running is not possible.');
}

$this->outputDisabled = false;

return $this;
}

/**
@return


*/
public function isOutputDisabled()
{
return $this->outputDisabled;
}

/**
@return
@throws
@throws



*/
public function getOutput()
{
$this->readPipesForOutput(__FUNCTION__);

if (false === $ret = stream_get_contents($this->stdout, -1, 0)) {
return '';
}

return $ret;
}

/**
@return
@throws
@throws






*/
public function getIncrementalOutput()
{
$this->readPipesForOutput(__FUNCTION__);

$latest = stream_get_contents($this->stdout, -1, $this->incrementalOutputOffset);
$this->incrementalOutputOffset = ftell($this->stdout);

if (false === $latest) {
return '';
}

return $latest;
}

/**
@param
@throws
@throws
@return




*/
public function getIterator($flags = 0)
{
$this->readPipesForOutput(__FUNCTION__, false);

$clearOutput = !(self::ITER_KEEP_OUTPUT & $flags);
$blocking = !(self::ITER_NON_BLOCKING & $flags);
$yieldOut = !(self::ITER_SKIP_OUT & $flags);
$yieldErr = !(self::ITER_SKIP_ERR & $flags);

while (null !== $this->callback || ($yieldOut && !feof($this->stdout)) || ($yieldErr && !feof($this->stderr))) {
if ($yieldOut) {
$out = stream_get_contents($this->stdout, -1, $this->incrementalOutputOffset);

if (isset($out[0])) {
if ($clearOutput) {
$this->clearOutput();
} else {
$this->incrementalOutputOffset = ftell($this->stdout);
}

yield self::OUT => $out;
}
}

if ($yieldErr) {
$err = stream_get_contents($this->stderr, -1, $this->incrementalErrorOutputOffset);

if (isset($err[0])) {
if ($clearOutput) {
$this->clearErrorOutput();
} else {
$this->incrementalErrorOutputOffset = ftell($this->stderr);
}

yield self::ERR => $err;
}
}

if (!$blocking && !isset($out[0]) && !isset($err[0])) {
yield self::OUT => '';
}

$this->checkTimeout();
$this->readPipesForOutput(__FUNCTION__, $blocking);
}
}

/**
@return


*/
public function clearOutput()
{
ftruncate($this->stdout, 0);
fseek($this->stdout, 0);
$this->incrementalOutputOffset = 0;

return $this;
}

/**
@return
@throws
@throws



*/
public function getErrorOutput()
{
$this->readPipesForOutput(__FUNCTION__);

if (false === $ret = stream_get_contents($this->stderr, -1, 0)) {
return '';
}

return $ret;
}

/**
@return
@throws
@throws







*/
public function getIncrementalErrorOutput()
{
$this->readPipesForOutput(__FUNCTION__);

$latest = stream_get_contents($this->stderr, -1, $this->incrementalErrorOutputOffset);
$this->incrementalErrorOutputOffset = ftell($this->stderr);

if (false === $latest) {
return '';
}

return $latest;
}

/**
@return


*/
public function clearErrorOutput()
{
ftruncate($this->stderr, 0);
fseek($this->stderr, 0);
$this->incrementalErrorOutputOffset = 0;

return $this;
}

/**
@return
@throws



*/
public function getExitCode()
{
if (!$this->enhanceSigchildCompatibility && $this->isSigchildEnabled()) {
throw new RuntimeException('This PHP has been compiled with --enable-sigchild. You must use setEnhanceSigchildCompatibility() to use this method.');
}

$this->updateStatus(false);

return $this->exitcode;
}

/**
@return
@see
@see






*/
public function getExitCodeText()
{
if (null === $exitcode = $this->getExitCode()) {
return null;
}

return isset(self::$exitCodes[$exitcode]) ? self::$exitCodes[$exitcode] : 'Unknown error';
}

/**
@return


*/
public function isSuccessful()
{
return 0 === $this->getExitCode();
}

/**
@return
@throws
@throws





*/
public function hasBeenSignaled()
{
$this->requireProcessIsTerminated(__FUNCTION__);

if (!$this->enhanceSigchildCompatibility && $this->isSigchildEnabled()) {
throw new RuntimeException('This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved.');
}

return $this->processInformation['signaled'];
}

/**
@return
@throws
@throws





*/
public function getTermSignal()
{
$this->requireProcessIsTerminated(__FUNCTION__);

if ($this->isSigchildEnabled() && (!$this->enhanceSigchildCompatibility || -1 === $this->processInformation['termsig'])) {
throw new RuntimeException('This PHP has been compiled with --enable-sigchild. Term signal can not be retrieved.');
}

return $this->processInformation['termsig'];
}

/**
@return
@throws





*/
public function hasBeenStopped()
{
$this->requireProcessIsTerminated(__FUNCTION__);

return $this->processInformation['stopped'];
}

/**
@return
@throws





*/
public function getStopSignal()
{
$this->requireProcessIsTerminated(__FUNCTION__);

return $this->processInformation['stopsig'];
}

/**
@return


*/
public function isRunning()
{
if (self::STATUS_STARTED !== $this->status) {
return false;
}

$this->updateStatus(false);

return $this->processInformation['running'];
}

/**
@return


*/
public function isStarted()
{
return self::STATUS_READY != $this->status;
}

/**
@return


*/
public function isTerminated()
{
$this->updateStatus(false);

return self::STATUS_TERMINATED == $this->status;
}

/**
@return




*/
public function getStatus()
{
$this->updateStatus(false);

return $this->status;
}

/**
@param
@param
@return



*/
public function stop($timeout = 10, $signal = null)
{
$timeoutMicro = microtime(true) + $timeout;
if ($this->isRunning()) {

 $this->doSignal(15, false);
do {
usleep(1000);
} while ($this->isRunning() && microtime(true) < $timeoutMicro);

if ($this->isRunning()) {

 
 $this->doSignal($signal ?: 9, false);
}
}

if ($this->isRunning()) {
if (isset($this->fallbackStatus['pid'])) {
unset($this->fallbackStatus['pid']);

return $this->stop(0, $signal);
}
$this->close();
}

return $this->exitcode;
}

/**
@internal
@param



*/
public function addOutput($line)
{
$this->lastOutputTime = microtime(true);

fseek($this->stdout, 0, \SEEK_END);
fwrite($this->stdout, $line);
fseek($this->stdout, $this->incrementalOutputOffset);
}

/**
@internal
@param



*/
public function addErrorOutput($line)
{
$this->lastOutputTime = microtime(true);

fseek($this->stderr, 0, \SEEK_END);
fwrite($this->stderr, $line);
fseek($this->stderr, $this->incrementalErrorOutputOffset);
}

/**
@return


*/
public function getCommandLine()
{
return \is_array($this->commandline) ? implode(' ', array_map([$this, 'escapeArgument'], $this->commandline)) : $this->commandline;
}

/**
@param
@return



*/
public function setCommandLine($commandline)
{
$this->commandline = $commandline;

return $this;
}

/**
@return


*/
public function getTimeout()
{
return $this->timeout;
}

/**
@return


*/
public function getIdleTimeout()
{
return $this->idleTimeout;
}

/**
@param
@return
@throws






*/
public function setTimeout($timeout)
{
$this->timeout = $this->validateTimeout($timeout);

return $this;
}

/**
@param
@return
@throws
@throws






*/
public function setIdleTimeout($timeout)
{
if (null !== $timeout && $this->outputDisabled) {
throw new LogicException('Idle timeout can not be set while the output is disabled.');
}

$this->idleTimeout = $this->validateTimeout($timeout);

return $this;
}

/**
@param
@return
@throws




*/
public function setTty($tty)
{
if ('\\' === \DIRECTORY_SEPARATOR && $tty) {
throw new RuntimeException('TTY mode is not supported on Windows platform.');
}
if ($tty) {
static $isTtySupported;

if (null === $isTtySupported) {
$isTtySupported = (bool) @proc_open('echo 1 >/dev/null', [['file', '/dev/tty', 'r'], ['file', '/dev/tty', 'w'], ['file', '/dev/tty', 'w']], $pipes);
}

if (!$isTtySupported) {
throw new RuntimeException('TTY mode requires /dev/tty to be read/writable.');
}
}

$this->tty = (bool) $tty;

return $this;
}

/**
@return


*/
public function isTty()
{
return $this->tty;
}

/**
@param
@return



*/
public function setPty($bool)
{
$this->pty = (bool) $bool;

return $this;
}

/**
@return


*/
public function isPty()
{
return $this->pty;
}

/**
@return


*/
public function getWorkingDirectory()
{
if (null === $this->cwd) {

 
 return getcwd() ?: null;
}

return $this->cwd;
}

/**
@param
@return



*/
public function setWorkingDirectory($cwd)
{
$this->cwd = $cwd;

return $this;
}

/**
@return


*/
public function getEnv()
{
return $this->env;
}

/**
@param
@return











*/
public function setEnv(array $env)
{

 $env = array_filter($env, function ($value) {
return !\is_array($value);
});

$this->env = $env;

return $this;
}

/**
@return


*/
public function getInput()
{
return $this->input;
}

/**
@param
@return
@throws






*/
public function setInput($input)
{
if ($this->isRunning()) {
throw new LogicException('Input can not be set while the process is running.');
}

$this->input = ProcessUtils::validateInput(__METHOD__, $input);

return $this;
}

/**
@return
@deprecated



*/
public function getOptions()
{
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0.', __METHOD__), \E_USER_DEPRECATED);

return $this->options;
}

/**
@param
@return
@deprecated




*/
public function setOptions(array $options)
{
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0.', __METHOD__), \E_USER_DEPRECATED);

$this->options = $options;

return $this;
}

/**
@return
@deprecated





*/
public function getEnhanceWindowsCompatibility()
{
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Enhanced Windows compatibility will always be enabled.', __METHOD__), \E_USER_DEPRECATED);

return $this->enhanceWindowsCompatibility;
}

/**
@param
@return
@deprecated




*/
public function setEnhanceWindowsCompatibility($enhance)
{
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Enhanced Windows compatibility will always be enabled.', __METHOD__), \E_USER_DEPRECATED);

$this->enhanceWindowsCompatibility = (bool) $enhance;

return $this;
}

/**
@return
@deprecated



*/
public function getEnhanceSigchildCompatibility()
{
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Sigchild compatibility will always be enabled.', __METHOD__), \E_USER_DEPRECATED);

return $this->enhanceSigchildCompatibility;
}

/**
@param
@return
@deprecated








*/
public function setEnhanceSigchildCompatibility($enhance)
{
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Sigchild compatibility will always be enabled.', __METHOD__), \E_USER_DEPRECATED);

$this->enhanceSigchildCompatibility = (bool) $enhance;

return $this;
}

/**
@param
@return



*/
public function inheritEnvironmentVariables($inheritEnv = true)
{
if (!$inheritEnv) {
@trigger_error('Not inheriting environment variables is deprecated since Symfony 3.3 and will always happen in 4.0. Set "Process::inheritEnvironmentVariables()" to true instead.', \E_USER_DEPRECATED);
}

$this->inheritEnv = (bool) $inheritEnv;

return $this;
}

/**
@return
@deprecated



*/
public function areEnvironmentVariablesInherited()
{
@trigger_error(sprintf('The %s() method is deprecated since Symfony 3.3 and will be removed in 4.0. Environment variables will always be inherited.', __METHOD__), \E_USER_DEPRECATED);

return $this->inheritEnv;
}

/**
@throws





*/
public function checkTimeout()
{
if (self::STATUS_STARTED !== $this->status) {
return;
}

if (null !== $this->timeout && $this->timeout < microtime(true) - $this->starttime) {
$this->stop(0);

throw new ProcessTimedOutException($this, ProcessTimedOutException::TYPE_GENERAL);
}

if (null !== $this->idleTimeout && $this->idleTimeout < microtime(true) - $this->lastOutputTime) {
$this->stop(0);

throw new ProcessTimedOutException($this, ProcessTimedOutException::TYPE_IDLE);
}
}

/**
@return


*/
public static function isPtySupported()
{
static $result;

if (null !== $result) {
return $result;
}

if ('\\' === \DIRECTORY_SEPARATOR) {
return $result = false;
}

return $result = (bool) @proc_open('echo 1 >/dev/null', [['pty'], ['pty'], ['pty']], $pipes);
}

/**
@return


*/
private function getDescriptors()
{
if ($this->input instanceof \Iterator) {
$this->input->rewind();
}
if ('\\' === \DIRECTORY_SEPARATOR) {
$this->processPipes = new WindowsPipes($this->input, !$this->outputDisabled || $this->hasCallback);
} else {
$this->processPipes = new UnixPipes($this->isTty(), $this->isPty(), $this->input, !$this->outputDisabled || $this->hasCallback);
}

return $this->processPipes->getDescriptors();
}

/**
@param
@return






*/
protected function buildCallback(callable $callback = null)
{
if ($this->outputDisabled) {
return function ($type, $data) use ($callback) {
if (null !== $callback) {
\call_user_func($callback, $type, $data);
}
};
}

$out = self::OUT;

return function ($type, $data) use ($callback, $out) {
if ($out == $type) {
$this->addOutput($data);
} else {
$this->addErrorOutput($data);
}

if (null !== $callback) {
\call_user_func($callback, $type, $data);
}
};
}

/**
@param


*/
protected function updateStatus($blocking)
{
if (self::STATUS_STARTED !== $this->status) {
return;
}

$this->processInformation = proc_get_status($this->process);
$running = $this->processInformation['running'];

$this->readPipes($running && $blocking, '\\' !== \DIRECTORY_SEPARATOR || !$running);

if ($this->fallbackStatus && $this->enhanceSigchildCompatibility && $this->isSigchildEnabled()) {
$this->processInformation = $this->fallbackStatus + $this->processInformation;
}

if (!$running) {
$this->close();
}
}

/**
@return


*/
protected function isSigchildEnabled()
{
if (null !== self::$sigchild) {
return self::$sigchild;
}

if (!\function_exists('phpinfo') || \defined('HHVM_VERSION')) {
return self::$sigchild = false;
}

ob_start();
phpinfo(\INFO_GENERAL);

return self::$sigchild = false !== strpos(ob_get_clean(), '--enable-sigchild');
}

/**
@param
@param
@throws



*/
private function readPipesForOutput($caller, $blocking = false)
{
if ($this->outputDisabled) {
throw new LogicException('Output has been disabled.');
}

$this->requireProcessIsStarted($caller);

$this->updateStatus($blocking);
}

/**
@param
@return
@throws




*/
private function validateTimeout($timeout)
{
$timeout = (float) $timeout;

if (0.0 === $timeout) {
$timeout = null;
} elseif ($timeout < 0) {
throw new InvalidArgumentException('The timeout value must be a valid positive integer or float number.');
}

return $timeout;
}

/**
@param
@param


*/
private function readPipes($blocking, $close)
{
$result = $this->processPipes->readAndWrite($blocking, $close);

$callback = $this->callback;
foreach ($result as $type => $data) {
if (3 !== $type) {
$callback(self::STDOUT === $type ? self::OUT : self::ERR, $data);
} elseif (!isset($this->fallbackStatus['signaled'])) {
$this->fallbackStatus['exitcode'] = (int) $data;
}
}
}

/**
@return


*/
private function close()
{
$this->processPipes->close();
if (\is_resource($this->process)) {
proc_close($this->process);
}
$this->exitcode = $this->processInformation['exitcode'];
$this->status = self::STATUS_TERMINATED;

if (-1 === $this->exitcode) {
if ($this->processInformation['signaled'] && 0 < $this->processInformation['termsig']) {

 $this->exitcode = 128 + $this->processInformation['termsig'];
} elseif ($this->enhanceSigchildCompatibility && $this->isSigchildEnabled()) {
$this->processInformation['signaled'] = true;
$this->processInformation['termsig'] = -1;
}
}


 
 
 $this->callback = null;

return $this->exitcode;
}




private function resetProcessData()
{
$this->starttime = null;
$this->callback = null;
$this->exitcode = null;
$this->fallbackStatus = [];
$this->processInformation = null;
$this->stdout = fopen('php://temp/maxmemory:'.(1024 * 1024), 'w+b');
$this->stderr = fopen('php://temp/maxmemory:'.(1024 * 1024), 'w+b');
$this->process = null;
$this->latestSignal = null;
$this->status = self::STATUS_READY;
$this->incrementalOutputOffset = 0;
$this->incrementalErrorOutputOffset = 0;
}

/**
@param
@param
@return
@throws
@throws
@throws




*/
private function doSignal($signal, $throwException)
{
if (null === $pid = $this->getPid()) {
if ($throwException) {
throw new LogicException('Can not send signal on a non running process.');
}

return false;
}

if ('\\' === \DIRECTORY_SEPARATOR) {
exec(sprintf('taskkill /F /T /PID %d 2>&1', $pid), $output, $exitCode);
if ($exitCode && $this->isRunning()) {
if ($throwException) {
throw new RuntimeException(sprintf('Unable to kill the process (%s).', implode(' ', $output)));
}

return false;
}
} else {
if (!$this->enhanceSigchildCompatibility || !$this->isSigchildEnabled()) {
$ok = @proc_terminate($this->process, $signal);
} elseif (\function_exists('posix_kill')) {
$ok = @posix_kill($pid, $signal);
} elseif ($ok = proc_open(sprintf('kill -%d %d', $signal, $pid), [2 => ['pipe', 'w']], $pipes)) {
$ok = false === fgets($pipes[2]);
}
if (!$ok) {
if ($throwException) {
throw new RuntimeException(sprintf('Error while sending signal `%s`.', $signal));
}

return false;
}
}

$this->latestSignal = (int) $signal;
$this->fallbackStatus['signaled'] = true;
$this->fallbackStatus['exitcode'] = -1;
$this->fallbackStatus['termsig'] = $this->latestSignal;

return true;
}

private function prepareWindowsCommandLine($cmd, array &$env)
{
$uid = uniqid('', true);
$varCount = 0;
$varCache = [];
$cmd = preg_replace_callback(
'/"(?:(
                [^"%!^]*+
                (?:
                    (?: !LF! | "(?:\^[%!^])?+" )
                    [^"%!^]*+
                )++
            ) | [^"]*+ )"/x',
function ($m) use (&$env, &$varCache, &$varCount, $uid) {
if (!isset($m[1])) {
return $m[0];
}
if (isset($varCache[$m[0]])) {
return $varCache[$m[0]];
}
if (false !== strpos($value = $m[1], "\0")) {
$value = str_replace("\0", '?', $value);
}
if (false === strpbrk($value, "\"%!\n")) {
return '"'.$value.'"';
}

$value = str_replace(['!LF!', '"^!"', '"^%"', '"^^"', '""'], ["\n", '!', '%', '^', '"'], $value);
$value = '"'.preg_replace('/(\\\\*)"/', '$1$1\\"', $value).'"';
$var = $uid.++$varCount;

$env[$var] = $value;

return $varCache[$m[0]] = '!'.$var.'!';
},
$cmd
);

$cmd = 'cmd /V:ON /E:ON /D /C ('.str_replace("\n", ' ', $cmd).')';
foreach ($this->processPipes->getFiles() as $offset => $filename) {
$cmd .= ' '.$offset.'>"'.$filename.'"';
}

return $cmd;
}

/**
@param
@throws



*/
private function requireProcessIsStarted($functionName)
{
if (!$this->isStarted()) {
throw new LogicException(sprintf('Process must be started before calling "%s()".', $functionName));
}
}

/**
@param
@throws



*/
private function requireProcessIsTerminated($functionName)
{
if (!$this->isTerminated()) {
throw new LogicException(sprintf('Process must be terminated before calling "%s()".', $functionName));
}
}

/**
@param
@return



*/
private function escapeArgument($argument)
{
if ('\\' !== \DIRECTORY_SEPARATOR) {
return "'".str_replace("'", "'\\''", $argument)."'";
}
if ('' === $argument = (string) $argument) {
return '""';
}
if (false !== strpos($argument, "\0")) {
$argument = str_replace("\0", '?', $argument);
}
if (!preg_match('/[\/()%!^"<>&|\s]/', $argument)) {
return $argument;
}
$argument = preg_replace('/(\\\\+)$/', '$1$1', $argument);

return '"'.str_replace(['"', '^', '%', '!', "\n"], ['""', '"^^"', '"^%"', '"^!"', '!LF!'], $argument).'"';
}

private function getDefaultEnv()
{
$env = [];

foreach ($_SERVER as $k => $v) {
if (\is_string($v) && false !== $v = getenv($k)) {
$env[$k] = $v;
}
}

foreach ($_ENV as $k => $v) {
if (\is_string($v)) {
$env[$k] = $v;
}
}

return $env;
}
}