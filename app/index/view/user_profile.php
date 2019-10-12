<div class="row my-5">
	{include file="user/left"}
	<div class="col-10">
		<form autocomplete="off">
			<div class="card">
				<div class="card-header">修改我的资料</div>
				<div class="card-body">
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">当前用户名</label>
						<div class="col-sm-10" style="line-height: 35px;">
							<div class="text-muted">{$_G['member']['username']}&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(不可修改)</div>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">邮箱地址</label>
						<div class="col-sm-10">
							<input type="email" name="email" class="form-control" value="{$_G['member']['email']}">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">手 机 号</label>
						<div class="col-sm-10">
							<input type="text" name="mobile" class="form-control" value="{$_G['member']['mobile']}">
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					<button type="button" class="btn btn-success btn-submit ajax-post">保存设置</button>
				</div>
			</div>
		</form>
	</div>
</div>
