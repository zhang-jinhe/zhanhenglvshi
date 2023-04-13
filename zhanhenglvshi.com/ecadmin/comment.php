<?php

define('IN_ECS', true);

require(dirname(__FILE__) . '/includes/init.php');
include_once(ROOT_PATH . 'includes/cls_image.php');

if ($_REQUEST['act'] == 'list') {
	admin_priv('mcomment_list');	




	$smarty -> assign('full_page', 1);

	$comment_list = get_commentslist();

	$smarty -> assign('comment_list', $comment_list['arr']);
	$smarty -> assign('filter', $comment_list['filter']);
	$smarty -> assign('record_count', $comment_list['record_count']);
	$smarty -> assign('page_count', $comment_list['page_count']);

	$sort_flag = sort_flag($comment_list['filter']);
	$smarty -> assign($sort_flag['tag'], $sort_flag['img']);

	assign_query_info();
	$smarty -> display('comment_list.htm');
} 

/**
 * ------------------------------------------------------
 */
// -- 翻页，排序
/**
 * ------------------------------------------------------
 */
if ($_REQUEST['act'] == 'query') {
	admin_priv('mcomment_list');	
	$smarty -> assign('full_page', 1);

	$comment_list = get_commentslist();

	$smarty -> assign('comment_list', $comment_list['arr']);
	$smarty -> assign('filter', $comment_list['filter']);
	$smarty -> assign('record_count', $comment_list['record_count']);
	$smarty -> assign('page_count', $comment_list['page_count']);

	$sort_flag = sort_flag($comment_list['filter']);
	$smarty -> assign($sort_flag['tag'], $sort_flag['img']);

	assign_query_info();

	make_json_result($smarty -> fetch('comment_list.htm'), '', array('filter' => $comment_list['filter'], 'page_count' => $comment_list['page_count']));
} 


if ($_REQUEST['act'] == 'view') {
	admin_priv('mcomment_edit');
	$sql = "SELECT * FROM " . $ecs -> table('comment') . " WHERE comment_id='$_REQUEST[id]'";
	$comment = $db -> GetRow($sql);

	$comment['content']=$comment['content'."".$GLOBALS['select_lang'].""];


	$comment['content'] = json_decode($comment['content'],true);
    $smarty -> assign('content_list', $comment['content']);
	$smarty -> assign('ur_here', '信息查看');
    $smarty -> assign('form_action', 'update');
	assign_query_info();
	$smarty -> display('comment_info.htm');
} 



if ($_REQUEST['act'] == 'batch') {
	admin_priv('mcomment_remove');
	$comment_id = !empty($_REQUEST['id']) ? join(',', $_REQUEST['id']) : 0;
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('comment') . " WHERE comment_id " . db_create_in($comment_id);
	$GLOBALS['db'] -> query($sql);
    $result=array('err'=>0,'message'=>'删除成功');
	echo json_encode($result);
	
	
} 



if ($_REQUEST['act'] == 'remove') {
	admin_priv('mcomment_remove');
	$comment_id = intval($_REQUEST['id']);
	$sql = "DELETE FROM " . $GLOBALS['ecs'] -> table('comment') . " WHERE comment_id " . db_create_in($comment_id);
	$GLOBALS['db'] -> query($sql);
	$url = 'comment.php?act=query&' . str_replace('act=remove', '', $_SERVER['QUERY_STRING']);
	ecs_header("Location: $url\n");
	exit;
} 

function get_form_info($form_id) {
$sql = "SELECT * FROM " . $GLOBALS['ecs'] -> table('form') . " WHERE form_id='$form_id' ";
$form = $GLOBALS['db'] -> GetRow($sql);
return $form;

}
function get_cat_info($cat_id) {
$sql = "SELECT * FROM " . $GLOBALS['ecs'] -> table('article_cat') . " WHERE cat_id='$cat_id' ";
$cat = $GLOBALS['db'] -> GetRow($sql);
$cat['cat_name']=$cat['cat_name'."".$GLOBALS['select_lang'].""];
return $cat;

}

/**
 * 获得信息列表
 */
function get_commentslist() {
	$result = get_filter();
	if ($result === false) {
		$filter = array();
		$filter['keyword'] = empty($_REQUEST['keyword']) ? '' : trim($_REQUEST['keyword']);
		if (isset($_REQUEST['is_ajax']) && $_REQUEST['is_ajax'] == 1) {
			$filter['keyword'] = json_str_iconv($filter['keyword']);
		} 
		$filter['sort_by'] = empty($_REQUEST['sort_by']) ? 'a.comment_id' : trim($_REQUEST['sort_by']);
		$filter['sort_order'] = empty($_REQUEST['sort_order']) ? 'DESC' : trim($_REQUEST['sort_order']);

		$where = '';
		if (!empty($filter['keyword'])) {
			$where = " AND a.title LIKE '%" . mysql_like_quote($filter['keyword']) . "%'";
		} 
		if ($filter['cat_id']) {
			$where .= " AND a." . get_comment_children($filter['cat_id']);
		} 

		/**
		 * 信息总数
		 */
		$sql = 'SELECT COUNT(*) FROM ' . $GLOBALS['ecs'] -> table('comment') . ' AS a '  .  'WHERE langvs="'.$GLOBALS['select_lang'].'"   ' . $where;
		$filter['record_count'] = $GLOBALS['db'] -> getOne($sql);

		$filter = page_and_size($filter);

		/**
		 * 获取信息数据
		 */
		$sql = 'SELECT a.*  ' . 'FROM ' . $GLOBALS['ecs'] -> table('comment') . ' AS a ' . 'WHERE langvs="'.$GLOBALS['select_lang'].'"   ' . $where . ' ORDER by ' . $filter['sort_by'] . ' ' . $filter['sort_order'];

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
		if($rows['form_id']){
		$rows['form']=get_form_info($rows['form_id']);
		}
		if($rows['cat_id']){
		$rows['cat']=get_cat_info($rows['cat_id']);
		}
		$arr[] = $rows;
	} 
	return array('arr' => $arr, 'filter' => $filter, 'page_count' => $filter['page_count'], 'record_count' => $filter['record_count']);
} 


?>