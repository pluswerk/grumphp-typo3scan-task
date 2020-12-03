<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\ArrayDimFetch;

/**
@internal



*/
class ArrayDimensionMatcher extends AbstractCoreMatcher
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitions();
$this->initializeLastArrayKeyNameArray();
}

/**
@param


*/
public function enterNode(Node $node)
{
if (!$this->isFileIgnored($node)
&& !$this->isLineIgnored($node)
&& $node instanceof ArrayDimFetch
&& isset($node->dim->value)
&& array_key_exists($node->dim->value, $this->flatMatcherDefinitions)
) {
$match = [
'restFiles' => [],
'line' => $node->getAttribute('startLine'),
'subject' => $node->dim->value,
'message' => 'Access to array key "' . $node->dim->value . '"',
'indicator' => 'weak',
];

foreach ($this->flatMatcherDefinitions[$node->dim->value]['candidates'] as $candidate) {
$match['restFiles'] = array_unique(array_merge($match['restFiles'], $candidate['restFiles']));
}
$this->matches[] = $match;
}
}




protected function initializeLastArrayKeyNameArray()
{
$methodNameArray = [];
foreach ($this->matcherDefinitions as $fullArrayString => $details) {

 
 $lastKey = strrev($fullArrayString);

 $lastKey = substr($lastKey, 2);
$lastKey = $this->trimExplode('\'[', $lastKey);

 $lastKey = $lastKey[0];

 $lastKey = strrev($lastKey);

if (!array_key_exists($lastKey, $methodNameArray)) {
$methodNameArray[$lastKey]['candidates'] = [];
}
$methodNameArray[$lastKey]['candidates'][] = $details;
}
$this->flatMatcherDefinitions = $methodNameArray;
}
}
