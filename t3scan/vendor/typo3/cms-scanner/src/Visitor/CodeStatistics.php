<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner\Visitor;














use PhpParser\Node;
use PhpParser\Node\Stmt\Class_;
use PhpParser\NodeVisitorAbstract;





class CodeStatistics extends NodeVisitorAbstract
{
/**
@var
@extensionScannerIgnoreFile */
protected $isCurrentFileIgnored = 0;

/**
@var
@extensionScannerIgnoreLine */
protected $numberOfIgnoreLines = 0;

/**
@var
*/
protected $numberOfEffectiveCodeLines = 0;

/**
@var
*/
protected $currentLineNumber = 0;

/**
@param


*/
public function enterNode(Node $node)
{
$startLineOfNode = $node->getAttribute('startLine');
if ($startLineOfNode !== $this->currentLineNumber) {
$this->currentLineNumber = $startLineOfNode;
$this->numberOfEffectiveCodeLines ++;


 if ($node instanceof Class_) {
$comments = $node->getAttribute('comments');
if (!empty($comments)) {
foreach ($comments as $comment) {
if (strstr($comment->getText(), '@extensionScannerIgnoreFile') !== false) {
$this->isCurrentFileIgnored = true;
break;
}
}
}
}


 $comments = $node->getAttribute('comments');
if (!empty($comments)) {
foreach ($comments as $comment) {
if (strstr($comment->getText(), '@extensionScannerIgnoreLine') !== false) {
$this->numberOfIgnoreLines ++;
break;
}
}
}
}
}

/**
@extensionScannerIgnoreFile
@return


*/
public function isFileIgnored()
{
return $this->isCurrentFileIgnored;
}

/**
@return




*/
public function getNumberOfEffectiveCodeLines()
{
return $this->numberOfEffectiveCodeLines;
}

/**
@extensionScannerIgnoreLine
@return


*/
public function getNumberOfIgnoredLines()
{
return $this->numberOfIgnoreLines;
}
}
