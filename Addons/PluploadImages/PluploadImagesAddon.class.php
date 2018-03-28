<?php

namespace Addons\PluploadImages;

use Common\Controller\Addon;

/**
 * Class UploadImagesAddon 图片批量上传插件
 * @package Addons\PluploadImages
 * @author 原作者：tjr&jj
 * @author 原作者：木梁大囧
 * @author 更新作者：波仔ischambers 931507706.qq.com>
 */
class PluploadImagesAddon extends Addon
{
    public $info = array(
        'name' => 'PluploadImages',
        'title' => '多图上传 ',
        'description' => '多图上传 {:hook(\'PluploadImages\', array(\'count\'=>(int)\'限制数量\',\'name\'=>\'input_name\',\'value\'=>(arr|str)\'图片ID集\'))}',
        'status' => 1,
        'author' => '宗米+波仔',
        'version' => '1.3'
    );

    /**
     * 获取数据库表前缀
     * 安装函数
     * @see \Common\Controller\Addons::install()
     */
    public function db_prefix()
    {
        $db_prefix = C('DB_PREFIX');
        return $db_prefix;
    }

    /**
     * [install] 安装
     * @return bool
     * @author 波仔ischambers
     */
    public function install()
    {
        $sql_hook=<<<INSERT
INSERT INTO `{$this->db_prefix()}hooks` (
`id`,  `name`, `description`, `type`, `update_time`, `addons`) VALUES
(null,'PluploadImages','多图上传钩子',1,0,'PluploadImages');
INSERT;
        D()->execute($sql_hook);
        return true;
    }

    /**
     * [uninstall] 卸载
     * @return bool
     * @author 波仔ischambers
     */
    public function uninstall()
    {
        $map['name']='PluploadImages';
        M('hooks')->where($map)->delete();
        return true;
    }

    /**
     * [PluploadImages] 实现UploadImages的钩子方法
     * @param $param array('count'=>(int)?,'name'=>?,'value'=>(arr|str)?)
     * @author 波仔ischambers
     */
    public function PluploadImages($param)
    {
        $name = $param['name'] ?: 'pics';
        $css = $param['css'] ?: 'PluploadImages';
        $width = $param['width'] ?: '100';
        $height = $param['height'] ?: '100';
        $buttonText = $param['buttonText'] ?: '选择图片';
        $valArr = $param['value'] ? is_array($param['value']) ? $param['value']:explode(',', $param['value']) : array();
        $valArr_array = array_filter($valArr);
        $count = (int)$param['count'] ? : 100;
        $this->assign('name', $name);
        $this->assign('img_tools', $param['img_tools']);
        $this->assign('valStr', $param['value']);
        $this->assign('css', $css);
        $this->assign('width', $width);
        $this->assign('height', $height);
        $this->assign('buttonText', $buttonText);
        $this->assign('backgroundImg', $param['background_img']);
        $this->assign('valArr', $valArr_array);
        $this->assign('count', $count);
        $this->display('zm_upload');
    }
}