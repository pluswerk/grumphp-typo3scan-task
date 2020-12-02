<?php
declare(strict_types=1);

namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Stmt\Property;





class PropertyExistsStaticMatcher extends AbstractCoreMatcher
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
&& $node instanceof Property
&& $node->isStatic()
&& !$node->isPrivate()
&& in_array($node->props[0]->name->name, array_keys($this->matcherDefinitions), true)
) {
$propertyName = $node->props[0]->name->name;
$match = [
'restFiles' => $this->matcherDefinitions[$propertyName]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->props[0]->name->name,
'message' => 'Use of property "' . $node->props[0]->name->name . '"',
'indicator' => static::INDICATOR_WEAK,
];
$this->matches[] = $match;
}
}
}
