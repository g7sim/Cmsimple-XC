<?php

/**
 * Abstraction over the plugin (language) configuration.
 *
 * @category  CMSimple_XH
 * @package   XH
 * @author    The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @copyright 2017 The CMSimple_XH developers <http://cmsimple-xh.org/?The_Team>
 * @license   http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link      http://cmsimple-xh.org/
 */

namespace XH;

use ArrayAccess;

/**
 * Abstraction over the plugin (language) configuration.
 *
 * Instead of plain arrays, `$plugin_cf` and `$plugin_tx` are objects, which
 * allow for lazy loading of the configuration and language files, respectively.
 *
 * @category CMSimple_XH
 * @package  XH
 * @author   The CMSimple_XH developers <devs@cmsimple-xh.org>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GNU GPLv3
 * @link     http://cmsimple-xh.org/
 * @since    1.7.0
 */
class PluginConfig implements ArrayAccess
{
    /**
     * Whether this is a language configuration.
     *
     * @var bool
     */
    private $language;

    /**
     * The loaded plugin configurations.
     *
     * @var array
     */
    private $configs = array();

    /**
     * Initializes a new instance.
     *
     * @param bool $language Whether this is a language configuration.
     */
    public function __construct($language = false)
    {
        $this->language = $language;
    }

    /**
     * Returns whether an offset exists.
     *
     * @param mixed $offset An offset.
     *
     * @return bool
     */
#[\ReturnTypeWillChange]
    public function offsetExists($offset)
    {
        if (!isset($this->configs[$offset])) {
            $this->loadConfig($offset);
        }
        return isset($this->configs[$offset]);
    }

    /**
     * Returns the value at an offset.
     *
     * @param mixed $offset An offset.
     *
     * @return array
     */
#[\ReturnTypeWillChange]
    public function offsetGet($offset)
{
    if (!isset($this->configs[$offset])) {
        $this->loadConfig($offset);
    }
    if ($offset === 'register' && !isset($this->configs[$offset])) { // Add this check
        echo "<pre>Error: 'register' key not set before return.\n";
        echo "Called from:\n";
        debug_print_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS);
        echo "</pre>";
        }
    return $this->configs[$offset];   
}

    /**
     * Sets the value at an offset.
     *
     * @param mixed $offset An offset.
     * @param mixed $value  A value.
     *
     * @return void
     */
#[\ReturnTypeWillChange]
    public function offsetSet($offset, $value)
    {
        if (!isset($this->configs[$offset])) {
            $this->loadConfig($offset);
        }
        $this->configs[$offset] = $value;
    }

    /**
     * Unsets an offset.
     *
     * @param mixed $offset An offset.
     *
     * @return void
     */
#[\ReturnTypeWillChange]	 
    public function offsetUnset($offset)
    {
        if (!isset($this->configs[$offset])) {
            $this->loadConfig($offset);
        }
        unset($this->configs[$offset]);
    }

    /**
     * Loads the configuration.
     *
     * @param string $pluginname A plugin name.
     *
     * @return void
     */
    private function loadConfig($pluginname)   {
		 global $pth;
	 // --- Add this block for debugging 'register' ---
        if ($pluginname === 'register') {
        error_log("DEBUG: loadConfig called for offset = 'register'");

        // Construct the expected path (adjust if CMSimple_XH does it differently)
        $expectedPath = CMSIMPLE_ROOT . 'plugins/' . $pluginname . '/config/config.php';
        error_log("DEBUG: Expecting config file at: " . $expectedPath);
        error_log("DEBUG: File exists? " . (file_exists($expectedPath) ? 'Yes' : 'No'));
        error_log("DEBUG: File readable? " . (is_readable($expectedPath) ? 'Yes' : 'No'));

        // Check $this->configs BEFORE attempting load
         error_log("DEBUG: BEFORE load, isset(\$this->configs['register'])? " . (isset($this->configs['register']) ? 'Yes' : 'No'));
        }
	        
        pluginFiles($pluginname);
        if ($this->language) {
            XH_createLanguageFile($pth['file']['plugin_language']);
        }
        $this->configs += XH_readConfiguration(true, $this->language);
        }
	}
