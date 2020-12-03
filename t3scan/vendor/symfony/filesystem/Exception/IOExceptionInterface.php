<?php










namespace Symfony\Component\Filesystem\Exception;

/**
@author


*/
interface IOExceptionInterface extends ExceptionInterface
{
/**
@return


*/
public function getPath();
}
