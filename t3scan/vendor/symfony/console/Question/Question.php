<?php










namespace Symfony\Component\Console\Question;

use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Exception\LogicException;

/**
@author


*/
class Question
{
private $question;
private $attempts;
private $hidden = false;
private $hiddenFallback = true;
private $autocompleterValues;
private $validator;
private $default;
private $normalizer;

/**
@param
@param
*/
public function __construct($question, $default = null)
{
$this->question = $question;
$this->default = $default;
}

/**
@return


*/
public function getQuestion()
{
return $this->question;
}

/**
@return


*/
public function getDefault()
{
return $this->default;
}

/**
@return


*/
public function isHidden()
{
return $this->hidden;
}

/**
@param
@return
@throws




*/
public function setHidden($hidden)
{
if ($this->autocompleterValues) {
throw new LogicException('A hidden question cannot use the autocompleter.');
}

$this->hidden = (bool) $hidden;

return $this;
}

/**
@return


*/
public function isHiddenFallback()
{
return $this->hiddenFallback;
}

/**
@param
@return



*/
public function setHiddenFallback($fallback)
{
$this->hiddenFallback = (bool) $fallback;

return $this;
}

/**
@return


*/
public function getAutocompleterValues()
{
return $this->autocompleterValues;
}

/**
@param
@return
@throws
@throws




*/
public function setAutocompleterValues($values)
{
if (\is_array($values)) {
$values = $this->isAssoc($values) ? array_merge(array_keys($values), array_values($values)) : array_values($values);
}

if (null !== $values && !\is_array($values) && !$values instanceof \Traversable) {
throw new InvalidArgumentException('Autocompleter values can be either an array, `null` or a `Traversable` object.');
}

if ($this->hidden) {
throw new LogicException('A hidden question cannot use the autocompleter.');
}

$this->autocompleterValues = $values;

return $this;
}

/**
@return


*/
public function setValidator(callable $validator = null)
{
$this->validator = $validator;

return $this;
}

/**
@return


*/
public function getValidator()
{
return $this->validator;
}

/**
@param
@return
@throws






*/
public function setMaxAttempts($attempts)
{
if (null !== $attempts) {
$attempts = (int) $attempts;
if ($attempts < 1) {
throw new InvalidArgumentException('Maximum number of attempts must be a positive value.');
}
}

$this->attempts = $attempts;

return $this;
}

/**
@return




*/
public function getMaxAttempts()
{
return $this->attempts;
}

/**
@return




*/
public function setNormalizer(callable $normalizer)
{
$this->normalizer = $normalizer;

return $this;
}

/**
@return




*/
public function getNormalizer()
{
return $this->normalizer;
}

protected function isAssoc($array)
{
return (bool) \count(array_filter(array_keys($array), 'is_string'));
}
}
