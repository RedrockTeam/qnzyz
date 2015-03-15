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

    if(isset($_POST['submit'])){
        $result = $logic->registerUserInfo($_POST);
        if($result){
            echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
            echo "<script type='text/javascript'>alert('登记成功！点击确定继续抽奖');location.href='draw.php?openId=".$openId."';</script>";
        }else{
            echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
            echo "<script>alert('登记失败！请重试');history.back();</script>";
        }
    }

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>完善个人信息</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="./css/bootstrap.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <link rel="stylesheet" href="./css/person.css"/>

</head>
<body>
<div class="container">
    <nav class="nav title">
        <h3>啦啦队投票</h3>
    </nav>
    <div class="form_container">
        <form action="" method="post" id="register">
            <p>完善个人信息</p>
            <section>
                <label for="name"><img src="./images/name.png" alt="name"/></label>
                <input type="text" id="name" name="name"  placeholder="姓名"  />
                <input type="hidden" name="openId" value="<?php echo $openId; ?>" />
            </section>
            <section>
                <label for="number"><img src="./images/number.png" alt="number"/></label>
                <input type="text" id="number" name="number" placeholder="学号" />
            </section>
            <section>
                <label for="phone"><img src="./images/phone.png" alt="phone"/></label>
                <input type="text" id="phone" name="phone" placeholder="手机号" />
            </section>
            <input type="submit" name="submit" class="submitButton" value="完成,继续抽奖"/>
        </form>
    </div>

    <footer>
        <p>本网站由红岩网校工作站负责开发，管理，不经红岩网校委员会书面同意，不得进行转载</p>
    </footer>
</div>
</body>
<script type="text/javascript" src="./js/jquery.js"></script>
<script type="text/javascript" src="./js/register.js"></script>
</html>