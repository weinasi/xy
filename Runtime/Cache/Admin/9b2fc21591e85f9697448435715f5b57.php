<?php if (!defined('THINK_PATH')) exit();?><!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>

<form action="<?php echo U('upload');?>" enctype="multipart/form-data" method="post" >
    <input type="text" name="name" />
    <input type="file" name="photo" />
    <input type="submit" value="提交" >
</form>
</body>
</html>