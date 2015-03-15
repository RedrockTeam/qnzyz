<?php

    //定义个常量，用来授权调用includes里面的文件
    define('IN_TG',true);
    //引入公共文件
    require dirname(__FILE__).'/includes/common.inc.php';

    /*状态码说明：
		* -1 传过来的数据不正确
		* -2 没有投票的机会了
		* -3 修改票数失败
		* -4 新增投票记录失败
		* -5 活动还没有开始
		* -6 不存在此openid(没有关注重游小帮手的openid)
        * -7 投票活动已经截止
		*  0 投票成功 
	*/

    $openId = $_POST['openId'];
    $teamStr = $_POST['teamStr'];

	//判断字符串时候满足条件，只有正确了才能进行分割
    if($teamStr == '' || is_null($teamStr) ||$openId == '' || is_null($openId)){
    	die(-1);
    }

    //把字符串分割成数组
    $voteCase = explode('&&',$teamStr);

    //实例化逻辑处理对象
    Logic::getInstance()->ajaxVote($openId,$voteCase);