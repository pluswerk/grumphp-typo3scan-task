<?php










namespace Symfony\Component\Process\Pipes;

/**
@author
@internal



*/
interface PipesInterface
{
const CHUNK_SIZE = 16384;

/**
@return


*/
public function getDescriptors();

/**
@return


*/
public function getFiles();

/**
@param
@param
@return



*/
public function readAndWrite($blocking, $close = false);

/**
@return


*/
public function areOpen();

/**
@return


*/
public function haveReadSupport();




public function close();
}
