<?php defined('SYSPATH') OR die('No direct access allowed.');
/**
 * HTML helper class.
 *
 * @package    HTML
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Kohana_HTML {

	/**
	 * @var  array  preferred order of attributes
	 */
	public static $attribute_order = array
	(
		'action',
		'method',
		'type',
		'id',
		'name',
		'value',
		'href',
		'src',
		'width',
		'height',
		'cols',
		'rows',
		'size',
		'maxlength',
		'rel',
		'media',
		'accept-charset',
		'accept',
		'tabindex',
		'accesskey',
		'alt',
		'title',
		'class',
		'style',
		'selected',
		'checked',
		'readonly',
		'disabled',
	);

	/**
	 * @var  boolean  automatically target external URLs to a new window
	 */
	public static $windowed_urls = FALSE;

	/**
	 * Convert special characters to HTML entities.
	 *
	 * @param   string   string to convert
	 * @param   boolean  encode existing entities
	 * @return  string
	 */
	public static function chars($value, $double_encode = TRUE)
	{
		return htmlspecialchars((string) $value, ENT_QUOTES, Kohana::$charset, $double_encode);
	}

	/**
	 * Create HTML link anchors. Note that the title is not escaped, to allow
	 * HTML elements within links (images, etc).
	 *
	 * @param   string  URL or URI string
	 * @param   string  link text
	 * @param   array   HTML anchor attributes
	 * @param   string  use a specific protocol
	 * @return  string
	 */
	public static function anchor($uri, $title = NULL, array $attributes = NULL, $protocol = NULL)
	{
		if ($title === NULL)
		{
			// Use the URI as the title
			$title = $uri;
		}

		if ($uri === '')
		{
			// Only use the base URL
			$uri = URL::base(FALSE, $protocol);
		}
		else
		{
			if (strpos($uri, '://') !== FALSE)
			{
				if (HTML::$windowed_urls === TRUE AND empty($attributes['target']))
				{
					// Make the link open in a new window
					$attributes['target'] = '_blank';
				}
			}
			elseif ($uri[0] !== '#')
			{
				// Make the URI absolute for non-id anchors
				$uri = URL::site($uri, $protocol);
			}
		}

		// Add the sanitized link to the attributes
		$attributes['href'] = $uri;

		return '<a'.HTML::attributes($attributes).'>'.$title.'</a>';
	}

	/**
	 * Creates an HTML anchor to a file. Note that the title is not escaped,
	 * to allow HTML elements within links (images, etc).
	 *
	 * @param   string  name of file to link to
	 * @param   string  link text
	 * @param   array   HTML anchor attributes
	 * @param   string  non-default protocol, eg: ftp
	 * @return  string
	 */
	public static function file_anchor($file, $title = NULL, array $attributes = NULL, $protocol = NULL)
	{
		if ($title === NULL)
		{
			// Use the file name as the title
			$title = $title;
		}

		// Add the file link to the attributes
		$attributes['href'] = URL::base(FALSE, $protocol).$file;

		return '<a'.HTML::attributes($attributes).'>'.$title.'</a>';

	}

	/**
	 * Generates an obfuscated version of an email address.
	 *
	 * @param   string  email address
	 * @return  string
	 */
	public static function email($email)
	{
		$safe = '';
		foreach (str_split($email) as $letter)
		{
			switch (rand(1, 3))
			{
				// HTML entity code
				case 1: $safe .= '&#'.ord($letter).';'; break;
				// Hex character code
				case 2: $safe .= '&#x'.dechex(ord($letter)).';'; break;
				// Raw (no) encoding
				case 3: $safe .= $letter;
			}
		}

		return $safe;
	}

	/**
	 * Creates an email anchor. Note that the title is not escaped, to allow
	 * HTML elements within links (images, etc).
	 *
	 * @param   string  email address to send to
	 * @param   string  link text
	 * @param   array   HTML anchor attributes
	 * @return  string
	 */
	public static function mailto($email, $title = NULL, array $attributes = NULL)
	{
		// Obfuscate email address
		$email = HTML::email($email);

		if ($title === NULL)
		{
			// Use the email address as the title
			$title = $email;
		}

		return '<a href="&#109;&#097;&#105;&#108;&#116;&#111;&#058;'.$email.'"'.HTML::attributes($attributes).'>'.$title.'</a>';
	}

	/**
	 * Creates a style sheet link.
	 *
	 * @param   string  file name
	 * @param   array   default attributes
	 * @param   boolean  include the index page
	 * @return  string
	 */
	public static function style($file, array $attributes = NULL, $index = FALSE)
	{
		if (strpos($file, '://') === FALSE)
		{
			// Add the base URL
			$file = URL::base($index).$file;
		}

		// Set the stylesheet link
		$attributes['href'] = $file;

		// Set the stylesheet rel
		$attributes['rel'] = 'stylesheet';

		// Set the stylesheet type
		$attributes['type'] = 'text/css';

		return '<link'.HTML::attributes($attributes).' />';
	}

	/**
	 * Creates a script link.
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @param   boolean  include the index page
	 * @return  string
	 */
	public static function script($file, array $attributes = NULL, $index = FALSE)
	{
		if (strpos($file, '://') === FALSE)
		{
			// Add the base URL
			$file = URL::base($index).$file;
		}

		// Set the script link
		$attributes['src'] = $file;

		// Set the script type
		$attributes['type'] = 'text/javascript';

		return '<script'.HTML::attributes($attributes).'></script>';
	}

	/**
	 * Creates a image link.
	 *
	 * @param   string   file name
	 * @param   array    default attributes
	 * @return  string
	 */
	public static function image($file, array $attributes = NULL)
	{
		if (strpos($file, '://') === FALSE)
		{
			// Add the base URL
			$file = URL::base(FALSE).$file;
		}

		// Add the image link
		$attributes['src'] = $file;

		return '<img'.HTML::attributes($attributes).' />';
	}

	/**
	 * Compiles an array of HTML attributes into an attribute string.
	 *
	 * @param   array   attribute list
	 * @return  string
	 */
	public static function attributes(array $attributes = NULL)
	{
		if (empty($attributes))
			return '';

		$sorted = array();
		foreach (HTML::$attribute_order as $key)
		{
			if (isset($attributes[$key]))
			{
				// Add the attribute to the sorted list
				$sorted[$key] = $attributes[$key];
			}
		}

		// Combine the sorted attributes
		$attributes = $sorted + $attributes;

		$compiled = '';
		foreach ($attributes as $key => $value)
		{
			if ($value === NULL)
			{
				// Skip attributes that have NULL values
				continue;
			}

			// Add the attribute value
			$compiled .= ' '.$key.'="'.htmlspecialchars($value, ENT_QUOTES, Kohana::$charset).'"';
		}

		return $compiled;
	}

	final private function __construct()
	{
		// This is a static class
	}

} // End html
