<?php










namespace Symfony\Component\Console\Output;

use Symfony\Component\Console\Formatter\OutputFormatterInterface;

/**
@author


*/
interface OutputInterface
{
const VERBOSITY_QUIET = 16;
const VERBOSITY_NORMAL = 32;
const VERBOSITY_VERBOSE = 64;
const VERBOSITY_VERY_VERBOSE = 128;
const VERBOSITY_DEBUG = 256;

const OUTPUT_NORMAL = 1;
const OUTPUT_RAW = 2;
const OUTPUT_PLAIN = 4;

/**
@param
@param
@param


*/
public function write($messages, $newline = false, $options = 0);

/**
@param
@param


*/
public function writeln($messages, $options = 0);

/**
@param


*/
public function setVerbosity($level);

/**
@return


*/
public function getVerbosity();

/**
@return


*/
public function isQuiet();

/**
@return


*/
public function isVerbose();

/**
@return


*/
public function isVeryVerbose();

/**
@return


*/
public function isDebug();

/**
@param


*/
public function setDecorated($decorated);

/**
@return


*/
public function isDecorated();

public function setFormatter(OutputFormatterInterface $formatter);

/**
@return


*/
public function getFormatter();
}
