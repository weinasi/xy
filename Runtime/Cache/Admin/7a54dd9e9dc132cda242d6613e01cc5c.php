<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html>
<head>
    <meta charset="UTF-8">
    <title><?php echo ($meta_title); ?>|雪月科技管理平台</title>
    <link href="/xy/Public/favicon.ico" type="image/x-icon" rel="shortcut icon">
    <link rel="stylesheet" type="text/css" href="/xy/Public/Admin/css/base.css" media="all">
    <link rel="stylesheet" type="text/css" href="/xy/Public/Admin/css/common.css" media="all">
    <link rel="stylesheet" type="text/css" href="/xy/Public/Admin/css/module.css">
    <link rel="stylesheet" type="text/css" href="/xy/Public/Admin/css/style.css" media="all">
	<link rel="stylesheet" type="text/css" href="/xy/Public/Admin/css/<?php echo (C("COLOR_STYLE")); ?>.css" media="all">
    <link href="bitbug_favicon.ico" rel="shortcut icon"/>
    <link rel="stylesheet" type="text/css" href="/xy/Public/Admin/laydate/laydate.css">

     <!--[if lt IE 9]>
    <script type="text/javascript" src="/xy/Public/static/jquery-1.10.2.min.js"></script>
    <![endif]--><!--[if gte IE 9]><!-->
    <script type="text/javascript" src="/xy/Public/static/jquery-2.0.3.min.js"></script>
    <script type="text/javascript" src="/xy/Public/Admin/js/jquery.mousewheel.js"></script>
    <!--<![endif]-->

    <script type="text/javascript" src="/xy/Public/Admin/laydate/laydate.js"></script>
    
    <style>
        .font8{
            font-size: 8px;
        }

        table th{
            white-space: nowrap;
        }
        table td{
            white-space: nowrap;
        }
        body,table{
            /*font-size:12px;*/
        }
        table{
            empty-cells:show;
            border-collapse: collapse;
            /*margin:0 auto;*/
        }

        h1,h2,h3{
            /*font-size:12px;*/
            /*margin:0;*/
            /*padding:0;*/
        }
        table.tab_css_1{
            /*border:1px solid #cad9ea;*/
            /*color:#666;*/
        }
        table.tab_css_1 th {
            /*background-image: url("th_bg1.gif");*/
            /*background-repeat:repeat-x;*/
            /*height:30px;*/
        }
        table.tab_css_1 td,table.tab_css_1 th{
            /*border:1px solid #cad9ea;*/
            /*padding:0 1em 0;*/
        }
        table.tab_css_1 tr.tr_css{
            /*background-color:#f5fafe;*/
            /*height:30px;*/
        }
    </style>
</head>
<body>
    <!-- 头部 -->
    <div class="header">
        <!-- Logo -->
        <!--<span class="logo"></span>-->
        <span style="float: left;margin-left: 16px;width: 184px;height: 49px;color: white;text-align: center;font-size: 25px;font-weight:bold;">雪月科技</span>
        <!-- /Logo -->

        <!-- 主导航 -->
        <ul class="main-nav">
            <?php if(is_array($__MENU__["main"])): $i = 0; $__LIST__ = $__MENU__["main"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li class="<?php echo ((isset($menu["class"]) && ($menu["class"] !== ""))?($menu["class"]):''); ?>"><a href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
        <!-- /主导航 -->

        <!-- 用户栏 -->
        <div class="user-bar">
            <a href="javascript:;" class="user-entrance"><i class="icon-user"></i></a>
            <ul class="nav-list user-menu hidden">
                <li class="manager">你好，<em title="<?php echo session('user_auth.username');?>"><?php echo session('user_auth.username');?></em></li>
                <li><a href="<?php echo U('User/updatePassword');?>">修改密码</a></li>
                <li><a href="<?php echo U('User/updateNickname');?>">修改昵称</a></li>
                <li><a href="<?php echo U('Public/logout');?>">退出</a></li>
            </ul>
        </div>
    </div>
    <!-- /头部 -->

    <!-- 边栏 -->
    <div class="sidebar">
        <!-- 子导航 -->
        
    <div id="subnav" class="subnav">
    <?php if(!empty($_extra_menu)): ?>
        <?php echo extra_menu($_extra_menu,$__MENU__); endif; ?>
    <?php if(is_array($__MENU__["child"])): $i = 0; $__LIST__ = $__MENU__["child"];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
        <?php if(!empty($sub_menu)): if(!empty($key)): ?><h3><i class="icon icon-unfold"></i><?php echo ($key); ?></h3><?php endif; ?>
            <ul class="side-sub-menu">
                <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li>
                        <a class="item" href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a>
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul><?php endif; ?>
        <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
 <h3>
 	<i class="icon <?php if(!in_array((ACTION_NAME), explode(',',"mydocument,draftbox,examine"))): ?>icon-fold<?php endif; ?>"></i>
 	个人中心
 </h3>
 	<ul class="side-sub-menu <?php if(!in_array((ACTION_NAME), explode(',',"mydocument,draftbox,examine"))): ?>subnav-off<?php endif; ?>">
 		<li <?php if((ACTION_NAME) == "mydocument"): ?>class="current"<?php endif; ?>><a class="item" href="<?php echo U('article/mydocument');?>">我的文档</a></li>
 		<?php if(($show_draftbox) == "1"): ?><li <?php if((ACTION_NAME) == "draftbox"): ?>class="current"<?php endif; ?>><a class="item" href="<?php echo U('article/draftbox');?>">草稿箱</a></li><?php endif; ?>
		<li <?php if((ACTION_NAME) == "draftbox"): ?>class="examine"<?php endif; ?>><a class="item" href="<?php echo U('article/examine');?>">待审核</a></li>
 	</ul>

    <?php if(is_array($nodes)): $i = 0; $__LIST__ = $nodes;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$sub_menu): $mod = ($i % 2 );++$i;?><!-- 子导航 -->
        <?php if(!empty($sub_menu)): ?><h3>
            	<i class="icon <?php if(($sub_menu['current']) != "1"): ?>icon-fold<?php endif; ?>"></i>
            	<?php if(($sub_menu['allow_publish']) > "0"): ?><a class="item" href="<?php echo (u($sub_menu["url"])); ?>"><?php echo ($sub_menu["title"]); ?></a><?php else: echo ($sub_menu["title"]); endif; ?>
            </h3>
            <ul class="side-sub-menu <?php if(($sub_menu["current"]) != "1"): ?>subnav-off<?php endif; ?>">
                <?php if(is_array($sub_menu['_child'])): $i = 0; $__LIST__ = $sub_menu['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu): $mod = ($i % 2 );++$i;?><li <?php if($menu['id'] == $cate_id or $menu['current'] == 1): ?>class="current"<?php endif; ?>>
                        <?php if(($menu['allow_publish']) > "0"): ?><a class="item" href="<?php echo (u($menu["url"])); ?>"><?php echo ($menu["title"]); ?></a><?php else: ?><a class="item" href="javascript:void(0);"><?php echo ($menu["title"]); ?></a><?php endif; ?>

                        <!-- 一级子菜单 -->
                        <?php if(isset($menu['_child'])): ?><ul class="subitem">
                        	<?php if(is_array($menu['_child'])): foreach($menu['_child'] as $key=>$three_menu): ?><li>
                                <?php if(($three_menu['allow_publish']) > "0"): ?><a class="item" href="<?php echo (u($three_menu["url"])); ?>"><?php echo ($three_menu["title"]); ?></a><?php else: ?><a class="item" href="javascript:void(0);"><?php echo ($three_menu["title"]); ?></a><?php endif; ?>
                                <!-- 二级子菜单 -->
                                <?php if(isset($three_menu['_child'])): ?><ul class="subitem">
                                	<?php if(is_array($three_menu['_child'])): foreach($three_menu['_child'] as $key=>$four_menu): ?><li>
                                        <?php if(($four_menu['allow_publish']) > "0"): ?><a class="item" href="<?php echo U('index','cate_id='.$four_menu['id']);?>"><?php echo ($four_menu["title"]); ?></a><?php else: ?><a class="item" href="javascript:void(0);"><?php echo ($four_menu["title"]); ?></a><?php endif; ?>
                                        <!-- 三级子菜单 -->
                                        <?php if(isset($four_menu['_child'])): ?><ul class="subitem">
                                        	<?php if(is_array($four_menu['_child'])): $i = 0; $__LIST__ = $four_menu['_child'];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$five_menu): $mod = ($i % 2 );++$i;?><li>
                                            	<?php if(($five_menu['allow_publish']) > "0"): ?><a class="item" href="<?php echo U('index','cate_id='.$five_menu['id']);?>"><?php echo ($five_menu["title"]); ?></a><?php else: ?><a class="item" href="javascript:void(0);"><?php echo ($five_menu["title"]); ?></a><?php endif; ?>
                                            </li><?php endforeach; endif; else: echo "" ;endif; ?>
                                        </ul><?php endif; ?>
                                        <!-- end 三级子菜单 -->
                                    </li><?php endforeach; endif; ?>
                                </ul><?php endif; ?>
                                <!-- end 二级子菜单 -->
                            </li><?php endforeach; endif; ?>
                        </ul><?php endif; ?>
                        <!-- end 一级子菜单 -->
                    </li><?php endforeach; endif; else: echo "" ;endif; ?>
            </ul><?php endif; ?>
        <!-- /子导航 --><?php endforeach; endif; else: echo "" ;endif; ?>
    <!-- 回收站 -->
	<?php if(($show_recycle) == "1"): ?><h3>
        <em class="recycle"></em>
        <a href="<?php echo U('article/recycle');?>">回收站</a>
    </h3><?php endif; ?>
</div>
<script>
    $(function(){
        $(".side-sub-menu li").hover(function(){
            $(this).addClass("hover");
        },function(){
            $(this).removeClass("hover");
        });
    })
</script>


        <!-- /子导航 -->
    </div>
    <!-- /边栏 -->

    <!-- 内容区 -->
    <div id="main-content">
        <div id="top-alert" class="fixed alert alert-error" style="display: none;">
            <button class="close fixed" style="margin-top: 4px;">&times;</button>
            <div class="alert-content">这是内容</div>
        </div>
        <div id="main" class="main">
            
            <!-- nav -->
            <?php if(!empty($_show_nav)): ?><div class="breadcrumb">
                <span>您的位置:</span>
                <?php $i = '1'; ?>
                <?php if(is_array($_nav)): foreach($_nav as $k=>$v): if($i == count($_nav)): ?><span><?php echo ($v); ?></span>
                    <?php else: ?>
                    <span><a href="<?php echo ($k); ?>"><?php echo ($v); ?></a>&gt;</span><?php endif; ?>
                    <?php $i = $i+1; endforeach; endif; ?>
            </div><?php endif; ?>
            <!-- nav -->
            

            
	<script type="text/javascript" src="/xy/Public/static/uploadify/jquery.uploadify.min.js"></script>
	<div class="main-title cf">
		<h2>
			编辑<?php echo ($data["hdmc"]); ?>
		</h2>
	</div>
    <!---->
    <div class="form-item">
        <label class="item-label">封面图<span class="check-tips">（封面图）</span></label>
        <div class="controls">
            <?php $_result=parse_config_attr($model['field_group']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><div id="tab<?php echo ($key); ?>" class="tab-pane <?php if(($key) == "1"): ?>in<?php endif; ?> tab<?php echo ($key); ?>">
                <?php if(is_array($fields[$key])): $i = 0; $__LIST__ = $fields[$key];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($i % 2 );++$i; if($field['is_show'] == 1 && $field['type'] == picture): ?><div class="controls">
                                <input type="file" id="upload_picture_<?php echo ($field["name"]); ?>">
                                <input type="hidden" name="<?php echo ($field["name"]); ?>" id="cover_id_<?php echo ($field["name"]); ?>"/>
                                <div class="upload-img-box">
                                    <notempty>
                                        <div class="upload-pre-item"><img src="<?php echo ($fmt); ?>"/></div>
                                    </notempty>
                                </div>
                            </div>
                            <script type="text/javascript">
                                //上传图片
                                /* 初始化上传插件 */
                                $("#upload_picture_<?php echo ($field["name"]); ?>").uploadify({
                                    "height"          : 30,
                                    "swf"             : "/xy/Public/static/uploadify/uploadify.swf",
                                    "fileObjName"     : "download",
                                    "buttonText"      : "上传图片",
                                    "uploader"        : "<?php echo U('File/uploadPicture',array('session_id'=>session_id()));?>",
                                    "width"           : 120,
                                    'removeTimeout'	  : 1,
                                    'fileTypeExts'	  : '*.jpg; *.png; *.gif;',
                                    "onUploadSuccess" : uploadPicture<?php echo ($field["name"]); ?>,
                                'onFallback' : function() {
                                    alert('未检测到兼容版本的Flash.');
                                }
                                });
                                function uploadPicture<?php echo ($field["name"]); ?>(file, data){
                                    var data = $.parseJSON(data);
                                    var src = '';
                                    if(data.status){
                                        $("#cover_id_<?php echo ($field["name"]); ?>").val(data.id);
                                        src = data.url || '/xy' + data.path;
                                        console.log(src);
                                        $("#cover_id_<?php echo ($field["name"]); ?>").parent().find('.upload-img-box').html(
                                                '<div class="upload-pre-item"><img src= '+src+' /></div>'
                                        );
                                        $('input[name="fmt"]').val(src);
                                    } else {
                                        updateAlert(data.info);
                                        setTimeout(function(){
                                            $('#top-alert').find('button').click();
                                            $(that).removeClass('disabled').prop('disabled',false);
                                        },1500);
                                    }
                                }
                            </script><?php endif; endforeach; endif; else: echo "" ;endif; ?>
        </div><?php endforeach; endif; else: echo "" ;endif; ?>
    </div>
    </div>
    <!---->
	<!-- 标签页导航 -->
<div class="tab-wrap">
	<div class="tab-content">
        <form action="<?php echo U('storeActivity');?>" method="post" class="form-horizontal">
            <input type="hidden" class="text input-large" name="id" value="<?php echo ($data["id"]); ?>">
            <div class="form-item">
                <label class="item-label">活动名称<span class="check-tips">（活动名称）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="hdmc" value="<?php echo ($data["hdmc"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">活动编号<span class="check-tips">（活动编号）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="hdbh" value="<?php echo ($data["hdbh"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">活动开始与结束日期<span class="check-tips">（活动开始与结束日期）</span></label>
                <div class="controls">
                    <input type="text" id="test10" name="startAndEnd" class="text input-large demo-input"  value="<?php echo ($data["rqs"]); ?> - <?php echo ($data["rqz"]); ?>">
                </div>
            </div>
            <div class="form-item" style="display: none;">
                <label class="item-label">日期始<span class="check-tips">（日期始）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="rqs" value="<?php echo ($data["rqz"]); ?>">
                </div>
            </div>
            <div class="form-item" style="display: none;">
                <label class="item-label">日期止<span class="check-tips">（日期止）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="rqz" value="<?php echo ($data["rqz"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">地点<span class="check-tips">（地点）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="dd" value="<?php echo ($data["dd"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">详细地址<span class="check-tips">（详细地址）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="address" value="<?php echo ($data["address"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">经度<span class="check-tips">（经度）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="longitude" value="<?php echo ($data["longitude"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">纬度<span class="check-tips">（纬度）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="latitude" value="<?php echo ($data["latitude"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">位置名<span class="check-tips">（位置名）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="name" value="<?php echo ($data["name"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">宣传图<span class="check-tips">（宣传图）</span></label>
                <div class="controls">
                    <?php $_result=parse_config_attr($model['field_group']);if(is_array($_result)): $i = 0; $__LIST__ = $_result;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$group): $mod = ($i % 2 );++$i;?><div id="tab<?php echo ($key); ?>" class="tab-pane <?php if(($key) == "1"): ?>in<?php endif; ?> tab<?php echo ($key); ?>">
                            <?php if(is_array($fields[$key])): $i = 0; $__LIST__ = $fields[$key];if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$field): $mod = ($i % 2 );++$i; if($field['is_show'] == 1 && $field['type'] == pluploadimages): if($isEdit == 1): echo hook('PluploadImages',array('count'=>'10','name'=>'pluploadimages','value'=>'')); endif; ?>
                                    <?php if($isEdit == 2): echo hook('PluploadImages', array('count'=>'10','name'=>'pluploadimages','value'=>$xcts)); endif; endif; endforeach; endif; else: echo "" ;endif; ?>
                        </div><?php endforeach; endif; else: echo "" ;endif; ?>
                </div>
            </div>

            <div class="form-item" style="">
                <label class="item-label">状态<span class="check-tips">（状态）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="zt" value="<?php if(empty($data["id"])): ?>100<?php else: echo ($data["zt"]); endif; ?> "/>
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">总数量<span class="check-tips">（总数量）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="zsl" value="<?php echo ($data["zsl"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">报名价格<span class="check-tips">（报名价格）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="bmjg" value="<?php echo ($data["bmjg"]); ?>">
                </div>
            </div>
            <div class="form-item" style="display: none;">
                <label class="item-label">宣传图<span class="check-tips">（宣传图）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="xct" value="<?php echo ($data["xct"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">男士数量<span class="check-tips">（男士数量）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="mansl" value="<?php echo ($data["mansl"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">女士数量<span class="check-tips">（女士数量）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="womensl" value="<?php echo ($data["womensl"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">其他数量<span class="check-tips">（其他数量）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="qtsl" value="<?php echo ($data["qtsl"]); ?>">
                </div>
            </div>
            <div class="form-item" style="display: none;">
                <label class="item-label">浏览次数<span class="check-tips">（浏览次数）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="llcs" value="<?php echo ($data["llcs"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">备注<span class="check-tips">（备注）</span></label>
                <div class="controls">
                    <textarea name="bz" id="" cols="60" rows="10"><?php echo ($data["bz"]); ?></textarea>
                    <!--<input type="text" class="text input-large" name="bz" value="<?php echo ($data["bz"]); ?>">-->
                </div>
            </div>
            <div class="form-item">
                <label class="item-label">活动详情<span class="check-tips">（活动详情）</span></label>
                <div class="controls">
                    <textarea name="hdxq" id="" cols="60" rows="10"><?php echo ($data["hdxq"]); ?></textarea>
                    <!--<input type="text" class="text input-large" name="hdxq" value="<?php echo ($data["hdxq"]); ?>">-->
                </div>
            </div>
            <div class="form-item" style="display: none;">
                <label class="item-label">封面图<span class="check-tips">（封面图）</span></label>
                <div class="controls">
                    <input type="text" class="text input-large" name="fmt" value="<?php echo ($data["fmt"]); ?>">
                </div>
            </div>
            <div class="form-item">
                <button class="btn submit-btn ajax-post" id="submit" type="submit" target-form="form-horizontal">确 定</button>
                <button class="btn btn-return" onclick="javascript:history.back(-1);return false;">返 回</button>
            </div>
        </form>
	</div>
</div>

        </div>
        <div class="cont-ft">
            <div class="copyright">
                <div class="fl">感谢使用<a href="#" target="_blank">雪月科技</a>管理平台</div>
                <div class="fr">V<?php echo (ONETHINK_VERSION); ?></div>
            </div>
        </div>
    </div>
    <!-- /内容区 -->
    <script type="text/javascript">
    (function(){
        var ThinkPHP = window.Think = {
            "ROOT"   : "/xy", //当前网站地址
            "APP"    : "/xy/index.php?s=", //当前项目地址
            "PUBLIC" : "/xy/Public", //项目公共目录地址
            "DEEP"   : "<?php echo C('URL_PATHINFO_DEPR');?>", //PATHINFO分割符
            "MODEL"  : ["<?php echo C('URL_MODEL');?>", "<?php echo C('URL_CASE_INSENSITIVE');?>", "<?php echo C('URL_HTML_SUFFIX');?>"],
            "VAR"    : ["<?php echo C('VAR_MODULE');?>", "<?php echo C('VAR_CONTROLLER');?>", "<?php echo C('VAR_ACTION');?>"]
        }
    })();
    </script>
    <script type="text/javascript" src="/xy/Public/static/think.js"></script>
    <script type="text/javascript" src="/xy/Public/Admin/js/common.js"></script>
    <script type="text/javascript">
        +function(){
            var $window = $(window), $subnav = $("#subnav"), url;
            $window.resize(function(){
                $("#main").css("min-height", $window.height() - 130);
            }).resize();

            /* 左边菜单高亮 */
            url = window.location.pathname + window.location.search;
            url = url.replace(/(\/(p)\/\d+)|(&p=\d+)|(\/(id)\/\d+)|(&id=\d+)|(\/(group)\/\d+)|(&group=\d+)/, "");
            $subnav.find("a[href='" + url + "']").parent().addClass("current");

            /* 左边菜单显示收起 */
            $("#subnav").on("click", "h3", function(){
                var $this = $(this);
                $this.find(".icon").toggleClass("icon-fold");
                $this.next().slideToggle("fast").siblings(".side-sub-menu:visible").
                      prev("h3").find("i").addClass("icon-fold").end().end().hide();
            });

            $("#subnav h3 a").click(function(e){e.stopPropagation()});

            /* 头部管理员菜单 */
            $(".user-bar").mouseenter(function(){
                var userMenu = $(this).children(".user-menu ");
                userMenu.removeClass("hidden");
                clearTimeout(userMenu.data("timeout"));
            }).mouseleave(function(){
                var userMenu = $(this).children(".user-menu");
                userMenu.data("timeout") && clearTimeout(userMenu.data("timeout"));
                userMenu.data("timeout", setTimeout(function(){userMenu.addClass("hidden")}, 100));
            });

	        /* 表单获取焦点变色 */
	        $("form").on("focus", "input", function(){
		        $(this).addClass('focus');
	        }).on("blur","input",function(){
				        $(this).removeClass('focus');
			        });
		    $("form").on("focus", "textarea", function(){
			    $(this).closest('label').addClass('focus');
		    }).on("blur","textarea",function(){
			    $(this).closest('label').removeClass('focus');
		    });

            // 导航栏超出窗口高度后的模拟滚动条
            var sHeight = $(".sidebar").height();
            var subHeight  = $(".subnav").height();
            var diff = subHeight - sHeight; //250
            var sub = $(".subnav");
            if(diff > 0){
                $(window).mousewheel(function(event, delta){
                    if(delta>0){
                        if(parseInt(sub.css('marginTop'))>-10){
                            sub.css('marginTop','0px');
                        }else{
                            sub.css('marginTop','+='+10);
                        }
                    }else{
                        if(parseInt(sub.css('marginTop'))<'-'+(diff-10)){
                            sub.css('marginTop','-'+(diff-10));
                        }else{
                            sub.css('marginTop','-='+10);
                        }
                    }
                });
            }
        }();
    </script>
    
<link href="/xy/Public/static/datetimepicker/css/datetimepicker.css" rel="stylesheet" type="text/css">
<?php if(C('COLOR_STYLE')=='blue_color') echo '<link href="/xy/Public/static/datetimepicker/css/datetimepicker_blue.css" rel="stylesheet" type="text/css">'; ?>
<link href="/xy/Public/static/datetimepicker/css/dropdown.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="/xy/Public/static/datetimepicker/js/bootstrap-datetimepicker.min.js"></script>
<script type="text/javascript" src="/xy/Public/static/datetimepicker/js/locales/bootstrap-datetimepicker.zh-CN.js" charset="UTF-8"></script>
<script type="text/javascript">

Think.setValue("type", <?php echo ((isset($data["type"]) && ($data["type"] !== ""))?($data["type"]):'""'); ?>);
Think.setValue("display", <?php echo ((isset($data["display"]) && ($data["display"] !== ""))?($data["display"]):0); ?>);

$('#submit').click(function(){
	$('#form').submit();
});

//date 组件开始
lay('#version').html('-v'+ laydate.v);

//执行一个laydate实例
//日期时间范围
laydate.render({
    elem: '#test10'
    ,type: 'datetime'
    ,range: true,
    change: function(value, date){ //监听日期被切换
//        lay('#testView').html(value);
        $('input[name="rqs"]').val(value.substring(0,19));
        $('input[name="rqz"]').val(value.substring(22));
    }
});
//组件结束


$(function(){
    $('.time').datetimepicker({
        format: 'yyyy-mm-dd hh:ii',
        language:"zh-CN",
        minView:2,
        autoclose:true
    });
    showTab();

	<?php if(C('OPEN_DRAFTBOX') and (ACTION_NAME == 'add' or $data['status'] == 3)): ?>//保存草稿
	var interval;
	$('#autoSave').click(function(){
        var target_form = $(this).attr('target-form');
        var target = $(this).attr('url')
        var form = $('.'+target_form);
        var query = form.serialize();
        var that = this;

        $(that).addClass('disabled').attr('autocomplete','off').prop('disabled',true);
        $.post(target,query).success(function(data){
            if (data.status==1) {
                updateAlert(data.info ,'alert-success');
                $('input[name=id]').val(data.data.id);
            }else{
                updateAlert(data.info);
            }
            setTimeout(function(){
                $('#top-alert').find('button').click();
                $(that).removeClass('disabled').prop('disabled',false);
            },1500);
        })

        //重新开始定时器
        clearInterval(interval);
        autoSaveDraft();
        return false;
    });

	//Ctrl+S保存草稿
	$('body').keydown(function(e){
		if(e.ctrlKey && e.which == 83){
			$('#autoSave').click();
			return false;
		}
	});

	//每隔一段时间保存草稿
	function autoSaveDraft(){
		interval = setInterval(function(){
			//只有基础信息填写了，才会触发
			var title = $('input[name=title]').val();
			var name = $('input[name=name]').val();
			var des = $('textarea[name=description]').val();
			if(title != '' || name != '' || des != ''){
				$('#autoSave').click();
			}
		}, 1000*parseInt(<?php echo C('DRAFT_AOTOSAVE_INTERVAL');?>));
	}
	autoSaveDraft();<?php endif; ?>


});
</script>

</body>
</html>