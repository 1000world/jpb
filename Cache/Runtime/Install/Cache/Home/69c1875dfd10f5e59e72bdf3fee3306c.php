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
      <li class="current"><em>1</em>检测环境</li>
      <li><em>2</em>创建数据</li>
      <li><em>3</em>完成安装</li>
    </ul>
</div>
<div class="server">
    <table width="100%">
        <tr>
            <td class="td1">环境检测</td>
            <td class="td1" width="25%">推荐配置</td>
            <td class="td1" width="25%">当前状态</td>
            <td class="td1" width="25%">最低要求</td>
        </tr>
        <tr>
            <td>操作系统</td>
            <td>类UNIX</td>
            <td><?php echo ($system_info); ?></td>
            <td>不限制</td>
        </tr>
        <tr>
            <td>PHP版本</td>
            <td>&gt;=5.3.x</td>
            <td><?php echo ($php_version); ?></td>
            <td>5.3.0</td>
        </tr>
        <tr>
            <td>服务器</td>
            <td>主流服务器</td>
            <td><?php echo ($server_info); ?></td>
            <td>不限制</td>
        </tr>
        <tr>
            <td>Mysql版本（client）</td>
            <td>>5.x.x</td>
            <td><?php echo ($mysql_info); ?></td>
            <td>4.2</td>
        </tr>
        <tr>
            <td>附件上传</td>
            <td>>2M</td>
            <td><?php echo ($upload_info); ?></td>
            <td>不限制</td>
        </tr>
        <tr>
            <td>Session</td>
            <td>开启</td>
            <td><?php echo ($session_info); ?></td>
            <td>开启</td>
        </tr>
        <tr>
            <td>GD库</td>
            <td>开启</td>
            <td><?php echo ($gd_info); ?></td>
            <td>开启</td>
        </tr>        
    </table>
    <table width="100%">
        <tr>
            <td class="td1">目录、文件权限检查</td>
            <td class="td1" width="25%">写入</td>
            <td class="td1" width="25%">读取</td>
        </tr>
        <?php if(is_array($directories_state)): $i = 0; $__LIST__ = $directories_state;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$dir_state): $mod = ($i % 2 );++$i;?><tr>
            <td><?php echo ($dir_state['dir_name']); ?></td>
            <td><?php echo ($dir_state['writable']); ?></td>
            <td><?php echo ($dir_state['readable']); ?></td>
        </tr><?php endforeach; endif; else: echo "" ;endif; ?>
    </table>
</div>

<div class="bottom tac">
  <a href="<?php echo U('CheckEnv/index');?>" class="btn">重新检测</a><a href="<?php echo U('Install/index');?>" class="btn">下一步</a>
</div>

        </div>
    </div>
    <div class="footer"> &copy; 2014 
    <a href="https://github.com/happen-zhang/easy-admin" target="_blank">Easy-Admin</a>
</div>

</body>
</html>