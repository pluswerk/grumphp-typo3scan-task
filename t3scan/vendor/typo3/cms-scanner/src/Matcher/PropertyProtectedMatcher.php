<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\PropertyFetch;
use PhpParser\Node\Identifier;





class PropertyProtectedMatcher extends AbstractCoreMatcher
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitions();
$this->initializeFlatMatcherDefinitions();
}

/**
@param


*/
public function enterNode(Node $node)
{
if (!$this->isFileIgnored($node)
&& !$this->isLineIgnored($node)
&& $node instanceof PropertyFetch
&& $node->name instanceof Identifier
&& isset($node->var->name)
&& $node->var->name !== 'this'
&& isset($node->name->name)
&& in_array($node->name->name, array_keys($this->flatMatcherDefinitions), true)
) {
$match = [
'restFiles' => [],
'line' => $node->getAttribute('startLine'),
'subject' => $node->name->name,
'message' => 'Fetch of property "' . $node->name->name . '"',
'indicator' => static::INDICATOR_WEAK,
];

foreach ($this->flatMatcherDefinitions[$node->name->name]['candidates'] as $candidate) {
$match['restFiles'] = array_unique(array_merge($match['restFiles'], $candidate['restFiles']));
}
$this->matches[] = $match;
}
}
}
