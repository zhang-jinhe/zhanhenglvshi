<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');

if ($_REQUEST['act'] == 'list') {
	admin_priv('mmodule_list');	
	$filter = array();
	$smarty -> assign('full_page', 1);
	$smarty -> assign('filter', $filter);
	$module_list = get_moduleslist();
	$smarty -> assign('module_list', $module_list['arr']);
	$smarty -> assign('filter', $module_list['filter']);
	$smarty -> assign('record_count', $module_list['record_count']);
	$smarty -> assign('page_count', $module_list['page_count']);
	$sort_flag = sort_flag($module_list['filter']);
	$smarty -> assign($sort_flag['tag'], $sort_flag['img']);

	assign_query_info();
	$smarty -> display('module_list.htm');
} 

if ($_REQUEST['act'] == 'query') {
	admin_priv('mmodule_list');	
	$filter = array();
	$smarty -> assign('full_page', 1);
	$smarty -> assign('filter', $filter);
	$module_list = get_moduleslist();
	$smarty -> assign('module_list', $module_list['arr']);
	$smarty -> assign('filter', $module_list['filter']);
	$smarty -> assign('record_count', $module_list['record_count']);
	$smarty -> assign('page_count', $module_list['page_count']);
	$sort_flag = sort_flag($module_list['filter']);
	$smarty -> assign($sort_flag['tag'], $sort_flag['img']);

	make_json_result($smarty -> fetch('module_list.htm'), '', array('filter' => $module_list['filter'], 'page_count' => $module_list['page_count']));
} 


/**
 * 编辑------------------------------------------------------
 **/
if ($_REQUEST['act'] == 'edit') {
	admin_priv('mmodule_edit');
	$sql = "SELECT * FROM " . $ecs -> table('module') . " WHERE module_id='$_REQUEST[id]'";
	$module = $db -> GetRow($sql);
	$module['data']=$module['data'."".$GLOBALS['select_lang'].""];
	$module['base']=json_decode($module['base'],true);


	if(!$module['module_data_type'])
	{
	$module_data_array=array();
	$module_data_array=json_decode($module['data'],true);

	$arr=array();
	if(is_array($module_data_array)){
	foreach($module_data_array as $key => $row)
	{
	$listarray = json_decode($row, true);
	$arr[$key]=$listarray;
	}
	}
	$smarty -> assign('itemlist', $arr);//json挂件数据
	}
	


	$smarty -> assign('item', $data_array[$_REQUEST['id']]);//数据具体的记录编辑$_REQUEST['id']健值 2018
    $smarty -> assign('id', $_REQUEST['id']);
    $articlecat = get_articlecat_openlist();
	$smarty -> assign('articlecat', $articlecat); 
    $smarty -> assign('module', $module);
	$smarty -> assign('ur_here', '信息编辑');
    $smarty -> assign('form_action', 'update');
	assign_query_info();
	$smarty -> display('module_info.htm');
} 


if ($_REQUEST['act'] == 'update') {
	admin_priv('mmodule_edit');
    
	$module_id = isset($_POST['id'])?trim($_POST['id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('module') . " WHERE module_id='$_REQUEST[id]'";
	$module = $db -> GetRow($sql);
	$module['base']=json_decode($module['base'],true);
 
    if($module['module_data_type'])
	{	
	$base=array();
	$base['dataid']=!empty($_POST['dataid'])?$_POST['dataid']:$module['base']['dataid'];
	$base['number']=!empty($_POST['number'])?$_POST['number']:$module['base']['number'];
	$base['target']=!empty($_POST['target'])?$_POST['target']:$module['base']['target'];
	$base['title']=!empty($_POST['title'])?$_POST['title']:$module['base']['title'];
	$base['link']=!empty($_POST['link'])?$_POST['link']:$module['base']['link'];
	$base['pagemode']=!empty($_POST['pagemode'])?$_POST['pagemode']:$module['base']['pagemode'];
	$base['pcnumber']=!empty($_POST['pcnumber'])?$_POST['pcnumber']:$module['base']['pcnumber'];
	$base['mbnumber']=!empty($_POST['mbnumber'])?$_POST['mbnumber']:$module['base']['mbnumber'];
	$base['pcshow']=!empty($_POST['pcshow'])?$_POST['pcshow']:$module['base']['pcshow'];
	$base['mbshow']=!empty($_POST['mbshow'])?$_POST['mbshow']:$module['base']['mbshow'];
  	$base['longitude']=!empty($_POST['longitude'])?$_POST['longitude']:$module['base']['longitude'];
  	$base['latitude']=!empty($_POST['latitude'])?$_POST['latitude']:$module['base']['latitude'];
  
	$arr=array();
    $arr['base']=json_encode($base,JSON_UNESCAPED_UNICODE);//php5.4 中文不转义
	$db -> autoExecute($ecs -> table('module'), $arr, 'UPDATE', " module_id = '".$module_id."' ");
	clear_cache_files();
	$link = array();
	$link[0]['text'] = '返回信息列表';
	$link[0]['href'] = 'module.php?act=list&' . list_link_postfix();
	$note = sprintf(stripslashes('信息' . $_POST['title'] . '修改成功'));
	sys_msg($note, 0, $link);
	}


	
	
}


//ajax 编辑模块数据
if ($_REQUEST['act'] == 'ajax_data_edit')
{
	check_authz_json('mmodule_list');
	$data_id = isset($_REQUEST['data_id'])?trim($_REQUEST['data_id']):0;
	$module_id = isset($_REQUEST['module_id'])?trim($_REQUEST['module_id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('module') . " WHERE module_id='$module_id'";
	$module = $db -> GetRow($sql);
	$module['data']=$module['data'."".$GLOBALS['select_lang'].""];


	$module_data_array = json_decode($module['data'], true);
	foreach($module_data_array as $key=>$value)
	{
    $data_array[$key] = json_decode($value, true);
    $data_array[$key]['content'] =  stripcslashes($data_array[$key]['content']);
    }
	$smarty -> assign('item', $data_array[$data_id]);//数据具体的记录编辑$_REQUEST['id']健值 2018

	$smarty -> assign('data_id', $data_id); 
	$smarty -> assign('module_id', $module_id); 
	$smarty -> assign('form_action', 'ajax_data_update'); 
	$str = $smarty -> fetch('ajax_data_edit.htm');
	$arr[] = array('id' => $module_id, 'str' => $str);
	make_json_result($arr);
}

//ajax 更新模块数据
if ($_REQUEST['act'] == 'ajax_data_update')
{       
	    $module_id = isset($_POST['module_id'])?trim($_POST['module_id']):0;
	    $data_id = isset($_POST['data_id'])?trim($_POST['data_id']):0;
	    $sql = "SELECT * FROM " . $ecs -> table('module') . " WHERE module_id='$module_id'";
	    $module = $db -> GetRow($sql);
    
	    $arr=array();
		$arr['title']=trim($_POST['title']);
		$arr['cat_name']=trim($_POST['cat_name']);
		$arr['spcdesc']=trim($_POST['spcdesc']);
		$arr['parent_id']=trim($_POST['parent_id']);
		$arr['content']=$_POST['content'];
		$arr['url']=$_POST['url'];

		$thumb=$mo->get_upload_img($_FILES['thumb'],ROOT_PATH.'data/images/');
		$arr['thumb']=empty($thumb)?trim($_POST['old_thumb']):$thumb;

		$thumbhover=$mo->get_upload_img($_FILES['thumbhover'],ROOT_PATH.'data/images/');
		$arr['thumbhover']=empty($thumbhover)?trim($_POST['old_thumbhover']):$thumbhover;

		
		$data=json_encode($arr,JSON_UNESCAPED_UNICODE);//编辑的数组转为json数据
		//传值  

		//入库前处理 
	    $arr=array();
		$module_data_array=array();
		if($module['data']){
		$module_data_array = json_decode($module['data'], true);
		$module_data_array[$data_id]=$data; //$_POST['id'] 健值$data为json
		$arr['data'."".$GLOBALS['select_lang'].""]=addslashes(json_encode($module_data_array,JSON_UNESCAPED_UNICODE));//5.4php 解决，中文unicode转码问题
		}
		//入库前处理 
        
      
		$db -> autoExecute($ecs -> table('module'), $arr, 'UPDATE', "  module_id='".$module_id."' ");
		$link[0]['text'] = '返回信息列表';
		$link[0]['href'] = 'module.php?act=edit&id='.$module_id;
		$note = sprintf(stripslashes('信息' . $_POST['title'] . '修改成功'));
		sys_msg($note, 0, $link);
}

if ($_REQUEST['act'] == 'ajax_data_add')
{
	check_authz_json('mmodule_list');
	$module_id = isset($_REQUEST['module_id'])?trim($_REQUEST['module_id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('module') . " WHERE module_id='$module_id'";
	$module = $db -> GetRow($sql);

	$module_data_array = json_decode($module['data'], true);
	foreach($module_data_array as $key=>$value)
	{
    $data_array[$key] = json_decode($value, true);
    $data_array[$key]['content'] =  stripcslashes($data_array[$key]['content']);
    }
	$smarty -> assign('item', $data_array[$data_id]);//数据具体的记录编辑$_REQUEST['id']健值 2018

	$smarty -> assign('module_id', $module_id); 
	$smarty -> assign('form_action', 'ajax_data_insert'); 
	$str = $smarty -> fetch('ajax_data_add.htm');
	$arr[] = array('id' => $module_id, 'str' => $str);
	make_json_result($arr);
}

if ($_REQUEST['act'] == 'ajax_data_insert')
{       $module_id = isset($_POST['module_id'])?trim($_POST['module_id']):0;
	    $data_id = isset($_POST['data_id'])?trim($_POST['data_id']):0;
	    $sql = "SELECT * FROM " . $ecs -> table('module') . " WHERE module_id='$module_id'";
	    $module = $db -> GetRow($sql);
		$module['data']=$module['data'."".$GLOBALS['select_lang'].""];

		$arr=array();
		$arr['title']=trim($_POST['title']);
		$arr['cat_name']=trim($_POST['cat_name']);
		$arr['spcdesc']=trim($_POST['spcdesc']);
		$arr['parent_id']=trim($_POST['parent_id']);
		$arr['content']=$_POST['content'];
		$arr['url']=$_POST['url'];
		$thumb=$mo->get_upload_img($_FILES['thumb'],ROOT_PATH.'data/images/');
		$arr['thumb']=empty($thumb)?trim($_POST['old_thumb']):$thumb;
		$thumbhover=$mo->get_upload_img($_FILES['thumbhover'],ROOT_PATH.'data/images/');
		$arr['thumbhover']=empty($thumbhover)?trim($_POST['old_hoverthumb']):$thumbhover;
		$data=json_encode($arr,JSON_UNESCAPED_UNICODE);//编辑的数组转为json数据
		//传值 
		

        //入库前处理 
	    $arr=array();
		$module_data_array=array();
		if($module['data']){
		$module_data_array=json_decode($module['data'],true);
		$module_data_array[]=$data;
		$arr['data'."".$GLOBALS['select_lang'].""]=addslashes(json_encode($module_data_array,JSON_UNESCAPED_UNICODE));
		}
		else
	    {
		$module_data_array[]=$data;
		$arr['data'."".$GLOBALS['select_lang'].""]=addslashes(json_encode($module_data_array,JSON_UNESCAPED_UNICODE));
		}

		
		


		
		




		//入库前处理 
		     
		$db -> autoExecute($ecs -> table('module'), $arr, 'UPDATE', "  module_id='".$module_id."' ");
		$link[0]['text'] = '返回信息列表';
		$link[0]['href'] = 'module.php?act=edit&id='.$module_id;
		$note = sprintf(stripslashes('信息' . $_POST['title'] . '添加成功'));
		sys_msg($note, 0, $link);
}


if ($_REQUEST['act'] == 'ajax_data_drop')
{
	check_authz_json('mmodule_list');
	$data_id = isset($_REQUEST['data_id'])?trim($_REQUEST['data_id']):0;
	$module_id = isset($_REQUEST['module_id'])?trim($_REQUEST['module_id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('module') . " WHERE module_id='$module_id'";
	$module = $db -> GetRow($sql);
	$module['data']=$module['data'."".$GLOBALS['select_lang'].""];

	$module_data_array = json_decode($module['data'], true);
	foreach($module_data_array as $key=>$value)
	{
    $data_array[$key] = json_decode($value, true);
    $data_array[$key]['content'] =  stripcslashes($data_array[$key]['content']);
    }
	$smarty -> assign('item', $data_array[$data_id]);//数据具体的记录编辑$_REQUEST['id']健值 2018

	$smarty -> assign('data_id', $data_id); 
	$smarty -> assign('module_id', $module_id); 
	$smarty -> assign('form_action', 'ajax_data_delete'); 
	$str = $smarty -> fetch('ajax_data_drop.htm');
	$arr[] = array('id' => $module_id, 'str' => $str);
	make_json_result($arr);

}


if ($_REQUEST['act'] == 'ajax_data_delete')
{
	check_authz_json('mmodule_list');
	$module_id = isset($_POST['module_id'])?trim($_POST['module_id']):0;
	$data_id = isset($_POST['data_id'])?trim($_POST['data_id']):0;
	$sql = "SELECT * FROM " . $ecs -> table('module') . " WHERE module_id='$module_id'";
	$module = $db -> GetRow($sql);
	$module['data']=$module['data'."".$GLOBALS['select_lang'].""];

	$arr=array();
	$module_data_array=array();
	if($module['data']){
	$module_data_array=json_decode($module['data'],true);
	unset($module_data_array[$data_id]);
	$arr['data'."".$GLOBALS['select_lang'].""]=addslashes(json_encode($module_data_array,JSON_UNESCAPED_UNICODE));
	}
	//入库前处理 

	$db -> autoExecute($ecs -> table('module'), $arr, 'UPDATE',  "  module_id='".$module_id."' ");

	$link[0]['text'] = '返回信息列表';
	$link[0]['href'] = 'module.php?act=edit&id='.$module_id;
	$note = sprintf(stripslashes('删除成功'));
	sys_msg($note, 0, $link);

}

 




/**
 * 获得信息列表
 */
function get_moduleslist() {
	$result = get_filter();
	if ($result === false) {
		$filter = array();
		$filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
			$filter['keyword'] = json_str_iconv($filter['keyword']);
		} 
		$filter['cat_id'] = empty($_REQUEST['cat_id']) ? 0 : intval($_REQUEST['cat_id']);
		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'a.module_id' : trim($_REQUEST['sort_by']);
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

		$where = '';
		if (!empty($filter['keyword'])) {
			$where = " AND a.module_id LIKE '%" . trim($filter['keyword']) . "%'";
		} 
		if ($filter['cat_id']) {
			$where .= " AND a." . get_module_children($filter['cat_id']);
		} 

		/**
		 * 信息总数
		 */
		$sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs'] -> table('module') . ' AS a ' .
		//'LEFT JOIN ' . $GLOBALS['ecs'] -> table('module_cat') .
		// ' AS ac ON ac.cat_id = a.cat_id ' .
		'WHERE 1 ' . $where;

		
		$filter['record_count'] = $GLOBALS['db'] -> getOne($sql);





		$filter = page_and_size($filter);

		/**
		 * 获取信息数据
		 */
		$sql = 'SELECT a.* ' . 'FROM ' . $GLOBALS['ecs'] -> table('module') . ' AS a ' .
			//'LEFT JOIN ' . $GLOBALS['ecs'] -> table('module_cat') . ' AS ac ON ac.cat_id = a.cat_id ' .
			'WHERE 1 ' . $where . ' ORDER by ' . $filter['sort_by'] . ' ' . $filter['sort_order'];

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

		$arr[] = $rows;
	} 
	return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
} 


?>