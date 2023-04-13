<?php



//信息开始
//信息管理，及信息分类管理菜单子项显示---------
$_LANG['marticle'] = '信息管理';
$_LANG['marticle_list'] = '内容';
$_LANG['marticle_add'] = '添加内容';
$_LANG['marticlecat_list'] = '栏目';
$_LANG['marticlecat_add'] = '添加栏目';
//信息管理，及信息分类管理菜单子项显示
$modules['marticle']['marticlecat_list']        = 'articlecat.php?act=list';
$modules['marticle']['marticlecat_add']        = 'articlecat.php?act=add';
$modules['marticle']['marticle_list']           = 'article.php?act=list';
$modules['marticle']['marticle_add']           = 'article.php?act=add';
//信息结束

$_LANG['mmodule'] = '模块管理';
$_LANG['mmodule_list'] = '模块列表';
$_LANG['mmodule_view'] = '页面模块';
$modules['mmodule']['mmodule_list']     = 'module.php?act=list';
$modules['mmodule']['mmodule_view']     = 'design.php?act=main';








//权限管理开始
//权限管理，及banner分类管理菜单子项显示---------
$_LANG['madmin'] = '权限管理';
$_LANG['madmin_list'] = '管理员列表';
//$_LANG['madmin_add'] = '添加管理员';
//$_LANG['madmin_role'] = '角色列表';
//$_LANG['madmin_role_add'] = '添加角色';
//权限管理，及banner分类管理菜单子项显示
$modules['madmin']['madmin_list'] = 'privilege.php?act=list';
//$modules['madmin']['madmin_add'] = 'privilege.php?act=add';
//$modules['madmin']['madmin_role'] = 'role.php?act=list';
//$modules['madmin']['madmin_role_add'] = 'role.php?act=add';
//权限管理结束






//系统设置开始
//系统设置管理部分的权限语言识别********************
$_LANG['msystem'] = '系统设置';
$_LANG['mweb_config'] = '基本配置';
//if($_OPE['mkefu_main']){
//$_LANG['mkefu_main'] = '客服管理';
//}
$_LANG['mcomment'] = '留言管理';
//$_LANG['mnavigator'] ='菜单管理';
//$_LANG['mcode'] ='渠道管理';
if($_OPE['mflink_list']){
$_LANG['mflink_list'] = '友情链接';
}




//系统设置管理，及信息分类管理菜单子项显示
$modules['msystem']['mweb_config']             = 'web_config.php?act=list_edit';

//if($_OPE['mkefu_main']){
//$modules['msystem']['mkefu_main'] = 'kefu.php?act=main';
//}



$modules['msystem']['mcomment'] = 'comment.php?act=list';

//$modules['msystem']['mnavigator']     = 'navigator.php?act=list';
//$modules['msystem']['mcode']     = 'code_manage.php?act=list';
if($_OPE['mflink_list']){
$modules['msystem']['mflink_list']       = 'friend_link.php?act=list';
}


if($_OPE['mhelp']){
//帮助管理开始
//帮助管理，及banner分类管理菜单子项显示---------
$_LANG['mhelp'] = '帮助管理';
$_LANG['mhelp_size'] = '尺寸说明';
$_LANG['mhelp_operation'] = '操作帮助';
$modules['mhelp']['mhelp_size'] = 'size.php?act=main';
$modules['mhelp']['mhelp_operation'] = 'http://help.zujianzhan.com/help/';
//帮助管理结束
}

?>