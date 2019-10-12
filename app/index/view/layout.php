{include file="public/header"}
<nav class="navbar navbar-expand-lg navbar-index bg-info">
    <div class="container">
        <a class="navbar-brand" href="{$_G['site_url']}">{$_G['setting']['logo_name']}</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav mr-auto">
                <li class="nav-item {if $Request.controller == 'Index' && $Request.action == 'index'}active{/if}">
                    <a class="nav-link" href="{:url('index/index/index')}">资源解析演示</a>
                </li>
                <li class="nav-item {if $Request.controller == 'Index' && $Request.action == 'demo'}active{/if}">
                    <a class="nav-link" href="{:url('index/index/demo')}">解析示例</a>
                </li>
                <li class="nav-item {if $Request.controller == 'Index' && $Request.action == 'demo'}active{/if}">
                    <a class="nav-link" href="{$_G['setting']['reception_key_url']|default=''}">购买会员</a>
                </li>
            </ul>
            <div class="navbar-nav">
                {if $_G['uid']}
                <li class="nav-item {if $Request.controller == 'User'}active{/if}">
                    <a class="nav-link" href="{:url('index/user/index')}">
                        {$_G['member']['username']}
                        {if $_G['member']['type'] == 'system'}
                        (管理员)
                        {else}
                        {if $_G['member']['out_time'] == 0}
                        (永久有效)
                        {elseif $_G['member']['out_time'] > 0 && $_G['member']['out_time'] <=
                        request()->time()}
                        (已过期)
                        {else}
                        ({:date('Y-m-d H:i',$_G['member']['out_time'])})
                        {/if}
                        {/if}
                    </a>
                </li>
                <li class="nav-item"><a class="nav-link"
                                        href="{:url('index/user/recharge')}">会员充值</a></li>
                <li class="nav-item"><a class="nav-link"
                                        href="{:url('index/account/logout')}">安全退出</a></li>
                {else}
                <li class="nav-item"><a class="nav-link" href="{:url('index/account/login')}">登录</a>
                </li>
                {if $_G['setting']['allow_register']}
                <li class="nav-item"><a class="nav-link"
                                        href="{:url('index/account/register')}">注册</a></li>
                {/if}
                {/if}
            </div>
        </div>
    </div>
</nav>
<div class="container">
    {if $_G['member']['out_time'] > 0 && $_G['member']['out_time'] <= request()->time()}
    <div class="alert alert-primary" role="alert">您的账户已过期，无法解析资源，请联系管理员增加时间或获取新账号！</div>
    {/if}
    {__CONTENT__}
</div>
{include file="public/footer"}
