<?php

global $theme;
if (!defined("IN_SB")) {
    echo "You should not be here. Only follow links!";
    die();
}

$number = -1;
if (!defined('IN_HOME')) {
    $GLOBALS['server_qry'] = "";
    if (isset($_GET['s'])) {
        $number = (int) $_GET['s'];
    }
}

$res     = $GLOBALS['db']->Execute("SELECT se.sid, se.ip, se.port, se.modid, se.rcon, md.icon FROM " . DB_PREFIX . "_servers se LEFT JOIN " . DB_PREFIX . "_mods md ON md.mid=se.modid WHERE se.sid > 0 AND se.enabled = 1 ORDER BY se.modid, se.sid");
$servers = array();
$i       = 0;
while (!$res->EOF) {
    if (isset($_SESSION['getInfo.' . $res->fields[1] . '.' . $res->fields[2]])) {
        $_SESSION['getInfo.' . $res->fields[1] . '.' . $res->fields[2]] = "";
    }
    $info          = array();
    $info['sid']   = $res->fields[0];
    $info['ip']    = $res->fields[1];
    $info['port']  = $res->fields[2];
    $info['icon']  = $res->fields[5];
    $info['index'] = $i;
    if (defined('IN_HOME')) {
        $info['evOnClick'] = "window.location = 'index.php?p=servers&s=" . $info['index'] . "';";
    }

    $GLOBALS['server_qry'] .= "xajax_ServerHostPlayers({$info['sid']}, 'servers', '', '" . $i . "', '" . $number . "', '" . defined('IN_HOME') . "', 70);";
    array_push($servers, $info);
    $i++;
    $res->MoveNext();
}

$theme->assign('access_bans', ($userbank->HasAccess(ADMIN_OWNER | ADMIN_ADD_BAN) ? true : false));
$theme->assign('server_list', $servers);
$theme->assign('IN_SERVERS_PAGE', !defined('IN_HOME'));
$theme->assign('opened_server', $number);

if (!defined('IN_HOME')) {
    $theme->display('page_servers.tpl');
}
