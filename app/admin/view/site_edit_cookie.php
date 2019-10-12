<form class="card" method="post" autocomplete="off" action="{:url('admin/site/edit_cookie')}">
    <input type="hidden" name="site_id" value="{$website['site_id']}">
    {if !empty($cookie)}
        <input type="hidden" name="cookie_id" value="{$cookie['cookie_id']}">
    {/if}
    <div class="card-header">为 {$website['title']} 新增Cookie</div>
    <div class="card-body">
        <div class="form-group">
            <label>名称</label>
            <input type="text" name="name" class="form-control" value="{$cookie['name']??''}">
            <small class="form-text text-muted">该cookie的备注，可填写来源账号，方便自己记录</small>
        </div>
        <div class="form-group">
            <label>每日可用总次数</label>
            <input type="number" name="times" class="form-control" value="{$cookie['times']??0}">
            <small class="form-text text-muted">每日可用总次数，填写0为无限制，注意，如果每日使用次数超过总次数后将无法解析</small>
        </div>
        <div class="form-group">
            <label>Cookie详细内容</label>
            <textarea placeholder="Cookie内容详情" class="form-control" name="content">{$cookie['content']??''}</textarea>
        </div>
        <div class="form-group">
            <label>状态</label>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status" value="1" {if empty($cookie) || $cookie['status'] == 1}checked{/if}>
                <label class="custom-control-label">启用</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status" value="0" {if !empty($cookie) && $cookie['status'] == 0}checked{/if}>
                <label class="custom-control-label">关闭</label>
            </div>
            <small class="form-text text-muted">关闭的Cookie不会被用于解析资源</small>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-success btn-submit ajax-post">提交保存</button>
    </div>
</form>
