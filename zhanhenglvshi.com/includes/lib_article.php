<?php



if (!defined('IN_ECS'))
{
die('Hacking attempt');
}

/**
* 获得文章分类下的文章列表
*
* @access  public
* @param   integer     $cat_id
* @param   integer     $page
* @param   integer     $size
*
* @return  array
*/
function get_cat_articles($cat_id, $page = 1, $size = 20 ,$requirement='')
{
$item_list = array_unique(array_merge(array($cat), array_keys(article_cat_list($cat_id, 0, false))));
foreach (array_filter($item_list) AS $item)
{
$cat_str.=$item.',';
}
$cat_str = substr($cat_str,0,strlen($cat_str)-1);
$sql = 'SELECT a.*' .' FROM ' .$GLOBALS['ecs']->table('article') . " AS a ".' WHERE a.is_show = 1   and  cat_id in(' . $cat_str .') ORDER BY  a.add_time DESC';
$res = $GLOBALS['db']->selectLimit($sql, $size, ($page-1) * $size);

$demos=csv_article_list(array_filter($item_list),$type=1);

$arr = array();
if ($res)
{
while ($row = $GLOBALS['db']->fetchRow($res))
{

$article_id = $row['article_id'];
$arr[$article_id]=$row;
$arr[$article_id]['id']          = $article_id;
$arr[$article_id]['title']       = $row['title'."".get_lang_code($_REQUEST['sitelang']).""];
$arr[$article_id]['thumb']     = (strpos($row['article_thumb'],'article_thumb') !== false)?$row['article_thumb']:'themes/default/data/'.$demos[$row['article_id']]['article_thumb'];
$arr[$article_id]['url']         = build_uri('article', array('aid'=>$article_id), $row['title']);
$arr[$article_id]['add_time']    = date($GLOBALS['_CFG']['date_format'], $row['add_time']);
$arr[$article_id]['description']=$row['description'];
$arr[$article_id]['spcdesc']=$row['spcdesc'."".get_lang_code($_REQUEST['sitelang']).""];
$arr[$article_id]['click_count']       = $row['click_count'];
$arr[$article_id]['keywords']       = $row['keywords'];
$arr[$article_id]['add_time_d'] = date('d', $row['add_time']);
$arr[$article_id]['add_time_ym'] = date('Y-m', $row['add_time']);
}
}

return $arr;
}


/**
* 获得指定分类下的文章总数
*
* @param   integer     $cat_id
*
* @return  integer
*/
function get_article_count($cat_id ,$requirement='')
{
global $db, $ecs;
if ($requirement != '')
{
$count = $db->getOne('SELECT COUNT(*) FROM ' . $ecs->table('article') . ' WHERE ' . get_article_children($cat_id) . ' AND  title like \'%' . $requirement . '%\'  AND is_show = 1');
}
else
{
$count = $db->getOne("SELECT COUNT(*) FROM " . $ecs->table('article') . " WHERE " . get_article_children($cat_id) . " AND is_show = 1");
}
return $count;
}











function get_extension($file)
{
return substr($file, strrpos($file, '.')+1);
}
?>