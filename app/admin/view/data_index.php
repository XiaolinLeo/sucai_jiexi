<div class="card">
    <div class="card-header d-flex justify-content-between">
        附件管理
        <a class="btn btn-sm btn-danger ajax-link" data-mode="confirm" href="{:url('admin/data/delete_all')}">清空附件及记录</a>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-card mb-0">
            <thead>
                <tr>
                    <th scope="col">请求地址</th>
                    <th scope="col">文件大小</th>
                    <th scope="col">创建时间</th>
                    <th scope="col">下载耗时</th>
                    <th scope="col">上传耗时</th>
                    <th scope="col">状态</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                {volist name="attach_list" id="attach"}
                    <tr>
                        <td>{$attach['request_url']}</td>
                        <td>{:format_bytes($attach['filesize'])}</td>
                        <td>{$attach['create_time']}</td>
                        <td>{$attach['download_time']}</td>
                        <td>{$attach['upload_time']}</td>
                        <td>{$attach['status_text']}</td>
                        <td>
                            <a class="ajax-link" data-mode="confirm" href="{:url('admin/data/delete',['attach_id'=>$attach['attach_id']])}">删除</a>
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
