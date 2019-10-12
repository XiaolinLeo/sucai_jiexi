<div class="url-box">
	<input type="text" class="url-text" placeholder="需要解析的链接地址" spellcheck="false">
	<div class="submit parse-btn {if !empty($between_time)}parse-disabled{/if}">{if !empty($between_time)}<span>{$between_time}</span> 秒{else}解 析{/if}</div>
</div>
<div class="download-url-box text-center d-none mb-5"></div>
<div class="card user-power mb-5" style="background-color:rgba(255,255,255,0.5);">
	<div class="card-header">支持的素材（资源）网站</div>
	<div class="card-body">
		<div class="site-list">
			{foreach $site_list as $site}
				<span class="badge badge-info" data-toggle="tooltip" data-placement="top" title="官网：{$site['url']}">{$site['title']}</span>
			{/foreach}
		</div>
		<p style="margin-top: 10px;">部分资源为独立收费资源，目前暂不支持该部分资源的解析！</p>
	</div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/clipboard.js/2.0.0/clipboard.min.js" type="text/javascript"></script>
<script>
	var updateTime,timeout = {$between_time??0};
	$(function(){
		$(document)
			.on('click','.submit',function(){
				if(!$('.download-url-box').data('showing')){
					$('.download-url-box').html('<p>正在努力解析中...</p>');
				}
				var link = $.trim($('.url-text').val());
				if(!link){
					$('.url-text').focus();
					return dialog.msg('请输入需要解析的网址');
				}
				parse_link(link);
			})
	})
    function copyDownload(data){
        var urllist = '';
        $.each(data, function (name, url) {
            urllist += '<a class="btn btn-danger ' + (urllist != '' ? 'ml-3' : '') + '" id="copy" data-clipboard-target="#download-url" href="javascript:;" title="点击复制下载连接，请在新的窗口打开下载" >点击复制下载链接</a>';
            urllist +='<br><p>请复制到新窗口按回车键进行下载</p>';
            urllist +='<input type="text" style="width: 100px;opacity: 0" value="'+url+'" id="download-url"/>';
        })
        timeout = {$_G['setting']['parse_between_time'] ? ? 0};
        if (timeout > 0) {
            parse_time();
        }
        $('.download-url-box').data('showing', true);
        $('.download-url-box').removeClass('d-none').html(urllist).show();
    }

        var clipboard = new ClipboardJS('#copy');
        // 显示用户反馈/捕获复制/剪切操作后选择的内容
        clipboard.on('success', function (e) {
                 console.info('Action:', e.action)//触发的动作/如：copy,cut等
                 console.info('Text:', e.text);//触发的文本
                 console.info('Trigger:', e.trigger);//触发的DOm元素
                 e.clearSelection();//清除选中样式（蓝色）
                 alert('复制成功，请复制到窗口按回车键下载文件');
         })
        clipboard.on('error', function (e) {
               console.error('Action:', e.action);
               console.error('Trigger:', e.trigger);
                 alert('复制成功，请手动复制下载的url');
        });
   
    function parse_link(link,verify_code){
		$('.submit').addClass('disabled').html('解析中...');
		$.ajax({
			url:'{:url('index/index/parse')}',
			data:{link:link,verify:verify_code},
			dataType:'json',
			type:'POST',
			timeout:60000,
			success:function(s){
				if(s.code == 'verify'){
					var op = {
						type: 1,
						title: s.title,
						closeBtn: false,
						area: s.area,
						shade: 0.3,
						id: 'LAY_layuipro',
						resize: false,
						btn: ['确定提交', '取消输入'],
						btnAlign: 'c',
						moveType: 1,
						content: s.content,
						yes: function(){
							dialog.closeAll();
							var verify_code = $('#verify_code').val();
							parse_link(link,verify_code);
						},
						btn2: function(){
							dialog.closeAll();
						}
					};
					if(s.no_button == 1){
						op.btn = false;
					}
					dialog.open(op);
					$('.verify-img-box img').off('click').on('click',function(){
						var verify_code = $(this).data('key');
						dialog.closeAll();
						parse_link(link,verify_code);
					})
					return false;
				}
				if(s.code == 1){
                    func = s.callback;
                    if (func && typeof(window[func]) ==='function') {
                        console.log(func);
                        eval('(' + func + ')')(s.data);
                    } else {
                        var urllist = '';
                        $.each(s.data, function (name, url) {
                            urllist += '<a class="btn btn-danger ' + (urllist != '' ? 'ml-3' : '') + '" href="'+url+'" rel="noreferrer"  target="_blank">' + name + '</a>';
                        });
                        timeout = {$_G['setting']['parse_between_time'] ? ? 0};
                        if (timeout > 0) {
                            parse_time();
                        }
                        $('.download-url-box').data('showing', true);
                    }
				}else{
					urllist = s.msg;
					$('.download-url-box').data('showing',false);
				}
				$('.download-url-box').removeClass('d-none').html(urllist).show();
                dialog.msg(s.msg);
			},
            complete:function(request, status){
                $('.submit').removeClass('disabled').html('解 析');
                if(status == 'error'){
                    dialog.msg('页面错误，请联系管理员！');
                }else if(status == 'timeout'){
                    dialog.msg('数据提交超时，请重试！');
                }
            }
		})
	}

	function parse_time(){
		timeout--;
		clearTimeout(updateTime);
		if(timeout <= 0){
			$('.parse-btn').html('解 析');
			return true;
		}
		$('.parse-btn').html(timeout+' 秒');
		updateTime = setTimeout(function(){
			parse_time();
		},1000)
	}
	{if !empty($between_time)}parse_time();{/if}
</script>
