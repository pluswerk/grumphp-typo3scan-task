<?php

namespace Psr\Log;




trait LoggerAwareTrait
{
/**
@var


*/
protected $logger;

/**
@param


*/
public function setLogger(LoggerInterface $logger)
{
$this->logger = $logger;
}
}
