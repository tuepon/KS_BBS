<?php

namespace KSBBS;

/**
 * IMAGE.class.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/09
 */
class IMAGE
{

	public static function resize($filename, $size = 200)
	{
		$width = $size;
		$height = $size;

		list($width_orig, $height_orig, $type) = getimagesize($filename);

		$ratio_orig = $width_orig / $height_orig;

		if ($width / $height > $ratio_orig) {
			$width = $height * $ratio_orig;
		} else {
			$height = $width / $ratio_orig;
		}

		$image_p = imagecreatetruecolor($width, $height);
		$image = imagecreatefromjpeg($filename);
		imagecopyresampled($image_p, $image
			, 0, 0, 0, 0
			, $width, $height, $width_orig, $height_orig
		);

		$destination = 'images/thumb/' . basename($filename);

		switch ($type) {
			case IMAGETYPE_PNG:
				return imagepng($image_p, $destination, 75);

			case IMAGETYPE_JPEG:
			case IMAGETYPE_JPEG2000:
				return imagejpeg($image_p, $destination, 75);
		}
	}

}
