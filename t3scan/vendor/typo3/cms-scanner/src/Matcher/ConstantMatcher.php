<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;






class ConstantMatcher extends AbstractCoreMatcher
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
&& $node instanceof ConstFetch
&& in_array($node->name->toString(), array_keys($this->matcherDefinitions), true)
) {

 $this->matches[] = [
'restFiles' => $this->matcherDefinitions[$node->name->toString()]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->name->toString(),
'message' => 'Call to global constant "' . $node->name->toString() . '"',
'indicator' => static::INDICATOR_STRONG,
];
}
}
}
