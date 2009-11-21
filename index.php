<?php

$core   = 's7ncms';
$config = 'config';
$themes = 'themes';

/**
 * The directory in which your application specific resources are located.
 * The application directory must contain the config/kohana.php file.
 *
 * @see  http://docs.kohanaphp.com/install#application
 */
$application = 'application';

/**
 * The directory in which your modules are located.
 *
 * @see  http://docs.kohanaphp.com/install#modules
 */
$modules = 'modules';

/**
 * The directory in which the Kohana resources are located. The system
 * directory must contain the classes/kohana.php file.
 *
 * @see  http://docs.kohanaphp.com/install#system
 */
$system = 'system';

/**
 * The default extension of resource files. If you change this, all resources
 * must be renamed to use the new extension.
 *
 * @see  http://docs.kohanaphp.com/install#ext
 */
define('EXT', '.php');

/**
* Set the PHP error reporting level. If you set this in php.ini, you remove this.
* @see http://php.net/error_reporting
*
* When developing your application, it is highly recommended to enable notices
* and strict warnings. Enable them by using: E_ALL | E_STRICT
*
* In a production environment, it is safe to ignore notices and strict warnings.
* Disable them by using: E_ALL ^ E_NOTICE
*
* When using a legacy application with PHP >= 5.3, it is recommended to disable
* deprecated notices. Disable with: E_ALL & ~E_DEPRECATED
*/
defined('E_DEPRECATED') OR define('E_DEPRECATED', 8192);
error_reporting(E_ALL | E_STRICT);
ini_set('display_errors', TRUE);

/**
 * End of standard configuration! Changing any of the code below should only be
 * attempted by those with a working knowledge of Kohana internals.
 *
 * @see  http://docs.kohanaphp.com/bootstrap
 */

// Set the full path to the docroot
define('DOCROOT', realpath(dirname(__FILE__)).DIRECTORY_SEPARATOR);

// Make the application relative to the docroot
if ( ! is_dir($application) AND is_dir(DOCROOT.$application))
	$application = DOCROOT.$application;

// Make the modules relative to the docroot
if ( ! is_dir($modules) AND is_dir(DOCROOT.$modules))
	$modules = DOCROOT.$modules;

// Make the system relative to the docroot
if ( ! is_dir($system) AND is_dir(DOCROOT.$system))
	$system = DOCROOT.$system;

// Make the core relative to the docroot
if ( ! is_dir($core) AND is_dir(DOCROOT.$core))
	$core = DOCROOT.$core;

// Make the config relative to the docroot
if ( ! is_dir($config) AND is_dir(DOCROOT.$config))
	$config = DOCROOT.$config;

// Make the themes relative to the docroot
if ( ! is_dir($themes) AND is_dir(DOCROOT.$themes))
	$themes = DOCROOT.$themes;

// Define the absolute paths for configured directories
define('APPPATH', realpath($application).DIRECTORY_SEPARATOR);
define('MODPATH', realpath($modules).DIRECTORY_SEPARATOR);
define('SYSPATH', realpath($system).DIRECTORY_SEPARATOR);
define('COREPATH', realpath($core).DIRECTORY_SEPARATOR);
define('CONFIGPATH', realpath($config).DIRECTORY_SEPARATOR);
define('THEMESPATH', realpath($themes).DIRECTORY_SEPARATOR);

// Clean up the configuration vars
unset($application, $modules, $system, $core, $config, $themes);

// Define the start time of the application
define('KOHANA_START_TIME', microtime(TRUE));

// Define the memory usage at the start of the application
define('KOHANA_START_MEMORY', memory_get_usage());

// Load the base, low-level functions
require SYSPATH.'base'.EXT;

// Load the core Kohana class
require SYSPATH.'classes/kohana/core'.EXT;

if (is_file(APPPATH.'classes/kohana'.EXT))
{
	// Application extends the core
	require APPPATH.'classes/kohana'.EXT;
}
else
{
	// Load empty core extension
	require SYSPATH.'classes/kohana'.EXT;
}

// Bootstrap the application
require APPPATH.'bootstrap'.EXT;
