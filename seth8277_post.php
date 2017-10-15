<?php
/**
 * Created by Seth8277
 */

if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}

function seth8277_post_addaction_navi()
{
    if (isset($_GET['plugin']) && $_GET['plugin'] == 'seth8277_post') {
        echo "<li class=\"active\"><a href=\"index.php?plugin=seth8277_post\"><span class=\"glyphicon glyphicon-cloud-upload\"></span> 智能灌水</a></li>";
    } else {
        echo "<li><a href=\"index.php?plugin=seth8277_post\"><span class=\"glyphicon glyphicon-cloud-upload\"></span> 智能灌水</a></li>";
    }
}

function seth8277_post_check()
{
    global $i, $m;
    $baiduAccounts = [];
    $posts = [];

    $query = $m->all('baiduid','`uid` = \''. UID . '\'');
    while ($baiduAccount = $m->fetch_array($query)){
        $baiduAccounts[] = $baiduAccount;
    }
    $query = $m->all('seth8277_post','`user_id` = \''. UID . '\'');
    while ($post = $m->fetch_array($query)){
        $posts[] = $post;
    }

    unset($post, $baiduAccount);
    if (count($baiduAccounts) < 1) {
        $m->query('DELETE FROM `' . DB_NAME . '`.`' . DB_PREFIX . 'seth8277_post` WHERE `user_id` = ' . UID);
        return;
    }
    $baiduAccounts = $i['user']['bduss'];
    foreach ($posts as $post) {
        if (!key_exists($post['baiduid_id'], $baiduAccounts)){
            $m->query('DELETE FROM `' . DB_NAME . '`.`' . DB_PREFIX . 'seth8277_post` WHERE `id` = ' . $post['id']);
        }
    }
}

addAction('navi_1', 'seth8277_post_addaction_navi');
addAction('navi_7', 'seth8277_post_addaction_navi');
addAction('baiduid_set', 'seth8277_post_check');
