<?

define("MIRROR_NONE", 0);
define("MIRROR_HORIZONTAL", 1);
define("MIRROR_VERTICAL", 2);
define("MIRROR_BOTH", 3);

function resize_jpg($img,$w,$h,$stLen='')
{
	$imagedata = getimagesize($img);
	switch($stLen)	{
		case 'W':
			$h = ($w / $imagedata[0]) * $imagedata[1];
		break;
		case 'H':
			$w = ($h / $imagedata[1]) * $imagedata[0];
		break;
		default:
			if ($w && ($imagedata[0] < $imagedata[1])) {
				$w = ($h / $imagedata[1]) * $imagedata[0];
			}	else	{
				$h = ($w / $imagedata[0]) * $imagedata[1];
			}
		break;
	}
	$im2 = ImageCreateTrueColor($w,$h);
	$image = ImageCreateFromJpeg($img);
	imagecopyResampled ($im2, $image, 0, 0, 0, 0, $w, $h, $imagedata[0], $imagedata[1]);
	ImageJpeg($im2, $img, 100);
}



function Mirror($src, $dest, $type)
{
  $imgsrc = imagecreatefromjpeg($src);
  $width = imagesx($imgsrc);
  $height = imagesy($imgsrc);
  $imgdest = imagecreatetruecolor($width, $height);

  for ($x=0 ; $x<$width ; $x++)
   {
     for ($y=0 ; $y<$height ; $y++)
   {
     if ($type == MIRROR_NONE) imagecopy($imgdest, $imgsrc, $x, $y, $x, $y, 1, 1);
     if ($type == MIRROR_HORIZONTAL) imagecopy($imgdest, $imgsrc, $width-$x-1, $y, $x, $y, 1, 1);
     if ($type == MIRROR_VERTICAL) imagecopy($imgdest, $imgsrc, $x, $height-$y-1, $x, $y, 1, 1);
     if ($type == MIRROR_BOTH) imagecopy($imgdest, $imgsrc, $width-$x-1, $height-$y-1, $x, $y, 1, 1);
   }
   }

  imagejpeg($imgdest, $dest);
  imagedestroy($imgsrc);
  imagedestroy($imgdest);
}

function MirrorGIF($src, $dest, $type)
{
//echo $src;
  $imgsrc = imagecreatefromgif($src);
  $width = imagesx($imgsrc);
  $height = imagesy($imgsrc);
  $imgdest = imagecreatetruecolor($width, $height);

  for ($x=0 ; $x<$width ; $x++)
   {
     for ($y=0 ; $y<$height ; $y++)
   {
     if ($type == MIRROR_NONE) imagecopy($imgdest, $imgsrc, $x, $y, $x, $y, 1, 1);
     if ($type == MIRROR_HORIZONTAL) imagecopy($imgdest, $imgsrc, $width-$x-1, $y, $x, $y, 1, 1);
     if ($type == MIRROR_VERTICAL) imagecopy($imgdest, $imgsrc, $x, $height-$y-1, $x, $y, 1, 1);
     if ($type == MIRROR_BOTH) imagecopy($imgdest, $imgsrc, $width-$x-1, $height-$y-1, $x, $y, 1, 1);
   }
   }

  imagegif($imgdest, $dest);
  imagedestroy($imgsrc);
  imagedestroy($imgdest);
}

function resize_gif($img,$w,$h,$stLen='')
{
	$imagedata = getimagesize($img);

	switch($stLen)	{
		case 'W':
			$h = ($w / $imagedata[0]) * $imagedata[1];
		break;
		case 'H':
			$w = ($h / $imagedata[1]) * $imagedata[0];
		break;
		default:
			if ($w && ($imagedata[0] < $imagedata[1])) {
				$w = ($h / $imagedata[1]) * $imagedata[0];
			}	else	{
				$h = ($w / $imagedata[0]) * $imagedata[1];
			}
		break;
	}
	$im2 = ImageCreateTrueColor($w,$h);
	$image = ImageCreateFromGif($img);
	imagecopyResampled ($im2, $image, 0, 0, 0, 0, $w, $h, $imagedata[0], $imagedata[1]);
	ImageGif($im2, $img, 100);
}
?>