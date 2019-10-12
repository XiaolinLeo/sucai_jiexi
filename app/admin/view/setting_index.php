<script type="text/javascript" src="static/js/summernote.min.js"></script>
<link rel="stylesheet" type="text/css" href="static/css/summernote.css">
<form class="card" method="post" action="{:url('admin/setting/index')}">
    <div class="card-header">基础设置</div>
    <div class="card-body">
        <div class="form-group">
            <label>网站名称</label>
            <input type="text" class="form-control" name="setting[site_name]"
                   value="{$_G['setting']['site_name']}">
            <small class="form-text text-muted">将显示在浏览器窗口标题等位置</small>
        </div>
        <div class="form-group">
            <label>LOGO标题名称</label>
            <input type="text" class="form-control" name="setting[logo_name]"
                   value="{$_G['setting']['logo_name']}">
            <small class="form-text text-muted">显示在首页LOGO标题处</small>
        </div>
        <div class="form-group">
            <label>管理员QQ</label>
            <input type="text" class="form-control" name="setting[qq]"
                   value="{$_G['setting']['qq']}">
            <small class="form-text text-muted">作为系统发邮件的时候的发件人地址</small>
        </div>
        <div class="form-group">
            <label>前端主页背景图片设置</label>
            <input type="hidden" class="form-control" name="setting[bg_reception]"
                   value="{$_G['setting']['bg_reception']|default=''}">
            <p><img id="bg_reception" src="{$_G['setting']['bg_reception']|default=''}" width="200" height="100"></p>
            <input type="file" name="bg_reception" value="">
            <a href="javascript:;" onclick="postPic('bg_reception')" class="bg-dark btn btn-dark">确定上传图片</a>
            <small class="form-text text-muted">作为前端主页背景图片</small>
        </div>
        <div class="form-group">
            <label>前端登录背景图片设置</label>
            <input type="hidden" class="form-control" name="setting[bg_reception_login]"
                   value="{$_G['setting']['bg_reception_login']|default=''}">
            <p><img id="bg_reception_login" src="{$_G['setting']['bg_reception_login']|default=''}" width="200"
                    height="100"></p>
            <input type="file" name="bg_reception_login" value="">
            <a href="javascript:;" onclick="postPic('bg_reception_login')" class="bg-dark btn btn-dark">确定上传图片</a>
            <small class="form-text text-muted">作为前端登录背景图片</small>
        </div>
        <div class="form-group">
            <label>前端激活码背景图片设置</label>
            <input type="hidden" class="form-control" name="setting[bg_reception_key]"
                   value="{$_G['setting']['bg_reception_key']|default=''}">
            <p><img id="bg_reception_key" src="{$_G['setting']['bg_reception_key']|default=''}" width="200" height="40"></p>
            <input type="file" name="bg_reception_key" value="">
            <a href="javascript:;" onclick="postPic('bg_reception_key')" class="bg-dark btn btn-dark">确定上传图片</a>
            <small class="form-text text-muted">作为前端激活码背景图片</small>
        </div>

        <div class="form-group">
            <label>前端注册背景图片设置</label>
            <input type="hidden" class="form-control" name="setting[bg_register_key]"
                   value="{$_G['setting']['bg_register_key']|default=''}">
            <p><img id="bg_register_key" src="{$_G['setting']['bg_register_key']|default=''}" width="200" height="100"></p>
            <input type="file" name="bg_register_key" value="">
            <a href="javascript:;" onclick="postPic('bg_register_key')" class="bg-dark btn btn-dark">确定上传图片</a>
            <small class="form-text text-muted">作为前端登录背景图片</small>
        </div>

        <div class="form-group">
            <label>前端激活码地址设置(前缀加http://或者https://)</label>
            <input type="text" class="form-control" name="setting[reception_key_url]"
                   value="{$_G['setting']['reception_key_url']|default=''}">
            <small class="form-text text-muted">作为前端激活码地址链接(前缀加http://或者https://)</small>
        </div>

        <div class="form-group">
            <label>网站页脚内容</label>
            <textarea class="form-control" id="editor" name="setting[footer]">{$_G['setting']['footer']}</textarea>
            <small class="form-text text-muted">网站页脚内容,支持自定义HTML</small>
        </div>
        <div class="form-group">
            <label>阿里云OSS AccessKeyId</label>
            <input type="text" class="form-control" name="setting[AccessKeyId]"
                   value="{$_G['setting']['AccessKeyId']}">
            <small class="form-text text-muted">如果不启用OSS存储则该项可不填</small>
        </div>
        <div class="form-group">
            <label>阿里云OSS AccessKeySecret</label>
            <input type="text" class="form-control" name="setting[AccessKeySecret]"
                   value="{$_G['setting']['AccessKeySecret']}">
            <small class="form-text text-muted">如果不启用OSS存储则该项可不填</small>
        </div>
        <div class="form-group">
            <label>阿里云OSS Endpoint</label>
            <input type="text" class="form-control" name="setting[Endpoint]"
                   value="{$_G['setting']['Endpoint']}">
            <small class="form-text text-muted">如果不启用OSS存储则该项可不填</small>
        </div>
        <div class="form-group">
            <label>阿里云OSS Bucket</label>
            <input type="text" class="form-control" name="setting[Bucket]"
                   value="{$_G['setting']['Bucket']}">
            <small class="form-text text-muted">如果不启用OSS存储则该项可不填</small>
        </div>
        <div class="form-group">
            <label>解析间隔时间</label>
            <input type="text" class="form-control" name="setting[parse_between_time]"
                   value="{$_G['setting']['parse_between_time']}">
            <small class="form-text text-muted">同一用户两次解析间隔时间，单位秒，建议值60</small>
        </div>
        <div class="form-group">
            <label>强制登录</label>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="setting[must_login]"
                       value="1" {if $_G['setting']['must_login']}checked{/if}>
                <label class="custom-control-label">启用强制登录</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="setting[must_login]"
                       value="0" {if !$_G['setting']['must_login']}checked{/if}>
                <label class="custom-control-label">禁用强制登录</label>
            </div>
            <small class="form-text text-muted">启用强制登陆后，用户必须处于登录状态才能进入网站（任何页面）</small>
        </div>
        <div class="form-group">
            <label>开启网站</label>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="setting[site_open]" value="1"
                       {if $_G['setting']['site_open']}checked{/if}>
                <label class="custom-control-label">启用网站</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="setting[site_open]" value="0"
                       {if !$_G['setting']['site_open']}checked{/if}>
                <label class="custom-control-label">关闭网站</label>
            </div>
            <small class="form-text text-muted">暂时将站点关闭，其他人无法访问，但不影响管理员访问</small>
        </div>
        <div class="form-group">
            <label>允许注册新用户</label>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="setting[allow_register]"
                       value="1" {if $_G['setting']['allow_register']}checked{/if}>
                <label class="custom-control-label">允许注册</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="setting[allow_register]"
                       value="0" {if !$_G['setting']['allow_register']}checked{/if}>
                <label class="custom-control-label">禁止注册</label>
            </div>
            <small class="form-text text-muted">关闭注册后用户无法在前台自行注册</small>
        </div>
        <div class="form-group">
            <label>调试模式</label>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="app_debug" value="1" {if
                       config('app.app_debug') == true}checked{/if}>
                <label class="custom-control-label">开启调试模式</label>
            </div>
            <div class="custom-control custom-radio">
                <input type="radio" class="custom-control-input" name="app_debug" value="0" {if
                       config('app.app_debug') == false}checked{/if}>
                <label class="custom-control-label">关闭调试模式</label>
            </div>
            <small class="form-text text-muted">如果你不知道该功能的作用，请选择关闭调试模式，由于服务器原因该项设置可能不是即时生效，更改后请等待
            </small>
        </div>
    </div>
    <div class="card-footer">
        <button type="button" class="btn btn-success btn-submit ajax-post">保存设置</button>
    </div>
</form>
<script type="text/javascript">
    function postPic(bg_pic) {
        var fileInfo = $("input[name='" + bg_pic + "']")[0].files[0];
        var formData = new FormData();
        formData.append("photo", fileInfo);
        formData.append("service", 'App.Bel-Ami.Info');
        console.log(fileInfo);
        console.log(formData);
        $.ajax({
            //请求方式
            type: "POST",
            contentType: false,
            processData: false,
            url: "{:url('admin/setting/uploadFile')}",
            data: formData,
            success: function (result) {
                if (result['error']) {
                    alert(result['error']);
                } else {
                    $("#" + bg_pic).attr("src", result['url']);
                    $("input[name='setting[" + bg_pic + "]']").val(result['url']);
                }
            }
        });
    }

    $(function () {
        $('#editor').summernote({
            tabsize: 2,
            height: 400
        });
    })
</script>
