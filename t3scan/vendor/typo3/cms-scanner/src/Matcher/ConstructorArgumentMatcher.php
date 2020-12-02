<?php

declare(strict_types=1);














namespace TYPO3\CMS\Scanner\Matcher;

use PhpParser\Node;
use PhpParser\Node\Expr\ConstFetch;
use PhpParser\Node\Expr\New_;

/**
@internal









*/
class ConstructorArgumentMatcher extends AbstractCoreMatcher
{
protected const TOPIC_TYPE_REQUIRED = 'required';
protected const TOPIC_TYPE_DROPPED = 'dropped';
protected const TOPIC_TYPE_CALLED = 'called';
protected const TOPIC_TYPE_UNUSED = 'unused';

/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;
$this->validateMatcherDefinitionsTopicRequirements([
self::TOPIC_TYPE_REQUIRED => ['numberOfMandatoryArguments'],
self::TOPIC_TYPE_DROPPED => ['maximumNumberOfArguments'],
self::TOPIC_TYPE_CALLED => ['numberOfMandatoryArguments', 'maximumNumberOfArguments'],
self::TOPIC_TYPE_UNUSED => ['unusedArgumentNumbers'],
]);
}

/**
@param



*/
public function enterNode(Node $node)
{
if ($this->isFileIgnored($node) || $this->isLineIgnored($node)) {
return;
}
$resolvedNode = $node->getAttribute(self::NODE_RESOLVED_AS, null) ?? $node;
if (!$resolvedNode instanceof New_
|| !isset($resolvedNode->class)
|| is_object($node->class) && !method_exists($node->class, '__toString')
|| !array_key_exists((string)$resolvedNode->class, $this->matcherDefinitions)
) {
return;
}


 
 if ($this->isArgumentUnpackingUsed($resolvedNode->args)) {
return;
}


 
 $this->handleRequiredArguments($node, $resolvedNode);
$this->handleDroppedArguments($node, $resolvedNode);
$this->handleCalledArguments($node, $resolvedNode);
$this->handleUnusedArguments($node, $resolvedNode);
}

/**
@param
@param
@return
*/
protected function handleRequiredArguments(Node $node, Node $resolvedNode): bool
{
$className = (string)$resolvedNode->class;
$candidate = $this->matcherDefinitions[$className][self::TOPIC_TYPE_REQUIRED] ?? null;
$mandatoryArguments = $candidate['numberOfMandatoryArguments'] ?? null;
$numberOfArguments = count($resolvedNode->args);

if ($candidate === null || $numberOfArguments >= $mandatoryArguments) {
return false;
}

$this->matches[] = [
'restFiles' => $candidate['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->toString(),
'message' => sprintf(
'%s::__construct requires at least %d arguments (%d given).',
$className,
$mandatoryArguments,
$numberOfArguments
),
'indicator' => 'strong',
];
return true;
}

/**
@param
@param
@return
*/
protected function handleDroppedArguments(Node $node, Node $resolvedNode): bool
{
$className = (string)$resolvedNode->class;
$candidate = $this->matcherDefinitions[$className][self::TOPIC_TYPE_DROPPED] ?? null;
$maximumArguments = $candidate['maximumNumberOfArguments'] ?? null;
$numberOfArguments = count($resolvedNode->args);

if ($candidate === null || $numberOfArguments <= $maximumArguments) {
return false;
}

$this->matches[] = [
'restFiles' => $candidate['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->toString(),
'message' => sprintf(
'%s::__construct supports only %d arguments (%d given).',
$className,
$maximumArguments,
$numberOfArguments
),
'indicator' => 'strong',
];
return true;
}

/**
@param
@param
@return
*/
protected function handleCalledArguments(Node $node, Node $resolvedNode): bool
{
$className = (string)$resolvedNode->class;
$candidate = $this->matcherDefinitions[$className][self::TOPIC_TYPE_CALLED] ?? null;
$isArgumentUnpackingUsed = $this->isArgumentUnpackingUsed($resolvedNode->args);
$mandatoryArguments = $candidate['numberOfMandatoryArguments'] ?? null;
$maximumArguments = $candidate['maximumNumberOfArguments'] ?? null;
$numberOfArguments = count($resolvedNode->args);

if ($candidate === null
|| !$isArgumentUnpackingUsed
&& ($numberOfArguments < $mandatoryArguments || $numberOfArguments > $maximumArguments)) {
return false;
}

$this->matches[] = [
'restFiles' => $candidate['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->toString(),
'message' => sprintf(
'%s::__construct being called (%d arguments given).',
$className,
$numberOfArguments
),
'indicator' => 'weak',
];
return true;
}

/**
@param
@param
@return
*/
protected function handleUnusedArguments(Node $node, Node $resolvedNode): bool
{
$className = (string)$resolvedNode->class;
$candidate = $this->matcherDefinitions[$className][self::TOPIC_TYPE_UNUSED] ?? null;

 
 $unusedArgumentPositions = $candidate['unusedArgumentNumbers'] ?? null;

if ($candidate === null || empty($unusedArgumentPositions)) {
return false;
}

$arguments = $resolvedNode->args;

 $unusedArgumentPositions = array_filter(
$unusedArgumentPositions,
function (int $position) use ($arguments) {
$index = $position - 1;
return isset($arguments[$index]->value)
&& !$arguments[$index]->value instanceof ConstFetch
&& (
!isset($arguments[$index]->value->name->name->parts[0])
|| $arguments[$index]->value->name->name->parts[0] !== null
);
}
);
if (empty($unusedArgumentPositions)) {
return false;
}

$this->matches[] = [
'restFiles' => $candidate['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $node->toString(),
'message' => sprintf(
'%s::__construct was called with argument positions %s not being null.',
$className,
implode(', ', $unusedArgumentPositions)
),
'indicator' => 'strong',
];
return true;
}

protected function validateMatcherDefinitionsTopicRequirements(array $topicRequirements): void
{
foreach ($this->matcherDefinitions as $key => $matcherDefinition) {
foreach ($topicRequirements as $topic => $requiredArrayKeys) {
if (empty($matcherDefinition[$topic])) {
continue;
}
$this->validateMatcherDefinitionKeys($key, $matcherDefinition[$topic], $requiredArrayKeys);
}
}
}
}
