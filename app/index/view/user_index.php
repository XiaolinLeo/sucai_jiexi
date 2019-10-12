<div class="row my-5">
	{include file="user/left"}
	<div class="col-10">
		{if $_G['member']['out_time'] > 0}
			{if $_G['member']['out_time'] <= request()->time()}
				<div class="alert alert-danger">您的账号已于 <strong>{:date('Y-m-d H:i',$_G['member']['out_time'])}</strong> 过期，无法继续解析素材，请联系管理员！</div>
			{else}
				<div class="alert alert-success">您的账号有效期至： <strong>{:date('Y-m-d H:i',$_G['member']['out_time'])}</strong> 当前可正常解析素材</div>
			{/if}
		{elseif $_G['member']['out_time'] == 0 && $_G['member']['type'] != 'system'}
			<div class="alert alert-success">您的账号为永久会员，当前可正常解析素材</div>
		{/if}
		<div class="card">
			<div class="card-header">我的权限 (每日凌晨00:00更新下载次数) 当前账户可解析 {$_G['member']['parse_max_times'] == -1 ? '无权解析' : ($_G['member']['parse_max_times'] ==0 ? '无限制' : $_G['member']['parse_max_times'].'次')}</div>
			<div class="card-body p-0">
				<table class="table table-striped table-hover mb-0">
					<colgroup>
						<col width="150">
						<col>
						<col>
						<col>
						<col>
					</colgroup>
					<tbody>
						{php}$i=0;{/php}
						{foreach $_G['member']['site_access'] as $site_id => $access}
							{php}
								if (empty($_G['web_site'][$site_id]) || $_G['web_site'][$site_id]['status']<=0 || $access['all'] < 0 || $access['day'] < 0){
									continue;
								}
							{/php}
							<tr>
								<td class="{if !$i}border-top-0{/if} text-success Clipboard" align="right" data-toggle="tooltip" data-placement="top" title="点击复制网址" data-clipboard-text="{$_G['web_site'][$site_id]['url']}">{$_G['web_site'][$site_id]['title']} ： </td>
								<td {if !$i}class="border-top-0"{/if}>
									今日已解析：<strong class="text-danger pr-3">{$access['day_used']??0}次</strong>
								</td>
								<td {if !$i}class="border-top-0"{/if}>
									每日可解析：<strong class="text-info pr-3">{$access['day']??0}次</strong>
								</td>
								<td {if !$i}class="border-top-0"{/if}>
									已解析：<strong class="text-danger pr-3">{$access['max_used']??0}次</strong>
								</td>
								<td {if !$i}class="border-top-0"{/if}>
									可解析次数：<strong class="text-info pr-3">{$access['all']??0}次</strong>
								</td>
							</tr>
							{php}$i++;{/php}
						{/foreach}
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>
