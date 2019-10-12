<form class="card" method="post" autocomplete="off" action="{:url('admin/queue/edit')}">
    {if !empty($job)}
        <input type="hidden" name="id" value="{$job['id']}">
        <div class="card-header">创建任务</div>
    {else}
        <div class="card-header">编辑任务</div>
    {/if}
    <div class="card-header">创建任务</div>
    <div class="card-body">
        <div class="form-group">
            <label>任务名称</label>
            <input type="text" name="title" class="form-control" value="{$job['title']??''}">
        </div>
        <div class="form-group">
            <label>队列名</label>
            <input type="text" name="queue" class="form-control" value="{$job['queue']??''}">
        </div>
        <div class="form-group">
            <label>执行文件</label>
            <input type="text" name="payload[job]" class="form-control" value="{$job->payload->job??''}">
        </div>
        <div class="form-group">
            <label>执行数据</label>
            <input type="number" name="payload[data]" class="form-control" value="{$job->payload->data??''}">
        </div>
        <div class="form-group">
            <label>状态</label>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status" value="1" {if empty($job) || $job['status'] == 1}checked{/if}>
                <label class="custom-control-label">启用</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="status" value="0" {if empty($job) || $job['status'] == 1}checked{/if}>
                <label class="custom-control-label">关闭</label>
            </div>
            <small class="form-text text-muted">关闭的任务不会执行</small>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-success btn-submit ajax-post">提交数据</button>
    </div>
</form>
