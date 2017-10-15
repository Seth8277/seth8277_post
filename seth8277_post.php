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

addAction('navi_1','seth8277_post_addaction_navi');
addAction('navi_7','seth8277_post_addaction_navi');

