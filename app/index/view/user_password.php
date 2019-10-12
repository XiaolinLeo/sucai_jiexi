<div class="row my-5">
	{include file="user/left"}
	<div class="col-10">
		<form autocomplete="off">
			<div class="card">
				<div class="card-header">修改密码</div>
				<div class="card-body">
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">当前密码</label>
						<div class="col-sm-10">
							<input type="password" name="oldpassword" class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">新 密 码</label>
						<div class="col-sm-10">
							<input type="password" name="password" class="form-control">
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">重复新密码</label>
						<div class="col-sm-10">
							<input type="password" name="password_confirm" class="form-control">
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					<button type="button" class="btn btn-success btn-submit ajax-post">确定修改</button>
				</div>
			</div>
		</form>
	</div>
</div>
