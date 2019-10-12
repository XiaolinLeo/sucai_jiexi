<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div >系统计划任务 - 系统自动化任务</div> <a class="btn btn-primary btn-sm" href="{:url('admin/queue/reset')}">重置所有任务</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-card mb-0">
            <thead>
                <tr>
                    <th scope="col">名称</th>
                    <th scope="col">执行文件</th>
                    <th scope="col">队列名</th>
                    <th scope="col">最后执行</th>
                    <th scope="col">下次执行</th>
                    <th scope="col">已执行次数</th>
                    <th scope="col">状态</th>
                </tr>
            </thead>
            <tbody>
                {volist name="queue_list" id="queue"}
                    <tr>
                        <td>{$queue['title']}</td>
                        <td>{$queue->payload->job}</td>
                        <td>{$queue['queue']}</td>
                        <td>{:date('Y-m-d H:i:s',$queue['reserved_at'])}</td>
                        <td>{:date('Y-m-d H:i:s',$queue['available_at'])}</td>
                        <td>{$queue['attempts']}</td>
                        <td>{$queue['status_text']}</td>
                    </tr>
                {/volist}
            </tbody>
        </table>
    </div>
    {if $page}
        <div class="card-footer">{$page|raw}</div>
    {/if}
</div>
