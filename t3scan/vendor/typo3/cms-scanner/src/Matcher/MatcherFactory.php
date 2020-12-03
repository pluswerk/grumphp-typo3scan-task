<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Matcher;














use PhpParser\NodeVisitor;
use TYPO3\CMS\Scanner\CodeScannerInterface;




class MatcherFactory
{
/**
@param
@return
@throws


*/
public function createAll(array $matcherConfigurations)
{
$instances = [];
foreach ($matcherConfigurations as $matcherConfiguration) {
if (empty($matcherConfiguration['class'])) {
throw new \RuntimeException(
'Each matcher must have a class name',
1501415721
);
}

if (empty($matcherConfiguration['configurationFile']) && !isset($matcherConfiguration['configurationArray'])) {
throw new \RuntimeException(
'Each matcher must have either a configurationFile or configurationArray defined',
1501416365
);
}

if (isset($matcherConfiguration['configurationFile']) && isset($matcherConfiguration['configurationArray'])) {
throw new \RuntimeException(
'Having both a configurationFile and configurationArray is invalid',
1501419367
);
}

$configuration = [];
if (isset($matcherConfiguration['configurationFile'])) {
$configuration = $matcherConfiguration['configurationFile'];
if (empty($configuration) || !is_file($configuration)) {
throw new \RuntimeException(
'Configuration file ' . $matcherConfiguration['configurationFile'] . ' not found',
1501509605
);
}
$configuration = require $configuration;
if (!is_array($configuration)) {
throw new \RuntimeException(
'Configuration file ' . $matcherConfiguration['configurationFile'] . ' must return an array',
1501509548
);
}
}

if (isset($matcherConfiguration['configurationArray'])) {
if (!is_array($matcherConfiguration['configurationArray'])) {
throw new \RuntimeException(
'Configuration array ' . $matcherConfiguration['configurationArray'] . ' must not be empty',
1501509738
);
}
$configuration = $matcherConfiguration['configurationArray'];
}

$configuration = $this->extendConfiguration(
$configuration,
$matcherConfiguration
);
$matcherInstance = new $matcherConfiguration['class']($configuration);
if (!$matcherInstance instanceof CodeScannerInterface
|| !$matcherInstance instanceof NodeVisitor) {
throw new \RuntimeException(
'Matcher ' . $matcherConfiguration['class'] . ' must implement CodeScannerInterface'
. ' and NodeVisitor',
1501510168
);
}
$instances[] = $matcherInstance;
}
return $instances;
}

private function extendConfiguration(
array $configuration,
$matcherConfiguration
): array
{
if (isset($matcherConfiguration['restFilePath'])) {
$restFilePath = $matcherConfiguration['restFilePath'];
array_walk(
$configuration,
function (array &$aspect) use ($restFilePath) {
if (empty($aspect['restFiles'])) {
return;
}
$aspect['restFiles'] = array_map(
function (string $restFile) use ($restFilePath) {
return $restFilePath . $restFile;
},
$aspect['restFiles']
);
}
);
}

return $configuration;
}
}
