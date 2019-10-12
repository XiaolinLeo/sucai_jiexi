<div class="col-2">
	<div class="list-group">
		<a class="list-group-item list-group-item-action {if $Request.action == 'index'}list-group-item-success{/if}" href="{:url('index/user/index')}">个人中心</a>
		{if in_array($_G['member']['type'],['system','proxy'])}
			<a class="list-group-item list-group-item-action {if $Request.action == 'proxy'}list-group-item-success{/if}" href="{:url('index/user/proxy')}">代理中心</a>
		{/if}
		<a class="list-group-item list-group-item-action {if $Request.action == 'recharge'}list-group-item-success{/if}" href="{:url('index/user/recharge')}">账户充值</a>
		<a class="list-group-item list-group-item-action {if $Request.action == 'download'}list-group-item-success{/if}" href="{:url('index/user/download')}">解析记录</a>
		<a class="list-group-item list-group-item-action {if $Request.action == 'profile'}list-group-item-success{/if}" href="{:url('index/user/profile')}">修改资料</a>
		<a class="list-group-item list-group-item-action {if $Request.action == 'password'}list-group-item-success{/if}" href="{:url('index/user/password')}">修改密码</a>
	</div>
</div>
