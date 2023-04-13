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
	admin_priv('mnavigator_list');
    $smarty->assign('full_page',   1);
    $navigator = get_navigator_list();
	$smarty -> assign('navigator', $navigator['nav']);
	$smarty->assign('filter', $navigator['filter']);
    $smarty->assign('record_count', $navigator['record_count']);
    $smarty->assign('page_count', $navigator['page_count']);

    assign_query_info();
    $smarty->display('navigator_list.htm');

}

/*------------------------------------------------------ */
//-- 查询
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'query')
{
	admin_priv('mnavigator_list');
	$navigator = get_navigator_list();
    $smarty->assign('navigator', $navigator['nav']);
    $smarty->assign('filter', $navigator['filter']);
    $smarty->assign('record_count', $navigator['record_count']);
    $smarty->assign('page_count', $navigator['page_count']);
    $sort_flag = sort_flag($navigator['filter']);
    $smarty->assign($sort_flag['tag'], $sort_flag['img']);
    make_json_result($smarty->fetch('navigator_list.htm') , '', array(
        'filter' => $navigator['filter'],
        'page_count' => $navigator['page_count']
    ));
}

/*------------------------------------------------------ */
//-- 添加分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
    admin_priv('mnavigator_add');
	$rt = array();
	$rt['ifshow'] = 1;
	$rt['ifxiala'] = 1;
	$sysmain = get_sysnav();
    $smarty->assign('sysmain',$sysmain);
	$smarty -> assign('rt', $rt);
    $smarty->assign('form_action', 'insert');
    assign_query_info();
    $smarty->display('navigator_add.htm');
}
/*------------------------------------------------------ */
//-- 添加分类执行
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert') {
	 admin_priv('mnavigator_add');
	$arr = array();
	$arr = $_POST;
	$arr['cid']=$_POST['menulist'];
	$db -> autoExecute($ecs -> table('nav'), $arr, 'INSERT');
	$link = array();
	$link[0]['text'] = '继续添加导航菜单';
	$link[0]['href'] = 'navigator.php?act=add';
	$link[1]['text'] = '返回导航菜单列表';
	$link[1]['href'] = 'navigator.php?act=list';
	clear_cache_files(); // 清除相关的缓存文件
	sys_msg('导航菜单添加成功', 0, $link);
} 
/*------------------------------------------------------ */
//-- 编辑导航菜单
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'edit')
{
 admin_priv('mnavigator_edit');
	$id=intval($_REQUEST['id']);
    $sql = "SELECT * FROM ". $ecs->table('nav'). " WHERE id='$id'";
    $rt = $db->GetRow($sql);
	$sysmain = get_sysnav();
    $smarty->assign('sysmain',$sysmain);
	$smarty->assign('rt', $rt);
    $smarty->assign('form_action', 'update');
    assign_query_info();
    $smarty->display('navigator_add.htm');
}
if ($_REQUEST['act'] == 'update')
{
    admin_priv('mnavigator_edit');
    $id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	$arr = array();
	$arr = $_POST;
    $arr['cid']=$_POST['menulist'];



	$db -> autoExecute($ecs -> table('nav'), $arr, 'UPDATE', "id = '$id'");
	clear_cache_files();
	$link = array();
	$link[0]['text'] = '返回导航菜单列表';
	$link[0]['href'] = 'navigator.php?act=list';
	$note = sprintf(stripslashes('导航菜单' . $_POST['cat_name'] . '修改成功'));
	sys_msg($note, 0, $link);

}
if ($_REQUEST['act'] == 'searcharticle') {
	$res    = array('err_msg' => 'error', 'err_code' => '0');
	$arr=array();
    $keywords =trim($_REQUEST['keywords']); 
	$zhiduan = trim($_REQUEST['zhiduan']);
	$cat_id = trim($_REQUEST['cat_id']);
	$type = trim($_REQUEST['type']);		
	if ($type == 'article') {
		$where = !empty($keywords)?
		" WHERE " . $zhiduan . " LIKE '%" . $keywords . "%'  " : ' WHERE 1';
		$where .= ' and  a.article_id >0 ';
		if(!empty($cat_id))
		{
		 $where .= " AND a." . get_article_children($cat_id);
		}
       
		$sql = "SELECT article_id, " . $zhiduan . " " . 'FROM ' . $GLOBALS['ecs'] -> table('article') . ' AS a ' . $where .' LIMIT 10';
        $sql = "SELECT a.article_id,a.".$zhiduan.", ac.cat_name " . 'FROM ' . $GLOBALS['ecs'] -> table('article') . ' AS a ' . 'LEFT JOIN ' . $GLOBALS['ecs'] -> table('article_cat') . ' AS ac ON ac.cat_id = a.cat_id '  .  $where .' LIMIT 10';
		
	} 
	$arr = $GLOBALS['db'] -> getAll($sql);
	$res = array('error' => "0", 'message' => '', 'content' => $arr, 'type' => $type, 'zhiduan' => $zhiduan, 'cat_id' => $cat_id);
	echo json_encode($res);	
} 

if ($_REQUEST['act'] == 'is_show') {
	 admin_priv('mnavigator_edit');
	$id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('nav') . " WHERE id='$id'";
	$val = $db -> GetRow($sql);
	if ($val['ifshow'] == 1) {
		$arr['ifshow'] = 0;
	} 
	if ($val['ifshow'] == 0) {
		$arr['ifshow'] = 1;
	} 
	$db -> autoExecute($GLOBALS['ecs'] -> table('nav'), $arr, 'UPDATE', "id='$id'");
	$GLOBALS['db'] -> query($sql);
	make_json_result($smarty -> fetch('navigator_list.htm'));
} 

if ($_REQUEST['act'] == 'edit_sort_order') {

	check_authz_json('mnavigator_edit');
	$id = intval($_POST['id']);
	$val = intval($_POST['sort_order']);
	$arr['vieworder']=$val;
    $db -> autoExecute($GLOBALS['ecs'] -> table('nav'), $arr, 'UPDATE', "id='$id'");
	$GLOBALS['db'] -> query($sql);
    make_json_result($val);

	
}

if($_REQUEST['act'] == 'remove') {
		 admin_priv('mnavigator_remove');
	$id = intval($_REQUEST['id']);
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('nav') . " WHERE id " . db_create_in($id);
	$GLOBALS['db'] -> query($sql);
	
	$url = 'navigator.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
} 



function get_navigator_list()
{
    $filter = array();

    /* 记录总数以及页数 */
    $sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs']->table('nav');
    $filter['record_count'] = $GLOBALS['db']->getOne($sql);

    $filter = page_and_size($filter);

    /* 查询数据 */
    $arr = array();
	if ($GLOBALS['_OPE']['article_cat'])
	{
    $ope_sql=" where  cid <>".$GLOBALS['_OPE']['article_cat'];
    }
    $sql = 'SELECT * FROM ' .$GLOBALS['ecs']->table('nav'). $ope_sql.' ORDER BY type DESC,  vieworder asc';
	
    $res = $GLOBALS['db']->selectLimit($sql, $filter['page_size'], $filter['start']);
    while ($rows = $GLOBALS['db']->fetchRow($res))
    {
      
        $arr[] = $rows;
    }

    return array('nav' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
}

function get_sysnav()
{
    global $_LANG;
    $sysmain = array();
    $catlist = get_articlecat_openlist(0, 0, false);
    foreach($catlist as $key => $val)
    {
        $val['view_name'] = $val['cat_name'];
		$val['url'] = $val['url'];
		$val['durl'] = $val['durl'];
		$val['type'] = $val['type'];
		$val['cat_id'] = $val['cat_id'];
        for($i=0;$i<$val['level'];$i++)
        {
            $val['view_name'] = '&nbsp;&nbsp;&nbsp;&nbsp;' . $val['view_name'];
        }
		if($GLOBALS['_OPE']['open_lang'])
		{
		$val['cat_name'.$GLOBALS['_OPE']['select_lang']] = $val['cat_name'.$GLOBALS['_OPE']['select_lang']];
         }

        $sysmain[] = $val;

		
			
		
		
		
    }
	


    return $sysmain;
}
?>
