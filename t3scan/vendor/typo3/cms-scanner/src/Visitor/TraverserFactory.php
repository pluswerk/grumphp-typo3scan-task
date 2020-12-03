<?php
namespace TYPO3\CMS\Scanner\Visitor;

use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use TYPO3\CMS\Scanner\CodeScannerInterface;
use TYPO3\CMS\Scanner\Domain\Model\MatcherBundleCollection;
use TYPO3\CMS\Scanner\Matcher\MatcherFactory;

class TraverserFactory
{
private $matcherFactory;

public function __construct(MatcherFactory $matcherFactory)
{
$this->matcherFactory = $matcherFactory;
}

public function createTraverser(CodeScannerInterface ...$matchers): NodeTraverser
{
$traverser = $this->buildNodeTraverser();

 foreach ($matchers as $matcher) {
$traverser->addVisitor($matcher);
}
return $traverser;
}

/**
@param
@return
*/
public function createMatchers(MatcherBundleCollection $collection): array
{
return $this->matcherFactory->createAll(
$collection->getConfiguration()
);
}

private function buildNodeTraverser(): NodeTraverser
{
$traverser = new NodeTraverser();

 
 
 $traverser->addVisitor(new NameResolver());

 
 $traverser->addVisitor(new GeneratorClassesResolver());

 $traverser->addVisitor(new CodeStatistics());

return $traverser;
}
}