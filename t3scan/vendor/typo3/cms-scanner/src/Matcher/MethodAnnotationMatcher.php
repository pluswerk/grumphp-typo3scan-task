<?php
declare(strict_types=1);

namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\ClassMethod;




class MethodAnnotationMatcher extends AbstractCoreMatcher
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
if ($node instanceof ClassMethod
&& ($docComment = $node->getDocComment()) instanceof Doc
&& !$this->isFileIgnored($node)
&& !$this->isLineIgnored($node)
) {
$isPossibleMatch = false;
$match = [
'restFiles' => [],
'subject' => $node->name,
'line' => $node->getAttribute('startLine'),
'indicator' => static::INDICATOR_STRONG,
];

$matches = [];
preg_match_all(
'/\s*\s@(?<annotations>[^\s.]*).*\n/',
$docComment->getText(),
$matches
);

foreach ($matches['annotations'] as $annotation) {
$annotation = '@' . $annotation;

if (!isset($this->matcherDefinitions[$annotation])) {
continue;
}

$isPossibleMatch = true;
$match['message'] = 'Method "' . $node->name . '" uses an ' . $annotation . ' annotation.';
$match['restFiles'] = array_unique(array_merge(
$match['restFiles'],
$this->matcherDefinitions[$annotation]['restFiles']
));
}

if ($isPossibleMatch) {
$this->matches[] = $match;
}
}
}
}
