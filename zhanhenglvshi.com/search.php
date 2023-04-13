<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php'); 
$module=$mo->moban_get_module(trim($_REQUEST['search_id']));
$_REQUEST['Keyword']=trim($_REQUEST['Keyword']);
$cat_id = trim($module['base']['dataid']);
$cat = $mo -> moban_get_article_cat_info($cat_id);
$moban=$cat['moban'];
if(!empty($moban))
{
$smarty -> display($moban . '.shtml');
}
else
{
$smarty -> display('article_cat.shtml');
} 
?>