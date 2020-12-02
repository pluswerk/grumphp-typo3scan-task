<?php










namespace Symfony\Component\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Command\HelpCommand;
use Symfony\Component\Console\Command\ListCommand;
use Symfony\Component\Console\CommandLoader\CommandLoaderInterface;
use Symfony\Component\Console\Event\ConsoleCommandEvent;
use Symfony\Component\Console\Event\ConsoleErrorEvent;
use Symfony\Component\Console\Event\ConsoleExceptionEvent;
use Symfony\Component\Console\Event\ConsoleTerminateEvent;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Formatter\OutputFormatter;
use Symfony\Component\Console\Helper\DebugFormatterHelper;
use Symfony\Component\Console\Helper\FormatterHelper;
use Symfony\Component\Console\Helper\Helper;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Helper\ProcessHelper;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputAwareInterface;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\StreamableInputInterface;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Debug\ErrorHandler;
use Symfony\Component\Debug\Exception\FatalThrowableError;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
@author












*/
class Application
{
private $commands = [];
private $wantHelps = false;
private $runningCommand;
private $name;
private $version;
private $commandLoader;
private $catchExceptions = true;
private $autoExit = true;
private $definition;
private $helperSet;
private $dispatcher;
private $terminal;
private $defaultCommand;
private $singleCommand = false;
private $initialized;

/**
@param
@param
*/
public function __construct($name = 'UNKNOWN', $version = 'UNKNOWN')
{
$this->name = $name;
$this->version = $version;
$this->terminal = new Terminal();
$this->defaultCommand = 'list';
}

public function setDispatcher(EventDispatcherInterface $dispatcher)
{
$this->dispatcher = $dispatcher;
}

public function setCommandLoader(CommandLoaderInterface $commandLoader)
{
$this->commandLoader = $commandLoader;
}

/**
@return
@throws



*/
public function run(InputInterface $input = null, OutputInterface $output = null)
{
putenv('LINES='.$this->terminal->getHeight());
putenv('COLUMNS='.$this->terminal->getWidth());

if (null === $input) {
$input = new ArgvInput();
}

if (null === $output) {
$output = new ConsoleOutput();
}

$renderException = function ($e) use ($output) {
if (!$e instanceof \Exception) {
$e = class_exists(FatalThrowableError::class) ? new FatalThrowableError($e) : new \ErrorException($e->getMessage(), $e->getCode(), \E_ERROR, $e->getFile(), $e->getLine());
}
if ($output instanceof ConsoleOutputInterface) {
$this->renderException($e, $output->getErrorOutput());
} else {
$this->renderException($e, $output);
}
};
if ($phpHandler = set_exception_handler($renderException)) {
restore_exception_handler();
if (!\is_array($phpHandler) || !$phpHandler[0] instanceof ErrorHandler) {
$debugHandler = true;
} elseif ($debugHandler = $phpHandler[0]->setExceptionHandler($renderException)) {
$phpHandler[0]->setExceptionHandler($debugHandler);
}
}

if (null !== $this->dispatcher && $this->dispatcher->hasListeners(ConsoleEvents::EXCEPTION)) {
@trigger_error(sprintf('The "ConsoleEvents::EXCEPTION" event is deprecated since Symfony 3.3 and will be removed in 4.0. Listen to the "ConsoleEvents::ERROR" event instead.'), \E_USER_DEPRECATED);
}

$this->configureIO($input, $output);

try {
$exitCode = $this->doRun($input, $output);
} catch (\Exception $e) {
if (!$this->catchExceptions) {
throw $e;
}

$renderException($e);

$exitCode = $e->getCode();
if (is_numeric($exitCode)) {
$exitCode = (int) $exitCode;
if (0 === $exitCode) {
$exitCode = 1;
}
} else {
$exitCode = 1;
}
} finally {

 
 if (!$phpHandler) {
if (set_exception_handler($renderException) === $renderException) {
restore_exception_handler();
}
restore_exception_handler();
} elseif (!$debugHandler) {
$finalHandler = $phpHandler[0]->setExceptionHandler(null);
if ($finalHandler !== $renderException) {
$phpHandler[0]->setExceptionHandler($finalHandler);
}
}
}

if ($this->autoExit) {
if ($exitCode > 255) {
$exitCode = 255;
}

exit($exitCode);
}

return $exitCode;
}

/**
@return


*/
public function doRun(InputInterface $input, OutputInterface $output)
{
if (true === $input->hasParameterOption(['--version', '-V'], true)) {
$output->writeln($this->getLongVersion());

return 0;
}

try {

 $input->bind($this->getDefinition());
} catch (ExceptionInterface $e) {

 }

$name = $this->getCommandName($input);
if (true === $input->hasParameterOption(['--help', '-h'], true)) {
if (!$name) {
$name = 'help';
$input = new ArrayInput(['command_name' => $this->defaultCommand]);
} else {
$this->wantHelps = true;
}
}

if (!$name) {
$name = $this->defaultCommand;
$definition = $this->getDefinition();
$definition->setArguments(array_merge(
$definition->getArguments(),
[
'command' => new InputArgument('command', InputArgument::OPTIONAL, $definition->getArgument('command')->getDescription(), $name),
]
));
}

try {
$e = $this->runningCommand = null;

 $command = $this->find($name);
} catch (\Exception $e) {
} catch (\Throwable $e) {
}
if (null !== $e) {
if (null !== $this->dispatcher) {
$event = new ConsoleErrorEvent($input, $output, $e);
$this->dispatcher->dispatch(ConsoleEvents::ERROR, $event);
$e = $event->getError();

if (0 === $event->getExitCode()) {
return 0;
}
}

throw $e;
}

$this->runningCommand = $command;
$exitCode = $this->doRunCommand($command, $input, $output);
$this->runningCommand = null;

return $exitCode;
}

public function setHelperSet(HelperSet $helperSet)
{
$this->helperSet = $helperSet;
}

/**
@return


*/
public function getHelperSet()
{
if (!$this->helperSet) {
$this->helperSet = $this->getDefaultHelperSet();
}

return $this->helperSet;
}

public function setDefinition(InputDefinition $definition)
{
$this->definition = $definition;
}

/**
@return


*/
public function getDefinition()
{
if (!$this->definition) {
$this->definition = $this->getDefaultInputDefinition();
}

if ($this->singleCommand) {
$inputDefinition = $this->definition;
$inputDefinition->setArguments();

return $inputDefinition;
}

return $this->definition;
}

/**
@return


*/
public function getHelp()
{
return $this->getLongVersion();
}

/**
@return


*/
public function areExceptionsCaught()
{
return $this->catchExceptions;
}

/**
@param


*/
public function setCatchExceptions($boolean)
{
$this->catchExceptions = (bool) $boolean;
}

/**
@return


*/
public function isAutoExitEnabled()
{
return $this->autoExit;
}

/**
@param


*/
public function setAutoExit($boolean)
{
$this->autoExit = (bool) $boolean;
}

/**
@return


*/
public function getName()
{
return $this->name;
}

/**
@param


*/
public function setName($name)
{
$this->name = $name;
}

/**
@return


*/
public function getVersion()
{
return $this->version;
}

/**
@param


*/
public function setVersion($version)
{
$this->version = $version;
}

/**
@return


*/
public function getLongVersion()
{
if ('UNKNOWN' !== $this->getName()) {
if ('UNKNOWN' !== $this->getVersion()) {
return sprintf('%s <info>%s</info>', $this->getName(), $this->getVersion());
}

return $this->getName();
}

return 'Console Tool';
}

/**
@param
@return



*/
public function register($name)
{
return $this->add(new Command($name));
}

/**
@param




*/
public function addCommands(array $commands)
{
foreach ($commands as $command) {
$this->add($command);
}
}

/**
@return





*/
public function add(Command $command)
{
$this->init();

$command->setApplication($this);

if (!$command->isEnabled()) {
$command->setApplication(null);

return null;
}


 $command->getDefinition();

if (!$command->getName()) {
throw new LogicException(sprintf('The command defined in "%s" cannot have an empty name.', \get_class($command)));
}

$this->commands[$command->getName()] = $command;

foreach ($command->getAliases() as $alias) {
$this->commands[$alias] = $command;
}

return $command;
}

/**
@param
@return
@throws




*/
public function get($name)
{
$this->init();

if (!$this->has($name)) {
throw new CommandNotFoundException(sprintf('The command "%s" does not exist.', $name));
}


 if (!isset($this->commands[$name])) {
throw new CommandNotFoundException(sprintf('The "%s" command cannot be found because it is registered under multiple names. Make sure you don\'t set a different name via constructor or "setName()".', $name));
}

$command = $this->commands[$name];

if ($this->wantHelps) {
$this->wantHelps = false;

$helpCommand = $this->get('help');
$helpCommand->setCommand($command);

return $helpCommand;
}

return $command;
}

/**
@param
@return



*/
public function has($name)
{
$this->init();

return isset($this->commands[$name]) || ($this->commandLoader && $this->commandLoader->has($name) && $this->add($this->commandLoader->get($name)));
}

/**
@return




*/
public function getNamespaces()
{
$namespaces = [];
foreach ($this->all() as $command) {
if ($command->isHidden()) {
continue;
}

$namespaces = array_merge($namespaces, $this->extractAllNamespaces($command->getName()));

foreach ($command->getAliases() as $alias) {
$namespaces = array_merge($namespaces, $this->extractAllNamespaces($alias));
}
}

return array_values(array_unique(array_filter($namespaces)));
}

/**
@param
@return
@throws




*/
public function findNamespace($namespace)
{
$allNamespaces = $this->getNamespaces();
$expr = preg_replace_callback('{([^:]+|)}', function ($matches) { return preg_quote($matches[1]).'[^:]*'; }, $namespace);
$namespaces = preg_grep('{^'.$expr.'}', $allNamespaces);

if (empty($namespaces)) {
$message = sprintf('There are no commands defined in the "%s" namespace.', $namespace);

if ($alternatives = $this->findAlternatives($namespace, $allNamespaces)) {
if (1 == \count($alternatives)) {
$message .= "\n\nDid you mean this?\n    ";
} else {
$message .= "\n\nDid you mean one of these?\n    ";
}

$message .= implode("\n    ", $alternatives);
}

throw new CommandNotFoundException($message, $alternatives);
}

$exact = \in_array($namespace, $namespaces, true);
if (\count($namespaces) > 1 && !$exact) {
throw new CommandNotFoundException(sprintf("The namespace \"%s\" is ambiguous.\nDid you mean one of these?\n%s.", $namespace, $this->getAbbreviationSuggestions(array_values($namespaces))), array_values($namespaces));
}

return $exact ? $namespace : reset($namespaces);
}

/**
@param
@return
@throws







*/
public function find($name)
{
$this->init();

$aliases = [];

foreach ($this->commands as $command) {
foreach ($command->getAliases() as $alias) {
if (!$this->has($alias)) {
$this->commands[$alias] = $command;
}
}
}

if ($this->has($name)) {
return $this->get($name);
}

$allCommands = $this->commandLoader ? array_merge($this->commandLoader->getNames(), array_keys($this->commands)) : array_keys($this->commands);
$expr = preg_replace_callback('{([^:]+|)}', function ($matches) { return preg_quote($matches[1]).'[^:]*'; }, $name);
$commands = preg_grep('{^'.$expr.'}', $allCommands);

if (empty($commands)) {
$commands = preg_grep('{^'.$expr.'}i', $allCommands);
}


 if (empty($commands) || \count(preg_grep('{^'.$expr.'$}i', $commands)) < 1) {
if (false !== $pos = strrpos($name, ':')) {

 $this->findNamespace(substr($name, 0, $pos));
}

$message = sprintf('Command "%s" is not defined.', $name);

if ($alternatives = $this->findAlternatives($name, $allCommands)) {

 $alternatives = array_filter($alternatives, function ($name) {
return !$this->get($name)->isHidden();
});

if (1 == \count($alternatives)) {
$message .= "\n\nDid you mean this?\n    ";
} else {
$message .= "\n\nDid you mean one of these?\n    ";
}
$message .= implode("\n    ", $alternatives);
}

throw new CommandNotFoundException($message, array_values($alternatives));
}


 if (\count($commands) > 1) {
$commandList = $this->commandLoader ? array_merge(array_flip($this->commandLoader->getNames()), $this->commands) : $this->commands;
$commands = array_unique(array_filter($commands, function ($nameOrAlias) use (&$commandList, $commands, &$aliases) {
if (!$commandList[$nameOrAlias] instanceof Command) {
$commandList[$nameOrAlias] = $this->commandLoader->get($nameOrAlias);
}

$commandName = $commandList[$nameOrAlias]->getName();

$aliases[$nameOrAlias] = $commandName;

return $commandName === $nameOrAlias || !\in_array($commandName, $commands);
}));
}

$exact = \in_array($name, $commands, true) || isset($aliases[$name]);
if (\count($commands) > 1 && !$exact) {
$usableWidth = $this->terminal->getWidth() - 10;
$abbrevs = array_values($commands);
$maxLen = 0;
foreach ($abbrevs as $abbrev) {
$maxLen = max(Helper::strlen($abbrev), $maxLen);
}
$abbrevs = array_map(function ($cmd) use ($commandList, $usableWidth, $maxLen) {
if ($commandList[$cmd]->isHidden()) {
return false;
}

$abbrev = str_pad($cmd, $maxLen, ' ').' '.$commandList[$cmd]->getDescription();

return Helper::strlen($abbrev) > $usableWidth ? Helper::substr($abbrev, 0, $usableWidth - 3).'...' : $abbrev;
}, array_values($commands));
$suggestions = $this->getAbbreviationSuggestions(array_filter($abbrevs));

throw new CommandNotFoundException(sprintf("Command \"%s\" is ambiguous.\nDid you mean one of these?\n%s.", $name, $suggestions), array_values($commands));
}

return $this->get($exact ? $name : reset($commands));
}

/**
@param
@return





*/
public function all($namespace = null)
{
$this->init();

if (null === $namespace) {
if (!$this->commandLoader) {
return $this->commands;
}

$commands = $this->commands;
foreach ($this->commandLoader->getNames() as $name) {
if (!isset($commands[$name]) && $this->has($name)) {
$commands[$name] = $this->get($name);
}
}

return $commands;
}

$commands = [];
foreach ($this->commands as $name => $command) {
if ($namespace === $this->extractNamespace($name, substr_count($namespace, ':') + 1)) {
$commands[$name] = $command;
}
}

if ($this->commandLoader) {
foreach ($this->commandLoader->getNames() as $name) {
if (!isset($commands[$name]) && $namespace === $this->extractNamespace($name, substr_count($namespace, ':') + 1) && $this->has($name)) {
$commands[$name] = $this->get($name);
}
}
}

return $commands;
}

/**
@param
@return



*/
public static function getAbbreviations($names)
{
$abbrevs = [];
foreach ($names as $name) {
for ($len = \strlen($name); $len > 0; --$len) {
$abbrev = substr($name, 0, $len);
$abbrevs[$abbrev][] = $name;
}
}

return $abbrevs;
}




public function renderException(\Exception $e, OutputInterface $output)
{
$output->writeln('', OutputInterface::VERBOSITY_QUIET);

$this->doRenderException($e, $output);

if (null !== $this->runningCommand) {
$output->writeln(sprintf('<info>%s</info>', sprintf($this->runningCommand->getSynopsis(), $this->getName())), OutputInterface::VERBOSITY_QUIET);
$output->writeln('', OutputInterface::VERBOSITY_QUIET);
}
}

protected function doRenderException(\Exception $e, OutputInterface $output)
{
do {
$message = trim($e->getMessage());
if ('' === $message || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
$title = sprintf('  [%s%s]  ', \get_class($e), 0 !== ($code = $e->getCode()) ? ' ('.$code.')' : '');
$len = Helper::strlen($title);
} else {
$len = 0;
}

$width = $this->terminal->getWidth() ? $this->terminal->getWidth() - 1 : \PHP_INT_MAX;

 if (\defined('HHVM_VERSION') && $width > 1 << 31) {
$width = 1 << 31;
}
$lines = [];
foreach ('' !== $message ? preg_split('/\r?\n/', $message) : [] as $line) {
foreach ($this->splitStringByWidth($line, $width - 4) as $line) {

 $lineLength = Helper::strlen($line) + 4;
$lines[] = [$line, $lineLength];

$len = max($lineLength, $len);
}
}

$messages = [];
if (!$e instanceof ExceptionInterface || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
$messages[] = sprintf('<comment>%s</comment>', OutputFormatter::escape(sprintf('In %s line %s:', basename($e->getFile()) ?: 'n/a', $e->getLine() ?: 'n/a')));
}
$messages[] = $emptyLine = sprintf('<error>%s</error>', str_repeat(' ', $len));
if ('' === $message || OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
$messages[] = sprintf('<error>%s%s</error>', $title, str_repeat(' ', max(0, $len - Helper::strlen($title))));
}
foreach ($lines as $line) {
$messages[] = sprintf('<error>  %s  %s</error>', OutputFormatter::escape($line[0]), str_repeat(' ', $len - $line[1]));
}
$messages[] = $emptyLine;
$messages[] = '';

$output->writeln($messages, OutputInterface::VERBOSITY_QUIET);

if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
$output->writeln('<comment>Exception trace:</comment>', OutputInterface::VERBOSITY_QUIET);


 $trace = $e->getTrace();

array_unshift($trace, [
'function' => '',
'file' => $e->getFile() ?: 'n/a',
'line' => $e->getLine() ?: 'n/a',
'args' => [],
]);

for ($i = 0, $count = \count($trace); $i < $count; ++$i) {
$class = isset($trace[$i]['class']) ? $trace[$i]['class'] : '';
$type = isset($trace[$i]['type']) ? $trace[$i]['type'] : '';
$function = isset($trace[$i]['function']) ? $trace[$i]['function'] : '';
$file = isset($trace[$i]['file']) ? $trace[$i]['file'] : 'n/a';
$line = isset($trace[$i]['line']) ? $trace[$i]['line'] : 'n/a';

$output->writeln(sprintf(' %s%s at <info>%s:%s</info>', $class, $function ? $type.$function.'()' : '', $file, $line), OutputInterface::VERBOSITY_QUIET);
}

$output->writeln('', OutputInterface::VERBOSITY_QUIET);
}
} while ($e = $e->getPrevious());
}

/**
@return
@deprecated



*/
protected function getTerminalWidth()
{
@trigger_error(sprintf('The "%s()" method is deprecated as of 3.2 and will be removed in 4.0. Create a Terminal instance instead.', __METHOD__), \E_USER_DEPRECATED);

return $this->terminal->getWidth();
}

/**
@return
@deprecated



*/
protected function getTerminalHeight()
{
@trigger_error(sprintf('The "%s()" method is deprecated as of 3.2 and will be removed in 4.0. Create a Terminal instance instead.', __METHOD__), \E_USER_DEPRECATED);

return $this->terminal->getHeight();
}

/**
@return
@deprecated



*/
public function getTerminalDimensions()
{
@trigger_error(sprintf('The "%s()" method is deprecated as of 3.2 and will be removed in 4.0. Create a Terminal instance instead.', __METHOD__), \E_USER_DEPRECATED);

return [$this->terminal->getWidth(), $this->terminal->getHeight()];
}

/**
@param
@param
@return
@deprecated






*/
public function setTerminalDimensions($width, $height)
{
@trigger_error(sprintf('The "%s()" method is deprecated as of 3.2 and will be removed in 4.0. Set the COLUMNS and LINES env vars instead.', __METHOD__), \E_USER_DEPRECATED);

putenv('COLUMNS='.$width);
putenv('LINES='.$height);

return $this;
}




protected function configureIO(InputInterface $input, OutputInterface $output)
{
if (true === $input->hasParameterOption(['--ansi'], true)) {
$output->setDecorated(true);
} elseif (true === $input->hasParameterOption(['--no-ansi'], true)) {
$output->setDecorated(false);
}

if (true === $input->hasParameterOption(['--no-interaction', '-n'], true)) {
$input->setInteractive(false);
} elseif (\function_exists('posix_isatty')) {
$inputStream = null;

if ($input instanceof StreamableInputInterface) {
$inputStream = $input->getStream();
}


 
 if (!$inputStream && $this->getHelperSet()->has('question')) {
$inputStream = $this->getHelperSet()->get('question')->getInputStream(false);
}

if (!@posix_isatty($inputStream) && false === getenv('SHELL_INTERACTIVE')) {
$input->setInteractive(false);
}
}

switch ($shellVerbosity = (int) getenv('SHELL_VERBOSITY')) {
case -1: $output->setVerbosity(OutputInterface::VERBOSITY_QUIET); break;
case 1: $output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE); break;
case 2: $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE); break;
case 3: $output->setVerbosity(OutputInterface::VERBOSITY_DEBUG); break;
default: $shellVerbosity = 0; break;
}

if (true === $input->hasParameterOption(['--quiet', '-q'], true)) {
$output->setVerbosity(OutputInterface::VERBOSITY_QUIET);
$shellVerbosity = -1;
} else {
if ($input->hasParameterOption('-vvv', true) || $input->hasParameterOption('--verbose=3', true) || 3 === $input->getParameterOption('--verbose', false, true)) {
$output->setVerbosity(OutputInterface::VERBOSITY_DEBUG);
$shellVerbosity = 3;
} elseif ($input->hasParameterOption('-vv', true) || $input->hasParameterOption('--verbose=2', true) || 2 === $input->getParameterOption('--verbose', false, true)) {
$output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
$shellVerbosity = 2;
} elseif ($input->hasParameterOption('-v', true) || $input->hasParameterOption('--verbose=1', true) || $input->hasParameterOption('--verbose', true) || $input->getParameterOption('--verbose', false, true)) {
$output->setVerbosity(OutputInterface::VERBOSITY_VERBOSE);
$shellVerbosity = 1;
}
}

if (-1 === $shellVerbosity) {
$input->setInteractive(false);
}

putenv('SHELL_VERBOSITY='.$shellVerbosity);
$_ENV['SHELL_VERBOSITY'] = $shellVerbosity;
$_SERVER['SHELL_VERBOSITY'] = $shellVerbosity;
}

/**
@return





*/
protected function doRunCommand(Command $command, InputInterface $input, OutputInterface $output)
{
foreach ($command->getHelperSet() as $helper) {
if ($helper instanceof InputAwareInterface) {
$helper->setInput($input);
}
}

if (null === $this->dispatcher) {
return $command->run($input, $output);
}


 try {
$command->mergeApplicationDefinition();
$input->bind($command->getDefinition());
} catch (ExceptionInterface $e) {

 }

$event = new ConsoleCommandEvent($command, $input, $output);
$e = null;

try {
$this->dispatcher->dispatch(ConsoleEvents::COMMAND, $event);

if ($event->commandShouldRun()) {
$exitCode = $command->run($input, $output);
} else {
$exitCode = ConsoleCommandEvent::RETURN_CODE_DISABLED;
}
} catch (\Exception $e) {
} catch (\Throwable $e) {
}
if (null !== $e) {
if ($this->dispatcher->hasListeners(ConsoleEvents::EXCEPTION)) {
$x = $e instanceof \Exception ? $e : new FatalThrowableError($e);
$event = new ConsoleExceptionEvent($command, $input, $output, $x, $x->getCode());
$this->dispatcher->dispatch(ConsoleEvents::EXCEPTION, $event);

if ($x !== $event->getException()) {
$e = $event->getException();
}
}
$event = new ConsoleErrorEvent($input, $output, $e, $command);
$this->dispatcher->dispatch(ConsoleEvents::ERROR, $event);
$e = $event->getError();

if (0 === $exitCode = $event->getExitCode()) {
$e = null;
}
}

$event = new ConsoleTerminateEvent($command, $input, $output, $exitCode);
$this->dispatcher->dispatch(ConsoleEvents::TERMINATE, $event);

if (null !== $e) {
throw $e;
}

return $event->getExitCode();
}

/**
@return


*/
protected function getCommandName(InputInterface $input)
{
return $this->singleCommand ? $this->defaultCommand : $input->getFirstArgument();
}

/**
@return


*/
protected function getDefaultInputDefinition()
{
return new InputDefinition([
new InputArgument('command', InputArgument::REQUIRED, 'The command to execute'),

new InputOption('--help', '-h', InputOption::VALUE_NONE, 'Display this help message'),
new InputOption('--quiet', '-q', InputOption::VALUE_NONE, 'Do not output any message'),
new InputOption('--verbose', '-v|vv|vvv', InputOption::VALUE_NONE, 'Increase the verbosity of messages: 1 for normal output, 2 for more verbose output and 3 for debug'),
new InputOption('--version', '-V', InputOption::VALUE_NONE, 'Display this application version'),
new InputOption('--ansi', '', InputOption::VALUE_NONE, 'Force ANSI output'),
new InputOption('--no-ansi', '', InputOption::VALUE_NONE, 'Disable ANSI output'),
new InputOption('--no-interaction', '-n', InputOption::VALUE_NONE, 'Do not ask any interactive question'),
]);
}

/**
@return


*/
protected function getDefaultCommands()
{
return [new HelpCommand(), new ListCommand()];
}

/**
@return


*/
protected function getDefaultHelperSet()
{
return new HelperSet([
new FormatterHelper(),
new DebugFormatterHelper(),
new ProcessHelper(),
new QuestionHelper(),
]);
}

/**
@param
@return



*/
private function getAbbreviationSuggestions($abbrevs)
{
return '    '.implode("\n    ", $abbrevs);
}

/**
@param
@param
@return





*/
public function extractNamespace($name, $limit = null)
{
$parts = explode(':', $name, -1);

return implode(':', null === $limit ? $parts : \array_slice($parts, 0, $limit));
}

/**
@param
@param
@return




*/
private function findAlternatives($name, $collection)
{
$threshold = 1e3;
$alternatives = [];

$collectionParts = [];
foreach ($collection as $item) {
$collectionParts[$item] = explode(':', $item);
}

foreach (explode(':', $name) as $i => $subname) {
foreach ($collectionParts as $collectionName => $parts) {
$exists = isset($alternatives[$collectionName]);
if (!isset($parts[$i]) && $exists) {
$alternatives[$collectionName] += $threshold;
continue;
} elseif (!isset($parts[$i])) {
continue;
}

$lev = levenshtein($subname, $parts[$i]);
if ($lev <= \strlen($subname) / 3 || '' !== $subname && false !== strpos($parts[$i], $subname)) {
$alternatives[$collectionName] = $exists ? $alternatives[$collectionName] + $lev : $lev;
} elseif ($exists) {
$alternatives[$collectionName] += $threshold;
}
}
}

foreach ($collection as $item) {
$lev = levenshtein($name, $item);
if ($lev <= \strlen($name) / 3 || false !== strpos($item, $name)) {
$alternatives[$item] = isset($alternatives[$item]) ? $alternatives[$item] - $lev : $lev;
}
}

$alternatives = array_filter($alternatives, function ($lev) use ($threshold) { return $lev < 2 * $threshold; });
ksort($alternatives, \SORT_NATURAL | \SORT_FLAG_CASE);

return array_keys($alternatives);
}

/**
@param
@param
@return



*/
public function setDefaultCommand($commandName, $isSingleCommand = false)
{
$this->defaultCommand = $commandName;

if ($isSingleCommand) {

 $this->find($commandName);

$this->singleCommand = true;
}

return $this;
}

/**
@internal
*/
public function isSingleCommand()
{
return $this->singleCommand;
}

private function splitStringByWidth($string, $width)
{

 
 
 if (false === $encoding = mb_detect_encoding($string, null, true)) {
return str_split($string, $width);
}

$utf8String = mb_convert_encoding($string, 'utf8', $encoding);
$lines = [];
$line = '';
foreach (preg_split('//u', $utf8String) as $char) {

 if (mb_strwidth($line.$char, 'utf8') <= $width) {
$line .= $char;
continue;
}

 $lines[] = str_pad($line, $width);
$line = $char;
}

$lines[] = \count($lines) ? str_pad($line, $width) : $line;

mb_convert_variables($encoding, 'utf8', $lines);

return $lines;
}

/**
@param
@return



*/
private function extractAllNamespaces($name)
{

 $parts = explode(':', $name, -1);
$namespaces = [];

foreach ($parts as $part) {
if (\count($namespaces)) {
$namespaces[] = end($namespaces).':'.$part;
} else {
$namespaces[] = $part;
}
}

return $namespaces;
}

private function init()
{
if ($this->initialized) {
return;
}
$this->initialized = true;

foreach ($this->getDefaultCommands() as $command) {
$this->add($command);
}
}
}
