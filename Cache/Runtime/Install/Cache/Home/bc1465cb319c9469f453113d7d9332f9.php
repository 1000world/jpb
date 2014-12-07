<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <title>Easy-Admin - Github</title>
    <link rel="stylesheet" href="/jpb/Public/stylesheets/install/install.css" />
</head>
<body>
    <div class="wrap">
        <div class="header">
    <h1 class="logo"><?php echo C('SYSTEM_NAME');?></h1>
    <div class="icon_install">安装向导</div>
    <div class="version">Version <?php echo C('SYSTEM_VERSION');?> by <?php echo C('AUTHOR_NAME');?></div>
</div>

        <div class="section">
            <div class="step">
    <ul>
        <li class="on"><em>1</em>检测环境</li>
        <li class="on"><em>2</em>创建数据</li>
        <li class="current"><em>3</em>完成安装</li>
    </ul>
</div>
<div class="install" id="log">
  <ul id="loginner"></ul>
</div>

<div class="bottom tac">
    <a href="javascript:;" class="btn_old"><img src="/jpb/Public/images/install/loading.gif" align="absmiddle" />&nbsp;正在安装...</a>
</div>
<!-- js -->
<script src="/jpb/Public/javascripts/install/jquery.js?v=9.0"></script>
<script src="/jpb/Public/javascripts/install/validate.js?v=9.0"></script>
<script src="/jpb/Public/javascripts/install/ajaxForm.js?v=9.0"></script>

<script type="text/javascript">

// 得到提交的数据
var data = <?php echo ($data); ?>;
var step = 0;
$.ajaxSetup({ cache: false });

function reloads(step) {
    // 提交数据
    var url = "<?php echo U('Install/create', array(), '');?>" + "/step/" + step;
    $.ajax({
        type: "POST",
        url: url,
        data: data,
        dataType: 'json',
        beforeSend: function() { },
        success: function(data) {
            // 安装完成
            if(data.step == '999999'){
                // $('#dosubmit').attr("disabled", false);
                // $('#dosubmit').removeAttr("disabled");
                // $('#dosubmit').removeClass("nonext");
                setTimeout('gonext()', 3000);
            }

            if(data.step){
                $('#loginner').append(data.info);

                // 递归
                reloads(data.step);
            }else{
                // 连接数据库失败或者是创建数据库失败
                alert(data.info);
            }
        }
    });
}

// 安装完成，跳转到5步
function gonext(){
    window.location.href="<?php echo U('Install/complete');?>";
}

// 开始提交数据
$(document).ready(function(){
    reloads(step);
})
</script>

        </div>
    </div>
    <div class="footer"> &copy; 2014 
    <a href="https://github.com/happen-zhang/easy-admin" target="_blank">Easy-Admin</a>
</div>

</body>
</html>