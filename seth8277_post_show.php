<?php
/**
 * Created by Seth8277
 */

loadhead();
$s = option::pget('seth8277_post');
$s['posts'] = [];

$query = $m->query('SELECT * FROM ' . DB_PREFIX . 'seth8277_post WHERE `user_id` = ' . UID);
while ($post = $m->fetch_array($query)) {
    $s['posts'][] = $post;
}
if ($_GET['mod'] == 'add') {
    // validate
    $units = ['seconds', 'minutes', 'hours'];
    $post_url = $_POST['post_url'];
    $post_url = str_replace('http://tieba.baidu.com/p/', '', $post_url);
    $post_url = str_replace('https://tieba.baidu.com/p/', '', $post_url);
    $spacing = "+ {$_POST['spacing_time']} {$_POST['spacing_unit']}";
    $user_id = UID;
    $baiduid_id = $_POST['pid'];
    $starttime = $_POST['starttime'];
    $stoptime = $_POST['stoptime'];
    if (empty($_POST['spacing_time'] || empty($_POST['pid']) || empty($_POST['starttime']) || empty($_POST['stoptime']))) {
        throw new Exception('信息不完整');
    } elseif (count($s['posts']) >= $s['max_post'] && ISVIP == false) {
        throw new Exception('您的灌水数量已到达上限');
    } elseif (!in_array($_POST['spacing_unit'], $units)) {
        throw new Exception('时间单位错误');
    } elseif (strtotime($spacing) < strtotime("+ {$s['min_interval']} seconds") || $_POST['spacing_time'] < 1) {
        throw new Exception('发帖频率过快');
    } elseif (!strtotime($starttime) || !strtotime($stoptime)) {
        throw new Exception('不要给我搞事情');
    } elseif (!is_numeric($post_url)) {
        throw new Exception('请输入干净的链接');
    } elseif ($m->once_fetch_array('SELECT * FROM ' . DB_PREFIX . "baiduid WHERE `id` = {$_POST['pid']}") < 1) {
        throw new Exception('不要作死');
    } elseif ($m->count("seth8277_post", "`post_url` = '{$post_url}'") > 0)
        throw new Exception('请勿重复添加');

    $m->query("INSERT INTO " . DB_PREFIX . <<<ORZ
seth8277_post (`user_id`, `baiduid_id`, `post_url`, `starttime`, `stoptime`, `spacing`) VALUES ('{$user_id}', '{$baiduid_id}', '{$post_url}', '{$starttime}','{$stoptime}', '{$spacing}');
ORZ
    );
    redirect('index.php?plugin=seth8277_post&ok=true');
} elseif ($_GET['mod'] == 'del') {
    $user_id = UID;
    if ($m->once_fetch_array('SELECT * FROM ' . DB_PREFIX . "seth8277_post WHERE `id` = {$_GET['post_id']} AND `user_id` = '{$user_id}'") < 1) {
        throw new Exception('不要作死');
    }
    $m->query('DELETE FROM ' . DB_PREFIX . "seth8277_post WHERE `id` = {$_GET['post_id']}");
    redirect('index.php?plugin=seth8277_post&ok=true');
}

function getStatusMsg($statusCode)
{
    $msgs = [
        '1' => '<span class="text-success"> 成功 </span>',
        '0' => '<span class="text-info"> 等待执行 </span>'
    ];
    if (array_key_exists($statusCode, $msgs))
        return $msgs[$statusCode];
    return '<span class="text-warning"> 未知错误 </span>';
}

?>
<input type="button" data-toggle="modal" data-target="#addpost" class="btn btn-info btn-lg" value="+ 增加灌水"
       style="float:right;">
<h2>智能灌水</h2>
<br/>
<?php
if (isset($_GET['ok'])) {
    echo '<div class="alert alert-success">操作成功</div>';
}
?>
<div class="alert alert-info">
    当前已设置 <?= count($s['posts']) ?> 个要灌水的帖子 <br/>
    根据管理员的设置，您目前最多可以添加 <?= $s['max_post'] ?> 个帖子，发帖最小间隔时间为 <?= $s['min_interval'] ?> 秒
</div>
<div class="table-responsive">
    <table class="table table-hover" style="width:100%;">
        <thead>
        <tr>
            <td>帖子</td>
            <td>用户</td>
            <td>开始时间</td>
            <td>结束时间</td>
            <td>下次执行</td>
            <td>执行状态</td>
            <td>间隔 (s)</td>
            <td>操作</td>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($s['posts'] as $key => $value) {
            echo "<tr>";
            echo "<td><a href='https://tieba.baidu.com/p/{$value['post_url']}' target='_blank'>{$value['post_url']}</a>";
            echo "<td>" . $i['user']['baidu'][$value['baiduid_id']] ?? "{$key}" . "</td>";
            echo "<td>{$value['starttime']}</td>";
            echo "<td>{$value['stoptime']}</td>";
            echo "<td>{$value['nextdo']}</td>";
            echo "<td>" . getStatusMsg($value['status']) . "</td>";
            echo "<td>{$value['spacing']}</td>";
            echo "<td><a href='index.php?plugin=seth8277_post&mod=del&post_id={$value['id']}' class='btn btn-warning'>删除</a></td>";
            echo "</tr>";
        } ?>
        </tbody>
    </table>
</div>

<div class="modal fade" id="addpost" tabindex="-1" role="dialog" aria-labelledby="addpost" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title" id="addpost">添加灌水</h4>
            </div>


            <form action="index.php?plugin=seth8277_post&mod=add" method="post">
                <div class="modal-body">
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">灌水账号 [PID]</span>
                        <select name="pid" class="form-control">
                            <?php
                            foreach ($i['user']['bduss'] as $key => $value) {
                                echo "<option value=\"{$key}\"> " . $i['user']['baidu'][$key] ?? "{$key}" . "</option>";
                            } ?>
                        </select>
                    </div>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">帖子地址</span>
                        <input type="text" name="post_url" class="form-control">
                    </div>
                    <br/>
                    <div class="input-group date" id="starttime">
                        <span class="input-group-addon">开始时间</span>
                        <input type='text' class="form-control" name="starttime"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                    </div>
                    <script type="text/javascript">
                        $(function () {
                            $('#starttime').datetimepicker({
                                format: 'H:s'
                            });
                        });
                    </script>
                    <br/>
                    <div class="input-group date" id="stoptime">
                        <span class="input-group-addon">停止时间</span>
                        <input type='text' class="form-control" name="stoptime"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-time"></span>
                        </span>
                    </div>
                    <script type="text/javascript">
                        $(function () {
                            $('#stoptime').datetimepicker({
                                format: 'H:s'
                            });
                        });
                    </script>
                    <br/>
                    <div class="input-group">
                        <span class="input-group-addon">间隔时间</span>
                        <input type="number" name="spacing_time" class="form-control" style="width: 60%;">

                        <select name="spacing_unit" class="form-control" style=" width: 40%; float: right">
                            <option value="seconds">秒</option>
                            <option value="minutes">分</option>
                            <option value="hours">小时</option>
                        </select>
                    </div>
                    <br/>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary" id="runsql_button">提交更改</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<link href="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/css/bootstrap-datetimepicker.min.css"
      rel="stylesheet">
<script src="https://cdn.bootcss.com/moment.js/2.19.0/moment.min.js"></script>
<script src="https://cdn.bootcss.com/bootstrap-datetimepicker/4.17.47/js/bootstrap-datetimepicker.min.js"></script>
<?= loadfoot(); ?>
