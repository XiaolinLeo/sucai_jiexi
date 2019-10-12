<div class="card">
    <div class="card-header d-flex justify-content-between">
        <div class="">
            <a class="btn btn-primary btn-sm" href="{:url('admin/card/create')}">新增充值卡</a>
            <a class="btn btn-info btn-sm" href="{:url('admin/card/export')}" target="_blank">导出充值卡</a>
        </div>
        <div class="">
            <a class="btn btn-sm btn-danger ajax-link" data-mode="confirm" href="{:url('admin/card/delete_used')}">清除已使用</a>
            <a class="btn btn-sm btn-danger ajax-link" data-mode="confirm" href="{:url('admin/card/delete_all')}">删除全部卡密</a>
        </div>
    </div>
    <div class="card-body p-0">
        <table class="table table-striped table-hover table-card mb-0">
            <thead>
                <tr>
                    <th scope="col">卡号</th>
                    <th width="200" scope="col">创建用户</th>
                    <th width="400" scope="col">卡密功能</th>
                    <th width="200" scope="col">充值用户</th>
                    <th width="80" scope="col">操作</th>
                </tr>
            </thead>
            <tbody>
                {volist name="card_list" id="card"}
                    <tr>
                        <td class="text-success Clipboard" data-toggle="tooltip" data-placement="top" title="" data-original-title="点击复制卡密" data-clipboard-text="{$card['card_id']}">{$card['card_id']}</td>
                        <td data-toggle="tooltip" data-placement="top" title="" data-original-title="{$card['create_time']}">{$card['create_user']['username']}</td>
                        <td>{$card['info']|raw}</td>
                        <td {if !empty($card['use_uid'])}data-toggle="tooltip" data-placement="top" title="" data-original-title="{$card['use_time']}"{/if}>
                            {if empty($card['use_uid'])}
                                未使用
                            {else}
                                {$card['use_user']['username']}
                            {/if}
                        </td>
                        <td>
                            <a class="ajax-link" data-mode="confirm" href="{:url('admin/card/delete',['card_id'=>$card['card_id']])}">删除</a>
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
<script type="text/javascript">
    $(function(){
        $(document)
            .on('click','.show-access-times',function(){
                var access = $(this).data('access-times');
                var message = '';
                $.each(access,function(index,value){
                    message += value.title+': 日解析+'+value.day+' 总解析+'+value.all+'<br>';
                })
                dialog.alert(message, {
                    title:'站点权限',
                });
            })
    })
</script>
