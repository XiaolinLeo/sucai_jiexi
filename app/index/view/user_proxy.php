<div class="row my-5">
	{include file="user/left"}
	<div class="col-10">
		<div class="card">
			<div class="card-header">
				<ul class="nav nav-tabs card-header-tabs">
					<li class="nav-item">
						<a class="nav-link {if request()->param('type') != 'create_card'}active{/if}" href="{:url('index/user/proxy')}">我的卡密</a>
					</li>
					<li class="nav-item">
						<a class="nav-link {if request()->param('type') == 'create_card'}active{/if}" href="{:url('index/user/proxy',['type'=>'create_card'])}">生成卡密</a>
					</li>
				</ul>
			</div>
			{if $Request.param.type == 'create_card'}
				<form method="post" action="{:url('index/user/proxy',['type'=>'create_card'])}">
					<div class="card-body">
				        <div class="form-group">
				            <label>生成数量</label>
				            <input type="number" name="numbers" class="form-control" value="10">
				            <div class="form-text text-muted small">当前还可生成{$_G['setting']['proxy_card_numbers'] - $count}张卡密</div>
				        </div>
				        <div class="form-group">
				            <label>延长时间</label>
				            <input type="text" name="valid_time" class="form-control">
				            <div class="form-text text-muted small">当类型为时间时此项必填，单位小时。</div>
				            <div class="form-text text-muted small">例如：此处填写72，那么会员使用该卡密后会延长账户有效期72小时（3天）</div>
				        </div>
				        <div class="form-group">
				            <label>账户解析总次数</label>
				            <input type="text" name="account_times" class="form-control">
				            <div class="form-text text-muted small">最大值：{$_G['setting']['proxy_card_account_times']}</div>
				        </div>
				        {foreach $site_list as $site}
				            <div class="form-group d-flex justify-content-between">
				                <div class="my-1 mr-3 text-right" style="width: 180px;">{$site['title']}</div>
				                <div class="input-group mr-3">
				                    <div class="input-group-prepend">
				                        <div class="input-group-text">日上限</div>
				                    </div>
				                    <input type="number" class="form-control" name="access_times[{$site['site_id']}][day]" placeholder="每日解析上限" value="0">
				                </div>
				                <div class="input-group">
				                    <div class="input-group-prepend">
				                        <div class="input-group-text">总次数</div>
				                    </div>
				                    <input type="number" class="form-control" name="access_times[{$site['site_id']}][all]" placeholder="总解析上限" value="0">
				                </div>
				            </div>
				        {/foreach}
					</div>
					<div class="card-footer"><button type="button" class="btn btn-success btn-submit ajax-post">提交数据</button></div>
			    </form>
			{else}
				<div class="card-body p-0">
			        <table class="table table-striped table-hover table-card mb-0">
			            <thead>
			                <tr>
			                    <th scope="col">卡号</th>
			                    <th width="400" scope="col">卡密功能</th>
			                    <th width="200" scope="col">充值用户</th>
			                </tr>
			            </thead>
			            <tbody>
			                {volist name="card_list" id="card"}
			                    <tr>
			                        <td>{$card['card_id']}</td>
			                        <td>{$card['info']|raw}</td>
			                        <td {if !empty($card['use_uid'])}data-toggle="tooltip" data-placement="top" title="" data-original-title="{$card['use_time']}"{/if}>
			                            {if empty($card['use_uid'])}
			                                未使用
			                            {else}
			                                {$card['use_user']['username']}
			                            {/if}
			                        </td>
			                    </tr>
			                {/volist}
			            </tbody>
			        </table>
				</div>
			{/if}
		</div>
	</div>
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
