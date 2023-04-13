<?php
define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');
$image = new cls_image($_CFG['bgcolor']);




/* act操作项的初始化 */
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
	admin_priv('marticlecat_list');

       
	





    $smarty->assign('full_page',   1);
    $articlecat = get_articlecat_openlist();
	$smarty -> assign('articlecat', $articlecat);
    assign_query_info();
    $smarty->display('articlecat_list.htm');
}

/*------------------------------------------------------ */
//-- 查询
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'query')
{
		admin_priv('marticlecat_list');
    $articlecat = article_cat_list(0, 0, false);
    foreach ($articlecat as $key => $cat)
    {
        $articlecat[$key]['type_name'] = $_LANG['type_name'][$cat['cat_type']];
    }
    $smarty->assign('articlecat',        $articlecat);

    make_json_result($smarty->fetch('articlecat_list.htm'));
}

/*------------------------------------------------------ */
//-- 添加分类
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'add')
{
   	admin_priv('marticlecat_add');
    $articlecat = get_articlecat_openlist();

    $smarty->assign('form_action', 'insert');
    $smarty->assign('articlecat',        $articlecat);
    assign_query_info();
    $smarty->display('articlecat_info.htm');
}
/*------------------------------------------------------ */
//-- 添加分类执行
/*------------------------------------------------------ */
if ($_REQUEST['act'] == 'insert') {
	admin_priv('marticlecat_add');
	$arr = array();
   	$arr['moban'] = trim($_POST['moban']);
	$arr['smallmoban'] = trim($_POST['smallmoban']);
	$arr['parent_id'] = trim($_POST['parent_id']);
	$arr['jump_url'] = trim($_POST['jump_url']);
	$arr['is_nav_show'] = trim($_POST['is_nav_show']);
	$arr['cat_name'."".$GLOBALS['select_lang'].""] = trim($_POST['cat_name']);
	$arr['title'."".$GLOBALS['select_lang'].""] = trim($_POST['title']);
	$arr['keywords'."".$GLOBALS['select_lang'].""] = trim($_POST['keywords']);
	$arr['description'."".$GLOBALS['select_lang'].""] = trim($_POST['description']);

	$img_name = basename($image -> upload_image($_FILES['file_url'], 'artilce/cat_thumb'));
	if ($img_name) {
		$arr['file_url'] = DATA_DIR . "/artilce/cat_thumb/" . $img_name;
	}
    $arr['is_show'] = 1;
    $is_only = is_only('define_url', $_POST['define_url']);
    if (!$is_only)
    {
    sys_msg(sprintf('已经存在URL', stripslashes($_POST['define_url'])), 1);
    }


	$db -> autoExecute($ecs -> table('article_cat'), $arr, 'INSERT');
	$article_cat_id = $db -> insert_id();


    $upFile = $_FILES['cat_thumb'];
	$allow_file_types = 'gif|jpg|jepg|png|bmp|sef|apk|ipa|zip|rar|pdf|doc|docx';
	$allow_file_types_array=explode('|',$allow_file_types);
	
	$explode=explode(".",$upFile['name']);
    $end=end($explode);
	if(in_array($end,$allow_file_types_array)){
	if ($upFile) {
	//判断文件是否为空或者出错
	if($upFile['error']=='0'&&!empty($upFile)){
	$filename=$_FILES['cat_thumb']['name'];
	$filename=time().rand(1,1000).'-'.date('Y-m-d').substr($filename,strrpos($filename,"."));
	$dirpath=creaDir('/../data/cat_thumb_file/d'.$article_id);
	$queryPath=ROOT_PATH.'data/cat_thumb_file/d'.$article_id.'/'.iconv('utf-8','gb2312',$filename);
	//move_uploaded_file将浏览器缓存file转移到服务器文件夹
	if(move_uploaded_file($_FILES['cat_thumb']['tmp_name'],$queryPath))
	{
	$arrs['cat_thumb'] = 'data/cat_thumb_file/d'.$article_cat_id.'/'.$filename;;
	}
	}
	$db -> autoExecute($ecs -> table('article_cat'), $arrs, 'UPDATE', "cat_id = '$article_cat_id'");
	}
	}



  
	$oarr = array();
    $oarr['define_url']=!empty($_POST['define_url'])?trim($_POST['define_url']):$article_cat_id;
	$db -> autoExecute($ecs -> table('article_cat'), $oarr, 'UPDATE', "cat_id = '$article_cat_id'");

	$link = array();
	$link[0]['text'] = '继续添加信息分类';
	$link[0]['href'] = 'articlecat.php?act=add';
	$link[1]['text'] = '返回信息分类列表';
	$link[1]['href'] = 'articlecat.php?act=list';
	clear_cache_files(); // 清除相关的缓存文件
	sys_msg('信息分类添加成功', 0, $link);
} 
/*------------------------------------------------------ */
//-- 编辑信息分类
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'edit')
{
    admin_priv('marticlecat_edit');
	$cat_id=intval($_REQUEST['id']);
    $sql = "SELECT * FROM ". $ecs->table('article_cat'). " WHERE cat_id='$cat_id'";

    $cat = $db->GetRow($sql);
	
	$cat['cat_name']=$cat['cat_name'."".$GLOBALS['select_lang'].""];
	$cat['title']=$cat['title'."".$GLOBALS['select_lang'].""];
	$cat['keywords']=$cat['keywords'."".$GLOBALS['select_lang'].""];
	$cat['description']=$cat['description'."".$GLOBALS['select_lang'].""];


    $articlecat = get_articlecat_openlist();
	$smarty -> assign('articlecat', $articlecat);
	$smarty->assign('parent_id', $cat['parent_id']);
	$smarty->assign('cat', $cat);
    $smarty->assign('form_action', 'update');
    assign_query_info();
    $smarty->display('articlecat_info.htm');
}
elseif ($_REQUEST['act'] == 'update')
{
  
   admin_priv('marticlecat_edit');
    $cat_id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	$arr = array();
	$arr['moban'] = trim($_POST['moban']);
	$arr['smallmoban'] = trim($_POST['smallmoban']);
	$arr['parent_id'] = trim($_POST['parent_id']);
	$arr['jump_url'] = trim($_POST['jump_url']);
	$arr['is_show'] = trim($_POST['is_show']);
	$arr['is_nav_show'] = trim($_POST['is_nav_show']);
	$arr['cat_name'."".$GLOBALS['select_lang'].""] = trim($_POST['cat_name']);
	$arr['title'."".$GLOBALS['select_lang'].""] = trim($_POST['title']);
	$arr['keywords'."".$GLOBALS['select_lang'].""] = trim($_POST['keywords']);
	$arr['description'."".$GLOBALS['select_lang'].""] = trim($_POST['description']);




    if ($_POST['define_url'] != $_POST['old_define_url'])
    {   
	
        $is_only = is_only('define_url', $_POST['define_url'], $_POST['id']);

        if (!$is_only)
        {
            sys_msg(sprintf('已经存在URL', stripslashes($_POST['define_url'])), 1);
        }
    }
	
/* 检查设定的分类的父分类是否合法 */
    $child_cat = article_cat_list($_POST['id'], 0, false);
    if (!empty($child_cat))
    {
        foreach ($child_cat as $child_data)
        {
            $catid_array[] = $child_data['cat_id'];
        }
    }
    if (in_array($_POST['parent_id'], $catid_array))
    {
        sys_msg(sprintf("分类名 ".$_POST['cat_name']." 的父分类不能设置成本身或本身的子分类", stripslashes($_POST['cat_name'])), 1);
    }



	$upFile = $_FILES['cat_thumb'];
	$allow_file_types = 'gif|jpg|jepg|png|bmp|sef|apk|ipa|zip|rar|pdf|doc|docx';
	$allow_file_types_array=explode('|',$allow_file_types);
    $ex=explode(".",$upFile['name']);
    $end=end($ex);
	$explode=explode(".",$upFile['name']);
    $end=end($explode);
	if(in_array($end,$allow_file_types_array)){
	if ($upFile) {
	//判断文件是否为空或者出错
	if($upFile['error']=='0'&&!empty($upFile)){
	$filename=$_FILES['cat_thumb']['name'];
	$filename=time().rand(1,1000).'-'.date('Y-m-d').substr($filename,strrpos($filename,"."));
	$dirpath=creaDir('/../data/cat_thumb_file/d'.$_REQUEST['id']);
	$queryPath=ROOT_PATH.'data/cat_thumb_file/d'.$_REQUEST['id'].'/'.iconv('utf-8','gb2312',$filename);
	//move_uploaded_file将浏览器缓存file转移到服务器文件夹
	if(move_uploaded_file($_FILES['cat_thumb']['tmp_name'],$queryPath))
	{
	$arr['cat_thumb'] = 'data/cat_thumb_file/d'.$_REQUEST['id'].'/'.$filename;;
	}
	}
	}

	
	}




	$db -> autoExecute($ecs -> table('article_cat'), $arr, 'UPDATE', "cat_id = '$cat_id'");

   // $oarr = array();
    //$oarr['url']=$_POST['define_url'].'/';

	//$sql = "SELECT durl FROM " . $ecs->table('nav') . " WHERE cid = '$cat_id'";
	//$durl=$GLOBALS['db'] -> getOne($sql);
	//if($durl)
	//{
	//$oldurl=$_POST['old_define_url'].'/';
    //$oarr['durl']=str_replace($oldurl,$oarr['url'],$durl);
    //}

	//if($_OPE['open_lang'])
	//{
	
	//$oarr['url'.$_OPE['select_lang']]=$langurl.'-'.$_POST['define_url'].'/';
	
	//$oarr['durl'.$_OPE['select_lang']]=str_replace($oldurl,$oarr['url'],$durl);

	//echo $oarr['url'.$_OPE['select_lang']].'bbbbbbbbbbb';
	
	//}




	//$db -> autoExecute($ecs -> table('nav'), $oarr, 'UPDATE', "cid = '$cat_id'");


	clear_cache_files();
	$link = array();
	$link[0]['text'] = '返回信息分类列表';
	$link[0]['href'] = 'articlecat.php?act=list';
	$note = sprintf(stripslashes('信息分类' . $_POST['cat_name'] . '修改成功'));
	sys_msg($note, 0, $link);

}



elseif ($_REQUEST['act'] == 'edit_sort_order') {
	check_authz_json('marticlecat_edit');  
	$id = intval($_POST['id']);
	$val = intval($_POST['sort_order']);
	$arr['sort_order']=$val;
    $db -> autoExecute($GLOBALS['ecs'] -> table('article_cat'), $arr, 'UPDATE', "cat_id='$id'");
	$GLOBALS['db'] -> query($sql);
    make_json_result($val);

	
}

elseif ($_REQUEST['act'] == 'edit_article_id') {
	check_authz_json('marticlecat_edit');  
	$cat_id = intval($_POST['cat_id']);
	$val = intval($_POST['article_id']);
	$arr['article_id']=$val;
	$arr['ifxiala']=intval($_POST['ifxiala']);
    $db -> autoExecute($GLOBALS['ecs'] -> table('article_cat'), $arr, 'UPDATE', "cat_id='$cat_id'");
	$GLOBALS['db'] -> query($sql);
    make_json_result($val);

	
}


elseif ($_REQUEST['act'] == 'remove') {
	admin_priv('marticlecat_remove');
    $cat_id = intval($_REQUEST['id']);

	if($_OPE['nodel_article_cat'])
	{
    $nodel_array=explode('|',$_OPE['nodel_article_cat']);
    if(in_array($cat_id,$nodel_array))
	{
	make_json_error('该分类在页面中有固定版块调用，不可删除');
	}
	}




	
	/* 当前分类下是否有子分类 */
	$sql = "SELECT COUNT(*) FROM " . $ecs->table('article_cat') . " WHERE parent_id = '$cat_id'";
	 /* 当前分类下是否有信息 */
    $sql2 = "SELECT COUNT(*) FROM ".$ecs->table('article')." WHERE cat_id = '$cat_id'";
	if ($db->getOne($sql) == 0 && $db->getOne($sql2) == 0)
    {
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('article_cat') . " WHERE cat_id " . db_create_in($cat_id);
	$GLOBALS['db'] -> query($sql);

	$db->query("DELETE FROM " . $ecs->table('nav') . "WHERE   cid = '$cat_id' AND type = 'middle'");
    clear_cache_files();

	}else{
	make_json_error('该分类不是末级分类或者此分类下还存在有信息,您不能删除!');
	}
	clear_cache_files();
	$url = 'articlecat.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
} 
elseif ($_REQUEST['act'] == 'is_nav_show') {
	check_authz_json('marticlecat_edit');
	$cat_id = isset($_REQUEST['id'])?intval($_REQUEST['id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('article_cat') . " WHERE cat_id='$cat_id'";
	$cat = $db -> GetRow($sql);
	if ($cat['is_nav_show'] == 1) {
		$arr['is_nav_show'] = 0;
	} 
	if ($cat['is_nav_show'] == 0) {
		$arr['is_nav_show'] = 1;
	} 
	$db -> autoExecute($GLOBALS['ecs'] -> table('article_cat'), $arr, 'UPDATE', "cat_id='$cat_id'");
	$GLOBALS['db'] -> query($sql);
	make_json_result($smarty -> fetch('articlecat_list.htm'));
} 
/**
 * 添加商品分类
 *
 * @param   integer $cat_id
 * @param   array   $args
 *
 * @return  mix
 */
function cat_update($cat_id, $args)
{
    if (empty($args) || empty($cat_id))
    {
        return false;
    }

    return $GLOBALS['db']->autoExecute($GLOBALS['ecs']->table('article_cat'), $args, 'update', "cat_id='$cat_id'");
}



function is_only($col, $name, $id = 0, $where='')
    {
        $sql = 'SELECT COUNT(*) FROM ' .$GLOBALS['ecs']->table('article_cat'). " WHERE define_url = '$name'";
            $sql .= empty($id) ? '' : " AND cat_id  <> '$id' ";
        $sql .= empty($where) ? '' : ' AND ' .$where;

        return ($GLOBALS['db']->getOne($sql) == 0);
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
