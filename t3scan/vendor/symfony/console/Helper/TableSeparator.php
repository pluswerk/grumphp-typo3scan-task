<?php










namespace Symfony\Component\Console\Helper;

/**
@author


*/
class TableSeparator extends TableCell
{
public function __construct(array $options = [])
{
parent::__construct('', $options);
}
}
