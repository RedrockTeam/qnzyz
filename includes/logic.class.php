<?php

	class Logic{

		private static $pdo = null;

		private static $instance = null;

		//每次投票规定的队伍数
		const VOTE_TEAM_NUM = 5;
		//每天规定的投票次数
		const VOTE_TIME_EVERYDAY = 1;
		//开始投票的时间
		const START_VOTE_TIME = '2015/3/21';
		//截止投票的时间
		const END_VOTE_TIME = '2015/3/30';

		//规定抽一次奖所需要的投票次数
		const VOTE_TIME_FOR_DRAW = 1;



		/**
		* 把构造方法私有化
		**/
		private function __construct() {
			if(self::$pdo == null){
				$dsn = "mysql:host=".HOST.";dbname=".DBNAME;
				$opt = array(
					PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES "UTF8"',
					PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => true,
				);
				self::$pdo = new PDO($dsn, USER, PASSWORD, $opt);
			}
		}
		
		/**
		* 本类唯一获取实例的方法
		**/
		public static function getInstance() {
			if (self::$instance == null) {
				self::$instance = new Logic();
			}
			return self::$instance;
		}
		

		/**
		* 重新克隆方法
		**/
		public function __clone() {
			return null;
		}


		/**
		* 查询某用户今天的投票次数
		* @param $openId 用户的openid
		* @return $voteTime 用户今天的投票次数
		**/
		public function voteTimeOfToday($openId){
			//先设置时区（中国）
        	date_default_timezone_set("PRC");
			//得到今天的日期
			$voteDate = date('Y-m-d');
			//查询的sql语句
			$sql = "SELECT `rec_id` FROM `vote_record` WHERE `open_id`='$openId' AND `vote_date`='$voteDate'";
			$statement = self::$pdo->query($sql);
			$voteTime = $statement->rowCount();

			return $voteTime;
		}


		/**
		* 查询某个用户总共的投票次数
		* @param $openId 用户的openid
		* @return $totalVoteTime 总共投票的次数
		**/
		public function getVoteTimeForUser($openId){
			//查询的sql语句
			$sql = "SELECT `rec_id` FROM `vote_record` WHERE `open_id`='$openId'";
			$statement = self::$pdo->query($sql);
			$totalVoteTime = $statement->rowCount();

			return $totalVoteTime;
		}


		/**
		* 查询某个用户的抽奖次数
		* @param $openId 用户的openid
		* @return $totalDrawTime 总共抽奖的次数
		**/
		public function getDrawTimeForUser($openId){
			//查询的sql语句
			$sql = "SELECT `dra_id` FROM `draw_record` WHERE `dra_open_id`='$openId'";
			$statement = self::$pdo->query($sql);
			$totalDrawTime = $statement->rowCount();

			return $totalDrawTime;
		}



		/**
		* ajax用来执行投票
		* @param $openId 投票者的openid
		* @param $voteCase 存放被投票队伍id的数组
		* @return 结果状态码
		* 状态码说明：
		* -1 传过来的数据不正确
		* -2 没有投票的机会了
		* -3 修改票数失败
		* -4 新增投票记录失败
		* -5 活动还没有开始
		* -6 不存在此openid(没有关注重游小帮手的openid)
		* -7 活动已经结束
		*  0 投票成功 
		**/
		public function ajaxVote($openId,$voteCase){
			//先检查活动开没开始
			if($this->getDayNumToStartVote() != 0){
				echo -5;
				return;
			}

			//再判断是否还在活动时间段里
			if(!$this->isOnVoteTime()){
				echo -7;
				return;
			}

			//是否存在此openId（是否关注了重游小帮手）
		    if(!$this->checkOpenId($openId)){
		        echo -6;
				return;
		    }

			//先检查数据的正确性
			if(is_null($openId) || $openId == '' || count($voteCase) != self::VOTE_TEAM_NUM){
				echo -1;
				return;
			}

			//再一次检查今天是否还有投票机会
			if(!$this->isHaveChange($openId)){
				echo -2;
				return;
			}

			//下面开始投票的逻辑
			//1.拼接队伍字符串和where条件
			$teamRecordStr = '#';
			$where = 'WHERE ';
			foreach ($voteCase as $key=>$value) {
	            $teamRecordStr .= $value.'#';
	            if($key == 0){
	                $where .= '`roo_id` = '.$value;
	            }else{
	                $where .= ' OR `roo_id` = '.$value;
	            }
	        }
	        //2.增加队伍票数
	        $updateTeamVoteSql = "UPDATE `rooters_info` SET `roo_vote_total`=`roo_vote_total`+1 {$where}";
	        $updateResult = self::$pdo->exec($updateTeamVoteSql);
	        if (!!$updateResult) {
	            //3.增加投票记录
	            //先设置时区（中国）
        		date_default_timezone_set("PRC");
	            $voteTime = date('Y-m-d');
	            $addVoteRecordSql = "INSERT INTO `vote_record`(`open_id`,`vote_date`,`vote_roo_id`) VALUES('$openId','$voteTime','$teamRecordStr')";
	            $addResult = self::$pdo->exec($addVoteRecordSql);
	            if(!!$addResult){
	                echo 0;
	                return;
	            }else{
	                echo -4;
	                return;
	            }
	        }else{
	            echo -3;
	            return;
	        }
		}


		/**
		* 用户获取所有的队伍消息
		* @return 所有队伍信息的数组
		**/
		public function getAll(){
			$sql = "SELECT * FROM `rooters_info`";
		    $source = self::$pdo->query($sql);
		    $result = $source->fetchAll(PDO::FETCH_ASSOC);
			shuffle($result);
		    return $result;
		}


		/**
		* 添加队伍消息
		**/
		public function addTeamInfo($info){
			if(empty($info)){
				echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
				echo "<script>alert('填写的数据不能为空！');history.back();</script>";
			}else{
	            $addTeamSql = "INSERT INTO `rooters_info`(`roo_name`,`roo_small_video_href`,`roo_detail_href`,`roo_face_path`,`roo_describe`) VALUES('{$info['teamName']}','{$info['smallVideoUrl']}','{$info['roo_detail_href']}','{$info['teamFaceUrl']}','{$info['teamDesc']}')";
	            $addResult = self::$pdo->exec($addTeamSql);
	            if(!!$addResult){
	               	echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
					echo "<script>alert('添加成功！点击确定继续提交');history.back();</script>";
	            }else{
	                echo "<meta http-equiv='Content-Type' content='text/html; charset=utf-8'>";
					echo "<script>alert('添加失败！点击确定重新提交');history.back();</script>";
	            }
			}
		}

		/**
		* 距离开始投票还有多少天
		* @return $intervalDay 距离的天数
		**/
		public function getDayNumToStartVote(){
			//先设置时区（中国）
        	date_default_timezone_set("PRC");

			//得到今天凌晨0点的时间戳
			$todayTime = strtotime(date('Y-m-d 00:00:00'));
			$startVoteTime = strtotime(self::START_VOTE_TIME.' 19:00:00');

 			$intervalDay = (int)(($startVoteTime - $todayTime)/60/60/24);
			//如果已经超过了就直接让间隔天数为0
			if($intervalDay < 0){
				$intervalDay = 0;
			}

			return $intervalDay;
		}


		/**
		* 判断是否在投票活动时间里
		* @return true/false 是否在投票时间段里
		**/
		public function isOnVoteTime(){
			//先设置时区（中国）
        	date_default_timezone_set("PRC");

			//开始投票的时间
			$startVoteTime = strtotime(self::START_VOTE_TIME.' 19:00:00');
			//结束投票的时间
			$endVoteTime = strtotime(self::END_VOTE_TIME.' 23:59:59');
			//现在的时间
			$nowTime = time();

			if($nowTime >= $startVoteTime && $nowTime <= $endVoteTime){
				return true;
			}else{
				return false;
			}
		}


		/**
		* 判断用户今天是否还有机会投票
		* @param $openId 用户的openid
		* @return boolean 是否还有机会 
		**/
		public function isHaveChange($openId){
			if($this->voteTimeOfToday($openId) >= self::VOTE_TIME_EVERYDAY){
				return false;
			}else{
				return true;
			}
		}

		/**
		* 检查用户是否关注过重游小帮手（数据库里有没有用户的信息）
		* @param $openId 用户的openid
		* @return true/false
		**/
		public function checkOpenId($openId){
			$result = $this->curl_get_contents(CHECK_OPENID_URL.'openid/'.$openId.'/token/gh_68f0a1ffc303');
            $ret = json_decode($result);
			if($ret['exist']){
				return true;
			}else{
				return false;
			}
		}

		/**
		* 用于抓取数据
		* @param $url 网址
		* @return $result 返回的json结果
		**/
		private function curl_get_contents($url)   
		{   
		    $ch = curl_init();   
		    curl_setopt($ch, CURLOPT_URL, $url);            //设置访问的url地址   
		    //curl_setopt($ch,CURLOPT_HEADER,0);            //是否显示头部信息   
		    curl_setopt($ch, CURLOPT_TIMEOUT, 3);           //设置超时   
		    //curl_setopt($ch, CURLOPT_USERAGENT, _USERAGENT_);   //用户访问代理 User-Agent   
		    //curl_setopt($ch, CURLOPT_REFERER,_REFERER_);        //设置 referer   
		    //curl_setopt($ch,CURLOPT_FOLLOWLOCATION,1);      //跟踪301   
		    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);        //返回结果   
		    $result = curl_exec($ch);   
		    curl_close($ch);   
		    
		    return $result;   
		}

		/**
		* 用来得到抽奖的奖品id
		* @return $award_id 奖品的id（0表示未中奖）
		**/
		public function getAwardIdForLottery(){ 
			//先取出奖品列表信息
			$sql = "SELECT * FROM `award_list`";
		    $source = self::$pdo->query($sql);
		    $awardArr = $source->fetchAll(PDO::FETCH_ASSOC);

			//随机获得一个幸运数字
			$luck_num = mt_rand(0, 99);
			//初始化几率区间
			$lucky_range = 0;
			//初始化奖品id
			$award_id = 0;

			foreach($awardArr as $sa){
				$prob = intval($sa['awa_prob']);
				if($luck_num >= $lucky_range && $luck_num < $lucky_range + $prob ){
					if($sa['awa_count'] == 0){
						$award_id = 0;
					}else{
						$award_id = $sa['awa_id'];
					}
					break;
				}else{
					$lucky_range += $prob;
				}
			}

			if($award_id != 0){
				$subAwardCountSql = "UPDATE `award_list` SET `awa_count`=`awa_count`-1 WHERE `awa_id`='$award_id'";
	        	$updateResult = self::$pdo->exec($subAwardCountSql);
	        	if (!$updateResult) {
	        		$award_id = 0;
	        		return $award_id;
				}
			}

			return $award_id;
		}

		/**
		* 用来登记抽奖记录
		* @param $openId 用户的openid
		* @param $awardId 奖品的id
		**/
		public function registerLotteryRecord($openId,$awardId){ 
			//先设置时区（中国）
        	date_default_timezone_set("PRC");
	        $drawTime = date('Y-m-d');
	        $addDrawRecordSql = "INSERT INTO `draw_record`(`dra_open_id`,`dra_award_id`,`dra_time`) VALUES('$openId','$awardId','$drawTime')";
	        self::$pdo->exec($addDrawRecordSql);
		}


		/**
		* 用来得到用户还剩多少次抽奖机会
		* @param $openId 用户的openid
		* @return $remainDrawTime 剩余的抽奖次数
		**/
		public function getRemainDrawTimeForOpenId($openId){ 
			//先得到用户投了几次票
			$voteTime = $this->getVoteTimeForUser($openId);
			//算出可以抽几次奖
			$drawChangeTime = floor($voteTime/self::VOTE_TIME_FOR_DRAW);

			//再得到已经抽了几次奖
			$drawedTime = $this->getDrawTimeForUser($openId);

			//算出剩余的抽奖机会
			$remainDrawTime = $drawChangeTime - $drawedTime;
			if($remainDrawTime < 0){
				$remainDrawTime = 0;
			}

			return $remainDrawTime;
		}
		

		/**
		* 通过奖品id来获取奖品的名字
		* @param $awardId 奖品id
		* @return 奖品名字
		*/
		public function getAwardNameByAwardId($awardId){
			if($awardId == 0){
				return '没有抽中';
			}
			$sql = "SELECT `awa_name` FROM `award_list` WHERE `awa_id`='$awardId'";
		    $source = self::$pdo->query($sql);
		    $result = $source->fetch(PDO::FETCH_ASSOC);
		    if(!empty($result)){
		    	return $result['awa_name'];
		    }else{
		    	return '该奖品不存在';
		    }
		}	

		/**
		* 用来查询用户已经获得的奖品
		* @param $openid 用户的openid
		* @return $gainAwardArr 存放用户已经获得奖品的数组
		*/
		public function getGainedAwardByOpenId($openId){
			$sql = "SELECT `dra_award_id`,`dra_time` FROM `draw_record` WHERE `dra_open_id`='$openId' AND `dra_award_id`<>0";
		    $source = self::$pdo->query($sql);
		    $gainAwardArr = $source->fetchAll(PDO::FETCH_ASSOC);
			
		    foreach ($gainAwardArr as &$value) {
		    	$value['awa_name'] = $this->getAwardNameByAwardId($value['dra_award_id']);
		    }

		    return $gainAwardArr;
		}

		/**
		* 用来判断用户是否填写了领奖信息
		* @param $openid 用户的openid
		* @return true/false
		*/
		public function isRegisterUserInfo($openId){
			$sql = "SELECT `use_id` FROM `register_user` WHERE `use_openid` = '{$openId}'";
			$statement = self::$pdo->query($sql);
			$result = $statement->rowCount();
			if(!!$result){
				return true;
			}else{
				return false;
			}
		}

		/**
		* 用来登记用户的领奖信息
		* @param $info 用户的登记信息
		**/
		public function registerUserInfo($info){ 
			if(!empty($info)){
				$openId = mysql_escape_string($_POST['openId']);
				$stuName = mysql_escape_string($_POST['name']);
    			$stuNum = mysql_escape_string($_POST['number']);
    			$phone = mysql_escape_string($_POST['phone']);

				$addUserInfoSql = "INSERT INTO `register_user`(`use_openid`,`use_name`,`use_stu_num`,`use_phone_num`) VALUES('$openId','$stuName','$stuNum','$phone')";
	        	$result = self::$pdo->exec($addUserInfoSql);
	        	if(!!$result){
	        		return true;
	        	}else{
	        		return false;
	        	}
			}else{
				return false;
			}
	        
		}



		/**
		* 用来通过openid获取用户的领奖信息
		* @param $openId 用户的openid
		* @return $info 用户的注册信息
		**/
		public function getUserInfoByOpenId($openId){ 
			$sql = "SELECT `use_name`,`use_stu_num`,`use_phone_num` FROM `register_user` WHERE `use_openid` = '{$openId}'";
			$statement = self::$pdo->query($sql);
			$info = $statement->fetch(PDO::FETCH_ASSOC);
	        if(empty($info)){
				$info['use_name'] = '还未登记';
				$info['use_stu_num'] = '还未登记';
				$info['use_phone_num'] = '还未登记';
	        }

	        return $info;
	        
		}



		/**
		* 用来得到所有中奖者的信息
		* @return $winUserInfoArr 存放所有中奖用户信息的数组
		*/
		public function getAllWinUserInfo(){
			$sql = "SELECT `dra_open_id`,`dra_award_id`,`dra_time` FROM `draw_record` WHERE `dra_award_id`<>0";
		    $source = self::$pdo->query($sql);
		    $winUserInfoArr = $source->fetchAll(PDO::FETCH_ASSOC);
			
		    foreach ($winUserInfoArr as &$value) {
		    	$value['award_name'] = $this->getAwardNameByAwardId($value['dra_award_id']);
		    }

		    foreach ($winUserInfoArr as &$value) {
		    	$value['user_info'] = $this->getUserInfoByOpenId($value['dra_open_id']);
		    }

		    return $winUserInfoArr;
		}

	}
