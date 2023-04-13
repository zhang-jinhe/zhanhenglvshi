<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');


if (empty($_REQUEST['act'])) {
	$_REQUEST['act'] = 'login';
} else {
	$_REQUEST['act'] = trim($_REQUEST['act']);
} 

if ($_REQUEST['act'] == 'logout') {
	/**
	 * 清除cookie
	 */
	setcookie('ECSCP[admin_id]', '', 1);
	setcookie('ECSCP[admin_pass]', '', 1);

	$sess -> destroy_session();

	$_REQUEST['act'] = 'login';
} 

if ($_REQUEST['act'] == 'login') {
	header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");

	if ((intval($_CFG['captcha']) &CAPTCHA_ADMIN) && gd_version() > 0) {
		$smarty -> assign('gd_version', gd_version());
		$smarty -> assign('random', mt_rand());
	} 

	$smarty -> display('login.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 角色列表页面
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'list') {
	admin_priv('mrole_list');
	$smarty -> assign('full_page', 1);
	$smarty -> assign('admin_list', get_role_list());
	assign_query_info();
	$smarty -> display('role_list.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 查询
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'query') {
		admin_priv('mrole_list');
	$smarty -> assign('admin_list', get_role_list());

	make_json_result($smarty -> fetch('role_list.htm'));
} 

/**
 * ------------------------------------------------------
 */
// -- 添加角色页面
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'add') {
		admin_priv('mrole_add');
	$smarty -> assign('form_act', 'insert');
	$smarty -> assign('action', 'add');
	$smarty -> assign('lang', $_LANG);
	$smarty -> assign('priv_arr', $priv_arr);

	/**
	 * 执行权限功能
	 */

	$action_lang = array();
	$action_lang['marticle'] = '信息管理';
	$action_lang['marticle_list'] = '信息列表';
	$action_lang['marticle_add'] = '添加新信息';
	$action_lang['marticle_edit'] = '编辑信息';
	$action_lang['marticle_remove'] = '删除信息';
	$action_lang['marticlecat_list'] = '信息分类';
	$action_lang['marticlecat_add'] = '添加信息分类';
	$action_lang['marticlecat_edit'] = '编辑信息分类';
	$action_lang['marticlecat_remove'] = '删除信息分类';
	$action_lang['mbanner'] = '广告管理';
	$action_lang['mad_position_list'] = '广告位';
	$action_lang['mad_position_add'] = '添加广告位';
	$action_lang['mad_position_edit'] = '编辑广告位';
	$action_lang['mad_position_remove'] = '删除广告位';
	$action_lang['mad_list'] = '广告列表';
	$action_lang['mad_add'] = '添加广告';
	$action_lang['mad_edit'] = '编辑广告';
	$action_lang['mad_remove'] = '删除广告';

	$action_lang['madmin'] = '权限管理';
	$action_lang['madmin_list'] = '管理员列表';
	$action_lang['madmin_add'] = '添加管理员';
	$action_lang['madmin_edit'] = '编辑管理员';
	$action_lang['madmin_remove'] = '删除管理员';
	$action_lang['madmin_role'] = '角色列表';
	$action_lang['madmin_role_add'] = '添加角色';
	$action_lang['madmin_role_edit'] = '编辑角色';
	$action_lang['madmin_role_remove'] = '删除角色';

	$action_lang['msystem'] = '系统设置';
	$action_lang['mweb_config'] = '基本配置';
	$action_lang['mcomment_list'] = '留言管理';
	$action_lang['mcomment_remove'] = '删除留言';
	$action_lang['mmkefu_main'] = '客服管理';
	$action_lang['mnavigator_list'] ='菜单管理';
	$action_lang['mnavigator_add'] = '添加菜单';
	$action_lang['mnavigator_edit'] = '编辑菜单';
	$action_lang['mnavigator_remove'] = '删除菜单';
	$action_lang['mcode_list'] ='渠道管理';
	$action_lang['mcode_add'] = '添加渠道';
	$action_lang['mcode_edit'] = '编辑渠道';
	$action_lang['mcode_remove'] = '删除渠道';

	$action_lang['mflink_list'] = '友情链接';
    foreach($action_lang as $key =>$value)
	{
     $actionlist[]= $key;
    }

	/**
	 * 全局设置
	 */
	$actionlistroles = explode(',', trim($user_info['action_list']));
	$acitonarray = array();
	foreach ($actionlist as $key => $cat) {
		$acitonarray[$key]['name'] = trim($action_lang[trim($cat)]);
		$acitonarray[$key]['role'] = $cat;
		if (in_array($cat, $actionlistroles)) {
			$acitonarray[$key]['is_open'] = 1;
		} 
	} 
	$smarty -> assign('actionlist', $acitonarray);

	assign_query_info();
	$smarty -> display('role_info.htm');
} 

elseif ($_REQUEST['act'] == 'insert') {
	admin_priv('mrole_add');

	$actionlistroles = @join(",", $_POST['actionlists']);
    $arr = array();
	$arr['role_name'] = trim($_POST['user_name']);
	$arr['action_list'] = $actionlistroles;
	$arr['role_describe'] = trim($_POST['role_describe']);
	$db -> autoExecute($ecs -> table('role'), $arr, 'INSERT');


	$link[0]['text'] = '角色列表';
	$link[0]['href'] = 'role.php?act=list';
	sys_msg('添加' . "&nbsp;" . $_POST['user_name'] . "&nbsp;成功" , 0, $link);
	admin_log($_POST['user_name'], 'add', 'role');
} 

/**
 * ------------------------------------------------------
 */
// -- 编辑角色信息
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'edit') {
	admin_priv('mrole_edit');
	$_REQUEST['id'] = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
	/**
	 * 获得该管理员的权限
	 */
	$priv_str = $db -> getOne("SELECT action_list FROM " . $ecs -> table('role') . " WHERE role_id = '$_GET[id]'");

	/**
	 * 查看是否有权限编辑其他管理员的信息
	 */
	if ($_SESSION['admin_id'] != $_REQUEST['id']) {
		admin_priv('admin_manage');
	} 

	/**
	 * 获取角色信息
	 */
	$sql = "SELECT * FROM " . $ecs -> table('role') . " WHERE role_id = '" . $_REQUEST['id'] . "'";
	$user_info = $db -> getRow($sql);

	/**
	 * 执行权限功能
	 */
	$action_lang = array();
	$action_lang['marticle'] = '信息管理';
	$action_lang['marticle_list'] = '信息列表';
	$action_lang['marticle_add'] = '添加新信息';
	$action_lang['marticle_edit'] = '编辑信息';
	$action_lang['marticle_remove'] = '删除信息';
	$action_lang['marticlecat_list'] = '信息分类';
	$action_lang['marticlecat_add'] = '添加信息分类';
	$action_lang['marticlecat_edit'] = '编辑信息分类';
	$action_lang['marticlecat_remove'] = '删除信息分类';
	$action_lang['mbanner'] = '广告管理';
	$action_lang['mad_position_list'] = '广告位';
	$action_lang['mad_position_add'] = '添加广告位';
	$action_lang['mad_position_edit'] = '编辑广告位';
	$action_lang['mad_position_remove'] = '删除广告位';
	$action_lang['mad_list'] = '广告列表';
	$action_lang['mad_add'] = '添加广告';
	$action_lang['mad_edit'] = '编辑广告';
	$action_lang['mad_remove'] = '删除广告';

	$action_lang['madmin'] = '权限管理';
	$action_lang['madmin_list'] = '管理员列表';
	$action_lang['madmin_add'] = '添加管理员';
	$action_lang['madmin_edit'] = '编辑管理员';
	$action_lang['madmin_remove'] = '删除管理员';
	$action_lang['madmin_role'] = '角色列表';
	$action_lang['madmin_role_add'] = '添加角色';
	$action_lang['madmin_role_edit'] = '编辑角色';
	$action_lang['madmin_role_remove'] = '删除角色';

	$action_lang['msystem'] = '系统设置';
	$action_lang['mweb_config'] = '基本配置';
	$action_lang['mcomment_list'] = '留言管理';
	$action_lang['mcomment_remove'] = '删除留言';
	$action_lang['mmkefu_main'] = '客服管理';
	$action_lang['mnavigator_list'] ='菜单管理';
	$action_lang['mnavigator_add'] = '添加菜单';
	$action_lang['mnavigator_edit'] = '编辑菜单';
	$action_lang['mnavigator_remove'] = '删除菜单';
	$action_lang['mcode_list'] ='渠道管理';
	$action_lang['mcode_add'] = '添加渠道';
	$action_lang['mcode_edit'] = '编辑渠道';
	$action_lang['mcode_remove'] = '删除渠道';

	$action_lang['mflink_list'] = '友情链接';
    foreach($action_lang as $key =>$value)
	{
     $actionlist[]= $key;
    }

	/**
	 * 全局设置
	 */
	$actionlistroles = explode(',', trim($user_info['action_list']));
	$acitonarray = array();
	foreach ($actionlist as $key => $cat) {
		$acitonarray[$key]['name'] = trim($action_lang[trim($cat)]);
		$acitonarray[$key]['role'] = $cat;
		if (in_array($cat, $actionlistroles)) {
			$acitonarray[$key]['is_open'] = 1;
		} 
	} 
	$smarty -> assign('actionlist', $acitonarray);
	/**
	 * 执行权限功能
	 */
	$smarty -> assign('user', $user_info);
	$smarty -> assign('form_act', 'update');
	$smarty -> assign('action', 'edit');
	$smarty -> assign('priv_arr', $priv_arr);
	$smarty -> assign('user_id', $_GET['id']);

	assign_query_info();
	$smarty -> display('role_info.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 更新角色信息
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'update') {
		admin_priv('mrole_edit');
	/**
	 * 获取角色信息
	 */
	$sql = "SELECT *  FROM " . $ecs -> table('role') . " WHERE role_id = '" . intval($_POST['id']) . "'";
	$user_info = $db -> getRow($sql); 

    $actionlistroles = @join(",", $_POST['actionlists']);
    $arr = array();
	$arr['role_name'] = trim($_POST['user_name']);
	$arr['action_list'] = $actionlistroles;
	$arr['role_describe'] = trim($_POST['role_describe']);   
	$db -> autoExecute($ecs -> table('role'), $arr, 'UPDATE', "role_id = '$_POST[id]' ");

	$user_sql = "UPDATE " . $ecs -> table('admin_user') . " SET action_list = '$actionlistroles'  " . "WHERE role_id = '$_POST[id]'";
	$db -> query($user_sql);

	$link[0]['text'] = '角色列表';
	$link[0]['href'] = 'role.php?act=list';
	sys_msg('修改' . "&nbsp;" . $_POST['user_name'] . "&nbsp;成功" , 0, $link);
	admin_log($_POST['user_name'], 'add', 'role');
} 


elseif ($_REQUEST['act'] == 'remove') {
		admin_priv('mrole_remove');
	$role_id = intval($_GET['id']);
	$num_sql = "SELECT count(*) FROM " .$ecs->table('admin_user'). " WHERE role_id = '$_GET[id]'";
    $remove_num = $db->getOne($num_sql);
    if($remove_num > 0)
    {
        make_json_error('该角色下有管理员，请先删除');
    }
    else
    {
        $sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('role') . " WHERE role_id " . db_create_in($role_id);
	$GLOBALS['db'] -> query($sql);
	$url = 'role.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
    }


	
} 

/**
 * 获取角色列表
 */
function get_role_list() {
	$list = array();
	$sql = 'SELECT role_id, role_name, action_list, role_describe ' . 'FROM ' . $GLOBALS['ecs'] -> table('role') . ' ORDER BY role_id DESC';
	$list = $GLOBALS['db'] -> getAll($sql);

	return $list;
} 





?>
