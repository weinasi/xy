<?php
/**
 * Created by PhpStorm.
 * User: liuxiangbao
 * Date: 2017/10/27
 * Time: 上午12:05
 */
namespace Home\Model;

use Think\Model;

class ActivityModel extends Model
{
  /**
     * 必须说明属性信息：
     * name             type               default            comment
       `id`             int(11)            NOT NULL           编号:自增键
       `hdmc`           varchar(255)       NOT NULL           活动名称
       `hdbh`           varchar(255)       NOT NULL           活动编号
       `rqs`            datetime           NOT NULL           日期始:YYYY-MM-DD HH:mm
       `rqz`            datetime           NOT NULL           日期止:YYYY-MM-DD HH:mm
       `dd`             varchar(255)       NOT NULL           地点
       `longitude`      float              NOT NULL           经度:范围为-180~180，负数表示西经
       `latitude`       float              NOT NULL           纬度:范围为-90~90，负数表示南纬
       `name`           varchar(255)       NOT NULL           位置名:集会的地址名称
       `fmt`            varchar(255)       NOT NULL           封面图
       `zt`             varchar(255)       NOT NULL           状态:1 正在报名  2 已截止 3 已取消 4 已完成
       `zsl`            int(11)            NOT NULL           总数量
       `bmzsl`          int(11)            NOT NULL           已报总数量
       `bmjg`           int(11)            NOT NULL           报名价格
       `yjjklb`         text               NOT NULL           已经缴款列表:list里面已经缴款的openid
       `xct`            varchar(255)       NOT NULL           宣传图
       `mansl`          int(11)            NOT NULL           男士数量
       `bmmansl`        int(11)            NOT NULL           报名男士数量
       `womensl`        int(11)            NOT NULL           女士数量
       `bmwomensl`      int(11)            NOT NULL           报名女士数量
       `qtsl`           int(11)            NOT NULL           其他数量
       `bmqtsl`         int(11)            NOT NULL           报名其他数量
       `mantxlb`        text               NOT NULL           男士头像列表:list里面放图片路径url
       `womantxlb`      text               NOT NULL           女士头像列表
       `qttxlb`         text               DEFAULT NULL       其他头像列表
       `llcs`           int(11)            NOT NULL           浏览次数
       `bz`             varchar(255)       DEFAULT NULL       备注
       `hdxq`           varchar(255)       NOT NULL           活动详情:存放对活动的描述
     */
    public function get($condition = [])
    {
        $activities = D('Activity');
        return $activities->where($condition)->select();
    }

    public function save($data = [])
    {
        $activities = M('Activity');
        $id = $data['id'];
        unset($data['id']);
        return $activities->where('id='.$id)->save($data);
    }

    public function dd($data = [])
    {
        $activities = D('Activity');
        return $activities->add($data);
    }
}