<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$define_url = trim($_REQUEST['defurl']);
$cat_id =  intval($define_url);
$cat = $mo -> moban_get_article_cat_info($cat_id);
$GLOBALS['smarty'] -> assign('cat_info', $cat);
$GLOBALS['smarty'] -> assign('page_title', $cat['title'] ? htmlspecialchars($cat['title']) : $cat['cat_name']);
$GLOBALS['smarty'] -> assign('keywords', htmlspecialchars($cat['keywords']));
$GLOBALS['smarty'] -> assign('description', htmlspecialchars($cat['description']));


$moban = $cat['moban'];
if(!empty($moban))
{
$smarty -> display($moban . '.shtml');
}
else
{
$smarty -> display('article_cat.shtml');
} 
?>