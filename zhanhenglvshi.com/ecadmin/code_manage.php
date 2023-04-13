<?php


define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ($_REQUEST['act'] == 'list')
{
	admin_priv('mcode_list');
    /* 取得过滤条件 */
    $filter = array();
    $smarty->assign('ur_here',      '渠道管理');
    $smarty->assign('action_link',  array('text' => '渠道添加', 'href' => 'code_manage.php?act=add'));
    $smarty->assign('full_page',    1);
    $smarty->assign('filter',       $filter);

    $code_list = get_codelist();

    $smarty->assign('code_list',    $code_list['arr']);
    $smarty->assign('filter',          $code_list['filter']);
    $smarty->assign('record_count',    $code_list['record_count']);
    $smarty->assign('page_count',      $code_list['page_count']);

    assign_query_info();
    $smarty->display('code_list.htm');
}

/*------------------------------------------------------ */
//-- 翻页，排序
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'query')
{
    
    $code_list = get_codelist();

    $smarty->assign('code_list',    $code_list['arr']);
    $smarty->assign('filter',          $code_list['filter']);
    $smarty->assign('record_count',    $code_list['record_count']);
    $smarty->assign('page_count',      $code_list['page_count']);

    make_json_result($smarty->fetch('code_list.htm'), '',
        array('filter' => $code_list['filter'], 'page_count' => $code_list['page_count']));
}


if ($_REQUEST['act'] == 'add')
{
    /* 权限判断 */
    admin_priv('mcode_add');

    $smarty->assign('ur_here',     '添加渠道');
    $smarty->assign('action_link', array('text' => '渠道列表', 'href' => 'code_manage.php?act=list'));
    $smarty->assign('form_action', 'insert');

    assign_query_info();
    $smarty->display('code_info.htm');
}

if ($_REQUEST['act'] == 'insert')
{
    /* 权限判断 */
    admin_priv('mcode_add');

    /*插入数据*/
	$arr = array();
    $arr['add_time'] = gmtime();
	$arr['title'] = !empty($_POST['title']) ? $_POST['title'] : '';
    $db -> autoExecute($ecs -> table('code'), $arr, 'INSERT');

    
    $link = array();
    $link[0]['text'] = '继续添加渠道';
	$link[0]['href'] = 'code_manage.php?act=add';
    $link[1]['text'] = '返回渠道列表';
    $link[1]['href'] = 'code_manage.php?act=list';


    clear_cache_files(); 

    sys_msg('渠道添加成功',0, $link);
}

if ($_REQUEST['act'] == 'edit')
{
    /* 权限判断 */
    admin_priv('mcode_edit');

	$sql = "SELECT * FROM " . $ecs -> table('code') . " WHERE code_id='$_REQUEST[id]'";
	$code = $db -> GetRow($sql);
	$smarty -> assign('code', $code);

    $smarty->assign('ur_here',     '编辑渠道');
    $smarty->assign('action_link', array('text' => '渠道列表', 'href' => 'code_manage.php?act=list'));
    $smarty->assign('form_action', 'update');

    assign_query_info();
    $smarty->display('code_info.htm');
}

if ($_REQUEST['act'] =='update')
{
    /* 权限判断 */
    admin_priv('mcode_edit');
	$code_id=intval($_REQUEST['id']);
	$arr = array();
	$arr['title'] = !empty($_POST['title']) ? $_POST['title'] : '';
    $db -> autoExecute($ecs -> table('code'), $arr, 'UPDATE', "code_id = '$code_id'");
	$link = array();
    $link[0]['text'] = '返回渠道列表';
    $link[0]['href'] = 'code_manage.php?act=list';
    clear_cache_files(); 
    sys_msg('渠道修改成功',0, $link);

}

if ($_REQUEST['act'] == 'batch') {
	admin_priv('mcode_remove');
	$code_id = !empty($_REQUEST['id']) ? join(',', $_REQUEST['id']) : 0;
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('code') . " WHERE code_id " . db_create_in($code_id);
	$GLOBALS['db'] -> query($sql);

	clear_cache_files();
	make_json_result($smarty -> fetch('code_list.htm'));
} 

elseif ($_REQUEST['act'] == 'remove')
{
    admin_priv('mcode_remove');
    $code_id = intval($_REQUEST['id']);
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('code') . " WHERE code_id " . db_create_in($code_id);
	$GLOBALS['db'] -> query($sql);
	$url = 'code_manage.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
}


function get_codelist()
{
    $result = get_filter();
    if ($result === false)
    {
        $filter = array();
        $filter['keyword']    = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
        if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1)
        {
            $filter['keyword'] = json_str_iconv($filter['keyword']);
        }
        $filter['cat_id'] = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
        $filter['sort_by']    = empty($_REQUEST['sort_by']) ? 'a.code_id' : trim($_REQUEST['sort_by']);
        $filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

        $where = '';
        if (!empty($filter['keyword']))
        {
            $where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
        }
     
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('code'). ' AS a '.
               'WHERE 1 ' .$where;
        $filter['record_count'] = $GLOBALS['db']->getOne($sql);

        $filter = page_and_size($filter);

        $sql = 'SELECT a.*  '.
               'FROM ' .$GLOBALS['ecs']->table('code'). ' AS a '.
               'WHERE 1 ' .$where. ' ORDER by '.$filter['sort_by'].' '.$filter['sort_order'];

        $filter['keyword'] = stripslashes($filter['keyword']);
        set_filter($filter, $sql);
    }
    else
    {
        $sql    = $result['sql'];
        $filter = $result['filter'];
    }
    $arr = array();
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);



    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
        $rows['date'] = local_date($GLOBALS['_CFG']['time_format'], $rows['add_time']);



  $rows['ccount']= $GLOBALS['db']->getOne("select count(*) from ". $GLOBALS['ecs']->table('comment') ." where mcode='$rows[code_id]' "); //by czneng




        $arr[] = $rows;
    }
    return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}





?>