<?php
define('IN_ECS', true);

$urlhost=$_SERVER['HTTP_HOST'];
require(dirname(__FILE__) .'/includes/init.php');

if ($_REQUEST['act'] == 'main')
{
admin_priv('design_main');
assign_query_info();
$smarty->display('design.htm');
}



?>