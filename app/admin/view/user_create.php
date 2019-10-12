<form class="card" method="post" autocomplete="off" action="{:url('admin/user/create')}">
    <div class="card-header">创建用户</div>
    <div class="card-body">
        <div class="form-group">
            <label>用户名</label>
            <input type="text" name="username" class="form-control">
        </div>
        <div class="form-group">
            <label>手机号</label>
            <input type="text" name="mobile" class="form-control">
        </div>
        <div class="form-group">
            <label>邮 箱</label>
            <input type="text" name="email" class="form-control">
        </div>
        <div class="form-group">
            <label>密码</label>
            <input type="text" name="password" class="form-control">
        </div>
        <div class="form-group">
            <label>最大解析次数</label>
            <input type="number" name="parse_max_times" class="form-control" value="0">
            <small class="form-text text-muted">当前账户拥有的最大解析次数，填0为无限制</small>
            <small class="form-text text-muted">此权限高于单站点解析限制权限，例如我图网最高可解析20次，但次数填写10，那么解析十次后账户便不可在解析任何站点</small>
        </div>
        <div class="form-group">
            <label>过期时间</label>
            <input type="number" name="out_time" class="form-control" value="0">
            <small class="form-text text-muted">单位小时，如果填写0，那么账户永久有效</small>
            <small class="form-text text-muted">例如：填写72，那么当该账户第一次登录时开始计算时间，72小时候过期，最大值：87600</small>
        </div>
        <div class="form-group">
            <label>账户类型</label>
            <select class="form-control" name="type">
                <option value="member" selected>会员</option>
                <option value="proxy">代理</option>
                <option value="system">管理员</option>
            </select>
            <small class="form-text text-muted">管理员可进入后台，请谨慎选择！</small>
            <small class="form-text text-muted">代理用户可在前台生成充值卡密，普通会员仅可解析资源</small>
        </div>
        <div class="form-text text-muted">日解析次数和最大解析次数填写0表示无限制，填写-1表示无权限</div>
        <div class="form-text text-muted">开启计划任务后，每日00:00会重置前日解析次数，最大解析次数是指该账号拥有这个站点的最大解析次数，除非使用充值卡充值或管理员手动调整，否则解析次数达到后就不可在解析</div>
        {foreach $site_list as $site}
            <input type="hidden" name="site_access[{$site['site_id']}][day_used]" value="0">
            <input type="hidden" name="site_access[{$site['site_id']}][max_used]" value="0">
            <div class="form-group d-flex justify-content-between">
                <div class="my-1 mr-3 text-right" style="width: 180px;">{$site['title']}</div>
                <div class="input-group mr-3">
                    <div class="input-group-prepend">
                        <div class="input-group-text">日解析最大次数</div>
                    </div>
                    <input type="number" class="form-control" name="site_access[{$site['site_id']}][day]" placeholder="单日可解析最大次数" value="-1">
                </div>
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">最大解析总次数</div>
                    </div>
                    <input type="number" class="form-control" name="site_access[{$site['site_id']}][all]" placeholder="最大解析总次数" value="-1">
                </div>
            </div>
        {/foreach}
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-success btn-submit ajax-post">提交数据</button>
    </div>
</form>
