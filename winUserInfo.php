<?php
    //定义个常量，用来授权调用includes里面的文件
    define('IN_TG',true);
    //引入公共文件
    require dirname(__FILE__).'/includes/common.inc.php';

    //实例化逻辑处理对象
    $logic = Logic::getInstance();
    
    //得到所有的队伍信息
    $result = $logic->getAllWinUserInfo();
?>

<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
        <title>中奖用户信息</title>
        <link rel="stylesheet" href="./css/userinfo.css"/>
    </head>
    <body>
        <div id="content">
            <h2>中奖用户信息</h2>
            <table>
                <tr>
                    <td>名字</td>
                    <td>学号</td>
                    <td>手机号</td>
                    <td>奖品</td>
                    <td>抽奖时间</td>
                </tr>
            <?php 
                foreach ($result as $value) {
            ?>
                    <tr>
                        <td><?php echo $value['user_info']['use_name']; ?></td>
                        <td><?php echo $value['user_info']['use_stu_num']; ?></td>
                        <td><?php echo $value['user_info']['use_phone_num']; ?></td>
                        <td><?php echo $value['award_name']; ?></td>
                        <td><?php echo $value['dra_time']; ?></td>
                    </tr>
            <?php
                }
            ?>
            </table>
        </div>
    </body>
</html>