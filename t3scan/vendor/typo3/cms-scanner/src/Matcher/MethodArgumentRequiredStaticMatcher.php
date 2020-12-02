<?php
declare(strict_types=1);

namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;





class MethodArgumentRequiredStaticMatcher extends AbstractCoreMatcher
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
$isArgumentUnpackingUsed = $this->isArgumentUnpackingUsed($node->args);

if ($node->class instanceof FullyQualified) {

 $fqdnClassWithMethod = $node->class->toString() . '::' . $node->name->name;
$numberOfArguments = count($node->args);
if (!$isArgumentUnpackingUsed
&& in_array($fqdnClassWithMethod, array_keys($this->matcherDefinitions), true)
&& $numberOfArguments < $this->matcherDefinitions[$fqdnClassWithMethod]['numberOfMandatoryArguments']

 && $numberOfArguments <= $this->matcherDefinitions[$fqdnClassWithMethod]['maximumNumberOfArguments']
) {
$this->matches[] = [
'restFiles' => $this->matcherDefinitions[$fqdnClassWithMethod]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->name->name,
'message' => 'Method "' . $node->name->name . '()" needs at least '
. $this->matcherDefinitions[$fqdnClassWithMethod]['numberOfMandatoryArguments']
. ' arguments.',
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
'indicator' => static::INDICATOR_WEAK,
];

$numberOfArguments = count($node->args);
$isPossibleMatch = false;
foreach ($this->flatMatcherDefinitions[$node->name->name]['candidates'] as $candidate) {

 
 if (!$isArgumentUnpackingUsed
&& $numberOfArguments < $candidate['numberOfMandatoryArguments']

 && $numberOfArguments <= $candidate['maximumNumberOfArguments']
) {
$isPossibleMatch = true;
$match['subject'] = $node->name->name;
$match['message'] = 'Method "' . $node->name->name . '()" needs at least '
. $candidate['numberOfMandatoryArguments'] . ' arguments.';
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
