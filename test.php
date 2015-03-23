<?php
/**
 * Created by PhpStorm.
 * User: Liuchenling
 * Date: 3/23/15
 * Time: 11:53
 */
function checkOpenId($openId){
    $result = file_get_contents('http://hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/UserCenter/UserCenter/checkIsWatch/openid/'.$openId.'/token/gh_f16bd8b2bf8e');
    $ret = json_decode($result);
    if($ret['exist']){
        return true;
    }else{
        return false;
    }
}
if(checkOpenId($_GET['openId'])){
    echo "true";
}else{
    echo "false";
};