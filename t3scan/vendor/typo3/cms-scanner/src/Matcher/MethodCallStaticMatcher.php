<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;












class MethodCallStaticMatcher extends AbstractCoreMatcher
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
&& $node instanceof StaticCall
) {
if ($node->class instanceof FullyQualified) {

 $fqdnClassWithMethod = $node->class->toString() . '::' . $node->name->name;
if (in_array($fqdnClassWithMethod, array_keys($this->matcherDefinitions), true)) {
$this->matches[] = [
'restFiles' => $this->matcherDefinitions[$fqdnClassWithMethod]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $fqdnClassWithMethod,
'message' => 'Use of static class method call "' . $fqdnClassWithMethod . '()"',
'indicator' => static::INDICATOR_STRONG,
];
}
} elseif ($node->class instanceof Variable
&& isset($node->name->name)
&& in_array($node->name->name, array_keys($this->flatMatcherDefinitions), true)
) {
$match = [
'restFiles' => [],
'line' => $node->getAttribute('startLine'),
'subject' => $node->name->name,
'message' => 'Use of static class method call "' . $node->name->name . '()"',
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
}
