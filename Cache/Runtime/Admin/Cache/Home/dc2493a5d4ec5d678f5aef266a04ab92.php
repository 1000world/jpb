<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="renderer" content="webkit|ie-comp|ie-stand">
    <title><?php echo C('SITE_TITLE');?> - 后台管理系统</title>
    <base href="/jpb/admin.php/Home" />
    <!-- css -->
    <link rel="stylesheet" type="text/css" href="/jpb/Public/Min/?f=/jpb/Public/stylesheets/admin/base.css|/jpb/Public/stylesheets/admin/layout.css|/jpb/Public/javascripts/admin/asyncbox/skins/default.css" />

    <!-- js -->
    <script type="text/javascript" src="/jpb/Public/Min/?f=/jpb/Public/javascripts/admin/jquery-1.7.2.min.js|/jpb/Public/javascripts/admin/jquery.lazyload.js|/jpb/Public/javascripts/admin/functions.js|/jpb/Public/javascripts/admin/base.js|/jpb/Public/javascripts/admin/jquery.form.js|/jpb/Public/javascripts/admin/asyncbox/asyncbox.js|/jpb/Public/javascripts/admin/jquery.watermark.js|/jpb/Public/javascripts/admin/datepicker/datetimepicker_css.js">
</script>

<script type="text/javascript">
    $(window).resize(autoSize);
    $(function(){
        autoSize();
        $(".loginOut").click(function(){
            var url=$(this).attr("href");
            popup.confirm('你确定要退出吗？','你确定要退出吗',function(action){
                if(action == 'ok'){ window.location=url; }
            });
            return false;
        });

        var time=self.setInterval(function(){$("#today").html(date("Y-m-d H:i:s"));},1000);
    });
</script>

</head>

<body>
    <div class="wrap">
        <!-- header -->
        <div id="Top">
    <div class="logo">
        <a href="<?php echo U('Index/index');?>"><img src="/jpb/Public/images/admin/logo.png" />
        </a>
    </div>

<!--     <div class="help">
        <a href="#">使用帮助</a><span><a href="#">关于</a></span>
    </div> -->

    <!-- menu -->
    <div class="menu">
    <ul>
        <?php if(is_array($main_menu)): $i = 0; $__LIST__ = $main_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu_item): $mod = ($i % 2 );++$i; if($i == 1): ?><li class="fisrt <?php echo activedLink($key, null, 'fisrt_current');?>">
                	<span><a href="<?php echo U($menu_item['target']);?>"><?php echo ($menu_item['name']); ?></a></span>
                </li>
            <?php elseif($i == count($main_menu)): ?>
                <li class="end <?php echo activedLink($key, null, 'end_current');?>">
                	<span><a href="<?php echo U($menu_item['target']);?>"><?php echo ($menu_item['name']); ?></a></span>
                </li>
            <?php else: ?>
                <li class="<?php echo activedLink($key, null, 'current');?>">
                	<span><a href="<?php echo U($menu_item['target']);?>"><?php echo ($menu_item['name']); ?></a></span>
                </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
    </ul>
</div>

</div>

<!-- tab -->
<div id="Tags">
    <div class="userPhoto"><img src="<?php echo getGravatar($_SESSION[current_account][email]);?>" /> </div>
    <div class="navArea">
        <div class="userInfo">
            <div>
                <a href="<?php echo U('Public/logout');?>" class="loginOut"><span>&nbsp;</span>退出系统</a>
            </div>
            欢迎您，<?php echo ($_SESSION['current_account']['email']); ?>
        </div>
        <div class="nav">
            <font id="today"><?php echo date("Y-m-d H:i:s");?></font>
            您的位置：<?php echo ($breadcrumbs); ?>
        </div>
    </div>
</div>


<div class="clear"></div>


        <!-- main -->
        <div class="mainBody">
            <!-- left -->
            <div id="Left">
    <div id="control" class=""></div>
    <div class="subMenuList">
        <div class="itemTitle">
            常用操作
        </div>
        <ul>
            <?php if(is_array($sub_menu)): $i = 0; $__LIST__ = $sub_menu;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$menu_item): $mod = ($i % 2 );++$i;?><li><a href="<?php echo U($key);?>"><?php echo ($menu_item); ?></a></li><?php endforeach; endif; else: echo "" ;endif; ?>
        </ul>
    </div>
</div>


            <!-- right -->
            <div id="Right">
                <script type="text/javascript" src="/jpb/Public/javascripts/admin/kindeditor/kindeditor.js"></script>
<script type="text/javascript" src="/jpb/Public/javascripts/admin/kindeditor/lang/zh_CN.js"></script>

<div class="Item hr">
    <div class="current">添加数据</div>
</div>
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="table1" id="form_inputs">
   <?php if(is_array($inputs)): $i = 0; $__LIST__ = $inputs;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$input): $mod = ($i % 2 );++$i;?><tr>
        <th width="100"><?php echo ($input['label']); ?>：</th>
        <td><?php echo ($input['html']); if ($input['remark'] != '' && $input['type'] != 'textarea' && $input['type'] != 'editor') { echo "（$input[remark]）"; } ?></td>
    </tr><?php endforeach; endif; else: echo "" ;endif; ?>
</table>
<div class="commonBtnArea" >
    <button class="btn submit">提交</button>
</div>

<script type="text/javascript">
function formSubmit(url) {
    $(".submit").click(function(){
        var fileForm = "<form id='ajaxupload' action='" + url + "' method='post' enctype='multipart/form-data'></form>";

        var inputs = $('#form_inputs');
        inputs.wrap(fileForm);

        $("#ajaxupload").ajaxSubmit({
            dataType: 'text',
            success: function (data) {
                data = eval('(' + data + ')');
                if(data.status==1){
                    popup.success(data.info);
                    setTimeout(function(){
                        popup.close("asyncbox_success");
                    },2000);
                }else{
                    popup.error(data.info);
                    setTimeout(function(){
                        popup.close("asyncbox_error");
                    },2000);
                }

                if(data.url&&data.url!=''){
                    setTimeout(function(){
                        top.window.location.href=data.url;
                    },2000);
                }

                if(data.status==1&&data.url==''){
                    setTimeout(function(){
                        top.window.location.reload();
                    },1000);
                }
                $(inputs).unwrap();
            }
        });

        return false;
    });
}
</script>

<script type="text/javascript">
formSubmit("<?php echo U(CONTROLLER_NAME . '/create');?>");
</script>

            </div>
        </div>
        <div class="clear"></div>

        <!-- footer -->
        <div id="Bottom">
    © 2014 Easy-Admin，Github项目地址：<a target="_blank" href="https://github.com/happen-zhang/easy-admin" target="_blank" >happen-zhang</a> Easy-Admin后台管理系统 All rights reserved
</div>

    </div>
</body>
</html>