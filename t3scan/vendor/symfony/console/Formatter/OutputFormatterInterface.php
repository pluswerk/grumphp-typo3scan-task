<?php










namespace Symfony\Component\Console\Formatter;

/**
@author


*/
interface OutputFormatterInterface
{
/**
@param


*/
public function setDecorated($decorated);

/**
@return


*/
public function isDecorated();

/**
@param
@param


*/
public function setStyle($name, OutputFormatterStyleInterface $style);

/**
@param
@return



*/
public function hasStyle($name);

/**
@param
@return
@throws




*/
public function getStyle($name);

/**
@param
@return



*/
public function format($message);
}
