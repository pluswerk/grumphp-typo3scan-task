<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Name\FullyQualified;




class ClassNameMatcher extends AbstractCoreMatcher
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
&& $node instanceof FullyQualified
) {
$fullyQualifiedClassName = $node->toString();
if (in_array($fullyQualifiedClassName, array_keys($this->matcherDefinitions), true)) {
$this->matches[] = [
'restFiles' => $this->matcherDefinitions[$fullyQualifiedClassName]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $fullyQualifiedClassName,
'message' => 'Usage of class "' . $fullyQualifiedClassName . '"',
'indicator' => static::INDICATOR_STRONG,
];
}
}
}
}
