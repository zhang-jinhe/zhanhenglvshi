<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php'); 
$id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;
$article = $mo -> moban_get_article_info($id);
$cat = $mo -> moban_get_article_cat_info($article['cat_id']);


$smallmoban = $cat['smallmoban'];
$smarty -> assign('article', $article);
$GLOBALS['smarty'] -> assign('cat_info', $cat);
$GLOBALS['smarty'] -> assign('page_title', $article['title'] ? htmlspecialchars($article['title']) : $article['title']);
$GLOBALS['smarty'] -> assign('keywords', htmlspecialchars($article['keywords']));
$GLOBALS['smarty'] -> assign('description', htmlspecialchars($article['description']));

$GLOBALS['db'] -> query("update " . $GLOBALS['ecs'] -> table('article') . " set click_count=click_count + 1 where    article_id = '$id'");


if(!empty($smallmoban))
{
$smarty -> display($smallmoban . '.shtml');
}
else
{
$smarty -> display('article.shtml');
} 
?>