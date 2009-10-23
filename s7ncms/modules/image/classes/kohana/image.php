<?php defined('SYSPATH') or die('No direct script access.');
/**
 * Image manipulation abstract class.
 *
 * @package    Image
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
abstract class Kohana_Image {

	// Resizing contraints
	const NONE    = 0x01;
	const WIDTH   = 0x02;
	const HEIGHT  = 0x03;
	const AUTO    = 0x04;
	const INVERSE = 0x05;

	// Flipping directions
	const HORIZONTAL = 0x11;
	const VERTICAL   = 0x12;

	/**
	 * @var  string  default driver: GD, ImageMagick, etc
	 */
	public static $default_driver = 'GD';

	// Status of the driver check
	protected static $_checked = FALSE;

	/**
	 * Creates an image wrapper.
	 *
	 * @param   string   image file path
	 * @param   string   driver type: GD, ImageMagick, etc
	 * @return  Image
	 */
	public static function factory($file, $driver = NULL)
	{
		if ($driver === NULL)
		{
			// Use the default driver
			$driver = Image::$default_driver;
		}

		// Set the class name
		$class = 'Image_'.$driver;

		return new $class($file);
	}

	/**
	 * @var  string  image file path
	 */
	public $file;

	/**
	 * @var  integer  image width
	 */
	public $width;

	/**
	 * @var  integer  image height
	 */
	public $height;

	/**
	 * @var  integer  one of the IMAGETYPE_* constants
	 */
	public $type;

	/**
	 * Loads information about the image.
	 *
	 * @throws  Kohana_Exception
	 * @param   string   image file path
	 * @return  void
	 */
	public function __construct($file)
	{
		try
		{
			// Get the real path to the file
			$file = realpath($file);

			// Get the image information
			$info = getimagesize($file);
		}
		catch (Exception $e)
		{
			// Ignore all errors while reading the image
		}

		if (empty($file) OR empty($info))
		{
			throw new Kohana_Exception('Not an image or invalid image: :file',
				array(':file' => Kohana::debug_path($file)));
		}

		// Store the image information
		$this->file   = $file;
		$this->width  = $info[0];
		$this->height = $info[1];
		$this->type   = $info[2];
		$this->mime   = image_type_to_mime_type($this->type);
	}

	/**
	 * Render the current image.
	 *
	 * The output of this function is binary and must be rendered with the
	 * appropriate Content-Type header or it will not be displayed correctly!
	 *
	 * @return  string
	 */
	public function __toString()
	{
		try
		{
			// Render the current image
			return $this->render();
		}
		catch (Exception $e)
		{
			if (is_object(Kohana::$log))
			{
				// Get the text of the exception
				$error = Kohana::exception_text($e);

				// Add this exception to the log
				Kohana::$log->add(Kohana::ERROR, $error);
			}

			// Showing any kind of error will be "inside" image data
			return '';
		}
	}

	/**
	 * Resize the image to the given size. Either the width or the height can
	 * be omitted and the image will be resized proportionally.
	 *
	 * @param   integer  new width
	 * @param   integer  new height
	 * @param   integer  master dimension
	 * @return  $this
	 */
	public function resize($width = NULL, $height = NULL, $master = NULL)
	{
		if ($master === NULL)
		{
			// Choose the master dimension automatically
			$master = Image::AUTO;
		}
		elseif ($master === Image::INVERSE)
		{
			if ($this->width === $this->height)
			{
				// Automatically choose the master dimension
				$master = Image::AUTO;
			}
			elseif ($this->width > $this->height)
			{
				// Keep the image from becoming too short
				$master = Image::HEIGHT;
			}
			else
			{
				// Keep the image from becoming too wide
				$master = Image::WIDTH;
			}
		}

		if (empty($width))
		{
			if ($master === Image::NONE)
			{
				// Use the current width
				$width = $this->width;
			}
			else
			{
				// Recalculate the width based on the height proportions
				// This must be done before the automatic master check
				$width = $this->width * $height / $this->height;
			}
		}

		if (empty($height))
		{
			if ($master === Image::NONE)
			{
				// Use the current height
				$height = $this->height;
			}
			else
			{
				// Recalculate the height based on the width
				// This must be done before the automatic master check
				$height = $this->height * $width / $this->width;
			}
		}

		if ($master === Image::AUTO)
		{
			// Choose direction with the greatest reduction ratio
			$master = ($this->width / $width) > ($this->height / $height) ? Image::WIDTH : Image::HEIGHT;
		}

		switch ($master)
		{
			case Image::WIDTH:
				// Proportionally set the height
				$height = $this->height * $width / $this->width;
			break;
			case Image::HEIGHT:
				// Proportionally set the width
				$width = $this->width * $height / $this->height;
			break;
		}

		// Convert the width and height to integers
		$width  = round($width);
		$height = round($height);

		$this->_do_resize($width, $height);

		return $this;
	}

	/**
	 * Crop an image to the given size. Either the width or the height can be
	 * omitted and the current width or height will be used.
	 *
	 * If no offset is specified, the center of the axis will be used.
	 * If an offset of TRUE is specified, the bottom of the axis will be used.
	 *
	 * @param   integer  new width
	 * @param   integer  new height
	 * @param   mixed    offset from the left
	 * @param   mixed    offset from the top
	 * @return  $this
	 */
	public function crop($width, $height, $offset_x = NULL, $offset_y = NULL)
	{
		if ($width > $this->width)
		{
			// Use the current width
			$width = $this->width;
		}

		if ($height > $this->height)
		{
			// Use the current height
			$height = $this->height;
		}

		if ($offset_x === NULL)
		{
			// Center the X offset
			$offset_x = round(($this->width - $width) / 2);
		}
		elseif ($offset_x === TRUE)
		{
			// Bottom the X offset
			$offset_x = $this->width - $width;
		}
		elseif ($offset_x < 0)
		{
			// Set the X offset from the right
			$offset_x = $this->width - $width + $offset_x;
		}

		if ($offset_y === NULL)
		{
			// Center the Y offset
			$offset_y = round(($this->height - $height) / 2);
		}
		elseif ($offset_y === TRUE)
		{
			// Bottom the Y offset
			$offset_y = $this->height - $height;
		}
		elseif ($offset_y < 0)
		{
			// Set the Y offset from the bottom
			$offset_y = $this->height - $height + $offset_y;
		}

		// Determine the maximum possible width and height
		$max_width  = $this->width  - $offset_x;
		$max_height = $this->height - $offset_y;

		if ($width > $max_width)
		{
			// Use the maximum available width
			$width = $max_width;
		}

		if ($height > $max_height)
		{
			// Use the maximum available height
			$height = $max_height;
		}

		$this->_do_crop($width, $height, $offset_x, $offset_y);

		return $this;
	}

	/**
	 * Rotate the image.
	 *
	 * @param   integer   degrees to rotate: -360-360
	 * @return  $this
	 */
	public function rotate($degrees)
	{
		// Make the degrees an integer
		$degrees = (int) $degrees;

		if ($degrees > 180)
		{
			do
			{
				// Keep subtracting full circles until the degrees have normalized
				$degrees -= 360;
			}
			while($degrees > 180);
		}

		if ($degrees < -180)
		{
			do
			{
				// Keep adding full circles until the degrees have normalized
				$degrees += 360;
			}
			while($degrees < -180);
		}

		$this->_do_rotate($degrees);

		return $this;
	}

	/**
	 * Flip the image along the horizontal or vertical axis.
	 *
	 * @param   integer  direction: Image::HORIZONTAL, Image::VERTICAL
	 * @return  $this
	 */
	public function flip($direction)
	{
		if ($direction !== Image::HORIZONTAL)
		{
			// Flip vertically
			$direction = Image::VERTICAL;
		}

		$this->_do_flip($direction);

		return $this;
	}

	/**
	 * Sharpen the image.
	 *
	 * @param   integer  amount to sharpen: 1-100
	 * @return  $this
	 */
	public function sharpen($amount)
	{
		// The amount must be in the range of 1 to 100
		$amount = min(max($amount, 1), 100);

		$this->_do_sharpen($amount);

		return $this;
	}

	/**
	 * Add a reflection to an image. The most opaque part of the reflection
	 * will be equal to the opacity setting and fade out to full transparent.
	 * By default, the reflection will be most transparent at the
	 *
	 * @param   integer   reflection height
	 * @param   integer   reflection opacity: 0-100
	 * @param   boolean   TRUE to fade in, FALSE to fade out
	 * @return  $this
	 */
	public function reflection($height = NULL, $opacity = 100, $fade_in = FALSE)
	{
		if ($height === NULL OR $height > $this->height)
		{
			// Use the current height
			$height = $this->height;
		}

		$this->_do_reflection($height, $opacity, $fade_in);

		return $this;
	}

	/**
	 * Add a watermark to an image with a specified opacity.
	 *
	 * If no offset is specified, the center of the axis will be used.
	 * If an offset of TRUE is specified, the bottom of the axis will be used.
	 *
	 * @param   object   watermark Image instance
	 * @param   integer  offset from the left
	 * @param   integer  offset from the top
	 * @param   integer  opacity of watermark
	 * @return  $this
	 */
	public function watermark(Image $watermark, $offset_x = NULL, $offset_y = NULL, $opacity = 100)
	{
		if ($offset_x === NULL)
		{
			// Center the X offset
			$offset_x = round(($this->width - $watermark->width) / 2);
		}
		elseif ($offset_x === TRUE)
		{
			// Bottom the X offset
			$offset_x = $this->width - $watermark->width;
		}
		elseif ($offset_x < 0)
		{
			// Set the X offset from the right
			$offset_x = $this->width - $watermark->width + $offset_x;
		}

		if ($offset_y === NULL)
		{
			// Center the Y offset
			$offset_y = round(($this->height - $watermark->height) / 2);
		}
		elseif ($offset_y === TRUE)
		{
			// Bottom the Y offset
			$offset_y = $this->height - $watermark->height;
		}
		elseif ($offset_y < 0)
		{
			// Set the Y offset from the bottom
			$offset_y = $this->height - $watermark->height + $offset_y;
		}

		// The opacity must be in the range of 1 to 100
		$opacity = min(max($opacity, 1), 100);

		$this->_do_watermark($watermark, $offset_x, $offset_y, $opacity);

		return $this;
	}

	/**
	 * Set the background color of an image.
	 *
	 * @param   string   hexadecimal color value
	 * @param   integer  background opacity: 0-100
	 * @return  $this
	 */
	public function background($color, $opacity = 100)
	{
		if ($color[0] === '#')
		{
			// Remove the pound
			$color = substr($color, 1);
		}

		if (strlen($color) === 3)
		{
			// Convert shorthand into longhand hex notation
			$color = preg_replace('/./', '$0$0', $color);
		}

		// Convert the hex into RGB values
		list ($r, $g, $b) = array_map('hexdec', str_split($color, 2));

		$this->_do_background($r, $g, $b, $opacity);

		return $this;
	}

	/**
	 * Save the image. If the filename is omitted, the original image will
	 * be overwritten.
	 *
	 * @param   string   new image path
	 * @param   integer  quality of image: 1-100
	 * @return  boolean
	 */
	public function save($file = NULL, $quality = 100)
	{
		if ($file === NULL)
		{
			// Overwrite the file
			$file = $this->file;
		}

		if (is_file($file))
		{
			if ( ! is_writable($file))
			{
				throw new Kohana_Exception('File must be writable: :file',
					array(':file' => Kohana::debug_path($file)));
			}
		}
		else
		{
			// Get the directory of the file
			$directory = realpath(pathinfo($file, PATHINFO_DIRNAME));

			if ( ! is_dir($directory) OR ! is_writable($directory))
			{
				throw new Kohana_Exception('Directory must be writable: :directory',
					array(':directory' => Kohana::debug_path($directory)));
			}
		}

		return $this->_do_save($file, $quality);
	}

	/**
	 * Render the image and return the data.
	 *
	 * @param   string   image type to return: png, jpg, gif, etc
	 * @param   integer  quality of image: 1-100
	 * @return  string
	 */
	public function render($type = NULL, $quality = 100)
	{
		if ($type === NULL)
		{
			// Use the current image type
			$type = image_type_to_extension($this->type, FALSE);
		}

		return $this->_do_render($type, $quality);
	}

	/**
	 * Execute a resize.
	 *
	 * @param   integer  new width
	 * @param   integer  new height
	 * @return  void
	 */
	abstract protected function _do_resize($width, $height);

	/**
	 * Execute a crop.
	 *
	 * @param   integer  new width
	 * @param   integer  new height
	 * @param   integer  offset from the left
	 * @param   integer  offset from the top
	 * @return  void
	 */
	abstract protected function _do_crop($width, $height, $offset_x, $offset_y);

	/**
	 * Execute a rotation.
	 *
	 * @param   integer  degrees to rotate
	 * @return  void
	 */
	abstract protected function _do_rotate($degrees);

	/**
	 * Execute a flip.
	 *
	 * @param   integer  direction to flip
	 * @return  void
	 */
	abstract protected function _do_flip($direction);

	/**
	 * Execute a sharpen.
	 *
	 * @param   integer  amount to sharpen
	 * @return  void
	 */
	abstract protected function _do_sharpen($amount);

	/**
	 * Execute a reflection.
	 *
	 * @param   integer   reflection height
	 * @param   integer   reflection opacity
	 * @param   boolean   TRUE to fade out, FALSE to fade in
	 * @return  void
	 */
	abstract protected function _do_reflection($height, $opacity, $fade_in);

	/**
	 * Execute a watermarking.
	 *
	 * @param   object   watermarking Image
	 * @param   integer  offset from the left
	 * @param   integer  offset from the top
	 * @param   integer  opacity of watermark
	 * @return  void
	 */
	abstract protected function _do_watermark(Image $image, $offset_x, $offset_y, $opacity);

	/**
	 * Execute a background.
	 *
	 * @param   integer  red
	 * @param   integer  green
	 * @param   integer  blue
	 * @param   integer  opacity
	 * @return void
	 */
	abstract protected function _do_background($r, $g, $b, $opacity);

	/**
	 * Execute a save.
	 *
	 * @param   string   new image filename
	 * @param   integer  quality
	 * @return  boolean
	 */
	abstract protected function _do_save($file, $quality);

	/**
	 * Execute a render.
	 *
	 * @param   string    image type: png, jpg, gif, etc
	 * @param   integer   quality
	 * @return  string
	 */
	abstract protected function _do_render($type, $quality);

} // End Image
