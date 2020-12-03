<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;
use TYPO3\CMS\Scanner\CodeScannerInterface;







abstract class AbstractCoreMatcher extends NodeVisitorAbstract implements CodeScannerInterface
{
const NODE_RESOLVED_AS = 'nodeResolvedAs';
const INDICATOR_IMPOSSIBLE = 'impossible';
const INDICATOR_STRONG = 'strong';
const INDICATOR_WEAK = 'weak';

/**
@var


*/
protected $matcherDefinitions = [];

/**
@var
*/
protected $matches = [];

/**
@var



*/
protected $flatMatcherDefinitions = [];

/**
@var
*/
protected $currentCodeLine = 0;

/**
@var
*/
protected $isCurrentLineIgnored = false;

/**
@var
@extensionScannerIgnoreFile */
protected $isFullFileIgnored = false;

/**
@return


*/
public function getMatches(): array
{
return $this->matches;
}

/**
@param
@throws








*/
protected function validateMatcherDefinitions(array $requiredArrayKeys = [])
{
foreach ($this->matcherDefinitions as $key => $matcherDefinition) {
$this->validateMatcherDefinitionKeys($key, $matcherDefinition, $requiredArrayKeys);
}
}

protected function validateMatcherDefinitionKeys(string $key, array $matcherDefinition, array $requiredArrayKeys = [])
{

 if (empty($matcherDefinition['restFiles'])) {
throw new \InvalidArgumentException(
'Each configuration must have at least one referenced "restFiles" entry. Offending key: ' . $key,
1500496068
);
}
foreach ($matcherDefinition['restFiles'] as $file) {
if (empty($file)) {
throw new \InvalidArgumentException(
'Empty restFiles definition',
1500735983
);
}
}

 $sharedArrays = array_intersect(array_keys($matcherDefinition), $requiredArrayKeys);
if (count($sharedArrays) !== count($requiredArrayKeys)) {
$missingKeys = array_diff($requiredArrayKeys, array_keys($matcherDefinition));
throw new \InvalidArgumentException(
'Required matcher definitions missing: ' . implode(', ', $missingKeys) . ' offending key: ' . $key,
1500492001
);
}
}

/**
@throws








*/
protected function initializeFlatMatcherDefinitions()
{
$methodNameArray = [];
foreach ($this->matcherDefinitions as $classAndMethod => $details) {
$method = $this->trimExplode('::', $classAndMethod);
if (count($method) !== 2) {
$method = $this->trimExplode('->', $classAndMethod);
}
if (count($method) !== 2) {
throw new \RuntimeException(
'Keys in $this->matcherDefinitions must have a Class\Name->method or Class\Name::method structure',
1500557309
);
}
$method = $method[1];
if (!array_key_exists($method, $methodNameArray)) {
$methodNameArray[$method]['candidates'] = [];
}
$methodNameArray[$method]['candidates'][] = $details;
}
$this->flatMatcherDefinitions = $methodNameArray;
}

/**
@param
@return



*/
protected function isArgumentUnpackingUsed(array $arguments = []): bool
{
foreach ($arguments as $arg) {
if ($arg->unpack === true) {
return true;
}
}
return false;
}

/**
@param
@return



*/
protected function isLineIgnored(Node $node): bool
{

 $startLineOfNode = $node->getAttribute('startLine');
if ($startLineOfNode === $this->currentCodeLine) {
return $this->isCurrentLineIgnored;
}

$currentLineIsIgnored = false;
if ($startLineOfNode !== $this->currentCodeLine) {
$this->currentCodeLine = $startLineOfNode;

 $comments = $node->getAttribute('comments');
if (!empty($comments)) {
foreach ($comments as $comment) {
if (strstr($comment->getText(), '@extensionScannerIgnoreLine') !== false) {
$this->isCurrentLineIgnored = true;
$currentLineIsIgnored = true;
break;
}
}
}
}
return $currentLineIsIgnored;
}

/**
@param
@return



*/
protected function isFileIgnored(Node $node): bool
{
if ($this->isFullFileIgnored) {
return true;
}
$currentFileIsIgnored = false;
if ($node instanceof Class_) {
$comments = $node->getAttribute('comments');
if (!empty($comments)) {
foreach ($comments as $comment) {
if (strstr($comment->getText(), '@extensionScannerIgnoreFile') !== false) {
$this->isFullFileIgnored = true;
$currentFileIsIgnored = true;
break;
}
}
}
}
return $currentFileIsIgnored;
}

protected function trimExplode(string $delimiter, string $string, bool $removeEmptyValues = false, int $limit = 0): array
{
$result = explode($delimiter, $string);
if ($removeEmptyValues) {
$temp = [];
foreach ($result as $value) {
if (trim($value) !== '') {
$temp[] = $value;
}
}
$result = $temp;
}
if ($limit > 0 && count($result) > $limit) {
$lastElements = array_splice($result, $limit - 1);
$result[] = implode($delimiter, $lastElements);
} elseif ($limit < 0) {
$result = array_slice($result, 0, $limit);
}
$result = array_map('trim', $result);
return $result;
}
}
