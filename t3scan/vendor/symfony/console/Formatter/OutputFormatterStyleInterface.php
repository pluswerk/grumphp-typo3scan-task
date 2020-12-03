<?php










namespace Symfony\Component\Console\Formatter;

/**
@author


*/
interface OutputFormatterStyleInterface
{
/**
@param


*/
public function setForeground($color = null);

/**
@param


*/
public function setBackground($color = null);

/**
@param


*/
public function setOption($option);

/**
@param


*/
public function unsetOption($option);




public function setOptions(array $options);

/**
@param
@return



*/
public function apply($text);
}
