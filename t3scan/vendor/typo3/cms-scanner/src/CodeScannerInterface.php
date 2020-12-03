<?php
declare(strict_types=1);
namespace TYPO3\CMS\Scanner;














use PhpParser\NodeVisitor;




interface CodeScannerInterface extends NodeVisitor
{
/**
@return


*/
public function getMatches(): array;
}
