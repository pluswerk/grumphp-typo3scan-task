<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Visitor;














use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Name\FullyQualified;
use PhpParser\Node\Scalar\String_;
use PhpParser\NodeVisitorAbstract;









class GeneratorClassesResolver extends NodeVisitorAbstract
{
/**
@param



*/
public function enterNode(Node $node)
{
if ($node instanceof StaticCall
&& $node->class instanceof FullyQualified
&& $node->class->toString() === 'TYPO3\CMS\Core\Utility\GeneralUtility'
&& $node->name->name === 'makeInstance'
&& isset($node->args[0]->value)
&& $node->args[0]->value instanceof String_
) {
$newSubNode = new FullyQualified($node->args[0]->value->value, $node->args[0]->value->getAttributes());
$node->args[0]->value = $newSubNode;
}
}
}
