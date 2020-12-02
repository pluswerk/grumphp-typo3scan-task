<?php

namespace MichielRoos\TYPO3Scan\Console;


use Symfony\Component\Console\Application as BaseApplication;

/**
 * @package
 */
class Application extends BaseApplication
{
    /**
     * @var
     */
    private static $logo = "   ________  ______  ____ _____
  /_  __/\ \/ / __ \/ __ \__  /   ______________ _____
   / /    \  / /_/ / / / //_ <   / ___/ ___/ __ `/ __ \
  / /     / / ____/ /_/ /__/ /  (__  ) /__/ /_/ / / / /
 /_/     /_/_/    \____/____/  /____/\___/\__,_/_/ /_/

        https://github.com/tuurlijk/typo3scan

          Hand coded with %s️ by Michiel Roos 

";

    /**
     * @return
     */
    public function getHelp()
    {
        $love = $this->isColorSupported() ? "\e[31m♥\e[0m" : "♥";
        return sprintf(self::$logo, $love) . parent::getHelp();
    }

    /**
     * @return
     */
    private function isColorSupported()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return getenv('ANSICON') !== false || getenv('ConEmuANSI') === 'ON';
        }
        return \function_exists('posix_isatty') && @posix_isatty(STDOUT);
    }
}
