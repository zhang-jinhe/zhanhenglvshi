<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

/**
 * ------------------------------------------------------
 */
// -- 框架
/**
 * ------------------------------------------------------
 */
if ($_REQUEST['act'] == '') {

	//ecs_header("Location: ./index.php?act=main\n");
	$smarty -> display('index.htm');
	exit();
} elseif ($_REQUEST['act'] == 'main') {
	$smarty -> display('index.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 顶部框架的内容
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'top') {
	// 获得管理员设置的菜单
	$lst = array();
	$nav = $db -> GetOne('SELECT nav_list FROM ' . $ecs -> table('admin_user') . " WHERE user_id = '" . $_SESSION['admin_id'] . "'");
	if (!empty($nav)) {
		$arr = explode(',', $nav);

		foreach ($arr AS $val) {
			$tmp = explode('|', $val);
			$lst[$tmp[1]] = $tmp[0];
		} 
	} 
	// 获得管理员设置的菜单
	// 获得管理员ID
	// $smarty->assign('nav_list', $lst);
	$smarty -> assign('admin_id', $_SESSION['admin_id']);


	$smarty -> display('header.htm');
} 
elseif ($_REQUEST['act'] == 'chang_lang') {
admin_priv('madmin_list'); 
$select_lang=trim($_REQUEST['select_lang']);
$sql = "UPDATE " . $GLOBALS['ecs']->table('moban') . " SET select_lang='$select_lang'  ";
$GLOBALS['db']->query($sql);
echo json_encode($select_lang);	
} 
/**
 * ------------------------------------------------------
 */
// -- 左边的框架
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'left') {
	$smarty -> display('left.htm');
} 


elseif ($_REQUEST['act'] == 'start') {
	/* 系统信息 */
	$mysql_ver = $db->version();
    $sys_info['os']            = PHP_OS;
    $sys_info['ip']            = $_SERVER['SERVER_ADDR'];
    $sys_info['web_server']    = $_SERVER['SERVER_SOFTWARE'];
    $sys_info['php_ver']       = PHP_VERSION;
    $sys_info['mysql_ver']     = $mysql_ver;
    $sys_info['zlib']          = function_exists('gzclose') ? $_LANG['yes']:$_LANG['no'];
    $sys_info['safe_mode']     = (boolean) ini_get('safe_mode') ?  $_LANG['yes']:$_LANG['no'];
    $sys_info['safe_mode_gid'] = (boolean) ini_get('safe_mode_gid') ? $_LANG['yes'] : $_LANG['no'];
    $sys_info['timezone']      = function_exists("date_default_timezone_get") ? date_default_timezone_get() : $_LANG['no_timezone'];
    $sys_info['socket']        = function_exists('fsockopen') ? $_LANG['yes'] : $_LANG['no'];

    if ($gd == 0)
    {
        $sys_info['gd'] = 'N/A';
    }
    else
    {
        if ($gd == 1)
        {
            $sys_info['gd'] = 'GD1';
        }
        else
        {
            $sys_info['gd'] = 'GD2';
        }

        $sys_info['gd'] .= ' (';

        /* 检查系统支持的图片类型 */
        if ($gd && (imagetypes() & IMG_JPG) > 0)
        {
            $sys_info['gd'] .= ' JPEG';
        }

        if ($gd && (imagetypes() & IMG_GIF) > 0)
        {
            $sys_info['gd'] .= ' GIF';
        }

        if ($gd && (imagetypes() & IMG_PNG) > 0)
        {
            $sys_info['gd'] .= ' PNG';
        }

        $sys_info['gd'] .= ')';
    }

    

    /* 允许上传的最大文件大小 */
    $sys_info['max_filesize'] = ini_get('upload_max_filesize');

    $smarty->assign('sys_info', $sys_info);
	$smarty -> display('start.htm');
}

/**
 * ------------------------------------------------------
 */
// -- 清除缓存
/**
 * ------------------------------------------------------
 */

elseif ($_REQUEST['act'] == 'clear_cache') {
	clear_all_files();

	sys_msg('页面缓存已经清除成功。');
} 

?>


