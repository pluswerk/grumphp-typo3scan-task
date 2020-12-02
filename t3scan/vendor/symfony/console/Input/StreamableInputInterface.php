<?php










namespace Symfony\Component\Console\Input;

/**
@author



*/
interface StreamableInputInterface extends InputInterface
{
/**
@param




*/
public function setStream($stream);

/**
@return


*/
public function getStream();
}
