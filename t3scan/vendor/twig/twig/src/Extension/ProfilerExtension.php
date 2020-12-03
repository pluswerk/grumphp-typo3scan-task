<?php










namespace Twig\Extension;

use Twig\Profiler\NodeVisitor\ProfilerNodeVisitor;
use Twig\Profiler\Profile;

class ProfilerExtension extends AbstractExtension
{
private $actives = [];

public function __construct(Profile $profile)
{
$this->actives[] = $profile;
}

public function enter(Profile $profile)
{
$this->actives[0]->addProfile($profile);
array_unshift($this->actives, $profile);
}

public function leave(Profile $profile)
{
$profile->leave();
array_shift($this->actives);

if (1 === \count($this->actives)) {
$this->actives[0]->leave();
}
}

public function getNodeVisitors()
{
return [new ProfilerNodeVisitor(static::class)];
}
}

class_alias('Twig\Extension\ProfilerExtension', 'Twig_Extension_Profiler');
