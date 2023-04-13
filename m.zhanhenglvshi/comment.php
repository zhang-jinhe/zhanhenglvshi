<?php
define('IN_ECS', true);
require(dirname(__FILE__) . '/includes/init.php'); 
$module=$mo->moban_get_module(trim($_REQUEST['ModuleID']));
$fields=$mo->moban_get_form_fields_list($module['base']['dataid']);
$langvs=$mo->moban_get_langvs();

//判断是不是直接内容
if(!empty($_POST['FormType'])&&$_POST['FormType']=='article')
{
$arrs=array();
$arr=array();
$arrs[]=array("head"=>$_POST['Lang_Contact'],"content"=>$_POST['Contact']);;
$arrs[]=array("head"=>$_POST['Lang_Tel'],"content"=>$_POST['Tel']);
$arrs[]=array("head"=>$_POST['Lang_Email'],"content"=>$_POST['Email']);
$arrs[]=array("head"=>$_POST['Lang_Content'],"content"=>$_POST['Content']);
$arr['content'."".$langvs['aftefix'].""]=json_encode($arrs,JSON_UNESCAPED_UNICODE);
$arr['form_id']=empty($module['base']['dataid'])?0:$module['base']['dataid'];
$arr['cat_id']=$_POST["cat_id"];
$arr['article_id']=$_POST["article_id"];
$arr['add_time'] = time();
$arr['langvs']=$langvs['aftefix'];
$db -> autoExecute($ecs -> table('comment'), $arr, 'INSERT');
$comment_id = $db -> insert_id();
echo "<script> {window.alert('".$_LANG['form_success']."');window.history.go(-1);} </script>";
}

//判断是不是留言表单自定义表表单
if(!empty($_POST['FormType'])&&$_POST['FormType']=='message')
{
	if(!$module['base']['dataid'])
	{
	echo "<script> {window.alert('".$_LANG['submit_notlegal_tips']."');window.history.back(-1);} </script>";
	die;
	}
	$arrs=array();
	$arr=array();	
	foreach($fields as $key=> $val)
	{
	$arrs[$key]['fields_id']=$val['id'];
	$arrs[$key]['head']=$fields_head=$val['reg_field_name'];
		if($val['type']=='checked')
		{
		$arrs[$key]['content']=$fields_content=join(',',$_POST["seller_info_".$val['id']]);
		}
		else
		{
		$arrs[$key]['content']=$fields_content=$_POST["seller_info"][$val['id']];
		}
		if(empty($arrs[$key]['content']))
		{
		echo "<script> {window.alert('".$_LANG['submit_empty_tips']."');window.history.back(-1);} </script>";
		die;
		}
	}
	$arr['content'."".$langvs['aftefix'].""]=json_encode($arrs,JSON_UNESCAPED_UNICODE);
	$arr['form_id']=$module['base']['dataid'];
	$arr['cat_id']=$_POST["cat_id"];
	$arr['add_time'] = time();
	$arr['langvs']=$langvs['aftefix'];
	$db -> autoExecute($ecs -> table('comment'), $arr, 'INSERT');
	$comment_id = $db -> insert_id();
	echo "<script> {window.alert('".$_LANG['form_success']."');window.history.go(-1);} </script>";

}
?>