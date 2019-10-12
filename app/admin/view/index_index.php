<div class="card">
	<div class="card-header border-bottom-0">系统信息</div>
	<div class="card-body p-0">
		<table class="table table-striped table-hover mb-0">
			<tbody>
				<tr>
					<td>程序版本：</td>
					<td><strong class="text-danger version">{$_G['setting']['version']}</strong></td>
				</tr>
				<tr>
					<td>服务器系统及 PHP：</td>
					<td><?php echo request()->server('SERVER_SOFTWARE'); ?></td>
				</tr>
				<tr>
					<td>服务器 MySQL 版本：</td>
					<td>{$mysql_version}</td>
				</tr>
				<tr>
					<td>上传许可：</td>
					<td><?php echo ini_get('file_uploads') ? ini_get('upload_max_filesize') : 'Disabled'; ?></td>
				</tr>
				<tr>
					<td>当前数据库尺寸：</td>
					<td>{$db_size}</td>
				</tr>
			</tbody>
		</table>
	</div>
</div>
<div class="upgrade-log d-none mt-3">
</div>