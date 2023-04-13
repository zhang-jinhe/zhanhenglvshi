<?php
define('IN_ECS', true);

$urlhost=$_SERVER['HTTP_HOST'];
define('CDN_URL','//www.zndrive.com');
define('IN_ECTOUCH', true);
require(dirname(__FILE__) .'/includes/init.php');
include_once(ROOT_PATH . '/includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);

if ($_REQUEST['act'] == 'main')
{
admin_priv('mkefu_main');

$Row =$GLOBALS['db']->getRow(" SELECT *  FROM ".$GLOBALS['ecs']->table('kefu') ." where 1 ");
$GLOBALS['smarty']->assign('kefu',$Row);
$GLOBALS['smarty']->assign('CDN_URL',CDN_URL);
$GLOBALS['smarty']->assign('form_act','update');
assign_query_info();
$smarty->display('kefu.htm');
}

if ($_REQUEST['act'] == 'update')
{
$arr=array();
		$arr['skin'] = $_POST['skin'];
		$arr['kefushow'] = $_POST['kefushow'];
		$arr['mshow'] = $_POST['mshow'];
		$arr['pshow'] = $_POST['pshow'];
		$arr['showlefttop'] = $_POST['showlefttop'];
		$arr['showleft'] = $_POST['showleft'];
		$arr['showrighttop'] = $_POST['showrighttop'];
		$arr['showright'] = $_POST['showright'];
		$arr['fs_show'] = $_POST['fs_show'];
		$arr['typeone'] = $_POST['typeone'];
		$arr['kfqq'] = $_POST['kfqq'];
		$arr['im'] = $_POST['im'];
		$arr['typetwo'] = $_POST['typetwo'];
		$arr['kfqqtwo'] = $_POST['kfqqtwo'];
		$arr['imtwo'] = $_POST['imtwo'];
		$arr['qqqun'] = $_POST['qqqun'];
		$arr['wwqun'] = $_POST['wwqun'];
		$arr['qqcss'] = $_POST['qqcss'];
		$arr['wwcss'] = $_POST['wwcss'];
		if(trim($_POST['fenxiang'])){
		$arr['fenxiang']=implode(",",$fenxiang);
		}
		$arr['kf53'] = $_POST['kf53'];
		$arr['kftel'] = $_POST['kftel'];
		$arr['shijian'] = $_POST['shijian']; 

		$upFile = $_FILES['code'];
		$allow_file_types = 'gif|jpg|jepg|png';
		$allow_file_types_array=explode('|',$allow_file_types);
		if(in_array(end(explode(".",$upFile['name'])), $allow_file_types_array)){

		if ($upFile) {
		//判断文件是否为空或者出错
		if($upFile['error']=='0'&&!empty($upFile)){
		$filename=$_FILES['code']['name'];
		$filename=time().rand(1,1000).'-'.date('Y-m-d').substr($filename,strrpos($filename,"."));
		$dirpath=creaDir('/../data/article_thumb_file/d'.$_REQUEST['id']);
		$queryPath=ROOT_PATH.'data/article_thumb_file/d'.$_REQUEST['id'].'/'.iconv('UTF-8','GBK',$filename);
		//move_uploaded_file将浏览器缓存file转移到服务器文件夹
		if(move_uploaded_file($_FILES['code']['tmp_name'],$queryPath))
		{
		$arr['code'] = 'data/article_thumb_file/d'.$_REQUEST['id'].'/'.$filename;
		}
		}
		}	
		}

		
		$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('kefu'), $arr, 'UPDATE', " 1 ");
		

clear_cache_files();
sys_msg('修改成功！',0,array(array('href'=>'kefu.php?act=main','text'=>'网站客服管理')));
}


if ($_REQUEST['act'] == 'drop_thumb') {
	
	$sql = "SELECT * FROM ". $GLOBALS['ecs']->table('kefu'). " WHERE  1    ";
	$Row = $GLOBALS['db']->GetRow($sql);
	$arr = array();
	$arr['code']='';
	$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('kefu'), $arr, 'UPDATE', " 1 ");
	$link = array();

	$link[0] = array('href' => 'kefu.php?act=main', 'text' => '删除成功');
	sys_msg('删除成功', 0, $link);
	
} 

function creaDir($dirPath)
{
$curPath = dirname(__FILE__);
$path = $curPath.$dirPath;
if(is_dir($path) || mkdir($path,0777,true))
{
return $dirPath;
}

}

?>