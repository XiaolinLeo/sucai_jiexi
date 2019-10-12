<!DOCTYPE html>
<html style="background: #f2f2f2;">
<head>
	<meta charset="utf-8">
	<meta name="renderer" content="webkit">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<meta content="always" name="referrer">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<title>管理中心</title>
	<base href="{$_G['site_url']}">
	<script src="static/js/jquery_3.4.1.min.js"></script>
	<script src="static/js/bootstrap.min.js"></script>
	<link rel="stylesheet" type="text/css" href="static/css/bootstrap.min.css">
	<link rel="stylesheet" type="text/css" href="static/css/common.css">
</head>
<body>
<nav class="d-flex justify-content-between fixed-top admin-top bg-white">
	<div class="d-flex justify-content-start pl-3">
		<a class="px-3" href="{$_G['site_url']}" target="_blank">前台首页</a>
		<a class="px-3 text-danger upgrade-cms d-none" href="{:url('admin/tools/check_version')}">更新程序</a>
	</div>
	<div class="d-flex justify-content-end pr-3">
		<a class="px-3" href="javascript:;">欢迎您，{$_G['member']['username']}</a>
		<a class="px-3" href="{:url('admin/account/logout')}">退出</a>
	</div>
</nav>
<div class="left-bar">
	<h5>管理中心</h5>
	<div class="left-nav">
		<a class="{if $Request.controller == 'Index'}active{/if}" href="{:url('admin/index/index')}">控 制 台</a>
		<a class="{if $Request.controller == 'Setting' && $Request.action == 'index'}active{/if}" href="{:url('admin/setting/index')}">系统设置</a>
		<a class="{if $Request.controller == 'Setting' && $Request.action == 'email'}active{/if}" href="{:url('admin/setting/email')}">邮件设置</a>
		<a class="{if $Request.controller == 'Setting' && $Request.action == 'proxy'}active{/if}" href="{:url('admin/setting/proxy')}">代理设置</a>
		<a class="{if $Request.controller == 'Site'}active{/if}" href="{:url('admin/site/index')}">站点配置</a>
		<a class="{if $Request.controller == 'User'}active{/if}" href="{:url('admin/user/index')}">会员数据</a>
		<a class="{if $Request.controller == 'Data'}active{/if}" href="{:url('admin/data/index')}">附件管理</a>
		<a class="{if $Request.controller == 'Log'}active{/if}" href="{:url('admin/log/index')}">解析记录</a>
		<a class="{if $Request.controller == 'Card'}active{/if}" href="{:url('admin/card/index')}">充值卡密</a>
		<a class="{if $Request.controller == 'Queue'}active{/if}" href="{:url('admin/queue/index')}">计划任务</a>
		<a class="{if $Request.controller == 'Tools'}active{/if}" href="{:url('admin/tools/index')}">更新缓存</a>
	</div>
</div>
<div class="admin-content">
	<div class="p-3">{__CONTENT__}</div>
</div>
<script type="text/javascript" src="static/js/common.js"></script>
<script type="text/javascript">
	$(function(){
		$.ajax({
			url:'{:url('admin/index/check')}',
			dataType:'json',
			success:function(s){
				if(s.has_new == 1){
					if($('.version').length>0){
						$('.version').after('<a class="pl-3 text-success" href="{:url('admin/tools/check_version')}">发现新版本：'+s.version+'，点击更新程序</a>');
					}
					if($('.upgrade-log').length>0){
						$('.upgrade-log').removeClass('d-none').html(s.upgrade_log);
					}
					$('.upgrade-cms').removeClass('d-none').html('发现新版本：'+s.version+'，点击更新程序');
				}
			}
		})
	})
</script>
</body>
</html>
