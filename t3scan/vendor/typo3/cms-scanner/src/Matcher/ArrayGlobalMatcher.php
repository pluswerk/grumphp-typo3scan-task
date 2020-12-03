<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Scalar\String_;

/**
@internal


*/
class ArrayGlobalMatcher extends AbstractCoreMatcher
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
&& $node instanceof ArrayDimFetch
&& $node->var instanceof Variable
&& $node->var->name === 'GLOBALS'
&& $node->dim instanceof String_
&& array_key_exists('$GLOBALS[\'' . $node->dim->value . '\']', $this->matcherDefinitions)
) {
$this->matches[] = [
'restFiles' => $this->matcherDefinitions['$GLOBALS[\'' . $node->dim->value . '\']']['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->dim->value,
'message' => 'Access to global array "' . $node->dim->value . '"',
'indicator' => static::INDICATOR_STRONG,
];
}
}
}
