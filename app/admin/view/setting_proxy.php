<form class="card" method="post" action="{:url('admin/setting/proxy')}">
	<div class="card-header">代理设置</div>
	<div class="card-body">
		<div class="form-group">
			<label>代理用户最多可生成卡密张数</label>
			<input type="text" class="form-control" name="setting[proxy_card_numbers]" value="{$_G['setting']['proxy_card_numbers']}">
			<small class="form-text text-muted">代理用户最多可生成卡密张数</small>
		</div>
		<div class="form-group">
			<label>代理用户最多可增加账户总解析次数</label>
			<input type="text" class="form-control" name="setting[proxy_card_account_times]" value="{$_G['setting']['proxy_card_account_times']}">
			<small class="form-text text-muted">代理用户最多可增加账户总解析次数</small>
		</div>
		<div class="form-group">
			<label>代理用户生成卡密的规则</label>
			<input type="text" class="form-control" name="setting[proxy_card_rule]" value="{$_G['setting']['proxy_card_rule']}">
			<div class="form-text text-muted small">"@"代表任意随机英文字符，"#"代表任意随机数字，"*"代表任意英文或数字</div>
			<div class="form-text text-muted small">规则样本：<strong class="text-success">@@@@@#####*****</strong></div>
			<div class="form-text text-muted small">注意：规则位数过小会造成用户名生成重复概率增大，过多的重复用户名会造成用户名生成终止</div>
			<div class="form-text text-muted small">用户名规则中不能带有中文及其他特殊符号</div>
			<div class="form-text text-muted small">为了避免用户名重复，随机位数最好不要少于8位</div>
		</div>
	</div>
	<div class="card-footer">
		<button type="button" class="btn btn-success btn-submit ajax-post">保存设置</button>
	</div>
</form>
