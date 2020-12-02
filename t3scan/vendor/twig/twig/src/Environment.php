<?php










namespace Twig;

use Twig\Cache\CacheInterface;
use Twig\Cache\FilesystemCache;
use Twig\Cache\NullCache;
use Twig\Error\Error;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;
use Twig\Extension\CoreExtension;
use Twig\Extension\EscaperExtension;
use Twig\Extension\ExtensionInterface;
use Twig\Extension\OptimizerExtension;
use Twig\Loader\ArrayLoader;
use Twig\Loader\ChainLoader;
use Twig\Loader\LoaderInterface;
use Twig\Node\ModuleNode;
use Twig\Node\Node;
use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\RuntimeLoader\RuntimeLoaderInterface;
use Twig\TokenParser\TokenParserInterface;

/**
@author


*/
class Environment
{
const VERSION = '2.13.1';
const VERSION_ID = 21301;
const MAJOR_VERSION = 2;
const MINOR_VERSION = 13;
const RELEASE_VERSION = 1;
const EXTRA_VERSION = '';

private $charset;
private $loader;
private $debug;
private $autoReload;
private $cache;
private $lexer;
private $parser;
private $compiler;
private $baseTemplateClass;
private $globals = [];
private $resolvedGlobals;
private $loadedTemplates;
private $strictVariables;
private $templateClassPrefix = '__TwigTemplate_';
private $originalCache;
private $extensionSet;
private $runtimeLoaders = [];
private $runtimes = [];
private $optionsHash;



































public function __construct(LoaderInterface $loader, $options = [])
{
$this->setLoader($loader);

$options = array_merge([
'debug' => false,
'charset' => 'UTF-8',
'base_template_class' => Template::class,
'strict_variables' => false,
'autoescape' => 'html',
'cache' => false,
'auto_reload' => null,
'optimizations' => -1,
], $options);

$this->debug = (bool) $options['debug'];
$this->setCharset($options['charset']);
$this->baseTemplateClass = '\\'.ltrim($options['base_template_class'], '\\');
if ('\\'.Template::class !== $this->baseTemplateClass && '\Twig_Template' !== $this->baseTemplateClass) {
@trigger_error('The "base_template_class" option on '.__CLASS__.' is deprecated since Twig 2.7.0.', E_USER_DEPRECATED);
}
$this->autoReload = null === $options['auto_reload'] ? $this->debug : (bool) $options['auto_reload'];
$this->strictVariables = (bool) $options['strict_variables'];
$this->setCache($options['cache']);
$this->extensionSet = new ExtensionSet();

$this->addExtension(new CoreExtension());
$this->addExtension(new EscaperExtension($options['autoescape']));
$this->addExtension(new OptimizerExtension($options['optimizations']));
}

/**
@return


*/
public function getBaseTemplateClass()
{
if (1 > \func_num_args() || \func_get_arg(0)) {
@trigger_error('The '.__METHOD__.' is deprecated since Twig 2.7.0.', E_USER_DEPRECATED);
}

return $this->baseTemplateClass;
}

/**
@param


*/
public function setBaseTemplateClass($class)
{
@trigger_error('The '.__METHOD__.' is deprecated since Twig 2.7.0.', E_USER_DEPRECATED);

$this->baseTemplateClass = $class;
$this->updateOptionsHash();
}




public function enableDebug()
{
$this->debug = true;
$this->updateOptionsHash();
}




public function disableDebug()
{
$this->debug = false;
$this->updateOptionsHash();
}

/**
@return


*/
public function isDebug()
{
return $this->debug;
}




public function enableAutoReload()
{
$this->autoReload = true;
}




public function disableAutoReload()
{
$this->autoReload = false;
}

/**
@return


*/
public function isAutoReload()
{
return $this->autoReload;
}




public function enableStrictVariables()
{
$this->strictVariables = true;
$this->updateOptionsHash();
}




public function disableStrictVariables()
{
$this->strictVariables = false;
$this->updateOptionsHash();
}

/**
@return


*/
public function isStrictVariables()
{
return $this->strictVariables;
}

/**
@param
@return





*/
public function getCache($original = true)
{
return $original ? $this->originalCache : $this->cache;
}

/**
@param




*/
public function setCache($cache)
{
if (\is_string($cache)) {
$this->originalCache = $cache;
$this->cache = new FilesystemCache($cache);
} elseif (false === $cache) {
$this->originalCache = $cache;
$this->cache = new NullCache();
} elseif ($cache instanceof CacheInterface) {
$this->originalCache = $this->cache = $cache;
} else {
throw new \LogicException(sprintf('Cache can only be a string, false, or a \Twig\Cache\CacheInterface implementation.'));
}
}

/**
@param
@param
@return
@internal













*/
public function getTemplateClass($name, $index = null)
{
$key = $this->getLoader()->getCacheKey($name).$this->optionsHash;

return $this->templateClassPrefix.hash('sha256', $key).(null === $index ? '' : '___'.$index);
}

/**
@param
@param
@return
@throws
@throws
@throws




*/
public function render($name, array $context = [])
{
return $this->load($name)->render($context);
}

/**
@param
@param
@throws
@throws
@throws



*/
public function display($name, array $context = [])
{
$this->load($name)->display($context);
}

/**
@param
@throws
@throws
@throws
@return




*/
public function load($name)
{
if ($name instanceof TemplateWrapper) {
return $name;
}

if ($name instanceof Template) {
@trigger_error('Passing a \Twig\Template instance to '.__METHOD__.' is deprecated since Twig 2.7.0, use \Twig\TemplateWrapper instead.', E_USER_DEPRECATED);

return new TemplateWrapper($this, $name);
}

return new TemplateWrapper($this, $this->loadTemplate($name));
}

/**
@param
@param
@return
@throws
@throws
@throws
@internal








*/
public function loadTemplate($name, $index = null)
{
return $this->loadClass($this->getTemplateClass($name), $name, $index);
}

/**
@internal
*/
public function loadClass($cls, $name, $index = null)
{
$mainCls = $cls;
if (null !== $index) {
$cls .= '___'.$index;
}

if (isset($this->loadedTemplates[$cls])) {
return $this->loadedTemplates[$cls];
}

if (!class_exists($cls, false)) {
$key = $this->cache->generateKey($name, $mainCls);

if (!$this->isAutoReload() || $this->isTemplateFresh($name, $this->cache->getTimestamp($key))) {
$this->cache->load($key);
}

$source = null;
if (!class_exists($cls, false)) {
$source = $this->getLoader()->getSourceContext($name);
$content = $this->compileSource($source);
$this->cache->write($key, $content);
$this->cache->load($key);

if (!class_exists($mainCls, false)) {





eval('?>'.$content);
}

if (!class_exists($cls, false)) {
throw new RuntimeError(sprintf('Failed to load Twig template "%s", index "%s": cache might be corrupted.', $name, $index), -1, $source);
}
}
}


 $this->extensionSet->initRuntime($this);

return $this->loadedTemplates[$cls] = new $cls($this);
}

/**
@param
@param
@return
@throws
@throws






*/
public function createTemplate($template, string $name = null)
{
$hash = hash('sha256', $template, false);
if (null !== $name) {
$name = sprintf('%s (string template %s)', $name, $hash);
} else {
$name = sprintf('__string_template__%s', $hash);
}

$loader = new ChainLoader([
new ArrayLoader([$name => $template]),
$current = $this->getLoader(),
]);

$this->setLoader($loader);
try {
return new TemplateWrapper($this, $this->loadTemplate($name));
} finally {
$this->setLoader($current);
}
}

/**
@param
@param
@return







*/
public function isTemplateFresh($name, $time)
{
return $this->extensionSet->getLastModified() <= $time && $this->getLoader()->isFresh($name, $time);
}

/**
@param
@return
@throws
@throws







*/
public function resolveTemplate($names)
{
if (!\is_array($names)) {
$names = [$names];
}

foreach ($names as $name) {
if ($name instanceof Template) {
return $name;
}
if ($name instanceof TemplateWrapper) {
return $name;
}

try {
return $this->loadTemplate($name);
} catch (LoaderError $e) {
if (1 === \count($names)) {
throw $e;
}
}
}

throw new LoaderError(sprintf('Unable to find one of the following templates: "%s".', implode('", "', $names)));
}

public function setLexer(Lexer $lexer)
{
$this->lexer = $lexer;
}

/**
@return
@throws



*/
public function tokenize(Source $source)
{
if (null === $this->lexer) {
$this->lexer = new Lexer($this);
}

return $this->lexer->tokenize($source);
}

public function setParser(Parser $parser)
{
$this->parser = $parser;
}

/**
@return
@throws



*/
public function parse(TokenStream $stream)
{
if (null === $this->parser) {
$this->parser = new Parser($this);
}

return $this->parser->parse($stream);
}

public function setCompiler(Compiler $compiler)
{
$this->compiler = $compiler;
}

/**
@return


*/
public function compile(Node $node)
{
if (null === $this->compiler) {
$this->compiler = new Compiler($this);
}

return $this->compiler->compile($node)->getSource();
}

/**
@return
@throws



*/
public function compileSource(Source $source)
{
try {
return $this->compile($this->parse($this->tokenize($source)));
} catch (Error $e) {
$e->setSourceContext($source);
throw $e;
} catch (\Exception $e) {
throw new SyntaxError(sprintf('An exception has been thrown during the compilation of a template ("%s").', $e->getMessage()), -1, $source, $e);
}
}

public function setLoader(LoaderInterface $loader)
{
$this->loader = $loader;
}

/**
@return


*/
public function getLoader()
{
return $this->loader;
}

/**
@param


*/
public function setCharset($charset)
{
if ('UTF8' === $charset = strtoupper($charset)) {

 $charset = 'UTF-8';
}

$this->charset = $charset;
}

/**
@return


*/
public function getCharset()
{
return $this->charset;
}

/**
@param
@return



*/
public function hasExtension($class)
{
return $this->extensionSet->hasExtension($class);
}




public function addRuntimeLoader(RuntimeLoaderInterface $loader)
{
$this->runtimeLoaders[] = $loader;
}

/**
@param
@return



*/
public function getExtension($class)
{
return $this->extensionSet->getExtension($class);
}

/**
@param
@return
@throws




*/
public function getRuntime($class)
{
if (isset($this->runtimes[$class])) {
return $this->runtimes[$class];
}

foreach ($this->runtimeLoaders as $loader) {
if (null !== $runtime = $loader->load($class)) {
return $this->runtimes[$class] = $runtime;
}
}

throw new RuntimeError(sprintf('Unable to load the "%s" runtime.', $class));
}

public function addExtension(ExtensionInterface $extension)
{
$this->extensionSet->addExtension($extension);
$this->updateOptionsHash();
}

/**
@param


*/
public function setExtensions(array $extensions)
{
$this->extensionSet->setExtensions($extensions);
$this->updateOptionsHash();
}

/**
@return


*/
public function getExtensions()
{
return $this->extensionSet->getExtensions();
}

public function addTokenParser(TokenParserInterface $parser)
{
$this->extensionSet->addTokenParser($parser);
}

/**
@return
@internal



*/
public function getTokenParsers()
{
return $this->extensionSet->getTokenParsers();
}

/**
@return
@internal



*/
public function getTags()
{
$tags = [];
foreach ($this->getTokenParsers() as $parser) {
$tags[$parser->getTag()] = $parser;
}

return $tags;
}

public function addNodeVisitor(NodeVisitorInterface $visitor)
{
$this->extensionSet->addNodeVisitor($visitor);
}

/**
@return
@internal



*/
public function getNodeVisitors()
{
return $this->extensionSet->getNodeVisitors();
}

public function addFilter(TwigFilter $filter)
{
$this->extensionSet->addFilter($filter);
}

/**
@param
@return
@internal







*/
public function getFilter($name)
{
return $this->extensionSet->getFilter($name);
}

public function registerUndefinedFilterCallback(callable $callable)
{
$this->extensionSet->registerUndefinedFilterCallback($callable);
}

/**
@return
@see
@internal






*/
public function getFilters()
{
return $this->extensionSet->getFilters();
}

public function addTest(TwigTest $test)
{
$this->extensionSet->addTest($test);
}

/**
@return
@internal



*/
public function getTests()
{
return $this->extensionSet->getTests();
}

/**
@param
@return
@internal




*/
public function getTest($name)
{
return $this->extensionSet->getTest($name);
}

public function addFunction(TwigFunction $function)
{
$this->extensionSet->addFunction($function);
}

/**
@param
@return
@internal







*/
public function getFunction($name)
{
return $this->extensionSet->getFunction($name);
}

public function registerUndefinedFunctionCallback(callable $callable)
{
$this->extensionSet->registerUndefinedFunctionCallback($callable);
}

/**
@return
@see
@internal






*/
public function getFunctions()
{
return $this->extensionSet->getFunctions();
}

/**
@param
@param





*/
public function addGlobal($name, $value)
{
if ($this->extensionSet->isInitialized() && !\array_key_exists($name, $this->getGlobals())) {
throw new \LogicException(sprintf('Unable to add global "%s" as the runtime or the extensions have already been initialized.', $name));
}

if (null !== $this->resolvedGlobals) {
$this->resolvedGlobals[$name] = $value;
} else {
$this->globals[$name] = $value;
}
}

/**
@return
@internal



*/
public function getGlobals()
{
if ($this->extensionSet->isInitialized()) {
if (null === $this->resolvedGlobals) {
$this->resolvedGlobals = array_merge($this->extensionSet->getGlobals(), $this->globals);
}

return $this->resolvedGlobals;
}

return array_merge($this->extensionSet->getGlobals(), $this->globals);
}

/**
@param
@return



*/
public function mergeGlobals(array $context)
{

 
 foreach ($this->getGlobals() as $key => $value) {
if (!\array_key_exists($key, $context)) {
$context[$key] = $value;
}
}

return $context;
}

/**
@return
@internal



*/
public function getUnaryOperators()
{
return $this->extensionSet->getUnaryOperators();
}

/**
@return
@internal



*/
public function getBinaryOperators()
{
return $this->extensionSet->getBinaryOperators();
}

private function updateOptionsHash()
{
$this->optionsHash = implode(':', [
$this->extensionSet->getSignature(),
PHP_MAJOR_VERSION,
PHP_MINOR_VERSION,
self::VERSION,
(int) $this->debug,
$this->baseTemplateClass,
(int) $this->strictVariables,
]);
}
}

class_alias('Twig\Environment', 'Twig_Environment');
