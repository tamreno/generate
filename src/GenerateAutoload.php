<?php

/**
 * Internal autoloader for spl_autoload_register().
 *
 * @param string $class
 */
function tamrenoGenerateAutoloader($class)
{
    // Don't interfere with other autoloaders
    if (0 !== strpos($class, 'Generate_')) {
        return;
    }

    $path = __DIR__.'/'.str_replace('_', '/', $class).'.php';

    if (!file_exists($path)) {
        return;
    }

    require $path;
}
spl_autoload_register('tamrenoGenerateAutoloader');
