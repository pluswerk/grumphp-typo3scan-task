<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\StaticCall;
use PhpParser\Node\Expr\Variable;
use PhpParser\Node\Name\FullyQualified;
use TYPO3\CMS\Scanner\CodeScannerInterface;





class MethodArgumentDroppedStaticMatcher extends AbstractCoreMatcher implements CodeScannerInterface
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitions(['maximumNumberOfArguments']);
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
if (!$isArgumentUnpackingUsed
&& in_array($fqdnClassWithMethod, array_keys($this->matcherDefinitions), true)
&& count($node->args) > $this->matcherDefinitions[$fqdnClassWithMethod]['maximumNumberOfArguments']
) {
$this->matches[] = [
'restFiles' => $this->matcherDefinitions[$fqdnClassWithMethod]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->name->name,
'message' => 'Method "' . $node->name->name . '()" supports only '
. $this->matcherDefinitions[$fqdnClassWithMethod]['maximumNumberOfArguments']
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
&& $numberOfArguments > $candidate['maximumNumberOfArguments']
) {
$isPossibleMatch = true;
$match['subject'] = $node->name->name;
$match['message'] = 'Method "' . $node->name->name . '()" supports only '
. $candidate['maximumNumberOfArguments'] . ' arguments.';
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
