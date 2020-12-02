<?php










namespace Symfony\Component\Console\Style;

/**
@author


*/
interface StyleInterface
{
/**
@param


*/
public function title($message);

/**
@param


*/
public function section($message);




public function listing(array $elements);

/**
@param


*/
public function text($message);

/**
@param


*/
public function success($message);

/**
@param


*/
public function error($message);

/**
@param


*/
public function warning($message);

/**
@param


*/
public function note($message);

/**
@param


*/
public function caution($message);




public function table(array $headers, array $rows);

/**
@param
@param
@param
@return



*/
public function ask($question, $default = null, $validator = null);

/**
@param
@param
@return



*/
public function askHidden($question, $validator = null);

/**
@param
@param
@return



*/
public function confirm($question, $default = true);

/**
@param
@param
@return



*/
public function choice($question, array $choices, $default = null);

/**
@param


*/
public function newLine($count = 1);

/**
@param


*/
public function progressStart($max = 0);

/**
@param


*/
public function progressAdvance($step = 1);




public function progressFinish();
}
