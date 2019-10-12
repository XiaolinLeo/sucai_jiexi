<?php
switch ($code) {
    case -1:
        $show_class = 'error';
        break;
    case 2:
        $show_class = 'loading';
        break;
    case 1:
        $show_class = 'right';
        break;
    default:
        $show_class = 'info';
}
?>
<style type="text/css">
/*Jump*/
.system-jump-box {
    border: 0.25rem solid #c6e9ff;
    margin: 1rem 0;
}

.system-jump-message>h5>.system-jump-icon {
    font-size: 1.25rem;
    width: 1.25rem;
    height: 1.25rem;
    margin-right: 0.75rem;
}

.system-jump-right {
    background: #e2ffea;
}

.system-jump-right>h5 {
    color: #59ba74;
}

.system-jump-info {
    background: #F2F9FD;
}

.system-jump-info>h5 {
    color: #888888;
}

.system-jump-error {
    background: #fff0f0;
}

.system-jump-error>h5 {
    color: #d70000;
}

.system-jump-loading {
    background: #e8ecff;
}

.system-jump-loading>h5 {
    color: #36befa;
}
</style>

<div class="system-jump-box">
    <div class="system-jump-message system-jump-{$show_class} p-3">
        <h5>
            {switch $code}
                {case -1}<i class="adt-icon system-jump-icon icon-show-error"></i>{/case}
                {case 2}<i class="adt-icon system-jump-icon icon-show-loading"></i>{/case}
                {case 1}<i class="adt-icon system-jump-icon icon-show-right"></i>{/case}
                {default}<i class="adt-icon system-jump-icon icon-show-info"></i>
            {/switch}
            {:strip_tags($msg)}
        </h5>
        {if $code === 2}
            <div class="progress mb-2">
                <div class="progress-bar progress-bar-striped progress-bar-animated bg-success w-100"></div>
            </div>
        {/if}
        <div class="system-jump form-text text-muted">
            页面将在<b id="system-jump-wait">{$wait}</b>秒后跳转，<a id="system-jump-href" href="{$url}">点击这里快速跳转</a>
        </div>
    </div>
</div>
<script>
    $(function(){
        var wait = {$wait},
            href = $('#system-jump-href').attr('href');
        if(parseInt(wait) <= 0){
            location.href = href;
        }else{
            var interval = setInterval(function(){
                wait--;
                $('#system-jump-wait').html(wait);
                if(wait <= 0) {
                    clearInterval(interval);
                    location.href = href;
                };
            }, 1000);
        }
    })
</script>
