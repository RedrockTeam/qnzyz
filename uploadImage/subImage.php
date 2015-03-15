<?php

	if(!isset($_POST["upload"])){
		die("Access Denied");
	}

	//先设置时区（中国）
    date_default_timezone_set("PRC");
	$large_image_name = "pic_".time().".jpg"; 		// 大图的新名字
	$thumb_image_name = "thumb_".$large_image_name;
	$upload_dir = "uploads"; 				// 图片保存的文件夹
	$upload_path = $upload_dir."/";				// 图片保存的路径
	$max_file = "1148576"; 						// 大约 1MB
	$max_width = "500";							// 大图允许的最大宽度
	$thumb_width = "310";						// 缩略图的宽度
	$thumb_height = "310";						// 缩略图的高度
	$error = '';

	//大图的位置
	$large_image_location = $upload_path.$large_image_name;
	//缩略图的位置
	$thumb_image_location = $upload_path.$thumb_image_name;

	function resizeImage($image,$width,$height,$scale) {
		$newImageWidth = ceil($width * $scale);
		$newImageHeight = ceil($height * $scale);
		$newImage = imagecreatetruecolor($newImageWidth,$newImageHeight);
		$source = imagecreatefromjpeg($image);
		imagecopyresampled($newImage,$source,0,0,0,0,$newImageWidth,$newImageHeight,$width,$height);
		imagejpeg($newImage,$image,90);
		chmod($image, 0777);
		
		return $image;
	}

	function getHeight($image) {
		$sizes = getimagesize($image);
		$height = $sizes[1];
		
		return $height;
	}


	function getWidth($image) {
		$sizes = getimagesize($image);
		$width = $sizes[0];
		
		return $width;
	}


	//如果上传的文件夹不存在则创建并设置权限
	if(!is_dir($upload_dir)){
		mkdir($upload_dir, 0777);
		chmod($upload_dir, 0777);
	}


	//获取文件的信息
	$userfile_name = $_FILES['image']['name'];//获取到文件名
	$userfile_tmp = $_FILES['image']['tmp_name'];//获取到临时文件名
	$userfile_size = $_FILES['image']['size'];//获取文件的大小
	$filename = basename($_FILES['image']['name']);//获取到带文件扩展的文件名
	$file_ext = substr($filename, strrpos($filename, '.') + 1);//获取到文件的扩展类型
	
	//只处理限定类型的文件
	if((!empty($_FILES["image"])) && ($_FILES['image']['error'] == 0)) {
		if (($file_ext != "jpg") && ($userfile_size > $max_file)) {
			$error = "目前只支持jpeg格式且小于1M的图片";
		}
	}else{
		$error = '没有找到上传的图片';
	}

	//如果没有错误信息
	if (strlen($error) == 0){
		//如果文件名存在
		if (isset($_FILES['image']['name'])){
			//把图片从临时位置转移到指定的位置
			move_uploaded_file($userfile_tmp, $large_image_location);
			chmod($large_image_location, 0777);
			
			//得到图片的宽度和高度
			$width = getWidth($large_image_location);
			$height = getHeight($large_image_location);

			//如果图片宽度大于设置的最大宽度则缩放
			if ($width > $max_width){
				$scale = $max_width/$width;
				//缩放图片
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}else{
				$scale = 1;
				$uploaded = resizeImage($large_image_location,$width,$height,$scale);
			}

			$current_large_image_width = getWidth($large_image_location);
			$current_large_image_height = getHeight($large_image_location);
		}
	}else{
		die($error);
	}

?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>图片截取</title>
		<link rel="stylesheet" href="../css/face.css"/>
		<script type="text/javascript" src="../js/jquery.js"></script>
		<script type="text/javascript" src="../js/jquery.imgareaselect-0.3.min.js"></script>
	</head>
	<body>
		<script type="text/javascript">
			function preview(img, selection) { 
				var scaleX = <?php echo $thumb_width;?> / selection.width; 
				var scaleY = <?php echo $thumb_height;?> / selection.height; 
				
				$('#thumbnail + div > img').css({ 
					width: Math.round(scaleX * <?php echo $current_large_image_width;?>) + 'px', 
					height: Math.round(scaleY * <?php echo $current_large_image_height;?>) + 'px',
					marginLeft: '-' + Math.round(scaleX * selection.x1) + 'px', 
					marginTop: '-' + Math.round(scaleY * selection.y1) + 'px' 
				});
				$('#x1').val(selection.x1);
				$('#y1').val(selection.y1);
				$('#x2').val(selection.x2);
				$('#y2').val(selection.y2);
				$('#w').val(selection.width);
				$('#h').val(selection.height);
			} 

			$(document).ready(function () { 
				$('#save_thumb').click(function() {
					var x1 = $('#x1').val();
					var y1 = $('#y1').val();
					var x2 = $('#x2').val();
					var y2 = $('#y2').val();
					var w = $('#w').val();
					var h = $('#h').val();
					if(x1=="" || y1=="" || x2=="" || y2=="" || w=="" || h==""){
						alert("请按下鼠标进行截取");
						return false;
					}else{
						return true;
					}
				});
			}); 

			$(window).load(function () { 
				$('#thumbnail').imgAreaSelect({ aspectRatio: '1:1', onSelectChange: preview }); 
			});

		</script>
		<div id="thumb">
			<h2>创建缩略图</h2>
			<dl>
				<img src="<?php echo $upload_path.$large_image_name;?>" style="float: left; margin-right: 10px;" id="thumbnail" alt="Create Thumbnail" />
				<div style="float:left; margin-top:10px; position:relative; overflow:hidden; width:<?php echo $thumb_width;?>px; height:<?php echo $thumb_height;?>px;">
					<img src="<?php echo $upload_path.$large_image_name;?>" style="position: relative;" alt="Thumbnail Preview" />
				</div>
				<br style="clear:both;"/>
				<form name="thumbnail" action="saveThumb.php" method="post">
					<input type="hidden" name="x1" value="" id="x1" />
					<input type="hidden" name="y1" value="" id="y1" />
					<input type="hidden" name="x2" value="" id="x2" />
					<input type="hidden" name="y2" value="" id="y2" />
					<input type="hidden" name="w" value="" id="w" />
					<input type="hidden" name="h" value="" id="h" />
					<input type="hidden" name="large_image_location" value="<?php echo $large_image_location; ?>" />
					<input type="hidden" name="thumb_image_location" value="<?php echo $thumb_image_location; ?>" />
					<input type="submit" name="upload_thumbnail" class="submit" value="保存" id="save_thumb" />
				</form>
			</dl>
		</div>
	<body>
</html>