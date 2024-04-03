<?
/**
 * 속도이슈로 인해 Mirror함수 수정
 * 2013.08.20 bgh
 */
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

function resize_png($img,$w,$h,$stLen='')
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
	$image = imagecreatefrompng($img);
	imagecopyResampled ($im2, $image, 0, 0, 0, 0, $w, $h, $imagedata[0], $imagedata[1]);
	//Imagepng($im2, $img, 100);
	imagepng($im2, $img, 9);
}

/**
 * 속도이슈로 인해
 * MIRROR_NONE인 경우 단순 복사이므로 copy로 변경 하였음
 * 2013.08.20 bgh 
 * @param String $src
 * @param String $dest
 * @param String $type
 * @return boolean
 */
function Mirror($src, $dest, $type)
{
	if ($type == MIRROR_NONE){
		if(!copy($src,$dest)){
			return false;
		}else{
			return true;
		}
	}
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

  imagejpeg($imgdest, $dest, 100);
  imagedestroy($imgsrc);
  imagedestroy($imgdest);
}

function MirrorGIF($src, $dest, $type)
{
	if ($type == MIRROR_NONE){
		if(!copy($src,$dest)){
			return false;
		}else{
			return true;
		}
	}
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

  imagegif($imgdest, $dest, 100);
  imagedestroy($imgsrc);
  imagedestroy($imgdest);
}

function MirrorPNG($src, $dest, $type)
{
	if ($type == MIRROR_NONE){
		if(!copy($src,$dest)){
			return false;
		}else{
			return true;
		}
	}
//echo $src;
  $imgsrc = imagecreatefrompng($src);
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

  imagepng($imgdest, $dest, 9);
  imagedestroy($imgsrc);
  imagedestroy($imgdest);
}

function WaterMarkPrint($handle_img_src, $target_directory){
//if($watermark) {
	require_once "../lib/class.upload.php";
	$file_name = returnFileName($handle_img_src);
	$orgin_file_name = $file_name;
	$file_name = str_replace("-","_",$file_name);
	$s_water_handle = new Upload($handle_img_src);
	$s_water_result = WaterMarkProcess2($s_water_handle, $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images", $target_directory);//, "TL"


	copy($target_directory."watermark/".$file_name,$target_directory."/".$orgin_file_name);
	//echo $target_directory."/".$orgin_file_name;
	chmod($target_directory."/".$orgin_file_name, 0777);
	rmdirr($target_directory."/watermark/");
//exit;
	//$image_type = "gif"; // 워터마크 처리후 마임타입이 gif로 바뀐다.
//}
}





function imagecopymerge_alpha($dst_im, $src_im, $dst_x, $dst_y, $src_x, $src_y, $src_w, $src_h, $pct){ 
    // creating a cut resource 
    $cut = imagecreatetruecolor($src_w, $src_h); 

    // copying relevant section from background to the cut resource 
    imagecopy($cut, $dst_im, 0, 0, $dst_x, $dst_y, $src_w, $src_h); 

	if($dst_x == ""){

		$w = imagesx($dst_im);
		$h = imagesy($dst_im);
		$ww = imagesx($src_im);
		$wh = imagesy($src_im);

		$img_paste_x = 0;
		while($img_paste_x < $w){
			$img_paste_y = 0;
			while($img_paste_y < $h){
				//echo $img_paste_y."<br>";
				imagecopy($dst_im, $src_im, $img_paste_x, $img_paste_y, $src_x, $src_y, $ww, $wh);
				$img_paste_y += $wh;
			}
			$img_paste_x += $ww;
		}
	}else{
		// copying relevant section from watermark to the cut resource 
		imagecopy($cut, $src_im, 0, 0, $src_x, $src_y, $src_w, $src_h);
	}

    // insert cut resource to destination image 
    imagecopymerge($dst_im, $cut, $dst_x, $dst_y, 0, 0, $src_w, $src_h, $pct); 

	// OUTPUT IMAGE: 
	/*
	header("Content-Type: image/png"); 
	imagesavealpha($img_a, true); 
	imagepng($img_a, NULL); 
	*/

} 


function WaterMarkPrint2($handle_img_src, $target_directory){
	$image = imagecreatefromjpeg($handle_img_src);
	$imagesource    = $image['file_path'];//;
	$watermarkPath  = $_SERVER["DOCUMENT_ROOT"].$_SESSION["admin_config"]["mall_data_root"]."/images/watermark/watermark.png";//$settings['watermark'];
	$filetype       = substr($imagesource,strlen($imagesource)-4,4);
	$filetype       = strtolower($filetype);
	$watermarkType  = substr($watermarkPath,strlen($watermarkPath)-4,4);
	$watermarkType  = strtolower($watermarkType);

	// Let's pretend that $watermark and $image are now GD resources.
	//echo $watermarkPath;
	$watermark = imagecreatefrompng($watermarkPath);

	$imagewidth         = imagesx($image);
	$imageheight        = imagesy($image);  
	$watermarkwidth     = imagesx($watermark);
	$watermarkheight    = imagesy($watermark);
	$settings['watermark_location'] = "repeat";

	switch ($settings['watermark_location'])
	{
		case "tl": //Top Left
			$startwidth     = 20;
			$startheight    = 20;
			break;
		case "bl": //Bottom Left
			$startwidth     = 20;
			$startheight    = (($imageheight - $watermarkheight) - 20);
			break;
		case "tr": //Top Right
			$startwidth     = (($imagewidth - $watermarkwidth) - 20);
			$startheight    = 20;
			break;
		case "br": //Bottom Right
			$startwidth     = (($imagewidth - $watermarkwidth) - 20);
			$startheight    = (($imageheight - $watermarkheight) - 20);
			break;
		case "middle": //Middle/center
			$startwidth     = (($imagewidth - $watermarkwidth) / 2);
			$startheight    = (($imageheight - $watermarkheight) / 2);
			break;
		case "repeat":
			// not sure what to do here
			break;
		default:
			$startwidth     = (($imagewidth - $watermarkwidth) / 2);
			$startheight    = (($imageheight - $watermarkheight) / 2);
	}


			imagecopymerge_alpha($image, $watermark,  $startwidth, $startheight, 0, 0, $watermarkwidth, $watermarkheight,10);//$settings['watermark_opacity']
			imagejpeg($image,NULL,90);
			imagedestroy($image);
			imagedestroy($watermark);    


}



?>