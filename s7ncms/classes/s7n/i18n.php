<?php defined('SYSPATH') or die('No direct script access.');
/**
 * S7Ncms - Open Source Content Management
 * Copyright (c) 2007-2010, Eduard Baun <eduard at baun.de>
 * All rights reserved.
 *
 * See license.txt for full text and disclaimer
 */

class S7N_I18n extends Kohana_I18n {

	/**
	 * Returns translation of a plural string.
	 *
	 * @param   string   text to translate
	 * @return  string
	 */
	public static function get_plural($string, $count)
	{
		if ( ! isset(I18n::$_cache[I18n::$lang]))
		{
			// Load the translation table
			I18n::load(I18n::$lang);
		}

		$key = I18n::get_plural_key(I18n::$lang, $count);

		// Return the translated string if it exists
		return isset(I18n::$_cache[I18n::$lang][$string][$key]) ? I18n::$_cache[I18n::$lang][$string][$key] : $string;
	}

	/**
	 * This method is borrowed from the Gallery3 code:
	 * http://github.com/gallery/gallery3/blob/a10063ff68cf5988297dcad889384ab2080c3850/modules/gallery/libraries/I18n.php
	 *
	 * Gallery - a web based photo album viewer and editor
	 * Copyright (C) 2000-2009 Bharat Mediratta
	 *
	 * This program is free software; you can redistribute it and/or modify
	 * it under the terms of the GNU General Public License as published by
	 * the Free Software Foundation; either version 2 of the License, or (at
	 * your option) any later version.
	 *
	 * This program is distributed in the hope that it will be useful, but
	 * WITHOUT ANY WARRANTY; without even the implied warranty of
	 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU
	 * General Public License for more details.
	 *
	 * You should have received a copy of the GNU General Public License
	 * along with this program; if not, write to the Free Software
	 * Foundation, Inc., 51 Franklin Street - Fifth Floor, Boston, MA  02110-1301, USA.
	 */
	private static function get_plural_key($locale, $count) {
		$language = substr($locale, 0, 2);

		// Data from CLDR 1.6 (http://unicode.org/cldr/data/common/supplemental/plurals.xml).
		// Docs: http://www.unicode.org/cldr/data/charts/supplemental/language_plural_rules.html
		switch ($language) {
			case 'az':
			case 'fa':
			case 'hu':
			case 'ja':
			case 'ko':
			case 'my':
			case 'to':
			case 'tr':
			case 'vi':
			case 'yo':
			case 'zh':
			case 'bo':
			case 'dz':
			case 'id':
			case 'jv':
			case 'ka':
			case 'km':
			case 'kn':
			case 'ms':
			case 'th':
				return 'other';

			case 'ar':
				if ($count == 0) {
					return 'zero';
				} else if ($count == 1) {
					return 'one';
				} else if ($count == 2) {
					return 'two';
				} else if (is_int($count) && ($i = $count % 100) >= 3 && $i <= 10) {
					return 'few';
				} else if (is_int($count) && ($i = $count % 100) >= 11 && $i <= 99) {
					return 'many';
				} else {
					return 'other';
				}

			case 'pt':
			case 'am':
			case 'bh':
			case 'fil':
			case 'tl':
			case 'guw':
			case 'hi':
			case 'ln':
			case 'mg':
			case 'nso':
			case 'ti':
			case 'wa':
				if ($count == 0 || $count == 1) {
					return 'one';
				} else {
					return 'other';
				}

			case 'fr':
				if ($count >= 0 and $count < 2) {
					return 'one';
				} else {
					return 'other';
				}

			case 'lv':
				if ($count == 0) {
					return 'zero';
				} else if ($count % 10 == 1 && $count % 100 != 11) {
					return 'one';
				} else {
					return 'other';
				}

			case 'ga':
			case 'se':
			case 'sma':
			case 'smi':
			case 'smj':
			case 'smn':
			case 'sms':
				if ($count == 1) {
					return 'one';
				} else if ($count == 2) {
					return 'two';
				} else {
					return 'other';
				}

			case 'ro':
			case 'mo':
				if ($count == 1) {
					return 'one';
				} else if (is_int($count) && $count == 0 && ($i = $count % 100) >= 1 && $i <= 19) {
					return 'few';
				} else {
					return 'other';
				}

			case 'lt':
				if (is_int($count) && $count % 10 == 1 && $count % 100 != 11) {
					return 'one';
				} else if (is_int($count) && ($i = $count % 10) >= 2 && $i <= 9 && ($i = $count % 100) < 11 && $i > 19) {
					return 'few';
				} else {
					return 'other';
				}

			case 'hr':
			case 'ru':
			case 'sr':
			case 'uk':
			case 'be':
			case 'bs':
			case 'sh':
				if (is_int($count) && $count % 10 == 1 && $count % 100 != 11) {
					return 'one';
				} else if (is_int($count) && ($i = $count % 10) >= 2 && $i <= 4 && ($i = $count % 100) < 12 && $i > 14) {
					return 'few';
				} else if (is_int($count) && ($count % 10 == 0 || (($i = $count % 10) >= 5 && $i <= 9) || (($i = $count % 100) >= 11 && $i <= 14))) {
					return 'many';
				} else {
					return 'other';
				}

			case 'cs':
			case 'sk':
				if ($count == 1) {
					return 'one';
				} else if (is_int($count) && $count >= 2 && $count <= 4) {
					return 'few';
				} else {
					return 'other';
				}

			case 'pl':
				if ($count == 1) {
					return 'one';
				} else if (is_int($count) && ($i = $count % 10) >= 2 && $i <= 4 &&
				($i = $count % 100) < 12 && $i > 14 && ($i = $count % 100) < 22 && $i > 24) {
					return 'few';
				} else {
					return 'other';
				}

			case 'sl':
				if ($count % 100 == 1) {
					return 'one';
				} else if ($count % 100 == 2) {
					return 'two';
				} else if (is_int($count) && ($i = $count % 100) >= 3 && $i <= 4) {
					return 'few';
				} else {
					return 'other';
				}

			case 'mt':
				if ($count == 1) {
					return 'one';
				} else if ($count == 0 || is_int($count) && ($i = $count % 100) >= 2 && $i <= 10) {
					return 'few';
				} else if (is_int($count) && ($i = $count % 100) >= 11 && $i <= 19) {
					return 'many';
				} else {
					return 'other';
				}

			case 'mk':
				if ($count % 10 == 1) {
					return 'one';
				} else {
					return 'other';
				}

			case 'cy':
				if ($count == 1) {
					return 'one';
				} else if ($count == 2) {
					return 'two';
				} else if ($count == 8 || $count == 11) {
					return 'many';
				} else {
					return 'other';
				}

			default: // en, de, etc.
				return $count == 1 ? 'one' : 'other';
		}
	}

} // End i18n

/**
 *
 * @param $singular
 * @param $plural
 * @param $count
 * @param $values
 * @param $lang
 * @return unknown_type
 */
function __n($singular, $plural, $count, array $values = array(), $lang = 'en-us')
{
	if ($lang !== I18n::$lang)
	{
		// The message and target languages are different
		// Get the translation for this message
		$string = $count === 1 ? I18n::get($singular) : I18n::get_plural($plural, $count);
	}
	else
	{
		$string = $count === 1 ? $singular : $plural;
	}

	return strtr($string, array_merge($values, array(':count' => $count)));
}