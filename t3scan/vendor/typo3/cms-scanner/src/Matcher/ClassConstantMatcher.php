<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\ClassConstFetch;
use PhpParser\Node\Name\FullyQualified;






class ClassConstantMatcher extends AbstractCoreMatcher
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitions();
}

/**
@param


*/
public function enterNode(Node $node)
{
if (!$this->isFileIgnored($node)
&& !$this->isLineIgnored($node)
&& $node instanceof ClassConstFetch
&& $node->class instanceof FullyQualified
&& in_array($node->class->toString() . '::' . $node->name, array_keys($this->matcherDefinitions), true)
) {

 $this->matches[] = [
'restFiles' => $this->matcherDefinitions[$node->class->toString() . '::' . $node->name]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->class->toString() . '::' . $node->name,
'message' => 'Call to class constant "' . $node->class->toString() . '::' . $node->name . '"',
'indicator' => static::INDICATOR_STRONG,
];
}
}
}
