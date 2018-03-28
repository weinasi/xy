<?php
namespace Home\Controller;
use Think\Controller;
use Think\Model;
use Home\Model\ActivityModel;
use Home\Model\UserModel;

class WxaController extends Controller {
    private $activity, $user;
    public function __construct() {
        $this->activity = new ActivityModel();
        $this->user = new UserModel();
        parent::__construct();
    }

    /**
     * 获取用户openid
     * @param $code:用户登录凭证
     * @return string
     */
    function getOpenid(){//获取用户openid（用户唯一标示）
        // https://mp.weixin.qq.com/debug/wxadoc/dev/api/api-login.html
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
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'openid is null']);
        }

        $data['openid'] = $_GET['openid'];
        $user = current($this->user->get($data));
        if(!$user){
            $this->AjaxReturn(['errorCode'=>2,'msg'=>'user not exist']);
        }

        $type = $_GET['type'];
        if(is_null($type)){
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
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'openid is null']);
        }

        $data['openid'] = $_GET['openid'];
        $user = current($this->user->get($data));
        if(!$user){
            $this->AjaxReturn(['errorCode'=>2,'msg'=>'user not exist']);
        }

        $hdbh = $_GET['hdbh'];
        if(is_null($hdbh)){
            $this->AjaxReturn(['errorCode'=>3,'msg'=>'hdbh is null']);
        }

        $condition['hdbh'] =  $hdbh;

        $activity = current($this->activity->get($condition));
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
        $this->activity->save($activity);


        if (in_array($openid,$userOpenIds)) {
            $activity['sfyjjk'] = true;
        } else {
            $activity['sfyjjk'] = false;
        }
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
            $this->AjaxReturn(['errorCode'=>1,'msg'=>'hdbh is null']);
        }

        $condition1['hdbh'] =  $hdbh;
        $activity = current($this->activity->get($condition1));
        if(!$activity){
            $this->AjaxReturn(['errorCode'=>2,'msg'=>'activity is not exist']);
        }

        $bmjg = $_POST['bmjg'];
        if(is_null($bmjg)){
            $this->AjaxReturn(['errorCode'=>3,'msg'=>'bmjg is null']);
        }

        $openid = $_POST['openid'];
        if(is_null($openid)){
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
            $this->AjaxReturn(['errorCode'=>0,'msg'=>'sucess']);
        }else{
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


