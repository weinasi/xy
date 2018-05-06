<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

if (version_compare(PHP_VERSION, '5.3.0', '<')) die('require PHP > 5.3.0 !');

/**
 * 系统调试设置
 * 项目正式部署后请设置为false
 */
define('APP_DEBUG', true);

/**
 * 应用目录设置
 * 安全期间，建议安装调试完成后移动到非WEB目录
 */
// 定义应用目录
define('APP_PATH', './Application/');
define('APP_ROOT', dirname(__FILE__));

//微信小程序支付相关信息
define('WECHAT_APPID','wx391505c49f08d8f3');//小程序appid
define('WECH_ID','1501681181');//商户号
define('WECHAT_KEY','fqbYMmhghvcsGXLqxwGI30NjFcjxUVeg');//可在微信商户后台生成支付秘钥
define('WECHAT_SECRET','c6f481dd60a48d5934f3d497c2d39be1');//小程序的 app secret

if (!is_file(APP_PATH . 'User/Conf/config.php')) {
    header('Location: ./install.php');
    exit;
}

/**
 * 缓存目录设置
 * 此目录必须可写，建议移动到非WEB目录
 */
define('RUNTIME_PATH', './Runtime/');

/**
 * 引入核心入口
 * ThinkPHP亦可移动到WEB以外的目录
 */
require './ThinkPHP/ThinkPHP.php';