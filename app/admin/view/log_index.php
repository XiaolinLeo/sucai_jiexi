<div class="card">
    <div class="card-header d-flex justify-content-between">
        会员解析记录
        <a class="btn btn-sm btn-danger ajax-link" data-mode="confirm" href="{:url('admin/log/delete_all')}">清空解析记录</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-card mb-0">
            <thead>
                <tr>
                    <th scope="col">用户</th>
                    <th scope="col">解析地址</th>
                    <th scope="col">解析时间</th>
                    <th scope="col">消耗次数</th>
                    <th scope="col">状态</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                {volist name="log_list" id="log"}
                    <tr>
                        <td>{$log['member']['username']}</td>
                        <td>{$log['parse_url']}</td>
                        <td>{$log['create_time']}</td>
                        <td>{$log['times']}</td>
                        <td>{$log['status_text']}</td>
                        <td>
                            <a class="ajax-link" data-mode="confirm" href="{:url('admin/log/delete',['log_id'=>$log['log_id']])}">删除</a>
                        </td>
                    </tr>
                {/volist}
            </tbody>
        </table>
    </div>
    {if $page}
        <div class="card-footer">{$page|raw}</div>
    {/if}
</div>
