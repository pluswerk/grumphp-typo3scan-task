<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use TYPO3\CMS\Scanner\CodeScannerInterface;






class MethodArgumentRequiredMatcher extends AbstractCoreMatcher implements CodeScannerInterface
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitions(['numberOfMandatoryArguments']);
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

 
 if (!$isArgumentUnpackingUsed
&& $numberOfArguments < $candidate['numberOfMandatoryArguments']
&& $numberOfArguments <= $candidate['maximumNumberOfArguments']
) {
$isPossibleMatch = true;
$match['subject'] = $node->name->name;
$match['message'] = 'Method ' . $node->name->name . '() needs at least ' . $candidate['numberOfMandatoryArguments'] . ' arguments.';
$match['restFiles'] = array_unique(array_merge($match['restFiles'], $candidate['restFiles']));
}
}
if ($isPossibleMatch) {
$this->matches[] = $match;
}
}
}
}
