<form method="post" data-after="export_user">
<div class="card">
    <div class="card-header d-flex justify-content-between">
        导出会员
    </div>
    <div class="card-body">
        <div class="form-group">
            {foreach $field_list as $i=>$field}
            <div class="custom-control custom-checkbox">
                <input type="checkbox" class="custom-control-input" name="fields[]" id="field_{$i}" value="{$field['Field']}">
                <label class="custom-control-label" for="field_{$i}">{$field['Comment']}</label>
            </div>
            {/foreach}
        </div>
        <div class="form-group">
            <label>文件储存名称</label>
            <input type="text" name="filename" class="form-control" placeholder="例如：黄金用户">
        </div>
        <div class="form-group">
            <label>账户类型</label>
            <select class="form-control" name="user_type">
                <option value="all" selected>会员</option>
                <option value="member">会员</option>
                <option value="proxy">代理</option>
                <option value="system">管理员</option>
            </select>
        </div>
        <div class="form-group d-flex justify-content-between">
            <div class="my-1 mr-3 text-right" style="width: 200px;">注册时间范围</div>
            <div class="input-group mr-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">开始时间</div>
                </div>
                <input type="text" class="form-control" name="start_time" value="0或不填为不限制">
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">结束时间</div>
                </div>
                <input type="text" class="form-control" name="end_time" value="0或不填为不限制">
            </div>
        </div>
        <div class="form-group d-flex justify-content-between">
            <div class="my-1 mr-3 text-right" style="width: 200px;">UID范围</div>
            <div class="input-group mr-3">
                <div class="input-group-prepend">
                    <div class="input-group-text">开始UID</div>
                </div>
                <input type="number" class="form-control" name="start_uid" placeholder="0或不填为不限制">
            </div>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">结束UID</div>
                </div>
                <input type="number" class="form-control" name="end_uid" placeholder="0或不填为不限制">
            </div>
        </div>
    </div>
    <div class="card-footer">
        <button class="btn btn-info btn-submit ajax-post" type="submit">导出符合条件的会员</button>
    </div>
</div>
</form>
<script type="text/javascript">
    function export_user(form,s,btn){
        console.log(s);
        window.location.href = s.url;
        return false;
    }
</script>
