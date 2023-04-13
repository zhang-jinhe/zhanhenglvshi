<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');


$_REQUEST['act'] = trim($_REQUEST['act']);
if (empty($_REQUEST['act']))
{
    $_REQUEST['act'] = 'list';
}


/*------------------------------------------------------ */
//-- 分类列表
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'list')
{
	admin_priv('mflink_list');
    $smarty->assign('full_page',   1);
    $link = get_link_list();
	$smarty -> assign('link_list', $link['link']);
	$smarty->assign('filter', $link['filter']);
    $smarty->assign('record_count', $link['record_count']);
    $smarty->assign('page_count', $link['page_count']);

    assign_query_info();
    $smarty->display('link_list.htm');

}

/*------------------------------------------------------ */
//-- 查询
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'query')
{
	 	admin_priv('mflink_list');
	$link = get_link_list();
    $smarty->assign('link_list', $link['link']);
    $smarty->assign('filter', $link['filter']);
    $smarty->assign('record_count', $link['record_count']);
    $smarty->assign('page_count', $link['page_count']);
    $sort_flag = sort_flag($link['filter']);
    make_json_result($smarty->fetch('link_list.htm') , '', array(
        'filter' => $link['filter'],
        'page_count' => $link['page_count']
    ));
}

/*------------------------------------------------------ */
//-- 添加分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    	admin_priv('mflink_list');
	$link = array();
	$link['is_show'] = 1;
	$link['show_order'] = 50;
	$smarty -> assign('link', $link);
    $smarty->assign('form_action', 'insert');
    assign_query_info();
    $smarty->display('link_info.htm');
}
/*------------------------------------------------------ */
//-- 添加分类执行
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert') {
		admin_priv('mflink_list');
	$arr = array();
	$arr['link_name'] = !empty($_POST['link_name']) ? $_POST['link_name'] : '';
	$arr['is_show']=intval($_POST['is_show']);
	$arr['link_url'] = $_POST['link_url'];
	$arr['show_order'] =intval($_POST['show_order']);
	$db -> autoExecute($ecs -> table('friend_link'), $arr, 'INSERT');
	$link = array();
	$link[0]['text'] = '继续添加友情链接';
	$link[0]['href'] = 'friend_link.php?act=add';
	$link[1]['text'] = '返回友情链接列表';
	$link[1]['href'] = 'friend_link.php?act=list';
	clear_cache_files(); // 清除相关的缓存文件
	sys_msg('友情链接添加成功', 0, $link);
} 
/*------------------------------------------------------ */
//-- 编辑友情链接
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    	admin_priv('mflink_list');
	$id=intval($_REQUEST['id']);
    $sql = "SELECT * FROM ". $ecs->table('friend_link'). " WHERE link_id='$id'";
    $link = $db->GetRow($sql);
	$smarty->assign('link', $link);
    $smarty->assign('form_action', 'update');
    assign_query_info();
    $smarty->display('link_info.htm');
}
elseif ($_REQUEST['act'] == 'update')
{
    	admin_priv('mflink_list');
    $id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	$arr = array();
	$arr['link_name'] = !empty($_POST['link_name']) ? $_POST['link_name'] : '';
	$arr['is_show']=intval($_POST['is_show']);
	$arr['link_url'] = $_POST['link_url'];
	$arr['show_order'] =intval($_POST['show_order']);
	$db -> autoExecute($ecs -> table('friend_link'), $arr, 'UPDATE', "link_id = '$id'");
	clear_cache_files();
	$link = array();
	$link[0]['text'] = '返回友情链接列表';
	$link[0]['href'] = 'friend_link.php?act=list';
	$note = sprintf(stripslashes('友情链接' . $_POST['link_name'] . '修改成功'));
	sys_msg($note, 0, $link);

}

elseif ($_REQUEST['act'] == 'is_show') {
		admin_priv('mflink_list');
	$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('friend_link') . " WHERE link_id='$id'";
	$val = $db -> GetRow($sql);
	if ($val['is_show'] == 1) {
		$arr['is_show'] = 0;
	} 
	if ($val['is_show'] == 0) {
		$arr['is_show'] = 1;
	} 
	$db -> autoExecute($GLOBALS['ecs'] -> table('friend_link'), $arr, 'UPDATE', "link_id='$id'");
	$GLOBALS['db'] -> query($sql);
	make_json_result($smarty -> fetch('link_list.htm'));
} 

elseif ($_REQUEST['act'] == 'edit_show_order') {
	check_authz_json('mflink_list');
	$id = intval($_POST['id']);
	$val = intval($_POST['show_order']);
	$arr['show_order']=$val;
    $db -> autoExecute($GLOBALS['ecs'] -> table('friend_link'), $arr, 'UPDATE', "link_id='$id'");
	$GLOBALS['db'] -> query($sql);
    make_json_result($val);

}

if ($_REQUEST['act'] == 'remove') {
admin_priv('mflink_list');
	$id = intval($_REQUEST['id']);
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('friend_link') . " WHERE link_id " . db_create_in($id);
	$GLOBALS['db'] -> query($sql);
	clear_cache_files();
	$url = 'friend_link.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
} 



function get_link_list()
{
    $filter = array();

    /* 记录总数以及页数 */
    $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('friend_link');
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 查询数据 */
    $arr = array();
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('friend_link'). ' ORDER BY show_order ASC,link_id DESC';
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
      
        $arr[] = $rows;
    }

    return array('link' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}
?>
