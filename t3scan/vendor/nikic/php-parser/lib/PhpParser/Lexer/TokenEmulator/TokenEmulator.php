<?php declare(strict_types=1);

namespace PhpParser\Lexer\TokenEmulator;

/**
@internal */
abstract class TokenEmulator
{
abstract public function getPhpVersion(): string;

abstract public function isEmulationNeeded(string $code): bool;

/**
@return
*/
abstract public function emulate(string $code, array $tokens): array;

/**
@return
*/
abstract public function reverseEmulate(string $code, array $tokens): array;

public function preprocessCode(string $code, array &$patches): string {
return $code;
}
}
