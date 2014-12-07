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
                <div class="contentArea">
    <div class="Item hr">
        <div class="current">编辑管理员信息</div>
    </div>
    <form>
        <table width="100%" border="0" cellspacing="0" cellpadding="0" class="table1">
            <tr>
                <th width="80">登录邮箱：</th>
                <td><input name="admin[email]" type="text" readonly class="input" size="30" value="<?php echo ($admin['email']); ?>" /></td>
            </tr>
            <tr>
                <th width="80">新密码：</th>
                <td><input class="input" name="admin[password]" type="password" size="30" /></td>
            </tr>
            <tr>
                <th width="80">确认密码：</th>
                <td><input class="input" name="admin[cfm_password]" type="password" size="30" /></td>
            </tr>
            <tr>
                <th width="80">状态：</th>
                <td>
                    <select name="admin[is_active]">
                        <option value="1" <?php if($admin['is_active'] == 1): ?>selected<?php endif; ?>>启用</option>
                        <option value="0" <?php if($admin['is_active'] == 0): ?>selected<?php endif; ?>>禁用</option>
                    </select>
                </td>
            </tr>
            <?php if(false == $admin['is_super']): ?><tr>
                <th width="80">所属角色：</th>
                <td>
                    <select name="admin[role_id]">
                        <?php if(is_array($roles)): $i = 0; $__LIST__ = $roles;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$role): $mod = ($i % 2 );++$i;?><option value="<?php echo ($role['id']); ?>" <?php if($admin['role_id'] == $role['id']): ?>selected<?php endif; ?> <?php if($role['pid'] == 0): ?>disabled="true"<?php endif; ?>><?php echo ($role['fullname']); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                </td>
            </tr><?php endif; ?>
            <tr>
                <th width="80">备注信息：</th>
                <td><textarea class="jq_watermark" rows = "5" cols="68" name="admin[remark]" placeholder="管理员备注信息"><?php echo ($admin['remark']); ?></textarea></td>
            </tr>
            <input type="hidden" name="admin[id]" value="<?php echo ($admin['id']); ?>" />
        </table>
    </form>
    <div class="commonBtnArea" >
        <button class="btn submit">提交</button>
    </div>
</div>

<script type="text/javascript">
$(".submit").click(function(){
    commonAjaxSubmit("<?php echo U('Admins/update');?>");
    return false;
});
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