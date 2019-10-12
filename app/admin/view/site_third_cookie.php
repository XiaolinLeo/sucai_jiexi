<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="">
            {$third['title']} 的Cookie <a class="btn btn-primary btn-sm" href="{:url('admin/site/third_edit_cookie',['third_id'=>$third['third_id']])}">新增站点Cookie</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-card mb-0">
            <thead>
                <tr>
                    <th scope="col">Cookie名称</th>
                    <th scope="col">今日已使用</th>
                    <th scope="col">每日总次数</th>
                    <th scope="col">状态</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                {volist name="third.cookies" id="cookie"}
                    <tr>
                        <td>{$cookie['name']}</td>
                        <td>{$cookie['used_times']}</td>
                        <td>{$cookie['times']}</td>
                        <td>{$cookie['status_text']}</td>
                        <td>
                            <a href="{:url('admin/site/third_edit_cookie',['third_id'=>$cookie['third_id'],'cookie_id'=>$cookie['cookie_id']])}">编辑</a>
                            <a class="ajax-link" data-mode="confirm" href="{:url('admin/site/third_delete_cookie',['cookie_id'=>$cookie['cookie_id']])}">删除</a>
                        </td>
                    </tr>
                {/volist}
            </tbody>
        </table>
    </div>
</div>
