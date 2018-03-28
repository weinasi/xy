<?php
/**
 * Created by PhpStorm.
 * User: liuxiangbao
 * Date: 2017/10/27
 * Time: 上午12:05
 */
namespace Home\Model;

use Think\Model;

class UserModel extends Model
{
    /**
     * 必须说明属性信息：
     * name             type               default            comment
       `id`             int(11)            NOT NULL           编号:自增键
       `openid`         varchar(255)       NOT NULL           确定用户唯一标识
       `ycjhd`          text               DEFAULT NULL       已参加活动:list里面放参加过的活动编号
       `wxh`            varchar(255)       DEFAULT NULL       微信号:有的为空
       `xb`             varchar(255)       NOT NULL           性别:1男性，2女性
       `tx`             varchar(255)       DEFAULT NULL       头像:存放图片URL
     */

    public function get($condition = [])
    {
        $users = D('User');
        return $users->where($condition)->select();
    }

    public function save($data = [])
    {
        $users = D('User');
        return $users->add($data);;
    }
}