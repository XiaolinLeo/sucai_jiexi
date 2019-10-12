<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="">
            <a class="btn btn-primary btn-sm" href="{:url('admin/site/edit')}">新增站点</a>
            <a class="btn btn-info btn-sm" href="{:url('admin/site/third')}">第三方站点</a>
            <a class="btn btn-success btn-sm" href="{:url('admin/site/third_edit')}">新增第三方站点</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-card mb-0">
            <thead>
                <tr>
                    <th scope="col">网站名称</th>
                    <th scope="col">官网地址</th>
                    <th scope="col">URL特征</th>
                    <th scope="col">状态</th>
                    <th scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                {volist name="site_list" id="site"}
                    <tr>
                        <td>{$site['title']}</td>
                        <td>{$site['url']}</td>
                        <td>{$site['url_regular']}</td>
                        <td>{$site['status_text']}</td>
                        <td>
                            <a href="{:url('admin/site/cookie',['site_id'=>$site['site_id']])}">Cookie</a>
                            <a href="{:url('admin/site/edit',['site_id'=>$site['site_id']])}">编辑</a>
                            <a class="ajax-link" data-mode="confirm" href="{:url('admin/site/delete',['site_id'=>$site['site_id']])}">删除</a>
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
