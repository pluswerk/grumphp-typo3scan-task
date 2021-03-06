<?php










namespace Symfony\Component\Console\Command;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Exception\ExceptionInterface;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;
use Symfony\Component\Console\Helper\HelperSet;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputDefinition;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

/**
@author


*/
class Command
{
/**
@var
*/
protected static $defaultName;

private $application;
private $name;
private $processTitle;
private $aliases = [];
private $definition;
private $hidden = false;
private $help = '';
private $description = '';
private $ignoreValidationErrors = false;
private $applicationDefinitionMerged = false;
private $applicationDefinitionMergedWithArgs = false;
private $code;
private $synopsis = [];
private $usages = [];
private $helperSet;

/**
@return
*/
public static function getDefaultName()
{
$class = static::class;
$r = new \ReflectionProperty($class, 'defaultName');

return $class === $r->class ? static::$defaultName : null;
}

/**
@param
@throws

*/
public function __construct($name = null)
{
$this->definition = new InputDefinition();

if (null !== $name || null !== $name = static::getDefaultName()) {
$this->setName($name);
}

$this->configure();
}






public function ignoreValidationErrors()
{
$this->ignoreValidationErrors = true;
}

public function setApplication(Application $application = null)
{
$this->application = $application;
if ($application) {
$this->setHelperSet($application->getHelperSet());
} else {
$this->helperSet = null;
}
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
return $this->helperSet;
}

/**
@return


*/
public function getApplication()
{
return $this->application;
}

/**
@return





*/
public function isEnabled()
{
return true;
}




protected function configure()
{
}

/**
@return
@throws
@see









*/
protected function execute(InputInterface $input, OutputInterface $output)
{
throw new LogicException('You must override the execute() method in the concrete command class.');
}








protected function interact(InputInterface $input, OutputInterface $output)
{
}

/**
@see
@see






*/
protected function initialize(InputInterface $input, OutputInterface $output)
{
}

/**
@return
@throws
@see
@see








*/
public function run(InputInterface $input, OutputInterface $output)
{

 $this->getSynopsis(true);
$this->getSynopsis(false);


 $this->mergeApplicationDefinition();


 try {
$input->bind($this->definition);
} catch (ExceptionInterface $e) {
if (!$this->ignoreValidationErrors) {
throw $e;
}
}

$this->initialize($input, $output);

if (null !== $this->processTitle) {
if (\function_exists('cli_set_process_title')) {
if (!@cli_set_process_title($this->processTitle)) {
if ('Darwin' === \PHP_OS) {
$output->writeln('<comment>Running "cli_set_process_title" as an unprivileged user is not supported on MacOS.</comment>', OutputInterface::VERBOSITY_VERY_VERBOSE);
} else {
cli_set_process_title($this->processTitle);
}
}
} elseif (\function_exists('setproctitle')) {
setproctitle($this->processTitle);
} elseif (OutputInterface::VERBOSITY_VERY_VERBOSE === $output->getVerbosity()) {
$output->writeln('<comment>Install the proctitle PECL to be able to change the process title.</comment>');
}
}

if ($input->isInteractive()) {
$this->interact($input, $output);
}


 
 
 if ($input->hasArgument('command') && null === $input->getArgument('command')) {
$input->setArgument('command', $this->getName());
}

$input->validate();

if ($this->code) {
$statusCode = \call_user_func($this->code, $input, $output);
} else {
$statusCode = $this->execute($input, $output);
}

return is_numeric($statusCode) ? (int) $statusCode : 0;
}

/**
@param
@return
@throws
@see








*/
public function setCode(callable $code)
{
if ($code instanceof \Closure) {
$r = new \ReflectionFunction($code);
if (null === $r->getClosureThis()) {
if (\PHP_VERSION_ID < 70000) {

 
 
 
 $code = @\Closure::bind($code, $this);
} else {
$code = \Closure::bind($code, $this);
}
}
}

$this->code = $code;

return $this;
}

/**
@param




*/
public function mergeApplicationDefinition($mergeArgs = true)
{
if (null === $this->application || (true === $this->applicationDefinitionMerged && ($this->applicationDefinitionMergedWithArgs || !$mergeArgs))) {
return;
}

$this->definition->addOptions($this->application->getDefinition()->getOptions());

$this->applicationDefinitionMerged = true;

if ($mergeArgs) {
$currentArguments = $this->definition->getArguments();
$this->definition->setArguments($this->application->getDefinition()->getArguments());
$this->definition->addArguments($currentArguments);

$this->applicationDefinitionMergedWithArgs = true;
}
}

/**
@param
@return



*/
public function setDefinition($definition)
{
if ($definition instanceof InputDefinition) {
$this->definition = $definition;
} else {
$this->definition->setDefinition($definition);
}

$this->applicationDefinitionMerged = false;

return $this;
}

/**
@return


*/
public function getDefinition()
{
if (null === $this->definition) {
throw new LogicException(sprintf('Command class "%s" is not correctly initialized. You probably forgot to call the parent constructor.', static::class));
}

return $this->definition;
}

/**
@return







*/
public function getNativeDefinition()
{
return $this->getDefinition();
}

/**
@param
@param
@param
@param
@throws
@return




*/
public function addArgument($name, $mode = null, $description = '', $default = null)
{
$this->definition->addArgument(new InputArgument($name, $mode, $description, $default));

return $this;
}

/**
@param
@param
@param
@param
@param
@throws
@return




*/
public function addOption($name, $shortcut = null, $mode = null, $description = '', $default = null)
{
$this->definition->addOption(new InputOption($name, $shortcut, $mode, $description, $default));

return $this;
}

/**
@param
@return
@throws









*/
public function setName($name)
{
$this->validateName($name);

$this->name = $name;

return $this;
}

/**
@param
@return






*/
public function setProcessTitle($title)
{
$this->processTitle = $title;

return $this;
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
@return

*/
public function setHidden($hidden)
{
$this->hidden = (bool) $hidden;

return $this;
}

/**
@return
*/
public function isHidden()
{
return $this->hidden;
}

/**
@param
@return



*/
public function setDescription($description)
{
$this->description = $description;

return $this;
}

/**
@return


*/
public function getDescription()
{
return $this->description;
}

/**
@param
@return



*/
public function setHelp($help)
{
$this->help = $help;

return $this;
}

/**
@return


*/
public function getHelp()
{
return $this->help;
}

/**
@return



*/
public function getProcessedHelp()
{
$name = $this->name;
$isSingleCommand = $this->application && $this->application->isSingleCommand();

$placeholders = [
'%command.name%',
'%command.full_name%',
];
$replacements = [
$name,
$isSingleCommand ? $_SERVER['PHP_SELF'] : $_SERVER['PHP_SELF'].' '.$name,
];

return str_replace($placeholders, $replacements, $this->getHelp() ?: $this->getDescription());
}

/**
@param
@return
@throws




*/
public function setAliases($aliases)
{
if (!\is_array($aliases) && !$aliases instanceof \Traversable) {
throw new InvalidArgumentException('$aliases must be an array or an instance of \Traversable.');
}

foreach ($aliases as $alias) {
$this->validateName($alias);
}

$this->aliases = $aliases;

return $this;
}

/**
@return


*/
public function getAliases()
{
return $this->aliases;
}

/**
@param
@return



*/
public function getSynopsis($short = false)
{
$key = $short ? 'short' : 'long';

if (!isset($this->synopsis[$key])) {
$this->synopsis[$key] = trim(sprintf('%s %s', $this->name, $this->definition->getSynopsis($short)));
}

return $this->synopsis[$key];
}

/**
@param
@return



*/
public function addUsage($usage)
{
if (0 !== strpos($usage, $this->name)) {
$usage = sprintf('%s %s', $this->name, $usage);
}

$this->usages[] = $usage;

return $this;
}

/**
@return


*/
public function getUsages()
{
return $this->usages;
}

/**
@param
@return
@throws
@throws




*/
public function getHelper($name)
{
if (null === $this->helperSet) {
throw new LogicException(sprintf('Cannot retrieve helper "%s" because there is no HelperSet defined. Did you forget to add your command to the application or to set the application on the command using the setApplication() method? You can also set the HelperSet directly using the setHelperSet() method.', $name));
}

return $this->helperSet->get($name);
}

/**
@param
@throws





*/
private function validateName($name)
{
if (!preg_match('/^[^\:]++(\:[^\:]++)*$/', $name)) {
throw new InvalidArgumentException(sprintf('Command name "%s" is invalid.', $name));
}
}
}
