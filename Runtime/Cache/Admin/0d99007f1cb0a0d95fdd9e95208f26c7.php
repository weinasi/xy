<?php if (!defined('THINK_PATH')) exit();?><script type="text/javascript" src="/xy/Addons/PluploadImages/plupload/plupload.full.min.js"></script>
<link rel="stylesheet" href="/xy/Addons/PluploadImages/style/<?php echo ($css); ?>.css">
<div class="upload-img-list">
<input type="hidden" name="<?php echo ($name); ?>" id="images" value="<?php echo ($valStr); ?>"/>
    <div class="controls">
        <div class="demo">
            <a class="btn" id="btn">上传图片</a> 最大500KB，支持jpg，gif，png格式。
            <ul id="ul_pics" class="ul_pics clearfix">
                <?php if(!empty($valArr)): if(is_array($valArr)): $i = 0; $__LIST__ = $valArr;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?><li class="item" data-id="<?php echo ($vo); ?>" data-type="picture">
                            <i class="deleteImg" style="position: absolute;margin-left: 145px;font-size: 20px;color: blue;" data-id="<?php echo ($vo); ?>">x</i>
                            <img src="/xy<?php echo ($vo); ?>"/>
                        </li><?php endforeach; endif; else: echo "" ;endif; endif; ?>
            </ul>
        </div>
    </div>    
</div>
<script type="text/javascript">
    var uploader = new plupload.Uploader( { //创建实例的构造方法
        runtimes: 'html5,flash,silverlight,html4', //上传插件初始化选用那种方式的优先级顺序
        browse_button: 'btn', // 上传按钮
        url: '<?php echo U('File/uploadPictures');?>', //远程上传地址
        flash_swf_url: '/xy/Addons/PluploadImages/plupload/Moxie.swf', //flash文件地址
        silverlight_xap_url: '/xy/Addons/PluploadImages/plupload/Moxie.xap', //silverlight文件地址
        filters: {  
            max_file_size: '1gb', //最大上传文件大小（格式100b, 10kb, 10mb, 1gb）
            mime_types: [//允许文件上传类型
                {title: "files", extensions: "jpg,png,gif" }
            ]
         },
        multi_selection: true, //true:ctrl多文件上传, false 单文件上传
        init: {
            FilesAdded: function(up, files) { //文件上传前
                if ($("#ul_pics").children("li").length > 20) {
                    alert("您上传的图片太多了！");
                    uploader.destroy();
                } else {
                    var li = '';
                    plupload.each(files, function(file) { //遍历文件
                        li += "<li id='" + file['id'] + "'><div class='progress'><span class='bar'></span><span class='percent'></span></div></li>";
                    });
                    $("#ul_pics").append(li);
                    uploader.start();
                }
            },
            UploadProgress: function(up, file) { //上传中，显示进度条
                $("#" + file.id).find('.bar').css({"width": file.percent + "%"}).find(".percent").text(file.percent + "%");
            },
            FileUploaded: function(up, file, info) { //文件上传成功的时候触发
                var data = JSON.parse(info.response);
                var items = $("#images");                   
                var images_ids = data.id+',';                
                var images_one = images_ids.split(',');
                var images_two = items.val().split(',');
                    items.val('');
                    if($.inArray(images_one) && $.inArray(images_two)){
                        var arr_ids = array_string(images_one, images_two);
                    }
                items.val(arr_ids);
                var str = '<i class="deleteImg" style="position: absolute;margin-left: 145px;font-size: 20px;color: blue;" data-id="'+data.path+'">x</i>';
                $("#" + file.id).html(str+"<img src='/xy" + data.path + "'/>");


                //上传成功触发对封面图的更新
                var urlImgs = $('input[name="xct"]').val();
                urlImgs = urlImgs && urlImgs !== '0'?urlImgs:null;//如果存在说明是编辑页面
                if (!urlImgs){
                    urlImgs = data.path + ',';;

                }else{
                    urlImgs = urlImgs + data.path + ',';
                }
                $("input[name='xct']").val(urlImgs);

            },
            Error: function(up, err) { //上传出错的时候触发
                alert(err.message);
            }
        }
    });
    uploader.init();
    function array_string(one, two){
        for (var i = 0 ; i < one.length ; i ++ ){
            for(var j = 0 ; j < two.length ; j ++ ){
                if (one[i] === two[j]){
                    one.splice(i,1);
                }
            }
        }
        for(var i = 0; i <two.length; i++){
            one.push(two[i]);
        }
        return one;
    }
    //图片删除
    $('.deleteImg').click(function(){
        var imgId = $(this).attr('data-id');
//        var activityId = $(this).attr(' activity-id');
        $.ajax({
            type : "POST", //提交方式
            url : '<?php echo U('Article/imgDelete');?>',//路径
            data : {
                imageId : imgId
            },
            success : function(result) {
                if(result.errorCode == 0){
                    window.location.reload();
                }else{
                    alert(result.msg);
                }
            }
        })
    });
</script>