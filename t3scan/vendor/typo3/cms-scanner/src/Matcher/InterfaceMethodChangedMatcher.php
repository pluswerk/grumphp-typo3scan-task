<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Expr\MethodCall;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassMethod;









class InterfaceMethodChangedMatcher extends AbstractCoreMatcher
{
/**
@param


*/
public function __construct(array $matcherDefinitions)
{
$this->matcherDefinitions = $matcherDefinitions;

 $this->validateMatcherDefinitions(['newNumberOfArguments']);
}

/**
@param
@return




*/
public function enterNode(Node $node)
{
if ($this->isFileIgnored($node) || $this->isLineIgnored($node)) {
return;
}


 if ($node instanceof ClassMethod
&& isset($node->name->name)
&& in_array($node->name->name, array_keys($this->matcherDefinitions), true)
&& $node->flags & Class_::MODIFIER_PUBLIC 
 && ($node->flags & Class_::MODIFIER_STATIC) !== Class_::MODIFIER_STATIC 
 ) {
$methodName = $node->name->name;
$numberOfUsedArguments = 0;
if (isset($node->params) && is_array($node->params)) {
$numberOfUsedArguments = count($node->params);
}
$numberOfAllowedArguments = $this->matcherDefinitions[$methodName]['newNumberOfArguments'];
if ($numberOfUsedArguments > $numberOfAllowedArguments) {
$this->matches[] = [
'restFiles' => $this->matcherDefinitions[$methodName]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $methodName,
'message' => 'Implementation of dropped interface argument for method "' . $methodName . '()"',
'indicator' => static::INDICATOR_WEAK,
];
}
}


 if ($node instanceof MethodCall
&& isset($node->name->name)
&& in_array($node->name->name, array_keys($this->matcherDefinitions), true)
) {
$methodName = $node->name->name;
$numberOfUsedArguments = 0;
if (isset($node->args) && is_array($node->args)) {
$numberOfUsedArguments = count($node->args);
}

 $numberOfAllowedArguments = $this->matcherDefinitions[$methodName]['newNumberOfArguments'];
if ($numberOfUsedArguments > $numberOfAllowedArguments) {
$this->matches[] = [
'restFiles' => $this->matcherDefinitions[$methodName]['restFiles'],
'line' => $node->getAttribute('startLine'),
'subject' => $methodName,
'message' => 'Call to interface method "' . $methodName . '()"',
'indicator' => static::INDICATOR_WEAK,
];
}
}
}
}
