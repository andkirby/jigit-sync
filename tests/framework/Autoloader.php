<?php
/**
 * Class for autoload classes
 */
class Autoloader
{
    /**
     * Exception loader code
     */
    const EXCEPTION_CODE = 564;

    /**
     * Aliases list
     *
     * @var array
     */
    protected static $_aliases = array();

    /**
     * Load class
     *
     * @param string $class
     * @throws Exception
     */
    static public function autoload($class)
    {
        if (strpos($class, '_')) {
            $class = str_replace('_', '/', $class);
        }
        $file = str_replace('\\', '/', $class) . '.php';
        $file = self::_applyAliases($file);
        if (self::_isExist($file)) {
            include_once $file;
        } else {
            throw new \Exception(
                'Could not load file "' . $file . '" in include path: ' .
                get_include_path(), self::EXCEPTION_CODE
            );
        }
    }

    /**
     * Check does class file
     *
     * @param string $file
     * @return bool
     */
    static protected function _isExist($file)
    {
        foreach (explode(PATH_SEPARATOR, get_include_path()) as $path) {
            $path = str_replace('\\', '/', rtrim($path, '\\/'));
            if (is_file($path . '/' . $file)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Register autoloader
     */
    static public function register()
    {
        spl_autoload_register(__NAMESPACE__ . '\Autoloader::autoload');
    }

    /**
     * Folder load alias
     *
     * @param string $name
     * @param string $alias
     */
    public static function addAlias($name, $alias)
    {
        /**
         * Add "|" to make search from start
         */
        self::$_aliases['|' . $name] = $alias;
    }

    /**
     * Apply aliases
     *
     * @param string $file
     * @return string
     */
    protected static function _applyAliases($file)
    {
        $fileNew = str_replace(array_keys(self::$_aliases), self::$_aliases, '|' . $file);
        if ('|' !== $fileNew[0]) {
            //return changed path to file
            $file = $fileNew;
        }
        return $file;
    }
}
