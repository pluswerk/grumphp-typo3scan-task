<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;






class MethodCallMatcher extends AbstractCoreMatcher
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitions(['numberOfMandatoryArguments', 'maximumNumberOfArguments']);
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
'subject' => $node->name->name,
'message' => 'Call to method "' . $node->name->name . '()"',
'indicator' => static::INDICATOR_WEAK,
];

$numberOfArguments = count($node->args);
$isArgumentUnpackingUsed = $this->isArgumentUnpackingUsed($node->args);

$isPossibleMatch = false;
foreach ($this->flatMatcherDefinitions[$node->name->name]['candidates'] as $candidate) {

 
 if ($isArgumentUnpackingUsed
|| ($numberOfArguments >= $candidate['numberOfMandatoryArguments']
&& $numberOfArguments <= $candidate['maximumNumberOfArguments'])
) {
$isPossibleMatch = true;
$match['restFiles'] = array_unique(array_merge($match['restFiles'], $candidate['restFiles']));
}
}
if ($isPossibleMatch) {
$this->matches[] = $match;
}
}
}
}
