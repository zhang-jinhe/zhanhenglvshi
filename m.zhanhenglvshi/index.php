<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');
$page_title = !empty($GLOBALS['_CFG']['title'])?$GLOBALS['_CFG']['title']:$GLOBALS['_CFG']['web_name'];
$smarty -> assign('page_title', $page_title); 
$smarty -> assign('keywords', htmlspecialchars($_CFG['keywords']));
$smarty -> assign('description', htmlspecialchars($_CFG['description']));
$smarty -> display('index.shtml');
?>