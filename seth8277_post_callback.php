<?php

/**
 * Created by Seth8277
 */
//$utils = function (){
//    function get_path(){
//        return dirname(__DIR__) . '/seth8277_post/sql';
//    }
//
//    function query($sql_file){
//        global $m;
//        $sql = file_get_contents(get_path() . $sql_file);
//        $sql = str_replace('{DB-PREFIX}', DB_PREFIX, $sql);
//        $m->query($sql);
//    }
//};

function callback_install()
{
    $options = [
        'max_post' => 20,
        'min_interval' => 60,
        'everytime' => 200
    ];
    option::pset('seth8277_post', $options);
    exec_sql('install.sql');
}

function callback_init(){
    $cron_options = [
        'file' => 'plugins/seth8277_post/seth8277_post_cron.php',
        'no' => 0,
        'desc' => '执行灌水任务',
        'freq' => 0,
        'log' => ''
    ];
    cron::aset('seth8277_post', $cron_options);
}

function callback_remove()
{
    global $m;
    option::pdel('seth8277_post');
    $m->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "seth8277_post`");
    $m->query("DROP TABLE IF EXISTS `" . DB_PREFIX . "seth8277_post_content`");
    cron::del('seth8277_post');
}

function callback_setting()
{
    global $i;
    $option = [
        'max_post' => $_POST['max_post'] ?? 20,
        'min_interval' => $_POST['min_interval'] ?? 60,
        'everytime' => $_POST['everytime'] ?? 200
    ];
    option::pset('seth8277_post', $option);
    Redirect("index.php?mod=admin:setplug&plug={$i['mode'][1]}&ok");
}

function exec_sql($sql_file)
{
    global $m;
    $sql = file_get_contents(dirname(__DIR__) . '/seth8277_post/sql/' . $sql_file);
    $sql = str_replace('{DB-PREFIX}', DB_PREFIX, $sql);
    $m->multi_query($sql);
}

