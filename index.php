<?php
    //定义个常量，用来授权调用includes里面的文件
    define('IN_TG',true);
    //引入公共文件
    require dirname(__FILE__).'/includes/common.inc.php';


    //实例化逻辑处理对象
    $logic = Logic::getInstance();


//    //是否有$_GET['openId']
    if(!isset($_GET['openId']) || !$logic->checkOpenId($_GET['openId'])){
        die("进入本页面的方式有误！请先用微信关注重邮青年，回复【我要投票】后进入本页面");
    }
    //得到所有的队伍信息
    $result = $logic->getAll();

    //查询此用户还剩多少次抽奖机会
    $remainDrawTime = $logic->getRemainDrawTimeForOpenId($_GET['openId']);

?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8"/>
    <title>优秀青年志愿者服务队评选投票!</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <!-- Bootstrap -->
    <link rel="stylesheet" href="./css/bootstrap.min.css">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <link rel="stylesheet" href="./css/main.css"/>

</head>
<body>
    <div class="container">
        <nav class="nav title">
            <h3>优秀青年志愿者服务队评选投票</h3>
        </nav>
        <header class="nav logo">
            <div class="img_wrapper"><img src="./images/logo.jpg" alt="wrapper"/></div>
            <div class="days"><span id="count" data-start="<?=$logic->getDayNumToStartVote();?>"><?php echo $logic->getDayNumToEndVote(); ?></span></div>
        </header>
        <section class="line"></section>
<!--        <section class="lottery">-->
<!--            <p>参加啦啦队投票抽大奖,您剩余的抽奖次数为<span id="lottery_count">--><?php //echo $remainDrawTime; ?><!--</span>次,投一次票即可获得一次抽奖机会-->
<!--</p>-->
<!--            <div class="lottery_button"><a href="draw.php?openId=--><?php //echo $_GET['openId']; ?><!--">抽奖</a></div>-->
<!--        </section>-->
        <section class="rules">
            <h2>投票规则</h2>
            <ul>
                <li>1. 所有关注重邮青年的用户均有权进行投票，本投票网址链接分享无效。</li>
                <li>2. 每个账号每天只能投1次票，每次票必须选择5支队伍。</li>
                <li>3. 投票时间：2015.3.24 18:00至2015.3.30 24:00</li>
            </ul>
        </section>
        <input type="hidden" id="openId" value="<?php echo $_GET['openId']; ?>" name="openId" />
        <section class="troopes_container">
            <?php
                foreach ($result as $value) {
            ?>
                <section class="troopes" data-vertification="true" data-troope="<?php echo $value['roo_id']; ?>">
                    <div class="statement">
                        <div class="head"><img class="lazy" data-original="./uploadImage/<?php echo $value['roo_face_path']; ?>" alt="face"/></div>
                        <div class="troope_info">
                            <h3><?php echo $value['roo_name']; ?></h3>
                            <p><?php echo mb_substr($value['roo_describe'],0,35,'utf-8'); ?>......</p>
                        </div>
                    </div>
                    <div class="panel">
                        <div class="heart">
                            <a class="vote"><img class="lazy" data-original="./images//broken_heart.png" alt="heart"/></a>
                            <span class="tickets"><?php echo $value['roo_vote_total']; ?>票</span>
                        </div>
                        <div class="heart">
                            <a href="<?php echo $value['roo_detail_href']; ?>"><img class="lazy" data-original="./images//troope.png" alt="Troope"/></a>
                            <span>队伍详情</span>
                        </div>
<!--                        <div class="heart">-->
<!--                            <a href="--><?php //echo $value['roo_small_video_href']; ?><!--"><img class="lazy" data-original="./images//TV.png" alt="Troope"/></a>-->
<!--                            <span>微视频</span>-->
<!--                        </div>-->
                    </div>
                </section>
            <?php 
                } 
            ?>
        </section>

        <footer>
            <p>本网站由红岩网校工作站负责开发，管理，不经红岩网校委员会书面同意，不得进行转载</p>
        </footer>
    </div>
    <div class="vote_success" style="display: none;">
        <div class="close"><img src="./images//close.png" alt="close"/></div>
        <div class="vote_wrapper" style="display: none;">
            <img src="./images//vote_success.png" alt="vote_success"/>
        </div>
        <div class="vote_fail" style="display: none;">
            <img src="./images//vote_error.png" alt="vote_error"/>
        </div>
    </div>
    <div class="headroom">
        <img class="move_up" src="./images//up.png" alt="up"/>
    </div>
    <div class="send">
        <div class="sendButton">提交</div>
        <!--<img id="send" src="./images//send.png" alt="send"/>-->
    </div>
</body>
<script type="text/javascript" src="./js/settings.js"></script>
<script type="text/javascript" src="./js/jquery.js"></script>
<script type="text/javascript" src="./js/jquery.lazyload.js"></script>
<script type="text/javascript">
    $("img.lazy").lazyload({
        threshold : 200
    });
</script>
<script type="text/javascript" src="./js/headroom.js"></script>
<script type="text/javascript" src="./js/static.js"></script>
<script type="text/javascript">
    var myElement = document.querySelector(".headroom");
    // 创建 Headroom 对象，将页面元素传递进去
    var headroom  = new Headroom(myElement);
    // 初始化
    headroom.init();
</script>
</html>