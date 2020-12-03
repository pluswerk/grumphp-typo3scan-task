<?php declare(strict_types=1);

namespace PhpParser\Node\Name;

class Relative extends \PhpParser\Node\Name
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
return false;
}

/**
@return


*/
public function isRelative() : bool {
return true;
}

public function toCodeString() : string {
return 'namespace\\' . $this->toString();
}

public function getType() : string {
return 'Name_Relative';
}
}
