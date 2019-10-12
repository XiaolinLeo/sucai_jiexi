<form class="card" method="post" autocomplete="off" action="{:url('admin/site/edit')}">
    {if !empty($site)}
        <input type="hidden" name="site_id" value="{$site['site_id']}">
        <div class="card-header">编辑站点</div>
    {else}
        <div class="card-header">新增站点</div>
    {/if}
    <div class="card-body">
        <div class="form-group">
            <label>网站名称</label>
            <input type="text" name="title" class="form-control" value="{$site['title']??''}">
        </div>
        <div class="form-group">
            <label>官网地址</label>
            <input type="text" name="url" class="form-control" value="{$site['url']??''}">
        </div>
        <div class="form-group">
            <label>网站特征码</label>
            <input type="text" name="url_regular" placeholder="用于区分不同网站" class="form-control" value="{$site['url_regular']??''}">
        </div>
        <div class="form-group">
            <label>Bucket</label>
            <div class="form-control-inline">
                <input type="text" name="bucket" class="form-control" value="{$site['bucket']??''}">
            </div>
            <small class="form-text text-muted">存储附件时使用的Bucket，留空使用基础设置中的Bucket</small>
        </div>
        <div class="form-group">
            <label>状态</label>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status" value="1"  {if empty($site) || $site['status'] == 1}checked{/if}>
                <label class="custom-control-label">启用</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status" value="0"  {if !empty($site) && $site['status'] == 0}checked{/if}>
                <label class="custom-control-label">关闭</label>
            </div>
            <small class="form-text text-muted">关闭后前台不显示该网站并且不可解析</small>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-success btn-submit ajax-post">提交数据</button>
    </div>
</form>
