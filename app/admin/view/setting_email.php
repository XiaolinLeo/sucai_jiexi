<form class="card" method="post" action="{:url('admin/setting/email')}">
	<div class="card-header">基础设置</div>
	<div class="card-body">
		<div class="form-group">
			<label>邮件发送主机地址</label>
			<input type="text" class="form-control" name="setting[email_host]" value="{$_G['setting']['email_host']}">
			<small class="form-text text-muted">邮件发送主机地址，QQ邮箱为：smtp.qq.com</small>
		</div>
		<div class="form-group">
			<label>邮件端口</label>
			<input type="text" class="form-control" name="setting[email_port]" value="{$_G['setting']['email_port']}">
			<small class="form-text text-muted">一般默认为25</small>
		</div>
		<div class="form-group">
			<label>发送邮件邮箱地址</label>
			<input type="text" class="form-control" name="setting[email_username]" value="{$_G['setting']['email_username']}">
			<small class="form-text text-muted">发送邮件邮箱地址，你的QQ或网易或其他邮箱地址</small>
		</div>
		<div class="form-group">
			<label>邮箱授权码</label>
			<input type="text" class="form-control" name="setting[email_password]" value="{$_G['setting']['email_password']}">
			<small class="form-text text-muted">注意是邮箱授权码，不是登陆密码</small>
		</div>
		<div class="form-group">
			<label>邮件来源名称</label>
			<input type="text" class="form-control" name="setting[email_fromname]" value="{$_G['setting']['email_fromname']}">
			<small class="form-text text-muted">邮箱邮件来源名称</small>
		</div>
	</div>
	<div class="card-footer">
		<button type="button" class="btn btn-success btn-submit ajax-post">保存设置</button>
	</div>
</form>
