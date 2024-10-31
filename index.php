<?php
/*
Plugin Name: Remove exif and metadata
Description: Automatically remove exif and metadata data after uploading. Just moment supported format: JPG and PNG. Using ImageMagick
Author: Edgar Kotov
Version: 1.0
Text Domain:  Remove exif and metadata

This program is free software; you can redistribute it and/or
modify it under the terms of the GNU General Public License
as published by the Free Software Foundation; either version 2
of the License, or (at your option) any later version.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.

Copyright (C) 2016 Edgar Kotov
*/

if (!defined('ABSPATH'))
	die('Wrong directly');

define( 'REMOVE_EXIF_METADATA_VERSION', '1.0' );

/* init */
if (is_admin())
	add_action( 'plugins_loaded', array( 'Exif', 'init' ) );

class Exif
{

	public function init()
	{
		if (!function_exists('wp_handle_upload'))
			require_once( ABSPATH . 'wp-admin/includes/file.php' );

		/* Apply new modifications */
		add_action('wp_handle_upload', array('Exif', 'setExtension'));
	}

	public function setExtension($array)
	{

		if ( empty($array['file']))
			return false;

		$fileInfo = pathinfo($array['file']);
		$filePath = $fileInfo['dirname'] . '/'.$fileInfo['basename'];
		switch ($fileInfo['extension']) {
			case 'jpg':
				$array['file'] = self::removeExif($filePath, 'jpg');
				break;
			case 'png':
				$array['file'] = self::removeExif($filePath, 'png');
				break;
		}

		return $array;
	}

	private function removeExif($imagePath, $type)
	{
		if (empty($imagePath) || !is_admin())
			return false;

		if ($type == 'jpg')
			$clearExif = imagecreatefromjpeg($imagePath);
		elseif ($type == 'png')
			$clearExif = imagecreatefrompng($imagePath);
		else
			return $imagePath;

		imagejpeg($clearExif, $imagePath, 100);
		imagedestroy($clearExif);

		return $imagePath;
	}
}