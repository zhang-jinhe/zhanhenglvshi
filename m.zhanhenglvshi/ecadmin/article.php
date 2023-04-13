<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);
/**
 * 允许上传的文件类型
 */
$allow_file_types = '|GIF|JPG|PNG|BMP|SWF|DOC|XLS|PPT|MID|WAV|ZIP|RAR|PDF|CHM|RM|TXT|';


//语言版本切换功能 


//语言版本切换功能 


/**
 * ------------------------------------------------------
 */
// -- 信息列表
/**
 * ------------------------------------------------------
 */
if ($_REQUEST['act'] == 'list') {
	admin_priv('marticle_list');	




	$filter = array();
	$smarty -> assign('cat_select', get_articlecat_openlist(0));
	$smarty -> assign('full_page', 1);
	$smarty -> assign('filter', $filter);

	$smarty -> assign('articlecat', get_articlecat_openlist(0, 0, false));

	$article_list = get_articleslist();

	$smarty -> assign('article_list', $article_list['arr']);
	$smarty -> assign('filter', $article_list['filter']);
	$smarty -> assign('record_count', $article_list['record_count']);
	$smarty -> assign('page_count', $article_list['page_count']);

	$sort_flag = sort_flag($article_list['filter']);
	$smarty -> assign($sort_flag['tag'], $sort_flag['img']);

	assign_query_info();
	$smarty -> display('article_list.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 翻页，排序
/**
 * ------------------------------------------------------
 */
if ($_REQUEST['act'] == 'query') {
	admin_priv('marticle_list');	
	$article_list = get_articleslist();
	$smarty -> assign('articlecat', get_articlecat_openlist(0, 0, false));

	$smarty -> assign('article_list', $article_list['arr']);
	$smarty -> assign('filter', $article_list['filter']);
	$smarty -> assign('record_count', $article_list['record_count']);
	$smarty -> assign('page_count', $article_list['page_count']);

	$sort_flag = sort_flag($article_list['filter']);
	$smarty -> assign($sort_flag['tag'], $sort_flag['img']);

	make_json_result($smarty -> fetch('article_list.htm'), '', array('filter' => $article_list['filter'], 'page_count' => $article_list['page_count']));
} 

/**
 * 添加------------------------------------------------------
 **/
if ($_REQUEST['act'] == 'add') {
	admin_priv('marticle_add');
	$article = array();
	$article['is_show'] = 1;
	$article['add_time'] = date('Y-m-d H:i:s', time());
	$smarty -> assign('ur_here', '添加信息');
	$smarty -> assign('articlecat', get_articlecat_openlist(0, 0, false));
	$smarty -> assign('form_action', 'insert');
	assign_query_info();
	$smarty -> display('article_info.htm');
} 
if ($_REQUEST['act'] == 'insert') {
	admin_priv('marticle_add');
	$arr = array();
	$arr['add_time'] = !empty($_POST['add_time']) ? strtotime($_POST['add_time']) : time();
	$arr['title'."".$GLOBALS['select_lang'].""] = trim($_POST['title']);
	$arr['cat_id'] = intval($_POST['article_cat']);
	$arr['spcdesc'."".$GLOBALS['select_lang'].""] = trim($_POST['spcdesc']);
	$arr['keywords'."".$GLOBALS['select_lang'].""] = trim($_POST['keywords']);
	$arr['description'."".$GLOBALS['select_lang'].""] = trim($_POST['description']);

	$arr['tab'."".$GLOBALS['select_lang'].""]=trim($_POST['tab']);
	$arr['tab1'."".$GLOBALS['select_lang'].""]=trim($_POST['tab1']);
	$arr['tab2'."".$GLOBALS['select_lang'].""]=trim($_POST['tab2']);
	$arr['tab3'."".$GLOBALS['select_lang'].""]=trim($_POST['tab3']);
	$arr['tab4'."".$GLOBALS['select_lang'].""]=trim($_POST['tab4']);
	$arr['content'."".$GLOBALS['select_lang'].""]=trim($_POST['content']);
	$arr['content1'."".$GLOBALS['select_lang'].""]=trim($_POST['content1']);
	$arr['content2'."".$GLOBALS['select_lang'].""]=trim($_POST['content2']);
	$arr['content3'."".$GLOBALS['select_lang'].""]=trim($_POST['content3']);
	$arr['content4'."".$GLOBALS['select_lang'].""]=trim($_POST['content4']);

	$db -> autoExecute($ecs -> table('article'), $arr, 'INSERT');
	$article_id = $db -> insert_id();

    $upFile = $_FILES['article_thumb'];
	$allow_file_types = 'gif|jpg|jepg|png|bmp|sef|apk|ipa|zip|rar|pdf|doc|docx';
	$allow_file_types_array=explode('|',$allow_file_types);

	$explode=explode(".",$upFile['name']);
    $end=end($explode);
	if(in_array($end,$allow_file_types_array)){
	if ($upFile) {
	//判断文件是否为空或者出错
	if($upFile['error']=='0'&&!empty($upFile)){
	$filename=$_FILES['article_thumb']['name'];
	$filename=time().rand(1,1000).'-'.date('Y-m-d').substr($filename,strrpos($filename,"."));
	$dirpath=creaDir('/../data/article_thumb_file/d'.$article_id);
	$queryPath=ROOT_PATH.'data/article_thumb_file/d'.$article_id.'/'.$filename;
	//move_uploaded_file将浏览器缓存file转移到服务器文件夹
	if(move_uploaded_file($_FILES['article_thumb']['tmp_name'],$queryPath))
	{
	$arrs['article_thumb'] = 'data/article_thumb_file/d'.$article_id.'/'.$filename;;
	}
	}


	}
	}

	$upFile = $_FILES['article_other_thumb'];
	$allow_file_types = 'gif|jpg|jepg|png|bmp|sef|apk|ipa|zip|rar|pdf|doc|docx';
	$allow_file_types_array=explode('|',$allow_file_types);

	$explode=explode(".",$upFile['name']);
    $end=end($explode);
	if(in_array($end,$allow_file_types_array)){
	if ($upFile) {
	//判断文件是否为空或者出错
	if($upFile['error']=='0'&&!empty($upFile)){
	$filename=$_FILES['article_other_thumb']['name'];
	$filename=time().rand(1,1000).'-'.date('Y-m-d').substr($filename,strrpos($filename,"."));
	$dirpath=creaDir('/../data/article_thumb_file/d'.$article_id);
	$queryPath=ROOT_PATH.'data/article_thumb_file/d'.$article_id.'/'.$filename;
	//move_uploaded_file将浏览器缓存file转移到服务器文件夹
	if(move_uploaded_file($_FILES['article_other_thumb']['tmp_name'],$queryPath))
	{
	$arrs['article_other_thumb'] = 'data/article_thumb_file/d'.$article_id.'/'.$filename;;
	}
	}


	}
	}



	//多图上传
	if($power_list['power_picture'])
	{
	$array_thumb=array();
	for ($i=0;$i<count($_FILES['userfile']['tmp_name']);$i++)
	{
	$filename_array=$_FILES['userfile']['name'][$i];//此处路径换成你的
	$filename_array=time().rand(1,1000).'-'.date('Y-m-d').substr($filename_array,strrpos($filename_array,"."));
	$dirpath_array=creaDir('/../data/article_thumb_array_file/d'.$article_id);
	$queryPath_array=ROOT_PATH.'data/article_thumb_array_file/d'.$article_id.'/'.iconv('UTF-8','GBK',$filename_array);
    if(move_uploaded_file($_FILES['userfile']['tmp_name'][$i],$queryPath_array))
	{
    $array_thumb[$i]['article_array_thumb'] ='data/article_thumb_array_file/d'.$article_id.'/'.$filename_array;
	}
	else
	{
	$array_thumb[$i]['article_array_thumb'] ='';
	}
    
	}
	$arr['article_array_thumb']=json_encode_ex($array_thumb);
	}
	//多图上传



    


if($arrs){

		$db -> autoExecute($ecs -> table('article'), $arrs, 'UPDATE', "article_id = '$article_id'");

		}



	$link = array();
	$link[0]['text'] = '继续添加信息';
	$link[0]['href'] = 'article.php?act=add';
	$link[1]['text'] = '返回信息列表';
	$link[1]['href'] = 'article.php?act=list';
	clear_cache_files(); // 清除相关的缓存文件
	sys_msg('信息添加成功', 0, $link);
} 

/**
 * 编辑------------------------------------------------------
 **/
if ($_REQUEST['act'] == 'edit') {
	admin_priv('marticle_edit');
	$sql = "SELECT * FROM " . $ecs -> table('article') . " WHERE article_id='$_REQUEST[id]'";
	$article = $db -> GetRow($sql);
	$article['title']=$article['title'."".$GLOBALS['select_lang'].""];
	$article['spcdesc']=$article['spcdesc'."".$GLOBALS['select_lang'].""];
	$article['content']=$article['content'."".$GLOBALS['select_lang'].""];

	$article['add_time'] = date('Y-m-d H:i:s', $article['add_time']);
  
	if($article['article_array_thumb'])
	{
	$article['thumb_list']=json_decode($article['article_array_thumb'],true); 
	}

    $smarty -> assign('article', $article);
	$smarty -> assign('cat_id', $article['cat_id']);
	//var_dump($article);
	$smarty -> assign('ur_here', '信息编辑');
   $smarty -> assign('form_action', 'update');
	$smarty -> assign('articlecat', get_articlecat_openlist(0, 0, false));
	assign_query_info();
	$smarty -> display('article_info.htm');
} 

if ($_REQUEST['act'] == 'update') {
	admin_priv('marticle_edit');

	$article_id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('article') . " WHERE article_id='$_REQUEST[id]'";
	$article = $db -> GetRow($sql);
	$arr = array();
	$arr['add_time'] = strtotime($_POST['add_time']);
	$arr['title'."".$GLOBALS['select_lang'].""] = trim($_POST['title']);
	$arr['cat_id'] = intval($_POST['article_cat']);
	$arr['spcdesc'."".$GLOBALS['select_lang'].""] = trim($_POST['spcdesc']);
	$arr['keywords'."".$GLOBALS['select_lang'].""] = trim($_POST['keywords']);
	$arr['description'."".$GLOBALS['select_lang'].""] = trim($_POST['description']);
	$arr['content'."".$GLOBALS['select_lang'].""]=trim($_POST['content']);
	$arr['content1'."".$GLOBALS['select_lang'].""]=trim($_POST['content1']);
	$arr['content2'."".$GLOBALS['select_lang'].""]=trim($_POST['content2']);
	$arr['content3'."".$GLOBALS['select_lang'].""]=trim($_POST['content3']);
	$arr['content4'."".$GLOBALS['select_lang'].""]=trim($_POST['content4']);


	
	$upFile = $_FILES['article_thumb'];
	$allow_file_types = 'gif|jpg|jepg|png|bmp|sef|apk|ipa|zip|rar|pdf|doc|docx';
	$allow_file_types_array=explode('|',$allow_file_types);
	$explode=explode(".",$upFile['name']);
    $end=end($explode);
	if(in_array($end,$allow_file_types_array)){
	if ($upFile) {
	//判断文件是否为空或者出错
	if($upFile['error']=='0'&&!empty($upFile)){
	$filename=$_FILES['article_thumb']['name'];
	$filename=time().rand(1,1000).'-'.date('Y-m-d').substr($filename,strrpos($filename,"."));
	$dirpath=creaDir('/../data/article_thumb_file/d'.$_REQUEST['id']);
	$queryPath=ROOT_PATH.'data/article_thumb_file/d'.$_REQUEST['id'].'/'.iconv('UTF-8','GBK',$filename);
	//move_uploaded_file将浏览器缓存file转移到服务器文件夹
	if(move_uploaded_file($_FILES['article_thumb']['tmp_name'],$queryPath))
	{
	$arr['article_thumb'] = 'data/article_thumb_file/d'.$_REQUEST['id'].'/'.$filename;
	}
	}
	}	
	}


	$upFile = $_FILES['article_ohter_thumb'];
	$allow_file_types = 'gif|jpg|jepg|png|bmp|sef|apk|ipa|zip|rar|pdf|doc|docx';
	$allow_file_types_array=explode('|',$allow_file_types);
	$explode=explode(".",$upFile['name']);
    $end=end($explode);
	if(in_array($end,$allow_file_types_array)){
	if ($upFile) {
	//判断文件是否为空或者出错
	if($upFile['error']=='0'&&!empty($upFile)){
	$filename=$_FILES['article_ohter_thumb']['name'];
	$filename=time().rand(1,1000).'-'.date('Y-m-d').substr($filename,strrpos($filename,"."));
	$dirpath=creaDir('/../data/article_thumb_file/d'.$_REQUEST['id']);
	$queryPath=ROOT_PATH.'data/article_thumb_file/d'.$_REQUEST['id'].'/'.iconv('UTF-8','GBK',$filename);
	//move_uploaded_file将浏览器缓存file转移到服务器文件夹
	if(move_uploaded_file($_FILES['article_ohter_thumb']['tmp_name'],$queryPath))
	{
	$arr['article_ohter_thumb'] = 'data/article_thumb_file/d'.$_REQUEST['id'].'/'.$filename;
	}
	}
	}	
	}


    //多图上传
	$array_thumb=array();
	if($power_list['power_picture'])
	{
	$article['thumb_list']=json_decode($article['article_array_thumb'],true); 
	for ($i=0;$i<count($_FILES['userfile']['tmp_name']);$i++)
	{
	$filename_array=$_FILES['userfile']['name'][$i];//此处路径换成你的
	$filename_array=time().rand(1,1000).'-'.date('Y-m-d').substr($filename_array,strrpos($filename_array,"."));
	$dirpath_array=creaDir('/../data/article_thumb_array_file/d'.$_REQUEST['id']);
	$queryPath_array=ROOT_PATH.'data/article_thumb_array_file/d'.$_REQUEST['id'].'/'.iconv('UTF-8','GBK',$filename_array);
    if(move_uploaded_file($_FILES['userfile']['tmp_name'][$i],$queryPath_array))
	{
    $array_thumb[$i]['article_array_thumb'] ='data/article_thumb_array_file/d'.$_REQUEST['id'].'/'.$filename_array;
	}
	else
	{
	$array_thumb[$i]['article_array_thumb'] =$article['thumb_list'][$i]["article_array_thumb"];
	}
    
	}
	$arr['article_array_thumb']=json_encode_ex($array_thumb);//解决中文转码问题php5.3
	}
	//多图上传
    





	$db -> autoExecute($ecs -> table('article'), $arr, 'UPDATE', "article_id = '$article_id'");
	clear_cache_files();
	$link = array();
	$link[0]['text'] = '返回信息列表';
	$link[0]['href'] = 'article.php?act=list&' . list_link_postfix();
	$note = sprintf(stripslashes('信息' . $_POST['title'] . '修改成功'));
	sys_msg($note, 0, $link);
}


if ($_REQUEST['act'] == 'edit_tab') {
check_authz_json('marticle_edit');
$id=intval($_POST['id']);
$res_val = json_str_iconv(trim($_POST['val']));	
$arr['tab']=$_POST['val'];
$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('article'), $arr, 'UPDATE', " article_id='$id' ");
clear_cache_files();
make_json_result(stripslashes($res_val));
}

if ($_REQUEST['act'] == 'edit_tab') {
check_authz_json('marticle_edit');
$id=intval($_POST['id']);
$res_val = json_str_iconv(trim($_POST['val']));	
$arr['tab']=$_POST['val'];
$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('article'), $arr, 'UPDATE', " article_id='$id' ");
clear_cache_files();
make_json_result(stripslashes($res_val));
}

if ($_REQUEST['act'] == 'edit_tab1') {
check_authz_json('marticle_edit');
$id=intval($_POST['id']);
$res_val = json_str_iconv(trim($_POST['val']));	
$arr['tab1']=$_POST['val'];
$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('article'), $arr, 'UPDATE', " article_id='$id' ");
clear_cache_files();
make_json_result(stripslashes($res_val));
}

if ($_REQUEST['act'] == 'edit_tab2') {
check_authz_json('marticle_edit');
$id=intval($_POST['id']);
$res_val = json_str_iconv(trim($_POST['val']));	
$arr['tab2']=$_POST['val'];
$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('article'), $arr, 'UPDATE', " article_id='$id' ");
clear_cache_files();
make_json_result(stripslashes($res_val));
}

if ($_REQUEST['act'] == 'edit_tab3') {
check_authz_json('marticle_edit');
$id=intval($_POST['id']);
$res_val = json_str_iconv(trim($_POST['val']));	
$arr['tab3']=$_POST['val'];
$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('article'), $arr, 'UPDATE', " article_id='$id' ");
clear_cache_files();
make_json_result(stripslashes($res_val));
}

if ($_REQUEST['act'] == 'edit_tab4') {
check_authz_json('marticle_edit');
$id=intval($_POST['id']);
$res_val = json_str_iconv(trim($_POST['val']));	
$arr['tab4']=$_POST['val'];
$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('article'), $arr, 'UPDATE', " article_id='$id' ");
clear_cache_files();
make_json_result(stripslashes($res_val));
}






if ($_REQUEST['act'] == 'batch') {
	admin_priv('marticle_remove');
	$article_id = !empty($_REQUEST['id']) ? join(',', $_REQUEST['id']) : 0;
    
	
    
	

	if($_OPE['nodel_article'])
	{
	$article_id_array = !empty($_REQUEST['id']) ? explode(',',join(',', $_REQUEST['id'])) : array();
    $nodel_array=explode('|',$_OPE['nodel_article']);
	
	$nodel_array_diff=array_intersect($article_id_array,$nodel_array);
	
    if($nodel_array_diff)
	{
	$result=array('err'=>1,'message'=>'存在内容ID'.implode(",", $nodel_array_diff).'在页面模块中调用，不可删除');
	echo json_encode($result);
	exit;
	}
	
	}
	
	


	


	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('article') . " WHERE article_id " . db_create_in($article_id);
	$GLOBALS['db'] -> query($sql);
    $result=array('err'=>0,'message'=>'删除成功');
	echo json_encode($result);
	
	//make_json_result($smarty -> fetch('article_list.htm'));
	
} 

if ($_REQUEST['act'] == 'is_show') {
	check_authz_json('marticle_edit');
	$article_id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('article') . " WHERE article_id='$article_id'";
	$article = $db -> GetRow($sql);
	if ($article['is_show'] == 1) {
		$arr['is_show'] = 0;
	} 
	if ($article['is_show'] == 0) {
		$arr['is_show'] = 1;
	} 
	$db -> autoExecute($GLOBALS['ecs'] -> table('article'), $arr, 'UPDATE', "article_id='$article_id'");
	$GLOBALS['db'] -> query($sql);
	make_json_result($smarty -> fetch('article_list.htm'));
} 

if ($_REQUEST['act'] == 'remove') {
	admin_priv('marticle_remove');
	$article_id = intval($_REQUEST['id']);

	if($_OPE['nodel_article'])
	{
    $nodel_array=explode('|',$_OPE['nodel_article']);
    if(in_array($article_id,$nodel_array))
	{
	make_json_error('该内容在页面中有固定版块调用，不可删除');
	}
	}


    if($article_id == '1' || $article_id == '2'){
	exit;
	}
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('article') . " WHERE article_id " . db_create_in($article_id);
	$GLOBALS['db'] -> query($sql);
	$url = 'article.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
} 
if ($_REQUEST['act'] == 'drop_thumb') {
	admin_priv('marticle_remove');
    $id=intval($_REQUEST["id"]);
	$gid=intval($_REQUEST["gid"]);
    $thumb=$_REQUEST["thumb"];
	$sql = "SELECT * FROM ". $GLOBALS['ecs']->table('article'). " WHERE   article_id='$id'  ";
	$info = $GLOBALS['db']->GetRow($sql);
    $thumb_list=json_decode($info['article_array_thumb'],true);

	switch($thumb)
	{  
	case "article_array_thumb":
    $thumb_list[$gid]["article_array_thumb"]='';
	$thumb_list=json_encode($thumb_list);
	$arr = array();
	$arr['article_array_thumb']=$thumb_list;
	$GLOBALS['db'] -> autoExecute($GLOBALS['ecs'] -> table('article'), $arr, 'UPDATE', "article_id = '$id'");
	$link = array();
	$link[1]['text'] = '返回修改';
	$link[1]['href'] = '?m=article&act=edit&id='.$id;
	clear_cache_files(); // 清除相关的缓存文件
	sys_msg('信息修改成功', 0, $link);
	break;
	case "article_thumb":
	$sql = "SELECT article_thumb FROM " . $GLOBALS['ecs'] -> table('article') . " WHERE article_id ='" . $id . "'";
	$thumb_name = $GLOBALS['db'] -> getOne($sql);
	if (!empty($thumb_name)) {
	@unlink(ROOT_PATH . $thumb_name);
	$sql = "UPDATE " . $GLOBALS['ecs'] -> table('article') . " SET article_thumb = '' WHERE article_id ='" . $id . "'";
	$GLOBALS['db'] -> query($sql);
	} 
	$link[1]['text'] = '返回修改';
	$link[1]['href'] = '?m=article&act=edit&id='.$id;
	clear_cache_files(); // 清除相关的缓存文件
	sys_msg('信息修改成功', 0, $link);
	break;
} 
}



 

/**
 * 获得信息列表
 */
function get_articleslist() {
	$result = get_filter();
	if ($result === false) {
		$filter = array();
		$filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
			$filter['keyword'] = json_str_iconv($filter['keyword']);
		} 
		$filter['cat_id'] = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'a.article_id' : trim($_REQUEST['sort_by']);
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

		$where = '';
		if (!empty($filter['keyword'])) {

			$where .= " AND a.title".$GLOBALS['select_lang']." LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";


		} 
		if ($filter['cat_id']) {
			$where .= " AND a." . get_article_children($filter['cat_id']);
		} 

		/**
		 * 信息总数
		 */
		$sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs'] -> table('article') . ' AS a ' . 'LEFT JOIN ' . $GLOBALS['ecs'] -> table('article_cat') . ' AS ac ON ac.cat_id = a.cat_id ' . 'WHERE 1 ' . $where;
		$filter['record_count'] = $GLOBALS['db'] -> getOne($sql);

		$filter = page_and_size($filter);

		/**
		 * 获取信息数据
		 */
		$sql = 'SELECT a.* , ac.cat_name ' . 'FROM ' . $GLOBALS['ecs'] -> table('article') . ' AS a ' . 'LEFT JOIN ' . $GLOBALS['ecs'] -> table('article_cat') . ' AS ac ON ac.cat_id = a.cat_id ' . 'WHERE 1 ' . $where . ' ORDER by ' . $filter['sort_by'] . ' ' . $filter['sort_order'];

		$filter['keyword'] = stripslashes($filter['keyword']);
		set_filter($filter, $sql);
	} else {
		$sql = $result['sql'];
		$filter = $result['filter'];
	} 
	$arr = array();
	$res = $GLOBALS['db'] -> selectLimit($sql, $filter['page_size'], $filter['start']);

	while ($rows = $GLOBALS['db'] -> fetchRow($res)) {
		$rows['date'] = date('Y-m-d H:i:s', $rows['add_time']);
        $rows['cat_name']=empty($rows['cat_name'."".$GLOBALS['select_lang'].""])?$rows['cat_name']:$rows['cat_name'."".$GLOBALS['select_lang'].""];
		$rows['title']=empty($rows['title'."".$GLOBALS['select_lang'].""])?$rows['title']:$rows['title'."".$GLOBALS['select_lang'].""];

		$arr[] = $rows;
	} 
	return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
} 

/**
 * 上传文件
 */
function upload_article_file($upload) {
	if (!make_dir("../" . DATA_DIR . "/article")) {
		/**
		 * 创建目录失败
		 */
		return false;
	} 

	$filename = cls_image :: random_filename() . substr($upload['name'], strpos($upload['name'], '.'));
	$path = ROOT_PATH . DATA_DIR . "/article/" . $filename;

	if (move_upload_file($upload['tmp_name'], $path)) {
		return '/' . DATA_DIR . "/article/" . $filename;
	} else {
		return false;
	} 
} 
/**
 * 上传文件
 */
function upload_array_article_file($upload) {
	if (!make_dir("../" . DATA_DIR . "/arrayarticle")) {
		/**
		 * 创建目录失败
		 */
		return false;
	} 

	$filename = cls_image :: random_filename() . substr($upload['name'], strpos($upload['name'], '.'));
	$path = ROOT_PATH . DATA_DIR . "/arrayarticle/" . $filename;

	if (move_upload_file($upload['tmp_name'], $path)) {
		return '/' . DATA_DIR . "/arrayarticle/" . $filename;
	} else {
		return false;
	} 
} 

function handle_gallery_image_article($article_id, $image_files, $image_descs, $image_urls) {
	/**
	 * 是否处理缩略图
	 */
	$proc_thumb = (isset($GLOBALS['shop_id']) && $GLOBALS['shop_id'] > 0)? false : true;
	foreach ($image_descs AS $key => $img_desc) {
		/**
		 * 是否成功上传
		 */
		$flag = false;
		if (isset($image_files['error'])) {
			if ($image_files['error'][$key] == 0) {
				$flag = true;
			} 
		} else {
			if ($image_files['tmp_name'][$key] != 'none') {
				$flag = true;
			} 
		} 

		if ($flag) {
			$upload = array('name' => $image_files['name'][$key],
				'type' => $image_files['type'][$key],
				'tmp_name' => $image_files['tmp_name'][$key],
				'size' => $image_files['size'][$key],
				);
			// 生成缩略图 
			// 复制文件
			$ress = upload_array_article_file($upload);

			if ($ress != false) {
				$file_urls = $ress;
			} 
			// var_dump($file_urls); 
			// if ($file_url == '')
			// {
			// $file_url = $_POST['file_url'];
			// }
			$sql = "INSERT INTO " . $GLOBALS['ecs'] -> table('articles_gallery') . " (article_id, img_url, img_desc, thumb_url, img_original) " . "VALUES ('$article_id', '$file_urls', '$img_desc', '$file_urls', '$img_original')";
			$GLOBALS['db'] -> query($sql);
		} elseif (!empty($image_urls[$key]) && ($image_urls[$key] != $GLOBALS['_LANG']['img_file']) && ($image_urls[$key] != 'http://') && copy(trim($image_urls[$key]), ROOT_PATH . 'temp/' . basename($image_urls[$key]))) {
		} 
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

/**
     * 不转义中文 json_encode
     * 中文转义成 unicode 字符的话不方便后台日志搜索，不转义吧
     * Add By TuJia
     */
function json_encode_ex($var) {
        if ($var === null)
            return 'null';
        if ($var === true)
            return 'true';

        if ($var === false)
            return 'false';

        static $reps = array(
            array("\\", "/", "\n", "\t", "\r", "\b", "\f", '"', ),
            array('\\\\', '\\/', '\\n', '\\t', '\\r', '\\b', '\\f', '\"', ),
        );

        if (is_scalar($var))
            return '"' . str_replace($reps[0], $reps[1], (string) $var) . '"';

        if (!is_array($var))
            throw new Exception('JSON encoder error!');

        $isMap = false;
        $i = 0;
        foreach (array_keys($var) as $k) {
            if (!is_int($k) || $i++ != $k) {
                $isMap = true;
                break;
            }
        }

        $s = array();

        if ($isMap) {
            foreach ($var as $k => $v)
                $s[] = '"' . $k . '":' . call_user_func(__FUNCTION__, $v);

            return '{' . implode(',', $s) . '}';
        } else {
            foreach ($var as $v)
                $s[] = call_user_func(__FUNCTION__, $v);

            return '[' . implode(',', $s) . ']';
        }
    }
?>