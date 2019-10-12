<form class="card" method="post" autocomplete="off" action="{:url('admin/site/third_edit')}">
    {if !empty($third)}
        <input type="hidden" name="third_id" value="{$third['third_id']}">
        <div class="card-header">编辑第三方站点</div>
    {else}
        <div class="card-header">新增第三方站点</div>
    {/if}
    <div class="card-body">
        <div class="form-group">
            <label>网站名称</label>
            <input type="text" name="title" class="form-control" value="{$third['title']??''}">
        </div>
        <div class="form-group">
            <label>官网地址</label>
            <input type="text" name="url" class="form-control" value="{$third['url']??''}">
        </div>
        <div class="form-group">
            <label>顶级域名</label>
            <input type="text" name="url_regular" placeholder="顶级域名" class="form-control" value="{$third['url_regular']??''}">
        </div>
        <div class="form-group">
            <label>状态</label>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status" value="1"  {if empty($third) || $third['status'] == 1}checked{/if}>
                <label class="custom-control-label">启用</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status" value="0"  {if !empty($third) && $third['status'] == 0}checked{/if}>
                <label class="custom-control-label">关闭</label>
            </div>
            <small class="form-text text-muted">关闭后不用使用该站点解析</small>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-success btn-submit ajax-post">提交数据</button>
    </div>
</form>
