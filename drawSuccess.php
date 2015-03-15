<?php
    //定义个常量，用来授权调用includes里面的文件
    define('IN_TG',true);
    //引入公共文件
    require dirname(__FILE__).'/includes/common.inc.php';

    if(!isset($_GET['openId']) || $_GET['openId'] == '' || !isset($_GET['aid'])){
        die("Access Denied");
    }

    $openId = $_GET['openId'];
    $awardId = $_GET['aid'];

    //实例化逻辑处理对象
    $logic = Logic::getInstance();

    $remainDrawTime = $logic->getRemainDrawTimeForOpenId($openId);
    $awardName = $logic->getAwardNameByAwardId($awardId);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>恭喜你抽中奖品</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0,  user-scalable=no">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="./css/bootstrap.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <link rel="stylesheet" href="./css/success.css"/>

</head>
<body>
<div class="container">
    <nav class="nav title">
        <h3>啦啦队投票</h3>
    </nav>
    <div class="sorry">
        <div class="sorry_logo">
            <img src="./images/success.png" alt="success"/>
            <p>恭喜您获得<?php echo $awardName; ?></p>
        </div>
        <?php
            if(!$logic->isRegisterUserInfo($openId)){
                echo "<div class='sendButton'><a href='registerInfo.php?openId=".$openId."'>完善兑奖信息</a></div>";
            } 
        ?>
        <?php 
            if($remainDrawTime == 0){
                echo "<div class='sendButton'><a href='index.php?openId=".$openId."'>回到投票页面</a></div>";
            }else{
                echo "<div class='sendButton'><a href='draw.php?openId=".$openId."'>再来一次</a></div>";
            } 
        ?>
        <p>您还剩<span><?php echo $remainDrawTime; ?></span>次机会</p>
    </div>
    <footer>
        <p>本网站由红岩网校工作站负责开发，管理，不经红岩网校委员会书面同意，不得进行转载</p>
    </footer>
</div>
</body>
</html>