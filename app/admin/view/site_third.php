<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="">
            当前位置：第三方站点
            <a class="btn btn-success btn-sm" href="{:url('admin/site/third_edit')}">新增第三方站点</a>
            <a class="btn btn-primary btn-sm" href="{:url('admin/site/index')}">返回素材站点</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-card mb-0">
            <thead>
                <tr>
                    <th scope="col">网站名称</th>
                    <th scope="col">官网地址</th>
                    <th scope="col">顶级域名</th>
                    <th scope="col">状态</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                {volist name="third_list" id="third"}
                    <tr>
                        <td>{$third['title']}</td>
                        <td>{$third['url']}</td>
                        <td>{$third['url_regular']}</td>
                        <td>{$third['status_text']}</td>
                        <td>
                            <a href="{:url('admin/site/third_cookie',['third_id'=>$third['third_id']])}">Cookie</a>
                            <a href="{:url('admin/site/third_edit',['third_id'=>$third['third_id']])}">编辑</a>
                            <a class="ajax-link" data-mode="confirm" href="{:url('admin/site/third_delete',['third_id'=>$third['third_id']])}">删除</a>
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
