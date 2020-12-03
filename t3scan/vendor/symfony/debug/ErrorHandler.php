<?php










namespace Symfony\Component\Debug;

use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;
use Symfony\Component\Debug\Exception\ContextErrorException;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\Debug\Exception\OutOfMemoryException;
use Symfony\Component\Debug\Exception\SilencedErrorContext;
use Symfony\Component\Debug\FatalErrorHandler\ClassNotFoundFatalErrorHandler;
use Symfony\Component\Debug\FatalErrorHandler\FatalErrorHandlerInterface;
use Symfony\Component\Debug\FatalErrorHandler\UndefinedFunctionFatalErrorHandler;
use Symfony\Component\Debug\FatalErrorHandler\UndefinedMethodFatalErrorHandler;

/**
@author
@author



















*/
class ErrorHandler
{
private $levels = [
\E_DEPRECATED => 'Deprecated',
\E_USER_DEPRECATED => 'User Deprecated',
\E_NOTICE => 'Notice',
\E_USER_NOTICE => 'User Notice',
\E_STRICT => 'Runtime Notice',
\E_WARNING => 'Warning',
\E_USER_WARNING => 'User Warning',
\E_COMPILE_WARNING => 'Compile Warning',
\E_CORE_WARNING => 'Core Warning',
\E_USER_ERROR => 'User Error',
\E_RECOVERABLE_ERROR => 'Catchable Fatal Error',
\E_COMPILE_ERROR => 'Compile Error',
\E_PARSE => 'Parse Error',
\E_ERROR => 'Error',
\E_CORE_ERROR => 'Core Error',
];

private $loggers = [
\E_DEPRECATED => [null, LogLevel::INFO],
\E_USER_DEPRECATED => [null, LogLevel::INFO],
\E_NOTICE => [null, LogLevel::WARNING],
\E_USER_NOTICE => [null, LogLevel::WARNING],
\E_STRICT => [null, LogLevel::WARNING],
\E_WARNING => [null, LogLevel::WARNING],
\E_USER_WARNING => [null, LogLevel::WARNING],
\E_COMPILE_WARNING => [null, LogLevel::WARNING],
\E_CORE_WARNING => [null, LogLevel::WARNING],
\E_USER_ERROR => [null, LogLevel::CRITICAL],
\E_RECOVERABLE_ERROR => [null, LogLevel::CRITICAL],
\E_COMPILE_ERROR => [null, LogLevel::CRITICAL],
\E_PARSE => [null, LogLevel::CRITICAL],
\E_ERROR => [null, LogLevel::CRITICAL],
\E_CORE_ERROR => [null, LogLevel::CRITICAL],
];

private $thrownErrors = 0x1FFF; 
 private $scopedErrors = 0x1FFF; 
 private $tracedErrors = 0x77FB; 
 private $screamedErrors = 0x55; 
 private $loggedErrors = 0;
private $traceReflector;

private $isRecursive = 0;
private $isRoot = false;
private $exceptionHandler;
private $bootstrappingLogger;

private static $reservedMemory;
private static $stackedErrors = [];
private static $stackedErrorLevels = [];
private static $toStringException = null;
private static $silencedErrorCache = [];
private static $silencedErrorCount = 0;
private static $exitCode = 0;

/**
@param
@param
@return



*/
public static function register(self $handler = null, $replace = true)
{
if (null === self::$reservedMemory) {
self::$reservedMemory = str_repeat('x', 10240);
register_shutdown_function(__CLASS__.'::handleFatalError');
}

if ($handlerIsNew = null === $handler) {
$handler = new static();
}

if (null === $prev = set_error_handler([$handler, 'handleError'])) {
restore_error_handler();

 set_error_handler([$handler, 'handleError'], $handler->thrownErrors | $handler->loggedErrors);
$handler->isRoot = true;
}

if ($handlerIsNew && \is_array($prev) && $prev[0] instanceof self) {
$handler = $prev[0];
$replace = false;
}
if (!$replace && $prev) {
restore_error_handler();
$handlerIsRegistered = \is_array($prev) && $handler === $prev[0];
} else {
$handlerIsRegistered = true;
}
if (\is_array($prev = set_exception_handler([$handler, 'handleException'])) && $prev[0] instanceof self) {
restore_exception_handler();
if (!$handlerIsRegistered) {
$handler = $prev[0];
} elseif ($handler !== $prev[0] && $replace) {
set_exception_handler([$handler, 'handleException']);
$p = $prev[0]->setExceptionHandler(null);
$handler->setExceptionHandler($p);
$prev[0]->setExceptionHandler($p);
}
} else {
$handler->setExceptionHandler($prev);
}

$handler->throwAt(\E_ALL & $handler->thrownErrors, true);

return $handler;
}

public function __construct(BufferingLogger $bootstrappingLogger = null)
{
if ($bootstrappingLogger) {
$this->bootstrappingLogger = $bootstrappingLogger;
$this->setDefaultLogger($bootstrappingLogger);
}
$this->traceReflector = new \ReflectionProperty('Exception', 'trace');
$this->traceReflector->setAccessible(true);
}

/**
@param
@param
@param


*/
public function setDefaultLogger(LoggerInterface $logger, $levels = \E_ALL, $replace = false)
{
$loggers = [];

if (\is_array($levels)) {
foreach ($levels as $type => $logLevel) {
if (empty($this->loggers[$type][0]) || $replace || $this->loggers[$type][0] === $this->bootstrappingLogger) {
$loggers[$type] = [$logger, $logLevel];
}
}
} else {
if (null === $levels) {
$levels = \E_ALL;
}
foreach ($this->loggers as $type => $log) {
if (($type & $levels) && (empty($log[0]) || $replace || $log[0] === $this->bootstrappingLogger)) {
$log[0] = $logger;
$loggers[$type] = $log;
}
}
}

$this->setLoggers($loggers);
}

/**
@param
@return
@throws




*/
public function setLoggers(array $loggers)
{
$prevLogged = $this->loggedErrors;
$prev = $this->loggers;
$flush = [];

foreach ($loggers as $type => $log) {
if (!isset($prev[$type])) {
throw new \InvalidArgumentException('Unknown error type: '.$type);
}
if (!\is_array($log)) {
$log = [$log];
} elseif (!\array_key_exists(0, $log)) {
throw new \InvalidArgumentException('No logger provided.');
}
if (null === $log[0]) {
$this->loggedErrors &= ~$type;
} elseif ($log[0] instanceof LoggerInterface) {
$this->loggedErrors |= $type;
} else {
throw new \InvalidArgumentException('Invalid logger provided.');
}
$this->loggers[$type] = $log + $prev[$type];

if ($this->bootstrappingLogger && $prev[$type][0] === $this->bootstrappingLogger) {
$flush[$type] = $type;
}
}
$this->reRegister($prevLogged | $this->thrownErrors);

if ($flush) {
foreach ($this->bootstrappingLogger->cleanLogs() as $log) {
$type = $log[2]['exception'] instanceof \ErrorException ? $log[2]['exception']->getSeverity() : \E_ERROR;
if (!isset($flush[$type])) {
$this->bootstrappingLogger->log($log[0], $log[1], $log[2]);
} elseif ($this->loggers[$type][0]) {
$this->loggers[$type][0]->log($this->loggers[$type][1], $log[1], $log[2]);
}
}
}

return $prev;
}

/**
@param
@return



*/
public function setExceptionHandler(callable $handler = null)
{
$prev = $this->exceptionHandler;
$this->exceptionHandler = $handler;

return $prev;
}

/**
@param
@param
@return



*/
public function throwAt($levels, $replace = false)
{
$prev = $this->thrownErrors;
$this->thrownErrors = ($levels | \E_RECOVERABLE_ERROR | \E_USER_ERROR) & ~\E_USER_DEPRECATED & ~\E_DEPRECATED;
if (!$replace) {
$this->thrownErrors |= $prev;
}
$this->reRegister($prev | $this->loggedErrors);

return $prev;
}

/**
@param
@param
@return



*/
public function scopeAt($levels, $replace = false)
{
$prev = $this->scopedErrors;
$this->scopedErrors = (int) $levels;
if (!$replace) {
$this->scopedErrors |= $prev;
}

return $prev;
}

/**
@param
@param
@return



*/
public function traceAt($levels, $replace = false)
{
$prev = $this->tracedErrors;
$this->tracedErrors = (int) $levels;
if (!$replace) {
$this->tracedErrors |= $prev;
}

return $prev;
}

/**
@param
@param
@return



*/
public function screamAt($levels, $replace = false)
{
$prev = $this->screamedErrors;
$this->screamedErrors = (int) $levels;
if (!$replace) {
$this->screamedErrors |= $prev;
}

return $prev;
}




private function reRegister($prev)
{
if ($prev !== $this->thrownErrors | $this->loggedErrors) {
$handler = set_error_handler('var_dump');
$handler = \is_array($handler) ? $handler[0] : null;
restore_error_handler();
if ($handler === $this) {
restore_error_handler();
if ($this->isRoot) {
set_error_handler([$this, 'handleError'], $this->thrownErrors | $this->loggedErrors);
} else {
set_error_handler([$this, 'handleError']);
}
}
}
}

/**
@param
@param
@param
@param
@return
@throws
@internal





*/
public function handleError($type, $message, $file, $line)
{
if (\PHP_VERSION_ID >= 70300 && \E_WARNING === $type && '"' === $message[0] && false !== strpos($message, '" targeting switch is equivalent to "break')) {
$type = \E_DEPRECATED;
}


 $level = error_reporting();
$silenced = 0 === ($level & $type);

 $level |= \E_RECOVERABLE_ERROR | \E_USER_ERROR | \E_DEPRECATED | \E_USER_DEPRECATED;
$log = $this->loggedErrors & $type;
$throw = $this->thrownErrors & $type & $level;
$type &= $level | $this->screamedErrors;

if (!$type || (!$log && !$throw)) {
return !$silenced && $type && $log;
}
$scope = $this->scopedErrors & $type;

if (4 < $numArgs = \func_num_args()) {
$context = func_get_arg(4) ?: [];
$backtrace = 5 < $numArgs ? func_get_arg(5) : null; 
 } else {
$context = [];
$backtrace = null;
}

if (isset($context['GLOBALS']) && $scope) {
$e = $context; 
 unset($e['GLOBALS'], $context); 
 $context = $e;
}

if (null !== $backtrace && $type & \E_ERROR) {

 
 
 $this->handleFatalError(compact('type', 'message', 'file', 'line', 'backtrace'));

return true;
}

$logMessage = $this->levels[$type].': '.$message;

if (null !== self::$toStringException) {
$errorAsException = self::$toStringException;
self::$toStringException = null;
} elseif (!$throw && !($type & $level)) {
if (!isset(self::$silencedErrorCache[$id = $file.':'.$line])) {
$lightTrace = $this->tracedErrors & $type ? $this->cleanTrace(debug_backtrace(\DEBUG_BACKTRACE_IGNORE_ARGS, 3), $type, $file, $line, false) : [];
$errorAsException = new SilencedErrorContext($type, $file, $line, $lightTrace);
} elseif (isset(self::$silencedErrorCache[$id][$message])) {
$lightTrace = null;
$errorAsException = self::$silencedErrorCache[$id][$message];
++$errorAsException->count;
} else {
$lightTrace = [];
$errorAsException = null;
}

if (100 < ++self::$silencedErrorCount) {
self::$silencedErrorCache = $lightTrace = [];
self::$silencedErrorCount = 1;
}
if ($errorAsException) {
self::$silencedErrorCache[$id][$message] = $errorAsException;
}
if (null === $lightTrace) {
return true;
}
} else {
if ($scope) {
$errorAsException = new ContextErrorException($logMessage, 0, $type, $file, $line, $context);
} else {
$errorAsException = new \ErrorException($logMessage, 0, $type, $file, $line);
}


 if ($throw || $this->tracedErrors & $type) {
$backtrace = $backtrace ?: $errorAsException->getTrace();
$lightTrace = $this->cleanTrace($backtrace, $type, $file, $line, $throw);
$this->traceReflector->setValue($errorAsException, $lightTrace);
} else {
$this->traceReflector->setValue($errorAsException, []);
}
}

if ($throw) {
if (\PHP_VERSION_ID < 70400 && \E_USER_ERROR & $type) {
for ($i = 1; isset($backtrace[$i]); ++$i) {
if (isset($backtrace[$i]['function'], $backtrace[$i]['type'], $backtrace[$i - 1]['function'])
&& '__toString' === $backtrace[$i]['function']
&& '->' === $backtrace[$i]['type']
&& !isset($backtrace[$i - 1]['class'])
&& ('trigger_error' === $backtrace[$i - 1]['function'] || 'user_error' === $backtrace[$i - 1]['function'])
) {

 
 
 
 
 

foreach ($context as $e) {
if (($e instanceof \Exception || $e instanceof \Throwable) && $e->__toString() === $message) {
if (1 === $i) {

 $errorAsException = $e;
break;
}
self::$toStringException = $e;

return true;
}
}

if (1 < $i) {

 $this->handleException($errorAsException);


 return false;
}
}
}
}

throw $errorAsException;
}

if ($this->isRecursive) {
$log = 0;
} elseif (self::$stackedErrorLevels) {
self::$stackedErrors[] = [
$this->loggers[$type][0],
($type & $level) ? $this->loggers[$type][1] : LogLevel::DEBUG,
$logMessage,
$errorAsException ? ['exception' => $errorAsException] : [],
];
} else {
if (\PHP_VERSION_ID < (\PHP_VERSION_ID < 70400 ? 70316 : 70404) && !\defined('HHVM_VERSION')) {
$currentErrorHandler = set_error_handler('var_dump');
restore_error_handler();
}

try {
$this->isRecursive = true;
$level = ($type & $level) ? $this->loggers[$type][1] : LogLevel::DEBUG;
$this->loggers[$type][0]->log($level, $logMessage, $errorAsException ? ['exception' => $errorAsException] : []);
} finally {
$this->isRecursive = false;

if (\PHP_VERSION_ID < (\PHP_VERSION_ID < 70400 ? 70316 : 70404) && !\defined('HHVM_VERSION')) {
set_error_handler($currentErrorHandler);
}
}
}

return !$silenced && $type && $log;
}

/**
@param
@param
@internal



*/
public function handleException($exception, array $error = null)
{
if (null === $error) {
self::$exitCode = 255;
}
if (!$exception instanceof \Exception) {
$exception = new FatalThrowableError($exception);
}
$type = $exception instanceof FatalErrorException ? $exception->getSeverity() : \E_ERROR;
$handlerException = null;

if (($this->loggedErrors & $type) || $exception instanceof FatalThrowableError) {
if ($exception instanceof FatalErrorException) {
if ($exception instanceof FatalThrowableError) {
$error = [
'type' => $type,
'message' => $message = $exception->getMessage(),
'file' => $exception->getFile(),
'line' => $exception->getLine(),
];
} else {
$message = 'Fatal '.$exception->getMessage();
}
} elseif ($exception instanceof \ErrorException) {
$message = 'Uncaught '.$exception->getMessage();
} else {
$message = 'Uncaught Exception: '.$exception->getMessage();
}
}
if ($this->loggedErrors & $type) {
try {
$this->loggers[$type][0]->log($this->loggers[$type][1], $message, ['exception' => $exception]);
} catch (\Exception $handlerException) {
} catch (\Throwable $handlerException) {
}
}
if ($exception instanceof FatalErrorException && !$exception instanceof OutOfMemoryException && $error) {
foreach ($this->getFatalErrorHandlers() as $handler) {
if ($e = $handler->handleError($error, $exception)) {
$exception = $e;
break;
}
}
}
$exceptionHandler = $this->exceptionHandler;
$this->exceptionHandler = null;
try {
if (null !== $exceptionHandler) {
$exceptionHandler($exception);

return;
}
$handlerException = $handlerException ?: $exception;
} catch (\Exception $handlerException) {
} catch (\Throwable $handlerException) {
}
if ($exception === $handlerException) {
self::$reservedMemory = null; 
 throw $exception; 
 }
$this->handleException($handlerException);
}

/**
@param
@internal



*/
public static function handleFatalError(array $error = null)
{
if (null === self::$reservedMemory) {
return;
}

$handler = self::$reservedMemory = null;
$handlers = [];
$previousHandler = null;
$sameHandlerLimit = 10;

while (!\is_array($handler) || !$handler[0] instanceof self) {
$handler = set_exception_handler('var_dump');
restore_exception_handler();

if (!$handler) {
break;
}
restore_exception_handler();

if ($handler !== $previousHandler) {
array_unshift($handlers, $handler);
$previousHandler = $handler;
} elseif (0 === --$sameHandlerLimit) {
$handler = null;
break;
}
}
foreach ($handlers as $h) {
set_exception_handler($h);
}
if (!$handler) {
return;
}
if ($handler !== $h) {
$handler[0]->setExceptionHandler($h);
}
$handler = $handler[0];
$handlers = [];

if ($exit = null === $error) {
$error = error_get_last();
}

try {
while (self::$stackedErrorLevels) {
static::unstackErrors();
}
} catch (\Exception $exception) {

 } catch (\Throwable $exception) {

 }

if ($error && $error['type'] &= \E_PARSE | \E_ERROR | \E_CORE_ERROR | \E_COMPILE_ERROR) {

 $handler->throwAt(0, true);
$trace = isset($error['backtrace']) ? $error['backtrace'] : null;

if (0 === strpos($error['message'], 'Allowed memory') || 0 === strpos($error['message'], 'Out of memory')) {
$exception = new OutOfMemoryException($handler->levels[$error['type']].': '.$error['message'], 0, $error['type'], $error['file'], $error['line'], 2, false, $trace);
} else {
$exception = new FatalErrorException($handler->levels[$error['type']].': '.$error['message'], 0, $error['type'], $error['file'], $error['line'], 2, true, $trace);
}
}

try {
if (isset($exception)) {
self::$exitCode = 255;
$handler->handleException($exception, $error);
}
} catch (FatalErrorException $e) {

 }

if ($exit && self::$exitCode) {
$exitCode = self::$exitCode;
register_shutdown_function('register_shutdown_function', function () use ($exitCode) { exit($exitCode); });
}
}

/**
@deprecated










*/
public static function stackErrors()
{
@trigger_error('Support for stacking errors is deprecated since Symfony 3.4 and will be removed in 4.0.', \E_USER_DEPRECATED);

self::$stackedErrorLevels[] = error_reporting(error_reporting() | \E_PARSE | \E_ERROR | \E_CORE_ERROR | \E_COMPILE_ERROR);
}

/**
@deprecated


*/
public static function unstackErrors()
{
@trigger_error('Support for unstacking errors is deprecated since Symfony 3.4 and will be removed in 4.0.', \E_USER_DEPRECATED);

$level = array_pop(self::$stackedErrorLevels);

if (null !== $level) {
$errorReportingLevel = error_reporting($level);
if ($errorReportingLevel !== ($level | \E_PARSE | \E_ERROR | \E_CORE_ERROR | \E_COMPILE_ERROR)) {

 error_reporting($errorReportingLevel);
}
}

if (empty(self::$stackedErrorLevels)) {
$errors = self::$stackedErrors;
self::$stackedErrors = [];

foreach ($errors as $error) {
$error[0]->log($error[1], $error[2], $error[3]);
}
}
}

/**
@return




*/
protected function getFatalErrorHandlers()
{
return [
new UndefinedFunctionFatalErrorHandler(),
new UndefinedMethodFatalErrorHandler(),
new ClassNotFoundFatalErrorHandler(),
];
}

private function cleanTrace($backtrace, $type, $file, $line, $throw)
{
$lightTrace = $backtrace;

for ($i = 0; isset($backtrace[$i]); ++$i) {
if (isset($backtrace[$i]['file'], $backtrace[$i]['line']) && $backtrace[$i]['line'] === $line && $backtrace[$i]['file'] === $file) {
$lightTrace = \array_slice($lightTrace, 1 + $i);
break;
}
}
if (!($throw || $this->scopedErrors & $type)) {
for ($i = 0; isset($lightTrace[$i]); ++$i) {
unset($lightTrace[$i]['args'], $lightTrace[$i]['object']);
}
}

return $lightTrace;
}
}
