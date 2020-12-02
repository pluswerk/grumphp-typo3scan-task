<?php

namespace Psr\Log;








abstract class AbstractLogger implements LoggerInterface
{
/**
@param
@param
@return



*/
public function emergency($message, array $context = array())
{
$this->log(LogLevel::EMERGENCY, $message, $context);
}

/**
@param
@param
@return






*/
public function alert($message, array $context = array())
{
$this->log(LogLevel::ALERT, $message, $context);
}

/**
@param
@param
@return





*/
public function critical($message, array $context = array())
{
$this->log(LogLevel::CRITICAL, $message, $context);
}

/**
@param
@param
@return




*/
public function error($message, array $context = array())
{
$this->log(LogLevel::ERROR, $message, $context);
}

/**
@param
@param
@return






*/
public function warning($message, array $context = array())
{
$this->log(LogLevel::WARNING, $message, $context);
}

/**
@param
@param
@return



*/
public function notice($message, array $context = array())
{
$this->log(LogLevel::NOTICE, $message, $context);
}

/**
@param
@param
@return





*/
public function info($message, array $context = array())
{
$this->log(LogLevel::INFO, $message, $context);
}

/**
@param
@param
@return



*/
public function debug($message, array $context = array())
{
$this->log(LogLevel::DEBUG, $message, $context);
}
}
