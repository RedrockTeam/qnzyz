<?php

    //定义个常量，用来授权调用includes里面的文件
    define('IN_TG',true);
    //引入公共文件
    require dirname(__FILE__).'/includes/common.inc.php';

    if(!isset($_GET['openId']) || $_GET['openId'] == ''){
        die("Access Denied");
    }

    $openId = $_GET['openId'];

    //实例化逻辑处理对象
    $logic = Logic::getInstance();

    //检查是否有抽奖机会
    if($logic->getRemainDrawTimeForOpenId($openId) == 0){
        die("抱歉！你的抽奖机会已经用完,<a href='./index.php?openId=".$openId."'>点击返回</a>");
    }

    //得到抽奖的奖品id
    $awardId = $logic->getAwardIdForLottery();

    //登记抽奖记录
    $logic->registerLotteryRecord($openId,$awardId);

    if($awardId != 0){
		header("Location:drawSuccess.php?openId=".$openId."&aid=".$awardId); 
    }else{
		header("Location:drawFailure.php?openId=".$openId); 
    }

?>