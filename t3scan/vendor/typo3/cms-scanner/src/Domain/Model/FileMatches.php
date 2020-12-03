<?php
namespace TYPO3\CMS\Scanner\Domain\Model;

use TYPO3\CMS\Scanner\Utility;

class FileMatches extends \ArrayObject
{
private $path;

public function __construct(string $path, Match ...$matches)
{
parent::__construct($matches);
$this->path = $path;
}

/**
@return
*/
public function getPath(): string
{
return $this->path;
}
}