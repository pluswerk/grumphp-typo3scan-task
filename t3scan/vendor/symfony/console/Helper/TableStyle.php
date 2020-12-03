<?php










namespace Symfony\Component\Console\Helper;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;

/**
@author
@author


*/
class TableStyle
{
private $paddingChar = ' ';
private $horizontalBorderChar = '-';
private $verticalBorderChar = '|';
private $crossingChar = '+';
private $cellHeaderFormat = '<info>%s</info>';
private $cellRowFormat = '%s';
private $cellRowContentFormat = ' %s ';
private $borderFormat = '%s';
private $padType = \STR_PAD_RIGHT;

/**
@param
@return



*/
public function setPaddingChar($paddingChar)
{
if (!$paddingChar) {
throw new LogicException('The padding char must not be empty.');
}

$this->paddingChar = $paddingChar;

return $this;
}

/**
@return


*/
public function getPaddingChar()
{
return $this->paddingChar;
}

/**
@param
@return



*/
public function setHorizontalBorderChar($horizontalBorderChar)
{
$this->horizontalBorderChar = $horizontalBorderChar;

return $this;
}

/**
@return


*/
public function getHorizontalBorderChar()
{
return $this->horizontalBorderChar;
}

/**
@param
@return



*/
public function setVerticalBorderChar($verticalBorderChar)
{
$this->verticalBorderChar = $verticalBorderChar;

return $this;
}

/**
@return


*/
public function getVerticalBorderChar()
{
return $this->verticalBorderChar;
}

/**
@param
@return



*/
public function setCrossingChar($crossingChar)
{
$this->crossingChar = $crossingChar;

return $this;
}

/**
@return


*/
public function getCrossingChar()
{
return $this->crossingChar;
}

/**
@param
@return



*/
public function setCellHeaderFormat($cellHeaderFormat)
{
$this->cellHeaderFormat = $cellHeaderFormat;

return $this;
}

/**
@return


*/
public function getCellHeaderFormat()
{
return $this->cellHeaderFormat;
}

/**
@param
@return



*/
public function setCellRowFormat($cellRowFormat)
{
$this->cellRowFormat = $cellRowFormat;

return $this;
}

/**
@return


*/
public function getCellRowFormat()
{
return $this->cellRowFormat;
}

/**
@param
@return



*/
public function setCellRowContentFormat($cellRowContentFormat)
{
$this->cellRowContentFormat = $cellRowContentFormat;

return $this;
}

/**
@return


*/
public function getCellRowContentFormat()
{
return $this->cellRowContentFormat;
}

/**
@param
@return



*/
public function setBorderFormat($borderFormat)
{
$this->borderFormat = $borderFormat;

return $this;
}

/**
@return


*/
public function getBorderFormat()
{
return $this->borderFormat;
}

/**
@param
@return



*/
public function setPadType($padType)
{
if (!\in_array($padType, [\STR_PAD_LEFT, \STR_PAD_RIGHT, \STR_PAD_BOTH], true)) {
throw new InvalidArgumentException('Invalid padding type. Expected one of (STR_PAD_LEFT, STR_PAD_RIGHT, STR_PAD_BOTH).');
}

$this->padType = $padType;

return $this;
}

/**
@return


*/
public function getPadType()
{
return $this->padType;
}
}
