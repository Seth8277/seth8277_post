<?php
if (!defined('SYSTEM_ROOT')) {
    die('Insufficient Permissions');
}
/**
 * Created by Seth8277
 */
function cron_seth8277_post()
{
    global $m;
    $s = option::pget("seth8277_post");
    $time = date('Y-m-d H:i:s');
    $posts = [];

//    获取本次计划执行的帖子
    if ($s['everytime'] > 0) {
        $posts = $m->rand(DB_PREFIX . "seth8277_post", 'id', $s['everytime'], "`nextdo` < '{$time}'");
    } elseif ($s['everytime'] = -1) {
        $query = $m->query('SELECT * FROM ' . DB_PREFIX . "seth8277_post WHERE `nextdo` < '{$time}'");
        while ($post = $m->fetch_array($query)) {
            $posts[] = $post;
        }
    }

    // 如果只有一条任务
    if (isset($posts['id'])) {
        $balduin = $posts['baiduid_id'];
        $content = wcurl::xget('https://sslapi.hitokoto.cn/?encode=text');
        $result = seth8277_post_send($posts['post_url'], $balduin, $content);

//        计算nextdo
        $nextdo = date('Y-m-d H:i:s', strtotime($posts['spacing']));
        if ($nextdo >= $posts['stoptime'] && $nextdo <= $posts['starttime']) {
            $nextdo = $posts['starttime'];
        }

//        写入数据库
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "seth8277_post` SET `status` = {$result['status']}, `nextdo` = '{$nextdo}' WHERE `id` = {$posts['id']}");
        return;
    }


//    发帖
    foreach ($posts as $post) {
        $balduin = $post['baiduid_id'];
        $content = wcurl::xget('https://sslapi.hitokoto.cn/?encode=text');
        $result = seth8277_post_send($post['post_url'], $balduin, $content);

//        计算nextdo
        $nextdo = date('Y-m-d H:i:s', strtotime($post['spacing']));
        if ($nextdo >= $post['stoptime'] && $nextdo <= $post['starttime']) {
            $nextdo = $post['starttime'];
        }

//        写入数据库
        $m->query("UPDATE `" . DB_NAME . "`.`" . DB_PREFIX . "seth8277_post` SET `status` = {$result['status']}, `nextdo` = '{$nextdo}' WHERE `id` = {$post['id']}");
        sleep(1);
    }
}

function seth8277_post_send($post_id, $baiduid, $content = 'StusGame Tieba Cloud Sign Plugin "wmzz_post"', $device = 4)
{
    if (empty($post_id) || empty($baiduid)) {
        return array(
            'status' => '1',
            'msg' => ''
        );
    }
    $ck = misc::GetCookie($baiduid);
    $xs = seth8277_post_gettie($post_id, $ck);
    $x = array(
        'BDUSS' => $ck,
        '_client_id' => 'wappc_136' . rand_int(10) . '_' . rand_int(3),
        '_client_type' => $device,
        '_client_version' => '5.0.0',
        '_phone_imei' => md5(rand_int(16)),
        'anonymous' => '0',
        'content' => $content,
        'fid' => $xs['fid'],
        'kw' => $xs['word'],
        'net_type' => '3',
        'tbs' => $xs['tbs'],
        'tid' => $post_id,
        'title' => ''
    );
    $y = '';
    foreach ($x as $key => $value) {
        $y .= $key . '=' . $value;
    }
    $x['sign'] = strtoupper(md5($y . 'tiebaclient!!!'));
    $c = new wcurl('http://c.tieba.baidu.com/c/c/post/add', array('Content-Type: application/x-www-form-urlencoded'));
    /* //Note:普通的
    $x = wmzz_post_gettie($tid,$ck);
    $c = new wcurl('http://tieba.baidu.com'.$x['__formurl']);
    unset($x['__formurl']);
    $x['co'] = $water;
    */
    $c->addcookie('BDUSS=' . $ck);
    $return = json_decode($c->post($x), true);
    $c->close();
    if (!empty($return['error_code']) && $return['error_code'] != '1') {
        return array(
            'status' => $return['error_code'],
            'msg' => $return['error_msg']
        );
    } else {
        return array(
            'status' => '1',
            'msg' => ''
        );
    }
}

function seth8277_post_gettie($tid, $ck)
{
    $c = new wcurl('http://tieba.baidu.com/mo/m?kz=' . $tid, array('User-Agent: Chinese Fucking Phone'));
    $c->addcookie('BDUSS=' . $ck);
    $t = $c->exec();
    preg_match('/<form action=\"(.*?)\" method=\"post\">/', $t, $formurl);
    preg_match('/<input type=\"hidden\" name=\"ti\" value=\"(.*?)\"\/>/', $t, $ti);
    preg_match('/<input type=\"hidden\" name=\"src\" value=\"(.*?)\"\/>/', $t, $src);
    preg_match('/<input type=\"hidden\" name=\"word\" value=\"(.*?)\"\/>/', $t, $word);
    preg_match('/<input type=\"hidden\" name=\"tbs\" value=\"(.*?)\"\/>/', $t, $tbs);
    preg_match('/<input type=\"hidden\" name=\"fid\" value=\"(.*?)\"\/>/', $t, $fid);
    preg_match('/<input type=\"hidden\" name=\"z\" value=\"(.*?)\"\/>/', $t, $z);
    preg_match('/<input type=\"hidden\" name=\"floor\" value=\"(.*?)\"\/>/', $t, $floor);
    return array(
        '__formurl' => $formurl[1],
        'co' => '',
        'ti' => $ti[1],
        'src' => $src[1],
        'word' => $word[1],
        'tbs' => $tbs[1],
        'ifpost' => '1',
        'ifposta' => '0',
        'post_info' => '0',
        'tn' => 'baiduWiseSubmit',
        'fid' => $fid[1],
        'verify' => '',
        'verify_2' => '',
        'pinf' => '1_2_0',
        'pic_info' => '',
        'z' => $z[1],
        'last' => '0',
        'pn' => '0',
        'r' => '0',
        'see_lz' => '0',
        'no_post_pic' => '0',
        'floor' => $floor[1],
        'sub1' => '回贴'
    );
}
