<?php

function checkOpenId($openId)
{
    $result = file_get_contents('http://hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/UserCenter/UserCenter/checkIsWatch/openid/' . $openId . '/token/gh_f16bd8b2bf8e');
    $ret = json_decode($result);
    if ($ret['exist']) {
        return true;
    } else {
        return false;
    }
}
if(checkOpenId('1')){
    echo 123;
}else{
    echo 456;
}
