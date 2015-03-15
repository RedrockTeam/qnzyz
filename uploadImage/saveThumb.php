<?php
	if(!isset($_POST["upload_thumbnail"])){
		die("Access Denied");
	}


	$thumb_width = "310";						// 缩略图的宽度
	$thumb_height = "310";						// 缩略图的高度


	function resizeThumbnailImage($thumb_image_name, $image, $width, $height, $start_width, $start_height, $scale){
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		$source = imagecreatefromjpeg($image);
		imagecopyresampled($newImage,$source,0,0,$start_width,$start_height,$newImageWidth,$newImageHeight,$width,$height);
		imagejpeg($newImage,$thumb_image_name,90);
		chmod($thumb_image_name, 0777);
		
		return $thumb_image_name;
	}

	
	//得到裁剪坐标
	$x1 = $_POST["x1"];
	$y1 = $_POST["y1"];
	$x2 = $_POST["x2"];
	$y2 = $_POST["y2"];
	$w = $_POST["w"];
	$h = $_POST["h"];
	//大图的保存位置
	$large_image_location = $_POST['large_image_location'];
	// 缩略图的新名字
	$thumb_image_location = $_POST['thumb_image_location'];
	
	//缩放到设置的缩略图大小
	$scale = $thumb_width/$w;
	$cropped = resizeThumbnailImage($thumb_image_location, $large_image_location,$w,$h,$x1,$y1,$scale);

?>

<input type="hidden" id="faceSrc" value="<?php echo $thumb_image_location; ?>" />


<script>
	var faceImg = opener.document.getElementById('faceId');
	var faceInput = opener.document.getElementById('faceInputId');
	var imageSrc = document.getElementById('faceSrc').value;
	faceInput.value = imageSrc;
	faceImg.src = 'uploadImage/'+imageSrc;
	window.close();
</script>