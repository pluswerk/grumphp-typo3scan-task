<?php










namespace Twig\Extension;

use Twig\NodeVisitor\NodeVisitorInterface;
use Twig\TokenParser\TokenParserInterface;
use Twig\TwigFilter;
use Twig\TwigFunction;
use Twig\TwigTest;

/**
@author


*/
interface ExtensionInterface
{
/**
@return


*/
public function getTokenParsers();

/**
@return


*/
public function getNodeVisitors();

/**
@return


*/
public function getFilters();

/**
@return


*/
public function getTests();

/**
@return


*/
public function getFunctions();

/**
@return


*/
public function getOperators();
}

class_alias('Twig\Extension\ExtensionInterface', 'Twig_ExtensionInterface');


class_exists('Twig\Environment');
