<?php










namespace Twig\Profiler;

/**
@author
@final

*/
class Profile implements \IteratorAggregate, \Serializable
{
const ROOT = 'ROOT';
const BLOCK = 'block';
const TEMPLATE = 'template';
const MACRO = 'macro';

private $template;
private $name;
private $type;
private $starts = [];
private $ends = [];
private $profiles = [];

public function __construct(string $template = 'main', string $type = self::ROOT, string $name = 'main')
{
if (__CLASS__ !== static::class) {
@trigger_error('Overriding '.__CLASS__.' is deprecated since Twig 2.4.0 and the class will be final in 3.0.', E_USER_DEPRECATED);
}

$this->template = $template;
$this->type = $type;
$this->name = 0 === strpos($name, '__internal_') ? 'INTERNAL' : $name;
$this->enter();
}

public function getTemplate()
{
return $this->template;
}

public function getType()
{
return $this->type;
}

public function getName()
{
return $this->name;
}

public function isRoot()
{
return self::ROOT === $this->type;
}

public function isTemplate()
{
return self::TEMPLATE === $this->type;
}

public function isBlock()
{
return self::BLOCK === $this->type;
}

public function isMacro()
{
return self::MACRO === $this->type;
}

public function getProfiles()
{
return $this->profiles;
}

public function addProfile(self $profile)
{
$this->profiles[] = $profile;
}

/**
@return


*/
public function getDuration()
{
if ($this->isRoot() && $this->profiles) {

 $duration = 0;
foreach ($this->profiles as $profile) {
$duration += $profile->getDuration();
}

return $duration;
}

return isset($this->ends['wt']) && isset($this->starts['wt']) ? $this->ends['wt'] - $this->starts['wt'] : 0;
}

/**
@return


*/
public function getMemoryUsage()
{
return isset($this->ends['mu']) && isset($this->starts['mu']) ? $this->ends['mu'] - $this->starts['mu'] : 0;
}

/**
@return


*/
public function getPeakMemoryUsage()
{
return isset($this->ends['pmu']) && isset($this->starts['pmu']) ? $this->ends['pmu'] - $this->starts['pmu'] : 0;
}




public function enter()
{
$this->starts = [
'wt' => microtime(true),
'mu' => memory_get_usage(),
'pmu' => memory_get_peak_usage(),
];
}




public function leave()
{
$this->ends = [
'wt' => microtime(true),
'mu' => memory_get_usage(),
'pmu' => memory_get_peak_usage(),
];
}

public function reset()
{
$this->starts = $this->ends = $this->profiles = [];
$this->enter();
}

public function getIterator()
{
return new \ArrayIterator($this->profiles);
}

public function serialize()
{
return serialize($this->__serialize());
}

public function unserialize($data)
{
$this->__unserialize(unserialize($data));
}

/**
@internal
*/
public function __serialize()
{
return [$this->template, $this->name, $this->type, $this->starts, $this->ends, $this->profiles];
}

/**
@internal
*/
public function __unserialize(array $data)
{
list($this->template, $this->name, $this->type, $this->starts, $this->ends, $this->profiles) = $data;
}
}

class_alias('Twig\Profiler\Profile', 'Twig_Profiler_Profile');
