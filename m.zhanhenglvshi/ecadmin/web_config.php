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
	 
	 
	 
	 $upFile = $_FILES['web_logo'];
	$allow_file_types = 'gif|jpg|jepg|png|bmp|sef|apk|ipa|zip|rar|pdf|doc|docx';
	$allow_file_types_array=explode('|',$allow_file_types);
	$explode=explode(".",$upFile['name']);
    $end=end($explode);
	if(in_array($end,$allow_file_types_array)){
	if ($upFile) {
	//判断文件是否为空或者出错
	if($upFile['error']=='0'&&!empty($upFile)){
	$filename=$_FILES['web_logo']['name'];
	$filename=time().rand(1,1000).'-'.date('Y-m-d').substr($filename,strrpos($filename,"."));
	$dirpath=creaDir('/../data/web_logo/d'.$_REQUEST['id']);
	$queryPath=ROOT_PATH.'data/web_logo/d'.$_REQUEST['id'].'/'.iconv('UTF-8','GBK',$filename);
	//move_uploaded_file将浏览器缓存file转移到服务器文件夹
	if(move_uploaded_file($_FILES['web_logo']['tmp_name'],$queryPath))
	{
	$arr['web_logo'] = 'data/web_logo/d'.$_REQUEST['id'].'/'.$filename;
	}
	}
	}	
	}
	
	
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
	
	
if ($_REQUEST['act'] == 'drop_thumb') {
	admin_priv('marticle_remove');
    $thumb=$_REQUEST["thumb"];

	switch($thumb)
	{  
	case "web_logo":
	$sql = "SELECT web_logo FROM " . $GLOBALS['ecs'] -> table('moban') . " WHERE  moban_id=".$_SESSION['moban_id']."  ";
	$thumb_name = $GLOBALS['db'] -> getOne($sql);
	if (!empty($thumb_name)) {
	@unlink(ROOT_PATH . $thumb_name);
	$sql = "UPDATE " . $GLOBALS['ecs'] -> table('moban') . " SET web_logo = '' WHERE moban_id=".$_SESSION['moban_id']."";
	$GLOBALS['db'] -> query($sql);
	} 

	$link[1]['text'] = '返回修改';
	$link[1]['href'] = 'web_config.php?act=list_edit';
	clear_cache_files(); // 清除相关的缓存文件
	sys_msg('信息修改成功', 0, $link);
	break;
} 
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