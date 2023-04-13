<?php

define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php');







/*------------------------------------------------------ */
//-- 列表编辑 ?act=list_edit
/*------------------------------------------------------ */


if ($_REQUEST['act'] == 'list_edit')
{
    /* 检查权限 */
    admin_priv('mopen_config');

    

   

    $smarty->assign('setting_list',   get_settings());
  

    if (strpos(strtolower($_SERVER['SERVER_SOFTWARE']), 'iis') !== false)
    {
        $rewrite_confirm = $_LANG['rewrite_confirm_iis'];
    }
    else
    {
        $rewrite_confirm = $_LANG['rewrite_confirm_apache'];
    }
    $smarty->assign('rewrite_confirm', $rewrite_confirm);

   
    $smarty->assign('cfg', $_CFG);


    assign_query_info();
    $smarty->display('open_config.htm');
}


/*------------------------------------------------------ */
//-- 提交   ?act=post
/*------------------------------------------------------ */
elseif ($_REQUEST['act'] == 'post')
{
    
admin_priv('mopen_config');
$display_order=array_combine($_POST['field'],$_POST['code']);
//$display_order['mflink_list']='test';
//unset($display_order['mflink_list']);
//var_dump($display_order);
foreach($display_order as $key=>$key){
//	echo $key;
//	echo "<hr>";
$sql = "UPDATE " . $GLOBALS['ecs']->table('open_config') . " SET value='$display_order[$key]' WHERE code='$key'";
$GLOBALS['db']->query($sql);
}
$link = array();
$link[0] = array('href' => 'open_config.php?act=list_edit', 'text' => '编辑成功');
sys_msg('编辑成功', 0, $link);
}

/**
 * 设置系统设置
 *
 * @param   string  $key
 * @param   string  $val
 *
 * @return  boolean
 */
function update_configure($key, $val='')
{
    if (!empty($key))
    {
        $sql = "UPDATE " . $GLOBALS['ecs']->table('open_config') . " SET value='$val' WHERE code='$key'";

        return $GLOBALS['db']->query($sql);
    }

    return true;
}

/**
 * 获得设置信息
 *
 * @param   array   $groups     需要获得的设置组
 * @param   array   $excludes   不需要获得的设置组
 *
 * @return  array
 */
function get_settings()
{
    global $db, $ecs, $_LANG;
    /* 取出全部数据：分组和变量 */
    $sql = "SELECT * FROM " . $ecs->table('open_config') .
            " WHERE type<>'hidden' AND parent_id=1 ORDER BY parent_id, sort_order, id";
    $item_list = $db->getAll($sql);

    /* 整理数据 */
    $group_list = array();
    foreach ($item_list AS $key => $item)
    {
        
        $group_list[$key]=$item;

	}

    return $group_list;
}

?>