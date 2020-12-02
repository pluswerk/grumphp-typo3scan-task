<?php











namespace Twig;

use Twig\Node\Node;

/**
@author


*/
class Compiler
{
private $lastLine;
private $source;
private $indentation;
private $env;
private $debugInfo = [];
private $sourceOffset;
private $sourceLine;
private $varNameSalt = 0;

public function __construct(Environment $env)
{
$this->env = $env;
}

/**
@return


*/
public function getEnvironment()
{
return $this->env;
}

/**
@return


*/
public function getSource()
{
return $this->source;
}

/**
@param
@return



*/
public function compile(Node $node, $indentation = 0)
{
$this->lastLine = null;
$this->source = '';
$this->debugInfo = [];
$this->sourceOffset = 0;

 $this->sourceLine = 1;
$this->indentation = $indentation;
$this->varNameSalt = 0;

$node->compile($this);

return $this;
}

public function subcompile(Node $node, $raw = true)
{
if (false === $raw) {
$this->source .= str_repeat(' ', $this->indentation * 4);
}

$node->compile($this);

return $this;
}

/**
@param
@return



*/
public function raw($string)
{
$this->source .= $string;

return $this;
}

/**
@return


*/
public function write(...$strings)
{
foreach ($strings as $string) {
$this->source .= str_repeat(' ', $this->indentation * 4).$string;
}

return $this;
}

/**
@param
@return



*/
public function string($value)
{
$this->source .= sprintf('"%s"', addcslashes($value, "\0\t\"\$\\"));

return $this;
}

/**
@param
@return



*/
public function repr($value)
{
if (\is_int($value) || \is_float($value)) {
if (false !== $locale = setlocale(LC_NUMERIC, '0')) {
setlocale(LC_NUMERIC, 'C');
}

$this->raw(var_export($value, true));

if (false !== $locale) {
setlocale(LC_NUMERIC, $locale);
}
} elseif (null === $value) {
$this->raw('null');
} elseif (\is_bool($value)) {
$this->raw($value ? 'true' : 'false');
} elseif (\is_array($value)) {
$this->raw('array(');
$first = true;
foreach ($value as $key => $v) {
if (!$first) {
$this->raw(', ');
}
$first = false;
$this->repr($key);
$this->raw(' => ');
$this->repr($v);
}
$this->raw(')');
} else {
$this->string($value);
}

return $this;
}

/**
@return


*/
public function addDebugInfo(Node $node)
{
if ($node->getTemplateLine() != $this->lastLine) {
$this->write(sprintf("// line %d\n", $node->getTemplateLine()));

$this->sourceLine += substr_count($this->source, "\n", $this->sourceOffset);
$this->sourceOffset = \strlen($this->source);
$this->debugInfo[$this->sourceLine] = $node->getTemplateLine();

$this->lastLine = $node->getTemplateLine();
}

return $this;
}

public function getDebugInfo()
{
ksort($this->debugInfo);

return $this->debugInfo;
}

/**
@param
@return



*/
public function indent($step = 1)
{
$this->indentation += $step;

return $this;
}

/**
@param
@return
@throws




*/
public function outdent($step = 1)
{

 if ($this->indentation < $step) {
throw new \LogicException('Unable to call outdent() as the indentation would become negative.');
}

$this->indentation -= $step;

return $this;
}

public function getVarName()
{
return sprintf('__internal_%s', hash('sha256', __METHOD__.$this->varNameSalt++));
}
}

class_alias('Twig\Compiler', 'Twig_Compiler');
