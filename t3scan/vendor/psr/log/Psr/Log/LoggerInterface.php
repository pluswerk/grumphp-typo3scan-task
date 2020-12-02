<?php

namespace Psr\Log;
















interface LoggerInterface
{
/**
@param
@param
@return



*/
public function emergency($message, array $context = array());

/**
@param
@param
@return






*/
public function alert($message, array $context = array());

/**
@param
@param
@return





*/
public function critical($message, array $context = array());

/**
@param
@param
@return




*/
public function error($message, array $context = array());

/**
@param
@param
@return






*/
public function warning($message, array $context = array());

/**
@param
@param
@return



*/
public function notice($message, array $context = array());

/**
@param
@param
@return





*/
public function info($message, array $context = array());

/**
@param
@param
@return



*/
public function debug($message, array $context = array());

/**
@param
@param
@param
@return
@throws




*/
public function log($level, $message, array $context = array());
}
