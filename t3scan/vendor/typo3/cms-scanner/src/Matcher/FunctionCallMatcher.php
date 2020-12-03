<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\FuncCall;
use PhpParser\Node\Name\FullyQualified;





class FunctionCallMatcher extends AbstractCoreMatcher
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitions(['numberOfMandatoryArguments', 'maximumNumberOfArguments']);
}

/**
@param



*/
public function enterNode(Node $node)
{

 if (!$this->isFileIgnored($node)
&& !$this->isLineIgnored($node)
&& $node instanceof FuncCall
&& $node->name instanceof FullyQualified
&& in_array($node->name->toString(), array_keys($this->matcherDefinitions), true)
) {
$functionName = $node->name->toString();
$matchDefinition = $this->matcherDefinitions[$functionName];

$numberOfArguments = count($node->args);
$isArgumentUnpackingUsed = $this->isArgumentUnpackingUsed($node->args);

if ($isArgumentUnpackingUsed
|| ($numberOfArguments >= $matchDefinition['numberOfMandatoryArguments']
&& $numberOfArguments <= $matchDefinition['maximumNumberOfArguments'])
) {
$this->matches[] = [
'restFiles' => $matchDefinition['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $functionName,
'message' => 'Call to function "' . $functionName . '"',
'indicator' => static::INDICATOR_STRONG,
];
}
}
}
}
