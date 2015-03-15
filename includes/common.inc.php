<?php

	//防止恶意调用
	if (!defined('IN_TG')) {
		exit('Access Denied!');
	}

	//设置字符集编码
	header('Content-Type: text/html; charset=utf-8');

	//转换硬路径常量
	define('ROOT_PATH',substr(dirname(__FILE__),0,-8));

	//拒绝PHP低版本
	if (PHP_VERSION < '4.1.0') {
		exit('Version is too Low!');
	}

	//引入逻辑处理类
	require ROOT_PATH.'includes/logic.class.php';

	//数据库连接
	define('HOST','localhost');
	define('DBNAME','weixin_rootervote');
	define('USER','redrock');
	define('PASSWORD','hongyanredrock');

	define('CHECK_OPENID_URL', 'http://hongyan.cqupt.edu.cn/MagicLoop/index.php?s=/addon/UserCenter/UserCenter/checkIsWatch/');
?>