<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
use Home\Model\ActivityModel;
use Home\Model\UserModel;

class WxaController extends Controller {
    private $activity, $user;
    //商户id
    const KEY ='kkkkksdio87923';

    private function xueyueNotice($message)
    {
        $time = date('Y-m-d H:i:s',time());
        $message = '时间：'.$time.';'.$message;
        $webhook = "https://oapi.dingtalk.com/robot/send?access_token=6a2165af0a05448f168d7bdd7c04e143b37fcd0e2e201fd72b6764d9d38ee330";
//        $message="我就是我, 是不一样的烟火";
        $data = array ('msgtype' => 'text','text' => array ('content' => $message));
        $data_string = json_encode($data);

        $result = request_by_curl($webhook, $data_string);
//        echo $result;
    }
    public function __construct() {
        $this->activity = new ActivityModel();
        $this->user = new UserModel();
        parent::__construct();
    }

    /**
     * 获取用户openid
     * @param $code:用户登录凭证
     * https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
     * 'https://api.weixin.qq.com/sns/jscode2session?appid=APPID&secret=SECRET&js_code=JSCODE&grant_type=authorization_code
     * @return string
     */
    function getOpenid(){//获取用户openid（用户唯一标示）
        $code = $_GET['code'];
        if(is_null($code)){
            $this->xueyueNotice('接口：getOpenid；错误信息：code is null；前端传递参数：code='.$code);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'code is null']);
        }
        //post提交的地址
        $url = 'https://api.weixin.qq.com/sns/jscode2session';
        $appid = 'wx28027d8e7fc9e84b';//小程序唯一标识
        $secret = '7a7264007fa3b970a2ae8cebc85d1772';//小程序的 app secret
        $js_code = $code;
        $grant_type = 'authorization_code';//填写为 authorization_code
        $str6 = 'appid='.$appid.'&secret='.$secret.'&js_code='.$js_code.'&grant_type='.$grant_type;
        $jsonstr=($str6);
        list($returncode,$returncontent)=http_post_json($url,$jsonstr);
        $obj = json_decode($returncontent);//json_decode是json解密的相关的函数
        $openid = $obj->openid;//获取result的值
        $errcode = $obj->errcode;//获取result的值
        if(isset($errcode)){
            $this->xueyueNotice('接口：getOpenid；错误信息：'.$obj->errmsg.'；前端传递参数：code='.$code);
            $this->AjaxReturn(['errorCode'=>$errcode,'msg'=>'error','errmsg'=>$obj->errmsg]);
        }else{
            if(isset($obj->unionid)){
                $this->xueyueNotice('接口：getOpenid；信息：success；前端传递参数：code='.$code);
                $this->AjaxReturn([
                    'errorCode'     => 0,
                    'msg'           => 'success',
                    'openid'        => $obj->openid,
                    'session_key'   => $obj->session_key,
                    'unionid'        => $obj->unionid
                ]);
            }else{
                $this->xueyueNotice('接口：getOpenid；信息：success；前端传递参数：code='.$code);
                $this->AjaxReturn([
                    'errorCode'     => 0,
                    'msg'           => 'success',
                    'openid'        => $obj->openid,
                    'session_key'   => $obj->session_key
                ]);
            }

        }
    }

    /**
     * 支付接口
     * @param $hdbh:活动编号
     * @param $bmjg:报名价格
     * @param $openid:用户唯一标识
     * @param $spxq:商品详情(默认：雪月之恋)
     * https://developers.weixin.qq.com/miniprogram/dev/api/api-pay.html#wxrequestpaymentobject
     * @return string
     */
    function payMoney(){//获取用户openid（用户唯一标示）
        $hdbh = $_GET['hdbh'];
        if(is_null($hdbh)){
            $this->xueyueNotice('接口：payMoney；错误信息：hdbh is null；前端传递参数：hdbh='.$hdbh);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'hdbh is null']);
        }
        $bmjg = $_GET['bmjg'];
        if(is_null($bmjg)){
            $this->xueyueNotice('接口：payMoney；错误信息：bmjg is null；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'bmjg is null']);
        }
        $openid = $_GET['openid'];
        if(is_null($openid)){
            $this->xueyueNotice('接口：payMoney；错误信息：openid is null；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'openid is null']);
        }
        $spxq = $_GET['spxq'];
        if(is_null($spxq)){
            $this->xueyueNotice('接口：payMoney；错误信息：spxq is null；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'spxq is null']);
        }
        //post提交的地址
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $appid = 'wx28027d8e7fc9e84b';//小程序唯一标识
        $mch_id = '';//微信支付分配的商户号
        $nonceStr = $this->createNonceStr();//随机字符串，不长于32位。推荐随机数生成算法
        $body = $spxq;//商品描述
        $out_trade_no = '';//商户订单号
        $total_fee = $bmjg;//标价金额
        $spbill_create_ip = '118.89.196.141';//终端IP
        $notify_url = '';//通知地址
        $trade_type = '';//交易类型

        //***********
        $signArr = [
            'appid' => $appid,
            'mch_id'=> $mch_id,
            'body'=> $body,
            //'sing'=>'CBEF716EF1A065E6979DE3170BE3B6B8',
        ];
        //要验证的签名数组
        $sign = $this->setSing($signArr);//签名

        if(!$this->checkSing($sign)) {
            $this->xueyueNotice('接口：payMoney；错误信息：验证签名失败；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
            $this->AjaxReturn(['errorCode'=>16,'msg'=>'signError','errmsg'=>'验证签名失败']);
        }
        //***********

        $str6 = 'appid='.$appid.'&mch_id='.$mch_id.'&nonceStr='.$nonceStr.'&sign='.$sign.'&body='.$body.'&out_trade_no='.$out_trade_no.'&total_fee='.$total_fee.'&spbill_create_ip='.$spbill_create_ip.'&notify_url='.$notify_url.'&trade_type='.$trade_type;
        $jsonstr=($str6);
        list($returncode,$returncontent)=http_post_json($url,$jsonstr);
        $obj = json_decode($returncontent);//json_decode是json解密的相关的函数

        $return_code = $obj->return_code;//获取return_code的值
        if($return_code == 'SUCCESS'){
            $appid        = $obj->appid;//调用接口提交的小程序ID
            $mch_id       = $obj->mch_id;//调用接口提交的商户号
            $device_info  = $obj->device_info;//自定义参数，可以为请求支付的终端设备号等
            $nonce_str    = $obj->nonce_str;//微信返回的随机字符串
            $sign         = $obj->sign;//微信返回的签名值，详见签名算法
            $result_code  = $obj->result_code;//SUCCESS/FAIL
            $err_code     = $obj->err_code;//详细参见下文错误列表
            $err_code_des = $obj->err_code_des;//错误信息描述
            if($result_code == 'SUCCESS'){
                $trade_type = $obj->trade_type;//交易类型，取值为：JSAPI，NATIVE，APP等，说明详见参数规定
                $prepay_id  = $obj->prepay_id;//微信生成的预支付会话标识，用于后续接口调用中使用，该值有效期为2小时
                $code_url   = $obj->code_url;//trade_type为NATIVE时有返回，用于生成二维码，展示给用户进行扫码支付

                $this->xueyueNotice('接口：payMoney；信息：SUCCESS；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);

                $this->AjaxReturn([
                    'errorCode'     => 0,
                    'msg'           => 'SUCCESS',
                    'appid'         => $appid,
                    'mch_id'        => $mch_id,
                    'device_info'   => $device_info,
                    'nonce_str'     => $nonce_str,
                    'sign'          => $sign,
                    'result_code'   => $result_code,
                    'err_code'      => $err_code,
                    'err_code_des'  => $err_code_des,
                    'trade_type'    => $trade_type,
                    'prepay_id'     => $prepay_id,
                    'code_url'      => $code_url,
                    'timeStamp'     => time()
                ]);
            }else{

                $this->xueyueNotice('接口：payMoney；信息：SUCCESS；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);

                $this->AjaxReturn([
                    'errorCode'     => 0,
                    'msg'           => 'SUCCESS',
                    'appid'         => $appid,
                    'mch_id'        => $mch_id,
                    'device_info'   => $device_info,
                    'nonce_str'     => $nonce_str,
                    'sign'          => $sign,
                    'result_code'   => $result_code,
                    'err_code'      => $err_code,
                    'err_code_des'  => $err_code_des,
                    'timeStamp'     => time()
                ]);
            }
        }else{
            if($return_code == 'NOAUTH'){
                $this->xueyueNotice('接口：payMoney；错误信息：商户未开通此接口权限；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>1,'msg'=>'NOAUTH','errmsg'=>'商户未开通此接口权限']);
            } elseif ($return_code == 'NOTENOUGH'){
                $this->xueyueNotice('接口：payMoney；错误信息：用户帐号余额不足；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>2,'msg'=>'NOTENOUGH','errmsg'=>'用户帐号余额不足']);
            }elseif ($return_code == 'ORDERPAID'){
                $this->xueyueNotice('接口：payMoney；错误信息：商户订单已支付，无需重复操作；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>3,'msg'=>'ORDERPAID','errmsg'=>'商户订单已支付，无需重复操作']);
            }elseif ($return_code == 'ORDERCLOSED'){
                $this->xueyueNotice('接口：payMoney；错误信息：当前订单已关闭，无法支付；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>4,'msg'=>'ORDERCLOSED','errmsg'=>'当前订单已关闭，无法支付']);
            }elseif ($return_code == 'SYSTEMERROR'){
                $this->xueyueNotice('接口：payMoney；错误信息：系统超时；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>5,'msg'=>'SYSTEMERROR','errmsg'=>'系统超时']);
            }elseif ($return_code == 'APPID_NOT_EXIST'){
                $this->xueyueNotice('接口：payMoney；错误信息：参数中缺少APPID；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>6,'msg'=>'APPID_NOT_EXIST','errmsg'=>'参数中缺少APPID']);
            }elseif ($return_code == 'MCHID_NOT_EXIST'){
                $this->xueyueNotice('接口：payMoney；错误信息：参数中缺少MCHID；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>7,'msg'=>'MCHID_NOT_EXIST','errmsg'=>'参数中缺少MCHID']);
            }elseif ($return_code == 'APPID_MCHID_NOT_MATCH'){
                $this->xueyueNotice('接口：payMoney；错误信息：appid和mch_id不匹配；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>8,'msg'=>'APPID_MCHID_NOT_MATCH','errmsg'=>'appid和mch_id不匹配']);
            }elseif ($return_code == 'LACK_PARAMS'){
                $this->xueyueNotice('接口：payMoney；错误信息：缺少必要的请求参数；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>9,'msg'=>'LACK_PARAMS','errmsg'=>'缺少必要的请求参数']);
            }elseif ($return_code == 'OUT_TRADE_NO_USED'){
                $this->xueyueNotice('接口：payMoney；错误信息：同一笔交易不能多次提交；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>10,'msg'=>'OUT_TRADE_NO_USED','errmsg'=>'同一笔交易不能多次提交']);
            }elseif ($return_code == 'SIGNERROR'){
                $this->xueyueNotice('接口：payMoney；错误信息：参数签名结果不正确；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>11,'msg'=>'SIGNERROR','errmsg'=>'参数签名结果不正确']);
            }elseif ($return_code == 'XML_FORMAT_ERROR'){
                $this->xueyueNotice('接口：payMoney；错误信息：XML格式错误；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>12,'msg'=>'XML_FORMAT_ERROR','errmsg'=>'XML格式错误']);
            }elseif ($return_code == 'REQUIRE_POST_METHOD'){
                $this->xueyueNotice('接口：payMoney；错误信息：未使用post传递参数；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>13,'msg'=>'REQUIRE_POST_METHOD','errmsg'=>'未使用post传递参数']);
            }elseif ($return_code == 'POST_DATA_EMPTY'){
                $this->xueyueNotice('接口：payMoney；错误信息：post数据不能为空；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>14,'msg'=>'POST_DATA_EMPTY','errmsg'=>'post数据不能为空']);
            }elseif ($return_code == 'NOT_UTF8'){
                $this->xueyueNotice('接口：payMoney；错误信息：未使用指定编码格式；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
                $this->AjaxReturn(['errorCode'=>15,'msg'=>'NOT_UTF8','errmsg'=>'未使用指定编码格式']);
            }

        }

    }


    private function createNonceStr($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";

        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }

        return $str;
    }
    /**
     * 生成签名  start
     */
    private function getSign($arr)
    {
        //去除数组中的空值
        $arr = array_filter($arr);
        //如果数组中有签名删除签名
        if(isset($arr['sing']))
        {
            unset($arr['sing']);
        }
        //按照键名字典排序
        ksort($arr);
        //生成URL格式的字符串
        //http_build_query()中文自动转码需要处理下
        $str = http_build_query($arr)."&key=".self::KEY;
        //echo  $str;
        //appid=dkdfg&body=2347%E4%BA%AC%E4%B8%9C%E5%95%86%E5%9F%8E&mch_id=sdfgd&key=kkkkksdio87923CBEF716EF1A065E6979DE3170BE3B6B8
        $str = $this->arrToUrl($str);
        //echo  $str;
        //header("Content-type: text/html; charset=utf-8");
//       echo  strtoupper(md5($str));
        return  strtoupper(md5($str));
    }

    //获取签名 待签名的数组
    private function setSing($arr)
    {
        $arr['sing'] = $this->getSign($arr);
        return $arr;
    }

    //URL解码为中文
    private function arrToUrl($str)
    {
        return urldecode($str);
    }
    //验证签名
    private function checkSing($arr)
    {
        //获取签名
        $sing = $this->getSign($arr);
        if($sing == $arr['sing'])
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    /**
     * 生成签名  end
     */


    /**
     * 活动列表查询
     * @param $openid:用户唯一标识
     * @param $type:类型(0:将来活动 1:历史活动 2:已参加活动)
     * @return array
     */
    function getHdList(){
//        file_put_contents("text522.txt", var_export($_GET['openid'],true)."\r\n",FILE_APPEND);
        $openid = $_GET['openid'];
        if(is_null($openid)){
            $this->xueyueNotice('接口：getHdList；错误信息：openid is null；前端传递参数：openid='.$openid);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'openid is null']);
        }

        $data['openid'] = $_GET['openid'];
        $user = current($this->user->get($data));
        if(!$user){
            $this->xueyueNotice('接口：getHdList；错误信息：user not exist；前端传递参数：openid='.$openid);
            $this->AjaxReturn(['errorCode'=>2,'msg'=>'user not exist']);
        }

        $type = $_GET['type'];
        if(is_null($type)){
            $this->xueyueNotice('接口：getHdList；错误信息：type is null；前端传递参数：openid='.$openid.';type='.$type);
            $this->AjaxReturn(['errorCode'=>3,'msg'=>'type is null']);
        }

        $nowTime =  date("Y-m-d H:i:s", time());

        if ($type<2){
            switch ($type) {
                case 0:
                    $condition['rqs'] = array('gt',$nowTime);
                    break;
                case 1:
                    $condition['rqs'] = array('lt',$nowTime);
                    break;
                default:
                    $condition['rqs'] = array('gt',$nowTime);
            }
            $activities = $this->activity->get($condition);
        }else{
            $joinedActivcetieIds =  $user['ycjhd'];
            $condition['id']  = array('in',$joinedActivcetieIds);
            $activities = $this->activity->get($condition);
        }

        foreach ($activities as &$l){
            if($l['xct']){
                $l['xcts'] = explode(',',substr($l['xct'],0,strlen($l['xct'])-1));
            }
        }

        $this->xueyueNotice('接口：getHdList；信息：success；前端传递参数：openid='.$openid.';type='.$type);
        $this->AjaxReturn(['errorCode'=>0,'msg'=>'success','activities'=>$activities]);
    }


    /**
     * 活动详情查询
     * @param $hdbh:活动编号
     * @param $openid:用户唯一标识
     * @return array
     */
    function getHdDetail(){

        $openid = $_GET['openid'];
        if(is_null($openid)){
            $this->xueyueNotice('接口：getHdDetail；错误信息：openid is null；前端传递参数：openid='.$openid);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'openid is null']);
        }

        $data['openid'] = $_GET['openid'];
        $user = current($this->user->get($data));
        if(!$user){
            $this->xueyueNotice('接口：getHdDetail；错误信息：openid is null；前端传递参数：openid='.$openid);
            $this->AjaxReturn(['errorCode'=>2,'msg'=>'user not exist']);
        }

        $hdbh = $_GET['hdbh'];
        if(is_null($hdbh)){
            $this->xueyueNotice('接口：getHdDetail；错误信息：hdbh is null；前端传递参数：openid='.$openid.';hdbh='.$hdbh);
            $this->AjaxReturn(['errorCode'=>3,'msg'=>'hdbh is null']);
        }

        $condition['hdbh'] =  $hdbh;

        $activity = current($this->activity->get($condition));

        if($activity['xct']){
            $activity['xcts'] = explode(',',substr($activity['xct'],0,strlen($activity['xct'])-1));
        }

        $userOpenIdStrings = $activity['yjjklb'];//已经交款列表（user的openid列表）
        $userOpenIds = [];
        if($userOpenIdStrings){
            $userOpenIds = explode(',',$userOpenIdStrings);
        }

        //对浏览次数进行更新
        if($activity['llcs']){
            $llcs = $activity['llcs'] + 1;
        }else{
            $llcs = 1;;
        }
        $activity['llcs'] = $llcs;

        if($activity['xct']){
            $activity['xcts'] = explode(',',substr($activity['xct'],0,strlen($activity['xct'])-1));
        }

        $this->activity->save($activity);


        if (in_array($openid,$userOpenIds)) {
            $activity['sfyjjk'] = true;
        } else {
            $activity['sfyjjk'] = false;
        }

        $this->xueyueNotice('接口：getHdDetail；信息：success；前端传递参数：openid='.$openid.';hdbh='.$hdbh);

        $this->AjaxReturn(['errorCode'=>0,'msg'=>'success','activity'=>$activity]);
    }


    /**
     * 活动报名
     * @param $hdbh:活动编号
     * @param $bmjg:报名价格
     * ---------------------
     * @param $openid:用户唯一标识
     * @param $wxh:微信号(有的为空)
     * @param $xb:性别(1男性，2女性)
     * @param $tx:头像(https://wx.qlogo.cn/mmopen/vi_32/Q0j4TwGTfTIRefvw0SWu1SjEk3UAvyDxFmAibQZrdMSSUbyFCJON1Y34Qof74LVbMOHciaib5n0swOPJn7ROx8nQA/0)
     * @return array
     */
    function storeHdBm(){
        $hdbh = $_POST['hdbh'];
        if(is_null($hdbh)){
            $this->xueyueNotice('接口：storeHdBm；错误信息：hdbh is null；前端传递参数：hdbh='.$hdbh);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'hdbh is null']);
        }

        $condition1['hdbh'] =  $hdbh;
        $activity = current($this->activity->get($condition1));
        if(!$activity){
            $this->xueyueNotice('接口：storeHdBm；错误信息：activity is not exist；前端传递参数：hdbh='.$hdbh);
            $this->AjaxReturn(['errorCode'=>2,'msg'=>'activity is not exist']);
        }

        $bmjg = $_POST['bmjg'];
        if(is_null($bmjg)){
            $this->xueyueNotice('接口：storeHdBm；错误信息：bmjg is null；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg);
            $this->AjaxReturn(['errorCode'=>3,'msg'=>'bmjg is null']);
        }

        $openid = $_POST['openid'];
        if(is_null($openid)){
            $this->xueyueNotice('接口：storeHdBm；错误信息：openid is null；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid);
            $this->AjaxReturn(['errorCode'=>4,'msg'=>'openid is null']);
        }


        $condition2['openid'] = $openid;
        $user = current($this->user->get($condition2));
        if($user){
            if($user['ycjhd']){
                $ycjhd = $user['ycjhd'].','.$hdbh;
            }else{
                $ycjhd = $hdbh;
            }
        }else{
            $ycjhd = $hdbh;
        }

        $user['openid'] = $openid;
        $user['wxh'] = $_POST['wxh'];
        $user['xb'] = $_POST['xb'];
        $user['tx'] = $_POST['tx'];
        $user['ycjhd'] = $ycjhd;
        $result1 = $this->user->save($user);

        $activity['hdbh'] = $hdbh;
        $activity['bmjg'] = $_POST['bmjg'];

        //已经缴费列表
        if($activity['yjjklb']){
            $yjjklb = $activity['yjjklb'].','.$openid;
        }else{
            $yjjklb = $openid;;
        }
        $activity['yjjklb'] = $yjjklb;

        //已报总数量
        if($activity['bmzsl']){
            $bmzsl = $activity['bmzsl'] + 1;
        }else{
            $bmzsl = 1;
        }
        $activity['bmzsl'] = $bmzsl;

        $condition3['openid'] = $openid;
        $newUser = current($this->user->get($condition3));
        if($newUser['xb'] == 1){
            //报名男士数量
            if($activity['bmmansl']){
                $bmmansl = $activity['bmmansl'] + 1;
            }else{
                $bmmansl = 1;
            }
            $activity['bmmansl'] = $bmmansl;

            //男士头像列表
            if($activity['mantxlb']){
                $mantxlb = $activity['mantxlb'] .','. $newUser['tx'];
            }else{
                $mantxlb = $newUser['tx'];
            }
            $activity['mantxlb'] = $mantxlb;
        }else{
            //报名女士数量
            if($activity['bmwomensl']){
                $bmwomensl = $activity['bmwomensl'] + 1;
            }else{
                $bmwomensl = 1;
            }
            $activity['bmwomensl'] = $bmwomensl;

            //女士头像列表
            if($activity['womantxlb']){
                $womantxlb = $activity['womantxlb'] .','. $newUser['tx'];
            }else{
                $womantxlb = $newUser['tx'];
            }
            $activity['womantxlb'] = $womantxlb;
        }

        $result2 = $this->activity->save($activity);

        if($result1 && $result2){
            $this->xueyueNotice('接口：storeHdBm；信息：sucess；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid);
            $this->AjaxReturn(['errorCode'=>0,'msg'=>'sucess']);
        }else{
            $this->xueyueNotice('接口：storeHdBm；错误信息：save is error；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid);
            $this->AjaxReturn(['errorCode'=>5,'msg'=>'save is error']);
        }

    }

    function activities(){
        $activities = $this->activity->select();
        $this -> assign('activities',$activities);
        $this->display();
    }

    function store(){
        $id = $_GET['id'];
        $errorCode= $_GET['errorCode'];
        $msg = $_GET['msg'];
        if($errorCode){
            switch ($errorCode) {
                case 1:
                    $this -> assign('errorHdmc',$msg);
                    break;
                case 2:
                    $this -> assign('errorHdmc',$msg);
                    break;
                case 3:
                    $this -> assign('errorHdbh',$msg);
                    break;
                case 4:
                    $this -> assign('errorRqs',$msg);
                    break;
                case 5:
                    $this -> assign('errorRqz',$msg);
                    break;
                case 6:
                    $this -> assign('errorDd',$msg);
                    break;
                case 7:
                    $this -> assign('errorLongitude',$msg);
                    break;
                case 8:
                    $this -> assign('errorLatitude',$msg);
                    break;
                case 9:
                    $this -> assign('errorName',$msg);
                    break;
                case 10:
                    $this -> assign('errorFmt',$msg);
                    break;
                case 11:
                    $this -> assign('errorZt',$msg);
                    break;
                case 12:
                    $this -> assign('errorZsl',$msg);
                    break;
                case 13:
                    $this -> assign('errorBmjg',$msg);
                    break;
                case 14:
                    $this -> assign('errorXct',$msg);
                    break;
                case 15:
                    $this -> assign('errorMansl',$msg);
                    break;
                case 16:
                    $this -> assign('errorWomensl',$msg);
                    break;
                case 17:
                    $this -> assign('errorQtsl',$msg);
                    break;
                case 18:
                    $this -> assign('errorLlcs',$msg);
                    break;
                case 19:
                    $this -> assign('errorBz',$msg);
                    break;
                case 20:
                    $this -> assign('errorHdxq',$msg);
                    break;
                default:
                    $this -> assign('errorHdmc',$msg);
            }
        }
        $condition1['id'] =  $id;

        $activity = current($this->activity->get($condition1));
        $this -> assign('activity',$activity);
        $this->display();
    }
    //添加活动
    function storeActivity(){
        $id = $_POST['id'];
        $type = 'create';
        if($id){
            $condition1['id'] =  $id;
            $ac = current($this->activity->get($condition1));
            if(!$ac){
                $this->redirect('Wxa/store', array('errorCode'=>1,'msg'=>'活动不存在！'));
            }
            $type = 'edit';
        }

        $hdmc = $_POST['hdmc'];
        if(!$hdmc){
            $this->redirect('Wxa/store', array('errorCode'=>2,'msg'=>'活动名称不能为空！'));
        }

        $hdbh = $_POST['hdbh'];
        if(!$hdbh){
            $this->redirect('Wxa/store', array('errorCode'=>3,'msg'=>'活动编号不能为空！'));
        }

        $rqs = $_POST['rqs'];
        if(!$rqs){
            $this->redirect('Wxa/store', array('errorCode'=>4,'msg'=>'开始日期不能为空！'));
        }

        $rqz = $_POST['rqz'];
        if(!$rqz){
            $this->redirect('Wxa/store', array('errorCode'=>5,'msg'=>'结束日期不能为空！'));
        }

        $dd = $_POST['dd'];
        if(!$dd){
            $this->redirect('Wxa/store', array('errorCode'=>6,'msg'=>'地点不能为空！'));
        }

        $longitude = $_POST['longitude'];
        if(!$longitude){
            $this->redirect('Wxa/store', array('errorCode'=>7,'msg'=>'经度不能为空！'));
        }

        $latitude = $_POST['latitude'];
        if(!$latitude){
            $this->redirect('Wxa/store', array('errorCode'=>8,'msg'=>'维度不能为空！'));
        }

        $name = $_POST['name'];
        if(!$name){
            $this->redirect('Wxa/store', array('errorCode'=>9,'msg'=>'位置名称不能为空！'));
        }

        $fmt = $_POST['fmt'];
        if(!$fmt){
            $this->redirect('Wxa/store', array('errorCode'=>10,'msg'=>'封面图不能为空！'));
        }

        $zt = $_POST['zt'];
        if(!$zt){
            $this->redirect('Wxa/store', array('errorCode'=>11,'msg'=>'状态不能为空！'));
        }

        $zsl = $_POST['zsl'];
        if(!$zsl){
            $this->redirect('Wxa/store', array('errorCode'=>12,'msg'=>'总数量不能为空！'));
        }

        $bmjg = $_POST['bmjg'];
        if(!$bmjg){
            $this->redirect('Wxa/store', array('errorCode'=>13,'msg'=>'报名价格不能为空！'));
        }

        $xct = $_POST['xct'];
        if(!$xct){
            $this->redirect('Wxa/store', array('errorCode'=>14,'msg'=>'宣传图不能为空！'));
        }

        $mansl =  $_POST['mansl'];
        if(!$mansl){
            $this->redirect('Wxa/store', array('errorCode'=>15,'msg'=>'男士数量不能为空！'));
        }

        $womensl = $_POST['womensl'];
        if(!$womensl){
            $this->redirect('Wxa/store', array('errorCode'=>16,'msg'=>'女士数量不能为空！'));
        }

        $qtsl = $_POST['qtsl'];
        if(!$qtsl){
            $this->redirect('Wxa/store', array('errorCode'=>17,'msg'=>'其他数量不能为空！'));
        }

        $llcs = $_POST['llcs'];
        if(!$llcs){
            $this->redirect('Wxa/store', array('errorCode'=>18,'msg'=>'浏览次数不能为空！'));
        }

        $bz = $_POST['bz'];
        if(!$bz){
            $this->redirect('Wxa/store', array('errorCode'=>19,'msg'=>'备注不能为空！'));
        }

        $hdxq = $_POST['hdxq'];
        if(!$hdxq){
            $this->redirect('Wxa/store', array('errorCode'=>20,'msg'=>'活动详情不能为空！'));
        }

        $activity['id'] = $id;
        $activity['hdmc'] = $hdmc;
        $activity['hdbh'] = $hdbh;
        $activity['rqs'] = $rqs;
        $activity['rqz'] = $rqz;
        $activity['dd'] = $dd;
        $activity['longitude'] = $longitude;
        $activity['latitude'] = $latitude;
        $activity['name'] = $name;
        $activity['fmt'] = $fmt;
        $activity['zt'] = $zt;
        $activity['zsl'] = $zsl;
        $activity['bmjg'] = $bmjg;
        $activity['xct'] = $xct;
        $activity['mansl'] = $mansl;
        $activity['womensl'] = $womensl;
        $activity['qtsl'] = $qtsl;
        $activity['llcs'] = $llcs;
        $activity['bz'] = $bz;
        $activity['hdxq'] = $hdxq;

        if($type == 'edit'){
            $result = $this->activity->save($activity);
            if(!$result){
                $this->redirect('Wxa/store', array('errorCode'=>22,'msg'=>'save error'));
            }else{
                $this->redirect('Wxa/store', array('id' => $id));
            }
        }else{
            $result = $this->activity->add($activity);
            if(!$result){
                $this->redirect('Wxa/store', array('errorCode'=>22,'msg'=>'save error'));
            }else{
                $this->redirect('Wxa/store', array('id' => $result));
            }
        }

    }


}


