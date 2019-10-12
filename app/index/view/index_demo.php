<table class="bg-white table table-bordered table-striped table-hover my-5">
	<colgroup>
		<col width="120">
		<col>
	</colgroup>
	<thead>
		<tr>
			<th class="text-right" scope="col">名称</th>
			<th scope="col">示例地址（解析式需要输入相应格式的地址才能解析，例如：百度文库地址最后加.html和不加都能访问，但本站需要添加.html才能解析）</th>
		</tr>
	</thead>
	<tbody>
		{foreach $site_list as $site}
			<tr>
				<td class="text-right">{$site['title']}</td>
				<td>{$site['download_url']}</td>
			</tr>
		{/foreach}
	</tbody>
</table>
