<div class="row my-5">
	{include file="user/left"}
	<div class="col-10">
		<form autocomplete="off">
			<div class="card">
				<div class="card-header">充值卡充值</div>
				<div class="card-body">
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">当前用户名</label>
						<div class="col-sm-10" style="line-height: 35px;">
							<div class="text-muted">{$_G['member']['username']}</div>
						</div>
					</div>
					<div class="form-group row">
						<label class="col-sm-2 col-form-label text-right">充值卡密</label>
						<div class="col-sm-10">
							<input type="text" name="card_id" class="form-control">
						</div>
					</div>
				</div>
				<div class="card-footer text-right">
					<button type="button" class="btn btn-success btn-submit ajax-post">确定充值</button>
				</div>
			</div>
		</form>
	</div>
</div>
