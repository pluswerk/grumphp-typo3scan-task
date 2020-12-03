<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Comment\Doc;
use PhpParser\Node;
use PhpParser\Node\Stmt\Property;
use PhpParser\Node\Stmt\PropertyProperty;




class PropertyAnnotationMatcher extends AbstractCoreMatcher
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
if ($node instanceof Property
&& ($property = reset($node->props)) instanceof PropertyProperty
&& ($docComment = $node->getDocComment()) instanceof Doc
&& !$this->isFileIgnored($node)
&& !$this->isLineIgnored($node)
) {
/**
@var */
$isPossibleMatch = false;
$match = [
'restFiles' => [],
'line' => $property->getAttribute('startLine'),
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
$match['message'] = 'Property "' . $property->name . '" uses an ' . $annotation . ' annotation.';
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
