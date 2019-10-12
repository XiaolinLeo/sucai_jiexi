<div class="row my-5">
	{include file="user/left"}
	<div class="col-10">
		<div class="card">
			<div class="card-header">解析记录 (可查看最近二十条解析记录)</div>
			<div class="card-body p-0">
				{if $log_list->isEmpty()}
					<div class="p-5 text-center text-muted display-1">暂无记录</div>
				{else}
					<table class="table table-striped table-hover mb-0">
						<colgroup>
							<col width="120">
							<col>
							<col>
							<col>
							<col>
						</colgroup>
						<thead>
							<tr>
								<th class="border-top-0" scope="col">源网站</th>
								<th class="border-top-0" scope="col">解析地址</th>
								<th class="border-top-0" scope="col">解析时间</th>
								<th class="border-top-0" scope="col">消耗次数</th>
								<th class="border-top-0" scope="col">状态</th>
							</tr>
						</thead>
						<tbody>
							{volist name="log_list" id="log"}
								<tr>
									<td align="right">{$log['website']['title']}</td>
									<td>{$log['parse_url']}</td>
									<td>{$log['create_time']}</td>
									<td>{$log['times']}</td>
									<td>{$log['status_text']}</td>
								</tr>
							{/volist}
						</tbody>
					</table>
				{/if}
			</div>
		</div>
	</div>
</div>
