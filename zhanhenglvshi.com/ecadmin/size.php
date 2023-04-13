<?php
define('IN_ECS', true);

require(dirname(__FILE__) .'/includes/init.php');



if ($_REQUEST['act'] == 'main') {
	/**
	 * 检查权限
	 */
	admin_priv('msize');

	$sql = "SELECT * FROM " . $ecs -> table('size') . " WHERE id=1";
	$size = $db -> GetRow($sql);

    $smarty -> assign('size', $size);
	assign_query_info();
	$smarty -> display('size.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 提交   ?act=post
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'post') {
	admin_priv('msize');
    $arr = array();
	$arr=$_POST; 
    $db -> autoExecute($ecs -> table('size'), $arr, 'UPDATE', "id = 1");
	clear_cache_files();
	$link = array();
	$link[0] = array('href' => 'size.php?act=main', 'text' => '编辑成功');
	sys_msg('编辑成功', 0, $link);
}





?>