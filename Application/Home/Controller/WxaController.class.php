<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
use Home\Model\ActivityModel;
use Home\Model\UserModel;

class WxaController extends Controller {
    private $activity, $user;
    //商户id
    const KEY ='fqbYMmhghvcsGXLqxwGI30NjFcjxUVeg';//可在微信商户后台生成支付秘钥
    const WECHAT_APPID ='wx391505c49f08d8f3';//小程序appid
    const WECH_ID ='1501681181';//商户号
    const WECHAT_KEY ='fqbYMmhghvcsGXLqxwGI30NjFcjxUVeg';//可在微信商户后台生成支付秘钥

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
        $appid = 'wx391505c49f08d8f3';//小程序唯一标识
        $secret = 'c6f481dd60a48d5934f3d497c2d39be1';//小程序的 app secret
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
//        if(!$user){
//            $this->xueyueNotice('接口：getHdList；错误信息：user not exist；前端传递参数：openid='.$openid);
//            $this->AjaxReturn(['errorCode'=>2,'msg'=>'user not exist']);
//        }

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
            if(!$user){
                $joinedActivcetieIds = [];
            }else{
                $joinedActivcetieIds =  $user['ycjhd'];
            }

            $condition['id']  = array('in',$joinedActivcetieIds);
            $activities = $this->activity->get($condition);
        }

        foreach ($activities as &$l){
            if($l['xct']){
                $l['xcts'] = explode(',',substr($l['xct'],0,strlen($l['xct'])-1));
                foreach ($l['xcts'] as &$xc){
                    $xc = 'http://www.snowzhai.com/xy'.$xc;
                }
            }

            if($l['zt']){
                //正在报名：当前时间小于活动开始时间
                if(time()<strtotime($l['rqs'])){
                    $l['zt'] = 1;
                    $l['ztDes'] = '正在报名';
                }
                //已截止：当前时间大于活动开始时间且小于活动结束时间
                if((time()>=strtotime($l['rqs'])) && (time()<=strtotime($l['rqz']))){
                    $l['zt'] = 2;
                    $l['ztDes'] = '已截止';
                }
                //已完成：当前时间大于活动结束时间
                if(time()>strtotime($l['rqz'])){
                    $l['zt'] = 4;
                    $l['ztDes'] = '已完成';
                }

            }else{
                $l['zt'] = 3;
                $l['ztDes'] = '已取消';
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
//        if(!$user){
//            $this->xueyueNotice('接口：getHdDetail；错误信息：openid is null；前端传递参数：openid='.$openid);
//            $this->AjaxReturn(['errorCode'=>2,'msg'=>'user not exist']);
//        }

        $hdbh = $_GET['hdbh'];
        if(is_null($hdbh)){
            $this->xueyueNotice('接口：getHdDetail；错误信息：hdbh is null；前端传递参数：openid='.$openid.';hdbh='.$hdbh);
            $this->AjaxReturn(['errorCode'=>3,'msg'=>'hdbh is null']);
        }

        $condition['hdbh'] =  $hdbh;

        $activity = current($this->activity->get($condition));
        if(is_null($activity)){
            $this->xueyueNotice('接口：getHdDetail；错误信息：activity is not exitst；前端传递参数：openid='.$openid.';hdbh='.$hdbh);
            $this->AjaxReturn(['errorCode'=>4,'msg'=>'activity is not exitst']);
        }
        //对浏览次数进行更新
        if($activity['llcs']){
            $llcs = $activity['llcs'] + 1;
        }else{
            $llcs = 1;;
        }
        $activity['llcs'] = $llcs;
        $this->activity->save($activity);

        $activity = current($this->activity->get($condition));

        $userOpenIdStrings = $activity['yjjklb'];//已经交款列表（user的openid列表）
        $userOpenIds = [];
        if($userOpenIdStrings){
            $userOpenIds = explode(',',$userOpenIdStrings);
        }

        if($activity['xct']){
            $activity['xcts'] = explode(',',substr($activity['xct'],0,strlen($activity['xct'])-1));
            foreach ($activity['xcts'] as &$xc){
                $xc = 'http://www.snowzhai.com/xy'.$xc;
            }
        }

        //活动状态进行处理
        if($activity['zt']){
            //正在报名：当前时间小于活动开始时间
            if(time()<strtotime($activity['rqs'])){
                $activity['zt'] = 1;
                $activity['ztDes'] = '正在报名';
            }
            //已截止：当前时间大于活动开始时间且小于活动结束时间
            if((time()>=strtotime($activity['rqs'])) && (time()<=strtotime($activity['rqz']))){
                $activity['zt'] = 2;
                $activity['ztDes'] = '已截止';
            }
            //已完成：当前时间大于活动结束时间
            if(time()>strtotime($activity['rqz'])){
                $activity['zt'] = 4;
                $activity['ztDes'] = '已完成';
            }

        }else{
            $activity['zt'] = 3;
            $activity['ztDes'] = '已取消';
        }


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
            $yjjklb = $openid;
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

    /**
     * 预支付请求接口(GET)
     * @param $hdbh:活动编号
     * @param $bmjg:报名价格
     * @param $openid:用户唯一标识
     * @param $spxq:商品详情(默认：雪月之恋)
     * @return  json的数据
     */
    public function payMoney(){

        //检查活动编号是否存在
        $hdbh = $_GET['hdbh'];
        if(is_null($hdbh)){
            $this->xueyueNotice('接口：payMoney；错误信息：hdbh is null；前端传递参数：hdbh='.$hdbh);
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'hdbh is null']);
        }

        //检查报名价格是否存在
        $bmjg = $_GET['bmjg'];
        if(is_null($bmjg)){
            $this->xueyueNotice('接口：payMoney；错误信息：bmjg is null；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg);
            $this->AjaxReturn(['errorCode'=>2,'msg'=>'bmjg is null']);
        }

        //检查openid是否存在
        $openid = $_GET['openid'];
        if(is_null($openid)){
            $this->xueyueNotice('接口：payMoney；错误信息：openid is null；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid);
            $this->AjaxReturn(['errorCode'=>3,'msg'=>'openid is null']);
        }
        $spxq = $_GET['spxq'];
        if(is_null($spxq)){
            $spxq = '雪月之恋';
        }

        $this->xueyueNotice('接口：payMoney；描述：开始进行下单参数的构造；请求参数：hdbh='.$hdbh.'&bmjg='.$bmjg.'&openid='.$openid.'&spxq='.$spxq);

        //统一下单参数构造
        $unifiedorder = array(
            'appid'			=> WECHAT_APPID,
            'mch_id'		=> self::WECH_ID,
            'nonce_str'		=> getNonceStr(),
            'body'			=> $spxq,
            'out_trade_no'	=> trade_no(),
            'total_fee'		=> $bmjg,
            'spbill_create_ip'	=> get_client_ip(),//客户端请求ip
            'notify_url'	=> 'http://www.snowzhai.com/xy/index.php/home/Wxa/notify',
            'trade_type'	=> 'JSAPI',
            'openid'		=> $openid
        );

        //订单号
        file_put_contents(date('Y-m-d',time())."_out_trade_no.txt", var_export("时间：".date('Y-m-d H:i:s',time())."；\r\n订单号：".$unifiedorder['out_trade_no'],true)."\r\n",FILE_APPEND);

        $unifiedorder['sign'] = makeSign($unifiedorder);
        file_put_contents(date('Y-m-d',time())."_handle_sign_time.txt", var_export("时间：".date('Y-m-d H:i:s',time())."；\r\n签名字符串：".$unifiedorder['sign'],true)."\r\n",FILE_APPEND);

        //请求数据
        $xmldata = array2xml($unifiedorder);
        file_put_contents(date('Y-m-d',time())."_handle_xml_sign_time.txt", var_export("时间：".date('Y-m-d H:i:s',time())."；\r\nxml构造：".$xmldata,true)."\r\n",FILE_APPEND);

        //发起请求
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $res = curl_post_ssl($url, $xmldata);
        if(!$res){
            return_err("Can't connect the server");
        }
        // 这句file_put_contents是用来查看服务器返回的结果 测试完可以删除了
        file_put_contents(date('Y-m-d',time())."_handle_server_result.txt", var_export("时间：".date('Y-m-d H:i:s',time())."；\r\n服务器返回的结果：".$res,true)."\r\n",FILE_APPEND);
        //file_put_contents(APP_ROOT.'/Statics/log1.txt',$res,FILE_APPEND);
        $content = xml2array($res);
        if(strval($content['result_code']) == 'FAIL'){
            return_err(strval($content['err_code_des']));
            $this->xueyueNotice('接口：payMoney；错误信息：'.strval($content['err_code_des']).'；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.'&spxq='.$spxq);
        }

        //self::return_data(array('data'=>$content));
        //根据预支付微信端返回的数据进行正式支付数据构造
        $payData = [];
        if($content['return_code'] == 'SUCCESS'){
            $data = array(
                'appId'		=> self::WECHAT_APPID,
                'timeStamp'	=> time(),
                'nonceStr'	=> getNonceStr(),
                'package'	=> 'prepay_id='.$content['prepay_id'],
                'signType'	=> 'MD5'
            );

            $data['paySign'] = makeSign($data);

            $payData['timeStamp'] = $data['timeStamp'];
            $payData['nonceStr']  = $data['nonceStr'];
            $payData['package']   = $data['package'];
            $payData['paySign']   = $data['paySign'];
        }

        if(count($payData)){
            file_put_contents(date('Y-m-d',time())."_pay_data.txt", var_export("时间：".date('Y-m-d H:i:s',time())."；\r\n预支付微信端返回的数据：".$payData,true)."\r\n",FILE_APPEND);

            $this->xueyueNotice('接口：payMoney；信息：success；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.'&spxq='.$spxq);

            $this->ajaxReturn($payData);
        }else{
            $this->xueyueNotice('接口：payMoney；错误信息：预支付微信端返回的数据为空；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.'&spxq='.$spxq);
            $this->AjaxReturn(['errorCode'=>4,'msg'=>'payData is null']);
        }

    }

    /**
     * 进行支付接口(POST)
     * @param string $prepay_id 预支付ID(调用prepay()方法之后的返回数据中获取)
     * @return  json的数据
     */
    public function pay(){

        $prepay_id = I('post.prepay_id');

        $data = array(
            'appId'		=> self::WECHAT_APPID,
            'timeStamp'	=> time(),
            'nonceStr'	=> getNonceStr(),
            'package'	=> 'prepay_id='.$prepay_id,
            'signType'	=> 'MD5'
        );

        $data['paySign'] = makeSign($data);

        $this->ajaxReturn($data);
    }

    //微信支付回调验证
    public function notify(){
        $xml = $GLOBALS['HTTP_RAW_POST_DATA'];

        // 这句file_put_contents是用来查看服务器返回的XML数据 测试完可以删除了
        //file_put_contents(APP_ROOT.'/Statics/log2.txt',$res,FILE_APPEND);

        //将服务器返回的XML数据转化为数组
        $data = xml2array($xml);
        // 保存微信服务器返回的签名sign
        $data_sign = $data['sign'];
        // sign不参与签名算法
        unset($data['sign']);
        $sign = makeSign($data);

        // 判断签名是否正确  判断支付状态
        if ( ($sign===$data_sign) && ($data['return_code']=='SUCCESS') && ($data['result_code']=='SUCCESS') ) {
            $result = $data;
            //获取服务器返回的数据
            $order_sn = $data['out_trade_no'];			//订单单号
            $openid = $data['openid'];					//付款人openID
            $total_fee = $data['total_fee'];			//付款金额
            $transaction_id = $data['transaction_id']; 	//微信支付流水号

            //更新数据库
//            $this->updateDB($order_sn,$openid,$total_fee,$transaction_id);

        }else{
            $result = false;
        }
        // 返回状态给微信服务器
        if ($result) {
            $str='<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        }else{
            $str='<xml><return_code><![CDATA[FAIL]]></return_code><return_msg><![CDATA[签名失败]]></return_msg></xml>';
        }
        echo $str;
        return $result;
    }

    /**
     * 支付接口以前测试
     * @param $hdbh:活动编号
     * @param $bmjg:报名价格
     * @param $openid:用户唯一标识
     * @param $spxq:商品详情(默认：雪月之恋)
     * https://developers.weixin.qq.com/miniprogram/dev/api/api-pay.html#wxrequestpaymentobject
     * @return string
     */
    function payMoneyTestOld(){//获取用户openid（用户唯一标示）
        import('Common.Wxpay.lib.WxPay#Config',APP_PATH,'.php');
        import('Common.Wxpay.lib.WxPay#Api',APP_PATH,'.php');
        import('Common.Wxpay.qrcode.WxPay#NativePay',APP_PATH,'.php');
        import('Common.Wxpay.qrcode.log',APP_PATH,'.php');
//        require_once __ROOT__."/Api/wxpay/lib/WxPay.Api.php";
//        require_once __ROOT__."/Api/wxpay/payment/WxPay.JsApiPay.php";
//        require_once APP_ROOT.'/Api/wxpay/payment/log.php';
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
        if(!is_null($spxq)){
            $spxq = '雪月之恋';
//            $this->xueyueNotice('接口：payMoney；错误信息：spxq is null；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
//            $this->AjaxReturn(['errorCode'=>1,'msg'=>'spxq is null']);
        }

        $this->xueyueNotice('接口：payMoney；请求参数：hdbh='.$hdbh.'&bmjg='.$bmjg.'&openid='.$openid.'&spxq='.$spxq);
        //post提交的地址
        $url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
        $appid = 'wx391505c49f08d8f3';//小程序唯一标识
        $mch_id = '1501681181';//微信支付分配的商户号
        $nonceStr = $this->createNonceStr();//随机字符串，不长于32位。推荐随机数生成算法
        $body = $spxq;//商品描述
        $out_trade_no = $hdbh;//商户订单号
        $total_fee = $bmjg;//标价金额
        $spbill_create_ip = '118.89.196.141';//终端IP
        $notify_url = 'http://www.snowzhai.com/xy/index.php/home/Wxa/getPayNotice
';//通知地址
        $trade_type = 'JSAPI';//交易类型

        //***********
        $signArr = [
            'appid' => $appid,
            'mch_id'=> $mch_id,
            'body'=> $body,
            //'sing'=>'CBEF716EF1A065E6979DE3170BE3B6B8',
        ];
        //要验证的签名数组
        $sign = $this->setSing($signArr);//签名
        $this->xueyueNotice('接口：payMoney；签名：sign='.$sign);
        file_put_contents("支付签名接口.txt", var_export($sign,true)."\r\n",FILE_APPEND);
        if(!$this->checkSing($sign)) {
            $this->xueyueNotice('接口：payMoney；错误信息：验证签名失败；前端传递参数：hdbh='.$hdbh.';bmjg='.$bmjg.';openid='.$openid.';spxq='.$spxq);
            $this->AjaxReturn(['errorCode'=>16,'msg'=>'signError','errmsg'=>'验证签名失败']);
        }
        //***********

        $str6 = 'appid='.$appid.'&mch_id='.$mch_id.'&nonceStr='.$nonceStr.'&sign='.$sign.'&body='.$body.'&out_trade_no='.$out_trade_no.'&total_fee='.$total_fee.'&spbill_create_ip='.$spbill_create_ip.'&notify_url='.$notify_url.'&trade_type='.$trade_type;
        $jsonstr=($str6);
        list($returncode,$returncontent)=http_post_json($url,$jsonstr);
        $obj = json_decode($returncontent);//json_decode是json解密的相关的函数
        file_put_contents("支付签名接口返回数据.txt", var_export($obj,true)."\r\n",FILE_APPEND);
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

}


