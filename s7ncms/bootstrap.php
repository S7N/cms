<?php defined('SYSPATH') or die('No direct script access.');

//-- Environment setup --------------------------------------------------------

/**
 * Set the default time zone.
 *
 * @see  http://docs.kohanaphp.com/about.configuration
 * @see  http://php.net/timezones
 */
date_default_timezone_set('Europe/Berlin');

/**
* Set the default locale.
*
* @see http://docs.kohanaphp.com/about.configuration
* @see http://php.net/setlocale
*/
setlocale(LC_ALL, 'de_DE.utf-8');

/**
 * Enable the Kohana auto-loader.
 *
 * @see  http://docs.kohanaphp.com/about.autoloading
 * @see  http://php.net/spl_autoload_register
 */
spl_autoload_register(array('Kohana', 'auto_load'));

/**
 * Enable the Kohana auto-loader for unserialization.
 *
 * @see  http://php.net/spl_autoload_call
 * @see  http://php.net/manual/var.configuration.php#unserialize-callback-func
 */
ini_set('unserialize_callback_func', 'spl_autoload_call');

/**
* Set the production status
*/
define('IN_PRODUCTION', FALSE);

//-- Configuration and initialization -----------------------------------------

/**
 * Initialize Kohana, setting the default options.
 *
 * The following options are available:
 *
 * - string   base_url    path, and optionally domain, of your application   NULL
 * - string   index_file  name of your index file, usually "index.php"       index.php
 * - string   charset     internal character set used for input and output   utf-8
 * - string   cache_dir   set the internal cache directory                   APPPATH/cache
 * - boolean  errors      enable or disable error handling                   TRUE
 * - boolean  profile     enable or disable internal profiling               TRUE
 * - boolean  caching     enable or disable internal caching                 FALSE
 */
Kohana::init(array(
	'base_url' => dirname($_SERVER['SCRIPT_NAME']),
	'index_file' => 'index.php',
	'cache_dir' => CONFIGPATH.'cache',
	'caching' => FALSE,
	'profile' => FALSE
));

/**
 * Attach the file write to logging. Multiple writers are supported.
 */
Kohana::$log->attach(new Kohana_Log_File(CONFIGPATH.'logs'));

/**
 * Attach a file reader to config. Multiple readers are supported.
 */
Kohana::$config->attach(new Kohana_Config_File);

/**
 * Enable modules. Modules are referenced by a relative or absolute path.
 */
Kohana::modules(array('s7ncms' => COREPATH));

/**
 * Execute the main request. A source of the URI can be passed, eg: $_SERVER['PATH_INFO'].
 * If no source is specified, the URI will be automatically detected.
 */
$request = Request::instance();

try {
	$request->execute();
}
catch (S7N_Exception_403 $e)
{
	$request = Request::factory('error/403')->execute();
	$request->status = 403;
}
catch (S7N_Exception_404 $e)
{
	$request = Request::factory('error/404')->execute();
	$request->status = 404;
}
catch (ReflectionException $e)
{
	$request = Request::factory('error/404')->execute();
	$request->status = 404;
}
catch (Exception $e)
{
	if ( ! IN_PRODUCTION)
	{
		throw $e;
	}

	$request = Request::factory('error/500')->execute();
	$request->status = 500;

	Kohana::$log->add(Kohana::ERROR, Kohana::exception_text($e));
}

echo $request->send_headers()->response;
