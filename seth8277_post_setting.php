<?php
/**
 * Created by Seth8277
 */
if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
if (isset($_GET['ok'])) {
    echo '
<div class="alert alert-success">保存成功</div>';
}
$s = option::pget('seth8277_post');
?>

<h2>智能灌水 - 设置</h2>
<form action="setting.php?mod=setplugin:seth8277_post" method="post">
    <table class="table table-striped">
        <thead>
        <tr>
            <th style="width:45%">参数</th>
            <th style="width:55%">值</th>
        </tr>
        </thead>

        <tbody>
        <tr>
            <td>
                用户最多设定帖子数量 （对管理员无效）
            </td>
            <td>
                <input type="number" min="0" class="form-control" name="max_post" value="<?php echo $s['max_post'] ?>"
                       required>
            </td>
        </tr>

        <tr>
            <td>
                用户发帖最小间隔时间 (s)
            </td>
            <td>
                <input type="number" min="0" class="form-control" name="max" value="<?php echo $s['min_interval'] ?>"
                       required>
            </td>
        </tr>
        <tr>
            <td>
                每次执行灌水数量 -1表示全部
            </td>
            <td>
                <input type="number" min="0" class="form-control" name="everytime" value="<?php echo $s['everytime'] ?>"
                       required>
            </td>
        </tr>
        </tbody>
    </table>
    <button type="submit" class="btn-primary btn">保存</button>
</form>