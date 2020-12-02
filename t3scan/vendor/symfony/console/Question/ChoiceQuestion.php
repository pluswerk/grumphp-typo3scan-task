<?php










namespace Symfony\Component\Console\Question;

use Symfony\Component\Console\Exception\InvalidArgumentException;

/**
@author


*/
class ChoiceQuestion extends Question
{
private $choices;
private $multiselect = false;
private $prompt = ' > ';
private $errorMessage = 'Value "%s" is invalid';

/**
@param
@param
@param
*/
public function __construct($question, array $choices, $default = null)
{
if (!$choices) {
throw new \LogicException('Choice question must have at least 1 choice available.');
}

parent::__construct($question, $default);

$this->choices = $choices;
$this->setValidator($this->getDefaultValidator());
$this->setAutocompleterValues($choices);
}

/**
@return


*/
public function getChoices()
{
return $this->choices;
}

/**
@param
@return





*/
public function setMultiselect($multiselect)
{
$this->multiselect = $multiselect;
$this->setValidator($this->getDefaultValidator());

return $this;
}

/**
@return


*/
public function isMultiselect()
{
return $this->multiselect;
}

/**
@return


*/
public function getPrompt()
{
return $this->prompt;
}

/**
@param
@return



*/
public function setPrompt($prompt)
{
$this->prompt = $prompt;

return $this;
}

/**
@param
@return





*/
public function setErrorMessage($errorMessage)
{
$this->errorMessage = $errorMessage;
$this->setValidator($this->getDefaultValidator());

return $this;
}

/**
@return


*/
private function getDefaultValidator()
{
$choices = $this->choices;
$errorMessage = $this->errorMessage;
$multiselect = $this->multiselect;
$isAssoc = $this->isAssoc($choices);

return function ($selected) use ($choices, $errorMessage, $multiselect, $isAssoc) {
if ($multiselect) {

 if (!preg_match('/^[^,]+(?:,[^,]+)*$/', $selected, $matches)) {
throw new InvalidArgumentException(sprintf($errorMessage, $selected));
}

$selectedChoices = array_map('trim', explode(',', $selected));
} else {
$selectedChoices = [trim($selected)];
}

$multiselectChoices = [];
foreach ($selectedChoices as $value) {
$results = [];
foreach ($choices as $key => $choice) {
if ($choice === $value) {
$results[] = $key;
}
}

if (\count($results) > 1) {
throw new InvalidArgumentException(sprintf('The provided answer is ambiguous. Value should be one of "%s".', implode('" or "', $results)));
}

$result = array_search($value, $choices);

if (!$isAssoc) {
if (false !== $result) {
$result = $choices[$result];
} elseif (isset($choices[$value])) {
$result = $choices[$value];
}
} elseif (false === $result && isset($choices[$value])) {
$result = $value;
}

if (false === $result) {
throw new InvalidArgumentException(sprintf($errorMessage, $value));
}

$multiselectChoices[] = (string) $result;
}

if ($multiselect) {
return $multiselectChoices;
}

return current($multiselectChoices);
};
}
}
