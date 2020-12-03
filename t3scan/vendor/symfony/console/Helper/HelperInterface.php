<?php










namespace Symfony\Component\Console\Helper;

/**
@author


*/
interface HelperInterface
{



public function setHelperSet(HelperSet $helperSet = null);

/**
@return


*/
public function getHelperSet();

/**
@return


*/
public function getName();
}
