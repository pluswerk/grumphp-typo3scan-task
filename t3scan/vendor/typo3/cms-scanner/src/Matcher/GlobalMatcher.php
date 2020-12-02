<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\Variable;





class GlobalMatcher extends AbstractCoreMatcher
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
&& $node instanceof Node\Expr\MethodCall
&& $node->var instanceof Variable
&& $node->var->name !== 'GLOBALS'
&& in_array(
'$' . $node->var->name,
array_keys($this->matcherDefinitions),
true
)
) {
$this->matches[] = [
'restFiles' => $this->matcherDefinitions['$' . $node->var->name]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->var->name,
'message' => 'Usage of global "' . $node->var->name . '"',
'indicator' => static::INDICATOR_STRONG,
];
}
}
}
