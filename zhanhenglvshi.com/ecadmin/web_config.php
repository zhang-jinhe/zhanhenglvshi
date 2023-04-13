<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');



/**
 * 代码
 */

/**
 * ------------------------------------------------------
 */
// -- 列表编辑 ?act=list_edit
/**
 * ------------------------------------------------------
 */
if ($_REQUEST['act'] == 'list_edit') {
	/**
	 * 检查权限
	 */
	admin_priv('mweb_config');

	$sql = "SELECT * "." FROM " .$GLOBALS['ecs']->table("moban") ." WHERE moban_id=".$_SESSION['moban_id']."  ";
	$item = $GLOBALS['db'] -> GetRow($sql);
	$item['web_name']=trim($item['web_name'."".$GLOBALS['select_lang'].""]);
	$item['title'] = trim($item['title'."".$GLOBALS['select_lang'].""]);
	$item['keywords'] = trim($item['keywords'."".$GLOBALS['select_lang'].""]);
	$item['description'] = trim($item['description'."".$GLOBALS['select_lang'].""]);
	$item['stats_code'] = trim($item['stats_code'."".$GLOBALS['select_lang'].""]);
    $GLOBALS['smarty'] -> assign('item', $item);
    $GLOBALS['smarty'] -> assign('form_action', 'update');
	assign_query_info();
	$GLOBALS['smarty'] -> display('web_config.htm');
} 
if ($_REQUEST['act'] == 'update') {
	admin_priv('mopen_config');

	$arr = array();
			$arr['web_name'."".$GLOBALS['select_lang'].""] = trim($_POST['web_name']);
			$arr['title'."".$GLOBALS['select_lang'].""] = trim($_POST['title']);
			$arr['keywords'."".$GLOBALS['select_lang'].""] = trim($_POST['keywords']);
			$arr['description'."".$GLOBALS['select_lang'].""] = trim($_POST['description']);
			$arr['stats_code'."".$GLOBALS['select_lang'].""] = trim($_POST['stats_code']);
			$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table("moban"), $arr, 'UPDATE', " moban_id=".$_SESSION['moban_id']." ");
			$link = array();
			$link[0] = array('href' => 'web_config.php?act=list_edit', 'text' => '编辑成功');
			sys_msg('编辑成功', 0, $link);

} 
 

?>