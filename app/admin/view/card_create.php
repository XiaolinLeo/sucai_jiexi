<form class="card" method="post" autocomplete="off" action="{:url('admin/card/create')}">
    <div class="card-header">充值卡密生成</div>
    <div class="card-body">
        <div class="form-group">
            <label>卡密规则</label>
            <input type="text" name="rule" class="form-control">
			<div class="form-text text-muted small">"@"代表任意随机英文字符，"#"代表任意随机数字，"*"代表任意英文或数字</div>
			<div class="form-text text-muted small">规则样本：<strong class="text-success">@@@@@#####*****</strong></div>
			<div class="form-text text-muted small">注意：规则位数过小会造成用户名生成重复概率增大，过多的重复用户名会造成用户名生成终止</div>
			<div class="form-text text-muted small">用户名规则中不能带有中文及其他特殊符号</div>
			<div class="form-text text-muted small">为了避免用户名重复，随机位数最好不要少于8位</div>
        </div>
        <div class="form-group">
            <label>生成数量</label>
            <input type="number" name="numbers" class="form-control" value="10">
            <div class="form-text text-muted small">每次生成数据建议在100以内</div>
        </div>
        <div class="form-group">
            <label>延长时间</label>
            <input type="text" name="valid_time" class="form-control">
            <div class="form-text text-muted small">为账户延长可用时间，单位小时，填写0时不增加账户会员时间，该时间对永久账户无效</div>
            <div class="form-text text-muted small">填写-1时可将账户提升至永久有效，提升后无法降权</div>
            <div class="form-text text-muted small">例如：此处填写72，那么会员使用该卡密后会延长账户有效期72小时（3天）</div>
        </div>
        <div class="form-group">
            <label>账户解析总次数</label>
            <input type="text" name="account_times" class="form-control">
            <div class="form-text text-muted small">为账户增加总解析次数,该次数对无限次账户无效</div>
            <div class="form-text text-muted small">填写0时不增加账户总解析次数，填写-1时可将账户提升为无限次下载，提升后无法降权</div>
        </div>
        {foreach $site_list as $site}
            <div class="form-group d-flex justify-content-between">
                <div class="my-1 mr-3 text-right" style="width: 180px;">{$site['title']}</div>
                <div class="input-group mr-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">日上限</div>
                    </div>
                    <input type="number" class="form-control" name="access_times[{$site['site_id']}][day]" placeholder="每日解析上限" value="0">
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">总次数</div>
                    </div>
                    <input type="number" class="form-control" name="access_times[{$site['site_id']}][all]" placeholder="总解析上限" value="0">
                </div>
            </div>
        {/foreach}
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-success btn-submit ajax-post">提交数据</button>
    </div>
</form>
