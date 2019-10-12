<?php
switch ($code) {
    case -1:
        $show_class = 'error';
        break;
    case 2:
        $show_class = 'loading';
        break;
    case 1:
        $show_class = 'right';
        break;
    default:
        $show_class = 'info';
}
?>
<div class="container">
	{switch $msg}
		{case site_close}
	        <div class="card my-5">
	            <div class="card-header">站点关闭</div>
	            <div class="card-body">{$_G['setting']['site_close_tip']}</div>
	        </div>
		{/case}
		{case login}
	        <div class="my-5 mx-auto" style="width: 25rem">
	            <form method="post" action="{:url('member/account/login')}">
	                <input type="hidden" name="referer" value="{$Request.url.true}">
	                <div class="card">
	                    <div class="card-header">请先登陆后再操作</div>
	                    <div class="card-body">
	                        <div class="form-group">
	                            <label>账户：</label>
	                            <input type="text" class="form-control" name="username" placeholder="邮箱/手机号/用户名" required>
	                        </div>
	                        <div class="form-group">
	                            <label>密码：</label>
	                            <input type="password" class="form-control" name="password" placeholder="账户密码" required>
	                        </div>
	                        <button class="btn btn-success d-block w-100 btn-submit ajax-post">登 陆</button>
	                    </div>
	                    <div class="card-footer clearfix">
	                        <div class="float-left">
	                            <a href="{:url('member/account/forget')}">忘记密码</a>
	                        </div>
	                        <div class="float-right">
	                            还没有帐号？ <a href="{:url('member/account/'.$_G['setting']['register_action'])}">立即注册&gt;&gt;</a>
	                        </div>
	                    </div>
	                </div>
	            </form>
	        </div>
		{/case}
		{default}
	        <div class="system-jump-box">
	            <div class="system-jump-message system-jump-{$show_class} p-3">
	                <h5>
	                    {switch $code}
			                {case -1}<i class="adt-icon system-jump-icon icon-show-error"></i>{/case}
			                {case 2}<i class="adt-icon system-jump-icon icon-show-loading"></i>{/case}
			                {case 1}<i class="adt-icon system-jump-icon icon-show-right"></i>{/case}
			                {default}<i class="adt-icon system-jump-icon icon-show-info"></i>
	                    {/switch}
	                    {:strip_tags($msg)}
	                </h5>
	                {if $code === 2}
	                    <div class="progress mb-2">
	                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-success w-100"></div>
	                    </div>
	                {/if}
	                <div class="system-jump form-text text-muted">
	                    页面将在<b id="system-jump-wait">{$wait}</b>秒后跳转，<a id="system-jump-href" href="{$url}">点击这里快速跳转</a>
	                </div>
	            </div>
	        </div>
	        <script>
	            require(['jquery'],function ($) {
	                $(function(){
	                    var wait = {$wait},
	                        href = $('#system-jump-href').attr('href');
	                    if(parseInt(wait) <= 0){
	                        location.href = href;
	                    }else{
	                        var interval = setInterval(function(){
	                            wait--;
	                            $('#system-jump-wait').html(wait);
	                            if(wait <= 0) {
	                                clearInterval(interval);
	                                location.href = href;
	                            };
	                        }, 1000);
	                    }
	                })
	            })
	        </script>
	{/switch}
</div>
