<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');

/**
 * act操作项的初始化
 */
if (empty($_REQUEST['act'])) {
	$_REQUEST['act'] = 'login';
} else {
	$_REQUEST['act'] = trim($_REQUEST['act']);
} 

/**
 * 初始化 $exc 对象
 */


/**
 * ------------------------------------------------------
 */
// -- 退出登录
/**
 * ------------------------------------------------------
 */
if ($_REQUEST['act'] == 'logout') {
	/**
	 * 清除cookie
	 */
	//$db -> query("UPDATE " . $ecs -> table('admin_user') . " SET loginssid='' " . " WHERE user_id='$_SESSION[admin_id]' ");
    $_SESSION=array();
	setcookie('loginssid','', 1);
	$_REQUEST['act'] = 'login';
} 

/**
 * ------------------------------------------------------
 */
// -- 登陆界面
/**
 * ------------------------------------------------------
 */
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
// -- 验证登陆信息
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'signin') {
	if (!empty($_SESSION['captcha_word']) && (intval($_CFG['captcha']) &CAPTCHA_ADMIN)) {
		include_once(ROOT_PATH . 'include/cls_captcha.php');

		/**
		 * 检查验证码是否正确
		 */
		$validator = new captcha();
		if (!empty($_POST['captcha']) && !$validator -> check_word($_POST['captcha'])) {
			sys_msg($_LANG['captcha_error'], 1);
		} 
	} 

	$_POST['username'] = isset($_POST['username']) ? trim($_POST['username']) : '';
	$_POST['password'] = isset($_POST['password']) ? trim($_POST['password']) : '';


    

	$sql = "SELECT `ec_salt` FROM " . $ecs -> table('admin_user') . "WHERE user_name = '" . $_POST['username'] . "'";
	$ec_salt = $db -> getOne($sql);
	if (!empty($ec_salt)) {
		/**
		 * 检查密码是否正确
		 */
		$sql = "SELECT user_id, user_name, password, last_login, action_list, last_login,suppliers_id,ec_salt,city,role_id,articlecatroles,adcatroles" . " FROM " . $ecs -> table('admin_user') . " WHERE user_name = '" . $_POST['username'] . "' AND password = '" . md5(md5($_POST['password']) . $ec_salt) . "'";
	} else {
		/**
		 * 检查密码是否正确
		 */
		$sql = "SELECT user_id, user_name, password, last_login, action_list, last_login,suppliers_id,ec_salt,city,role_id,articlecatroles,adcatroles" . " FROM " . $ecs -> table('admin_user') . " WHERE user_name = '" . $_POST['username'] . "' AND password = '" . md5($_POST['password']) . "'";
	} 
	$row = $db -> getRow($sql);
	if ($row) {
		$row['action_list']='all';
		// 登录成功
		set_admin_session($row['user_id'], $row['user_name'], $row['action_list'], $row['city'], $row['role_id'], $row['articlecatroles'], $row['adcatroles'], $row['last_login']);
		$_SESSION['suppliers_id'] = $row['suppliers_id'];
		
	    $moban = $db -> getOne("SELECT moban_id " . " FROM " . $ecs -> table('moban') . " WHERE moban_id = '" . $row['user_id'] . "' ");
	    if($moban)
		{
		$_SESSION['moban_id']=$_SESSION['admin_id'];
		}

		if ($row['action_list'] == 'all' && empty($row['last_login'])) {
			$_SESSION['shop_guide'] = true;
		} 

		$last_ip=$sess->get_client_ip();
		// 更新最后登录时间和IP
		$db -> query("UPDATE " . $ecs -> table('admin_user') . " SET last_login='" . gmtime() . "', last_ip='" . $last_ip . "'" . " WHERE user_id='$_SESSION[admin_id]' ");

		//if (isset($_POST['remember'])) {
		$loginssid=md5($_SESSION['admin_id'].md5(time()));//作为唯一值存入记录
		setcookie('loginssid','',1);
        setcookie('loginssid',$loginssid,time()+7*24*3600);
		$db -> query("UPDATE " . $ecs -> table('admin_user') . " SET loginssid='" . $loginssid . "' " . " WHERE user_id='$_SESSION[admin_id]' ");


		//} 

		ecs_header("Location: ./index.php\n");

		exit;
	} else {
		sys_msg('登录失败，账号或密码错误！', 1);
	} 
} 

/**
 * ------------------------------------------------------
 */
// -- 管理员列表页面
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'list') {
	admin_priv('madmin_list'); 
	/**
	 * 模板赋值
	 */
	$smarty -> assign('ur_here', '管理员列表');
	$smarty -> assign('action_link', array('href' => 'privilege.php?act=add', 'text' => '添加管理员'));
	$smarty -> assign('full_page', 1);
	$smarty -> assign('admin_list', get_admin_userlist());

	/**
	 * 显示页面
	 */
	assign_query_info();
	$smarty -> display('privilege_list.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 查询
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'query') {
	admin_priv('madmin_list'); 
	$smarty -> assign('admin_list', get_admin_userlist());

	make_json_result($smarty -> fetch('privilege_list.htm'));
} 

/**
 * ------------------------------------------------------
 */
// -- 添加管理员页面
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'add') {
	admin_priv('madmin_add'); 
	$smarty -> assign('ur_here', '添加管理员');
	$smarty -> assign('action_link', array('href' => 'privilege.php?act=list', 'text' => '管理员列表'));
	$smarty -> assign('form_act', 'insert');
	$smarty -> assign('action', 'add');
	assign_query_info();
	$smarty -> display('privilege_info.htm');
} 
elseif ($_REQUEST['act'] == 'insert') {
		admin_priv('madmin_add'); 
   /*检查分类名是否重复*/
    $is_only = is_only('user_name', $_POST['user_name']);
    if (!$is_only)
    {
        sys_msg(sprintf('已经存在管理员', stripslashes($_POST['user_name'])), 1);
    }
	/**
	 * 获取添加日期及密码
	 */
	$add_time = gmtime();

	$password = md5($_POST['password']);
    $role_id=intval($_POST['select_role']);

	if (!empty($_POST['select_role'])) {
		$sql = "SELECT * FROM " . $ecs -> table('role') . " WHERE role_id = '" . $role_id . "'";
		$row = $db -> getRow($sql);
		$action_list = $row['action_list'];
	} 
    
	$arr = array();
	$arr['user_name']=trim($_POST['user_name']);
	$arr['role_id'] = $role_id;
	$arr['action_list'] = $action_list;
	$arr['password'] = $password;
	$arr['add_time'] = $add_time;
    $db -> autoExecute($ecs -> table('admin_user'), $arr, 'INSERT');
	$new_id = $db -> Insert_ID();
	clear_cache_files();
	$link = array();
	$link[0]['text'] = '返回管理员列表';
	$link[0]['href'] = 'privilege.php?act=list';
	$note = sprintf(stripslashes('管理员列表' . $_POST['cat_name'] . '管理员成功'));
	sys_msg($note, 0, $link);	
} 

/**
 * ------------------------------------------------------
 */
// -- 编辑管理员信息
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'edit') {
	admin_priv('madmin_edit'); 
	$id = !empty($_GET['id']) ? intval($_GET['id']) : 0;

   

	if ($_SESSION['admin_id'] != $id) {
		admin_priv('admin_add');
	} 

	/**
	 * 获取管理员信息
	 */
	$sql = "SELECT * FROM " . $ecs -> table('admin_user') . " WHERE user_id = '" . $id . "'";
	$user_info = $db -> getRow($sql);
	$smarty -> assign('user', $user_info);

	/**
	 * 获得该管理员的权限
	 */
	$priv_str = $db -> getOne("SELECT action_list FROM " . $ecs -> table('admin_user') . " WHERE user_id = '$_GET[id]'");

	
	$smarty -> assign('form_act', 'update');
	$smarty -> assign('action', 'edit');
	assign_query_info();
	$smarty -> display('privilege_info.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 更新管理员信息
/**
 * ------------------------------------------------------
 */
elseif ($_REQUEST['act'] == 'update') {
	/**
	 * 变量初始化
	 */
	 admin_priv('madmin_edit'); 
	$id = !empty($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
	$role_id = !empty($_REQUEST['select_role']) ? intval($_REQUEST['select_role']) : 0;
	
    if ($_SESSION['admin_id'] != $_REQUEST['id']) {
			admin_priv('admin_add');
	}

   if (!empty($_POST['select_role'])) {
		$sql = "SELECT * FROM " . $ecs -> table('role') . " WHERE role_id = '" . $role_id . "'";
		$row = $db -> getRow($sql);
		$action_list = $row['action_list'];
	} 
    
	$arr = array();
	$arr['user_name']=trim($_POST['user_name']);
	$arr['role_id'] = $role_id;
	if($id=='1')
	{
	$arr['action_list'] = 'all';
	}else
	{
	$arr['action_list'] = $action_list;
	}
	
	$arr['add_time'] = $add_time;
	/**
	* 比较新密码和确认密码是否相同
	*/
	 
    if(!empty($_POST['new_password']))
	{
    if ($_POST['new_password'] <> $_POST['pwd_confirm']) {
	$link[] = array('text' => '返回', 'href' => 'javascript:history.back(-1)');
	sys_msg('密码不匹配', 0, $link);
	}
	//$ec_salt = rand(1, 9999);
	$arr['password'] = md5($_POST['new_password']);
	}


    $db -> autoExecute($ecs -> table('admin_user'), $arr, 'UPDATE', "user_id = '$id'");



    if($id=='1')
	{
	//修改密码后
	if(!empty($_POST['new_password'])&&$_POST['new_password']==$_POST['pwd_confirm'])
	{
	$sess -> delete_spec_admin_session($_SESSION['admin_id']);
	}

	}else
	{

	//修改密码后
	if(!empty($_POST['new_password'])&&$_POST['new_password']==$_POST['pwd_confirm'])
	{
	}

	}


	clear_cache_files();
	$link = array();
	$link[0]['text'] = '返回管理员列表';
	$link[0]['href'] = 'privilege.php?act=list';
	$note = sprintf(stripslashes('管理员列表' . $_POST['cat_name'] . '修改成功'));
	sys_msg($note, 0, $link);


	
	
	
} 

 


elseif ($_REQUEST['act'] == 'remove') {
admin_priv('madmin_remove'); 
	$user_id = intval($_REQUEST['id']);


	/**
	 * ID为1的不允许删除
	 */
	if ($user_id == 1) {
		make_json_error('不可删除！');
	} 

	/**
	 * 管理员不能删除自己
	 */
	if ($user_id == $_SESSION['admin_id']) {
		make_json_error('不可删除自己！');
	} 
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('admin_user') . " WHERE user_id " . db_create_in($user_id);
	$GLOBALS['db'] -> query($sql);
	$url = 'privilege.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
} 

/**
 * 获取管理员列表
 */
function get_admin_userlist() {
	$list = array();
	$sql = 'SELECT user_id, user_name, email, add_time, last_login ' . 'FROM ' . $GLOBALS['ecs'] -> table('admin_user') . ' ORDER BY user_id DESC';
	$list = $GLOBALS['db'] -> getAll($sql);

	foreach ($list AS $key => $val) {
		$list[$key]['add_time'] = local_date($GLOBALS['_CFG']['time_format'], $val['add_time']);
		$list[$key]['last_login'] = local_date($GLOBALS['_CFG']['time_format'], $val['last_login']);
	} 

	return $list;
} 

/**
 * 清除购物车中过期的数据
 */
function clear_cart() {
	/**
	 * 取得有效的session
	 */
	$sql = "SELECT DISTINCT session_id " . "FROM " . $GLOBALS['ecs'] -> table('cart') . " AS c, " . $GLOBALS['ecs'] -> table('sessions') . " AS s " . "WHERE c.session_id = s.sesskey ";
	$valid_sess = $GLOBALS['db'] -> getCol($sql); 
	// 删除cart中无效的数据
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('cart') . " WHERE add_time < " . ($time-86400 * 30) . " AND session_id NOT " . db_create_in($valid_sess);
	$GLOBALS['db'] -> query($sql);
} 

/**
 * 获取角色列表
 */
function get_role_list() {
	$list = array();
	$sql = 'SELECT role_id, role_name, action_list ' . 'FROM ' . $GLOBALS['ecs'] -> table('role');
	$list = $GLOBALS['db'] -> getAll($sql);
	return $list;
} 

function is_only($col, $name, $id = 0, $where='')
    {
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('admin_user'). " WHERE user_name = '$name'";
            $sql .= empty($id) ? '' : " AND user_id  <> '$id' ";
        $sql .= empty($where) ? '' : ' AND ' .$where;

        return ($GLOBALS['db']->getOne($sql) == 0);
    }
?>
