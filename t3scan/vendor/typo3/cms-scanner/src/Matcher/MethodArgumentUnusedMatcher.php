<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\MethodCall;








class MethodArgumentUnusedMatcher extends AbstractCoreMatcher
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitions(['unusedArgumentNumbers']);
$this->initializeFlatMatcherDefinitions();
}

/**
@param


*/
public function enterNode(Node $node)
{

 if (!$this->isFileIgnored($node)
&& !$this->isLineIgnored($node)
&& $node instanceof MethodCall
&& isset($node->name->name)
&& in_array($node->name->name, array_keys($this->flatMatcherDefinitions), true)
) {
$match = [
'restFiles' => [],
'line' => $node->getAttribute('startLine'),
'indicator' => static::INDICATOR_WEAK,
];

$isArgumentUnpackingUsed = $this->isArgumentUnpackingUsed($node->args);

$numberOfArguments = count($node->args);
$isPossibleMatch = false;
foreach ($this->flatMatcherDefinitions[$node->name->name]['candidates'] as $candidate) {
foreach ($candidate['unusedArgumentNumbers'] as $droppedArgumentNumber) {

 
 if (!$isArgumentUnpackingUsed
&& $numberOfArguments >= $droppedArgumentNumber
&& !($node->args[$droppedArgumentNumber - 1]->value instanceof ConstFetch)
&& (!isset($node->args[$droppedArgumentNumber - 1]->value->name->name->parts[0])
|| $node->args[$droppedArgumentNumber - 1]->value->name->name->parts[0] !== null)
) {
$isPossibleMatch = true;
$match['subject'] = $node->name->name;
$match['message'] = 'Call to method "' . $node->name->name . '()" with'
. ' argument ' . $droppedArgumentNumber . ' not given as null.';
$match['restFiles'] = array_unique(array_merge($match['restFiles'], $candidate['restFiles']));
}
}
}
if ($isPossibleMatch) {
$this->matches[] = $match;
}
}
}
}
