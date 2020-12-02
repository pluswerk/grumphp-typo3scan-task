<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;




class ClassNamePatternMatcher extends AbstractCoreMatcher
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
@return


*/
public function enterNode(Node $node)
{
if (!$node instanceof FullyQualified
|| $this->isFileIgnored($node)
|| $this->isLineIgnored($node)
) {
return;
}

$fullyQualifiedClassName = $node->toString();
$matchedPattern = $this->match($fullyQualifiedClassName);
if ($matchedPattern === null) {
return;
}

$this->matches[] = [
'restFiles' => $this->matcherDefinitions[$matchedPattern]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $fullyQualifiedClassName,
'message' => 'Usage of class "' . $fullyQualifiedClassName . '"',
'indicator' => static::INDICATOR_STRONG,
];
}

private function match(string $value)
{
foreach (array_keys($this->matcherDefinitions) as $pattern) {
if (preg_match($pattern, $value)) {
return $pattern;
}
}

return null;
}
}