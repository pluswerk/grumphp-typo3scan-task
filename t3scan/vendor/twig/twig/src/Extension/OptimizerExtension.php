<?php










namespace Twig\Extension;

use Twig\NodeVisitor\OptimizerNodeVisitor;

final class OptimizerExtension extends AbstractExtension
{
private $optimizers;

public function __construct($optimizers = -1)
{
$this->optimizers = $optimizers;
}

public function getNodeVisitors()
{
return [new OptimizerNodeVisitor($this->optimizers)];
}
}

class_alias('Twig\Extension\OptimizerExtension', 'Twig_Extension_Optimizer');
