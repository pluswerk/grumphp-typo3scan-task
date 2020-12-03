<?php declare(strict_types=1);

namespace PhpParser\Node\Name;

class FullyQualified extends \PhpParser\Node\Name
{
/**
@return


*/
public function isUnqualified() : bool {
return false;
}

/**
@return


*/
public function isQualified() : bool {
return false;
}

/**
@return


*/
public function isFullyQualified() : bool {
return true;
}

/**
@return


*/
public function isRelative() : bool {
return false;
}

public function toCodeString() : string {
return '\\' . $this->toString();
}

public function getType() : string {
return 'Name_FullyQualified';
}
}
