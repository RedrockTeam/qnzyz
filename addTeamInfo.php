<?php

	//定义个常量，用来授权调用includes里面的文件
    define('IN_TG',true);
    //引入公共文件
    require dirname(__FILE__).'/includes/common.inc.php';

	if(isset($_POST['submit'])){
    	Logic::getInstance()->addTeamInfo($_POST);
    	die();
	}
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<title>投票条目添加</title>
		<link rel="stylesheet" href="./css/add.css"/>
	</head>
	<body>
		<div id="content">
			<h2>添加</h2>
			<form action="" method="post" name="addTeamForm">
			<dl>
				<dd>条目名： <input type="text" name="teamName" class="text" /></dd>

				<dd>图片选择： <input type="button" class="text face" value="点击选择" onclick="javascript:window.open('uploadImage/selectImage.php','face','width=600,height=600,top=200,left=200')" /></dd>
				<dd>
					图片显示： <img src="" style="width:100px;height:100px;" id="faceId" alt="图片" />
					<input type="hidden" name="teamFaceUrl" id="faceInputId" />
				</dd>
<!--				<dd>video链接： <input type="text" name="smallVideoUrl" class="text" /></dd>-->
				<dd>详情链接： <input type="text" name="roo_detail_href" class="text" /></dd>
				<dd><span style="vertical-align:190px">描　　述：</span> <textarea name="teamDesc"></textarea></dd>
				
				<dd style="padding:0 0 0 80px;"><input type="submit" name="submit" value="提 交" class="submit" onClick="return check()" /></dd>
			</dl>
			</form>
		</div>

		<script type="text/javascript">

			var fm = document.getElementsByTagName('form')[0];
			fm.reset();

			function checkUrl(url){
				if (url) {
					if (/^https?:\/\/(\w+\.)?[\w\-\.]+(\.\w+)+/.test(url)) {
						return true;
					}else{
						return false;
					}
				}
			}

			function check()
			{
				if (addTeamForm.teamName.value==""){
					alert("请输入条目名！");
					return false;
				}
				if (addTeamForm.teamFaceUrl.value==""){
					alert("请选择图片！");
					return false;
				}
//				if (addTeamForm.smallVideoUrl.value==""){
//					alert("请输入视频链接!");
//					return false;
//				}
				if (addTeamForm.roo_detail_href.value==""){
					alert("请输入详情链接!");
					return false;
				}
				if (addTeamForm.teamDesc.value==""){
					alert("请输入描述！");
					return false;
				}
//				if(!checkUrl(addTeamForm.smallVideoUrl.value)){
//					alert('微视频链接格式不正确');
//					return false;
//				}
				if(!checkUrl(addTeamForm.roo_detail_href.value)){
					alert('详情链接格式不正确');
					return false;
				}
				
				return true;
			}

		</script>
	</body>
</html>