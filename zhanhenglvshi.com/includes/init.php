<?php
if (!defined('IN_ECS'))
{
die('Hacking attempt');
}
define('ROOT_PATH', str_replace('includes/init.php', '', str_replace('\\', '/', __FILE__)));
define('CDN_URL','//www.zndrive.com');
if (!file_exists(ROOT_PATH . 'data/install.lock') && !file_exists(ROOT_PATH . 'includes/install.lock')
&& !defined('NO_CHECK_INSTALL'))
{
header("Location: ./install/start.php\n");

exit;
}

/* 初始化设置 */
@ini_set('memory_limit',          '64M');
@ini_set('session.cache_expire',  180);
@ini_set('session.use_trans_sid', 0);
@ini_set('session.use_cookies',   1);
@ini_set('session.auto_start',    0);
@ini_set('display_errors',        1);

if (DIRECTORY_SEPARATOR == '\\')
{
@ini_set('include_path', '.;' . ROOT_PATH);
}
else
{
@ini_set('include_path', '.:' . ROOT_PATH);
}
require(ROOT_PATH . 'data/config.php');

if (defined('DEBUG_MODE') == false)
{
define('DEBUG_MODE', 0);
}

if (PHP_VERSION >= '5.1' && !empty($timezone))
{
date_default_timezone_set($timezone);
}
$php_self = isset($_SERVER['PHP_SELF']) ? $_SERVER['PHP_SELF'] : $_SERVER['SCRIPT_NAME'];
if ('/' == substr($php_self, -1))
{
$php_self .= 'index.php';
}
define('PHP_SELF', $php_self);
require(ROOT_PATH . 'includes/cls_ecsolve.php');
require(ROOT_PATH . 'includes/cls_error.php');
require(ROOT_PATH . 'includes/cls_web.php');
require(ROOT_PATH . 'includes/lib_time.php');
require(ROOT_PATH . 'includes/lib_base.php');
require(ROOT_PATH . 'includes/lib_lang.php');
require(ROOT_PATH . 'includes/lib_common.php');
require(ROOT_PATH . 'includes/lib_main.php');
require(ROOT_PATH . 'includes/lib_insert.php');
require(ROOT_PATH . 'includes/lib_article.php');

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
/* 创建 ecsolve 对象 */
$ecs = new ECS($db_name, $prefix);
define('DATA_DIR', $ecs->data_dir());
define('IMAGE_DIR', $ecs->image_dir());
/* 初始化数据库类 */
require(ROOT_PATH . 'includes/cls_mysql.php');
$db = new cls_mysql($db_host, $db_user, $db_pass, $db_name);
/* 创建错误处理对象 */
$err = new ecs_error('message.dwt');

/* 载入语言文件 */

$sitelang = empty($_REQUEST['sitelang'])?'lang':@$_REQUEST['sitelang'];

require(ROOT_PATH . 'lang/'.$sitelang.'/common.php');



if(isset($_SERVER['PHP_SELF']))
{
$_SERVER['PHP_SELF']=htmlspecialchars($_SERVER['PHP_SELF']);
}
if (!defined('INIT_NO_SMARTY'))
{
header('Cache-control: private');
header('Content-type: text/html; charset='.EC_CHARSET);
/* 创建 Smarty 对象。*/
require(ROOT_PATH . 'includes/cls_template.php');
$smarty = new cls_template;
$weburl = $ecs-> get_domain();
$smarty->cache_lifetime = 0;
$smarty->template_dir   = ROOT_PATH . 'themes/default';
$smarty->cache_dir      = ROOT_PATH . 'temp/caches';
$smarty->compile_dir    = ROOT_PATH . 'temp/compiled';

if ((DEBUG_MODE & 2) == 2)
{
$smarty->direct_output = true;
$smarty->force_compile = true;
}
else
{
$smarty->direct_output = false;
$smarty->force_compile = false;
}
$smarty->assign('ecs_charset', EC_CHARSET);
$smarty->assign('lang', $_LANG);
$is_mobile=(is_mobile()==true)?1:0;
$smarty -> assign('is_mobile',$is_mobile);
$smarty->assign('ecsolve_path', '/themes/default');
$smarty->assign('sitelang', $sitelang);
$weburl=get_lang(@$_REQUEST['sitelang'])?get_lang(@$_REQUEST['sitelang']).'.html':'';
$smarty->assign('weburl', '/'. $weburl);

}
$mo = new module();
$_CFG = $mo->get_moban();
$smarty->assign('stats_code', $_CFG['stats_code']);
$smarty->assign('_CFG', $_CFG);
if ((DEBUG_MODE & 1) == 1)
{
error_reporting(E_ALL);
}
else
{
error_reporting(E_ALL ^ (E_NOTICE | E_WARNING)); 
}
if ((DEBUG_MODE & 4) == 4)
{
include(ROOT_PATH . 'includes/lib.debug.php');
}


/* 判断是否支持 Gzip 模式 */
if (!defined('INIT_NO_SMARTY') && gzip_enabled())
{
ob_start('ob_gzhandler');
}
else
{
ob_start();
}

$_CFG['is_mobile']=1;

if ($_CFG['is_mobile'])
{
$mobileurl='http://m.zhanhenglvshi.com';

if (is_mobile())
    {    
		$Loaction=$mobileurl;
		if (!empty($Loaction))
		{
			ecs_header("Location: $Loaction\n");
		}  
    }

}

 



?>