<?php


if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}



define('ECS_ADMIN', true);

error_reporting(E_ALL);
error_reporting(0); //
if (__FILE__ == '')
{
    die('Fatal error code: 0');
}

/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  0);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

if (DIRECTORY_SEPARATOR == '\\')
{
    @ini_set('include_path',      '.;' . ROOT_PATH);
}
else
{
    @ini_set('include_path',      '.:' . ROOT_PATH);
}
ob_start();
if (file_exists('../data/config.php'))
{
    include('../data/config.php');
}
else
{
    include('../includes/config.php');
}

/* 取得当前所在的根目录 */
if(!defined('ADMIN_PATH'))
{
    define('ADMIN_PATH','admin');
}
define('ROOT_PATH', str_replace(ADMIN_PATH . '/includes/init.php', '', str_replace('\\', '/', __FILE__)));
if (defined('DEBUG_MODE') == false)
{
    define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
    date_default_timezone_set($timezone);
}

if (isset($_SERVER['PHP_SELF']))
{
    define('PHP_SELF', $_SERVER['PHP_SELF']);
}
else
{
    define('PHP_SELF', $_SERVER['SCRIPT_NAME']);
}

define('WEB_PATH',substr(ROOT_PATH,0,-7));
require(ROOT_PATH . 'includes/cls_ecsolve.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/cls_web.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_lang.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . ADMIN_PATH . '/includes/lib_main.php');
/* 对用户传入的变量进行转义操作。*/
if (!get_magic_quotes_gpc())
{
    if (!empty($_GET))
    {
        $_GET  = addslashes_deep($_GET);
    }
    if (!empty($_POST))
    {
        $_POST = addslashes_deep($_POST);
    }

    $_COOKIE   = addslashes_deep($_COOKIE);
    $_REQUEST  = addslashes_deep($_REQUEST);
}

/* 对路径进行安全处理 */
if (strpos(PHP_SELF, '.php/') !== false)
{
    ecs_header("Location:" . substr(PHP_SELF, 0, strpos(PHP_SELF, '.php/') + 4) . "\n");
    exit();
}

/* 创建 ecsolve 对象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());

/* 初始化数据库类 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
$db_host = $db_user = $db_pass = $db_name = NULL;

/* 创建错误处理对象 */
$err = new ecs_error('message.htm');
require(ROOT_PATH . 'includes/cls_session.php');
$sess = new cls_session($db, $ecs->table('sessions'));


/* 初始化 action */
if (!isset($_REQUEST['act']))
{
    $_REQUEST['act'] = '';
}
elseif (($_REQUEST['act'] == 'login' || $_REQUEST['act'] == 'logout' || $_REQUEST['act'] == 'signin') &&
    strpos(PHP_SELF, '/privilege.php') === false)
{
    $_REQUEST['act'] = '';
}
elseif (($_REQUEST['act'] == 'forget_pwd' || $_REQUEST['act'] == 'reset_pwd' || $_REQUEST['act'] == 'get_pwd') &&
    strpos(PHP_SELF, '/get_password.php') === false)
{
    $_REQUEST['act'] = '';
}











// TODO : 登录部分准备拿出去做，到时候把以下操作一起挪过去
if ($_REQUEST['act'] == 'captcha')
{
    include(ROOT_PATH . 'includes/cls_captcha.php');

    $img = new captcha('../data/captcha/');
    @ob_end_clean(); //清除之前出现的多余输入
    $img->generate_image();

    exit;
}


require(ROOT_PATH.'lang/lang/admin/common.php');

if (!file_exists('../data/caches'))
{
    @mkdir('../data/caches', 0777);
    @chmod('../data/caches', 0777);
}

if (!file_exists('../data/compiled/admin'))
{
    @mkdir('../data/compiled/admin', 0777);
    @chmod('../data/compiled/admin', 0777);
}

clearstatcache();





/* 创建 Smarty 对象。*/
require(ROOT_PATH . 'includes/cls_template.php');
$smarty = new cls_template;

$smarty->template_dir  = ROOT_PATH . ADMIN_PATH . '/templates';
$smarty->compile_dir   = ROOT_PATH . 'data/compiled/admin';
if ((DEBUG_MODE & 2) == 2)
{
    $smarty->force_compile = true;
}


$smarty->assign('lang', $_LANG);






$weburl = $ecs-> get_domain();
$smarty->assign('ecsolve_path', $weburl.'/'.ADMIN_PATH.'/templates');
$smarty->assign('domain', $ecs->get_domain().'/'.$GLOBALS['select_html']);




if($_OPE['open_lang'])
{

$smarty -> assign('select_lang', $_OPE['select_lang']);

$langurl=str_replace("_","lang",$_OPE['select_lang']);
$smarty -> assign('langurl', str_replace("_","lang",$_OPE['select_lang']));


$more_lang = explode(',', $_OPE['more_lang']);
foreach($more_lang as $key=>$value)
{
$key++;
$langc['_'.$key]=$value;
}
$smarty -> assign('langc', $langc);
}

$mo = new module();




$sql = 'SELECT open_lang,select_lang,more_lang,power_list  FROM ' . $GLOBALS['ecs']->table('moban') . ' ';
$langvs = $GLOBALS['db']->getRow($sql);

if($langvs['power_list']){
$power_list_array=explode(",", $langvs['power_list']);
foreach($power_list_array as $key => $val)
{
$power_list[$val]=1;
}
$smarty->assign('power', $power_list);
}



if($langvs['open_lang'])
{
	
$smarty->assign('langvs', $langvs);
$select_lang=intval($langvs['select_lang'])?'_'.intval($langvs['select_lang']):''; //当前选择的语言，字段后缀
$select_html=intval($langvs['select_lang'])?'lang'.intval($langvs['select_lang']).'.html':''; //当前选择首页html，在设计里有效
$select_cat=intval($langvs['select_lang'])?'lang'.intval($langvs['select_lang']).'-':'/'; //当前选择导航页，在设计里有效
$smarty -> assign('select_lang', $langvs['select_lang']);
$langurl=str_replace("_","lang",$langvs['select_lang']);
$smarty -> assign('langurl', str_replace("_","lang",$langvs['select_lang']));
$more_lang = explode(',', $langvs['more_lang']);
$smarty -> assign('more_lang', $more_lang);
$smarty -> assign('select_html', $select_html);
}


require(ROOT_PATH.ADMIN_PATH.'/languages/zh_cn.php');

   
/* 验证管理员身份 */
if ((!isset($_SESSION['admin_id']) || intval($_SESSION['admin_id']) <= 0) &&
    $_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
    $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order')
{
	$last_ip=$sess->get_client_ip();
    /* session 不存在，检查cookie */
    if (!empty($_COOKIE['loginssid']))
    {
        // 找到了cookie, 验证cookie信息
        $sql = 'SELECT * ' .
                ' FROM ' .$ecs->table('admin_user') .
                " WHERE loginssid = '" . trim($_COOKIE['loginssid']) . "' and last_ip = '" . trim($last_ip) . "'  ";
        $row = $db->GetRow($sql);


        if (!$row)
        {
            // 没有找到这个记录
            setcookie($_COOKIE['loginssid'],   '', 1);

            if (!empty($_REQUEST['is_ajax']))
            {
                make_json_error($_LANG['priv_error']);
            }
            else
            {
                ecs_header("Location: privilege.php?act=login\n");
            }

            exit;
        }
        else
        {

		

		set_admin_session($row['user_id'], $row['user_name'],'all');
	
         
        }
    }
    else
    {
        if (!empty($_REQUEST['is_ajax']))
        {
            make_json_error($_LANG['priv_error']);
        }
        else
        {
            ecs_header("Location: privilege.php?act=login\n");
        }

        exit;
    }
}



if ($_REQUEST['act'] != 'login' && $_REQUEST['act'] != 'signin' &&
    $_REQUEST['act'] != 'forget_pwd' && $_REQUEST['act'] != 'reset_pwd' && $_REQUEST['act'] != 'check_order')
{
    $admin_path = preg_replace('/:\d+/', '', $ecs->url()) . ADMIN_PATH;
    if (!empty($_SERVER['HTTP_REFERER']) &&
        strpos(preg_replace('/:\d+/', '', $_SERVER['HTTP_REFERER']), $admin_path) === false)
    {
        if (!empty($_REQUEST['is_ajax']))
        {
            make_json_error($_LANG['priv_error']);
        }
        else
        {
            ecs_header("Location: privilege.php?act=login\n");
        }

        exit;
    }
}


//header('Cache-control: private');
header('content-type: text/html; charset=' . EC_CHARSET);
header('Expires: Fri, 14 Mar 1980 20:53:00 GMT');
header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
header('Cache-Control: no-cache, must-revalidate');
header('Pragma: no-cache');

if ((DEBUG_MODE & 1) == 1)
{
    error_reporting(E_ALL);
}
else
{
    error_reporting(E_ALL ^ E_NOTICE);
}
if ((DEBUG_MODE & 4) == 4)
{
    include(ROOT_PATH . 'includes/lib.debug.php');
}

/* 判断是否支持gzip模式 */
if (gzip_enabled())
{
    ob_start('ob_gzhandler');
}
else
{
    ob_start();
}



$arrzh = explode(',', $_SESSION["action_list"]);
if (empty($_REQUEST['is_ajax']) ){




    foreach ($modules AS $key => $val)
    {
        $menus[$key]['label'] = $_LANG[$key];
        if (is_array($val))
        {
            foreach ($val AS $k => $v)
            {
                if ( isset($purview[$k]))
                {
                    if (is_array($purview[$k]))
                    {
                        $boole = false;
                        foreach ($purview[$k] as $action)
                        {
                             $boole = $boole || admin_priv($action, '', false);
                        }
                        if (!$boole)
                        {
                            continue;
                        }

                    }
                    else
                    {
                        if (! admin_priv($purview[$k], '', false))
                        {
                            continue;
                        }
                    }
                }
                

				//if($_SESSION["admin_id"]!==$_SESSION['moban_id'])
				//{

				//if(in_array($k, $arrzh)&&$_SESSION["admin_id"]!==$_SESSION['moban_id'])
				//{
				//$menus[$key]['children'][$k]['label']  = $_LANG[$k];
                //$menus[$key]['children'][$k]['action'] = $v;
				//}

				//}
				//else
				//{
				$menus[$key]['children'][$k]['label']  = $_LANG[$k];
                $menus[$key]['children'][$k]['action'] = $v;
				//}
            }
        }
        else
        {
            $menus[$key]['action'] = $val;
        }

        // 如果children的子元素长度为0则删除该组
        if(empty($menus[$key]['children']))
        {
            unset($menus[$key]);
        }

    }

    $smarty->assign('main_menu',     $menus);












    $lst = array();
    $nav = $db->GetOne('SELECT nav_list FROM ' . $ecs->table('admin_user') . " WHERE user_id = '" . $_SESSION['admin_id'] . "'");

    if (!empty($nav))
    {
        $arr = explode(',', $nav);

        foreach ($arr AS $val)
        {
            $tmp = explode('|', $val);
            $lst[$tmp[1]] = $tmp[0];
        }
    }
    $smarty->assign('nav_list', $lst);
}
if( $_REQUEST['act']!='modif'){unset($modules,$menus,$lst,$nav);}
?>
