<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<title>选择需要截取的图片</title>
		<link rel="stylesheet" href="../css/face.css"/>
	</head>
	<body>
		<div id="content">
			<h2>选择图片</h2>
			<form name="photo" enctype="multipart/form-data" action="subImage.php" method="post">
			<dl>
				<dd>选择图片： <input type="file" name="image" size="30" class="text" /></dd>
				<dd><input type="submit" name="upload" value="开始截取" class="submit" /></dd>
			</dl>
			</form>
		</div>
	</body>
</html>