<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayItem;
use PhpParser\Node\Scalar\String_;











class ArrayMatcher extends AbstractCoreMatcher
{
/**
@var
*/
protected $matchedNodes = [];

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
&& $node instanceof Array_
) {
$flatArray = $this->flattenArray($node);

foreach ($this->matcherDefinitions as $candidateKey => $candidate) {
$candidateKey = '/' . ltrim($candidateKey, '/');
/**
@var
@var
*/
foreach ($flatArray as $flatKey => $nodeItem) {
if (\strlen($candidateKey) > \strlen($flatKey)) {
continue;
}
if (($candidateKey === $flatKey
|| substr($flatKey, -\strlen($candidateKey)) === $candidateKey)
&& !\in_array($nodeItem->getStartLine() . $candidateKey, $this->matchedNodes, true)
) {
if (array_key_exists('matchOnValues', $candidate) && !$this->nodeValueMatches($nodeItem->value, $candidate['matchOnValues'])) {
continue;
}
$match = [
'restFiles' => [],
'line' => $nodeItem->getStartLine(),
'subject' => $nodeItem->key->value,
'message' => 'Usage of array key "' . $nodeItem->key->value . '"',
'indicator' => substr_count($candidateKey, '/') > 1 ? static::INDICATOR_STRONG : static::INDICATOR_WEAK,
];
$match['restFiles'] = array_unique(array_merge($match['restFiles'], $candidate['restFiles']));
$this->matches[] = $match;
$this->matchedNodes[] = $nodeItem->getStartLine() . $candidateKey;
}
}
}
}
}

/**
@param
@param
@return





*/
protected function flattenArray($node, $prefix = '/')
{
$result = [];
/**
@var */
foreach ($node->items as $nodeItem) {
if ($nodeItem->value instanceof Array_ && $nodeItem->value->items) {
if ($nodeItem instanceof ArrayItem && $nodeItem->key instanceof String_) {
$result += $this->flattenArray($nodeItem->value, $prefix . $nodeItem->key->value . '/');
}
} else {
if ($nodeItem instanceof ArrayItem && $nodeItem->key instanceof String_) {
$result[$prefix . $nodeItem->key->value] = $nodeItem;
}
}
}
return $result;
}

/**
@param
@param
@return


*/
protected function nodeValueMatches($nodeValue, $matches = [])
{
$value = null;
switch (\get_class($nodeValue)) {
case Node\Scalar\LNumber::class:
$value = $nodeValue->value;
break;
case Node\Expr\ConstFetch::class:
$parts = $nodeValue->name->parts;
if (\count($parts) === 1) {
$value = $parts[0];
}
break;
default:
if (isset($nodeValue->value)) {
$value = $nodeValue->value;
}
}

return \in_array($value, $matches, true);
}
}
