<?php
if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}
class module
{
    var $module_id = '';
    function __construct()
    {
        module::module();

    }

	function moban_get_langvs()
    {
	$sitelang=@$_REQUEST["sitelang"];
	$langvs=array();
	switch ($sitelang) 
	{
	case "lang1" :
	$langvs['langpack']='lang1';     //设置语言版本
	$langvs['buildlang']='lang1';    //设置链接
	$langvs['langhtm']='lang1.html'; //设置base 首页
	$langvs['aftefix']='_1';         //设置表字段值如data_1
	break;
	case "lang2" :
	$langvs['langpack']='lang2';     
	$langvs['buildlang']='lang2';
	$langvs['langhtm']='lang2.html';
	$langvs['aftefix']='_2';
	break;
	case "lang3" :
	$langvs['langpack']='lang3';     
	$langvs['buildlang']='lang3';	
	$langvs['langhtm']='lang3.html';
	$langvs['aftefix']='_3';
	break;
	default:
	$langvs['langpack']='lang';    
	$langvs['buildlang']='';
	$langvs['langhtm']='';
	$langvs['aftefix']='';
	}
    return $langvs;
	}

	function get_moban()
	{
	$arr = array();
	$sql = 'SELECT *  FROM ' . $GLOBALS['ecs']->table('moban') . ' ';
	$arr = $GLOBALS['db']->getRow($sql);
	$arr['web_name']=$arr['web_name'."".$this->langvs['aftefix'].""];
	$arr['keywords']=$arr['keywords'."".$this->langvs['aftefix'].""];
	$arr['description']=$arr['description'."".$this->langvs['aftefix'].""];
	$arr['stats_code']=html_entity_decode($arr['stats_code'."".$this->langvs['aftefix'].""]);

	$GLOBALS['smarty']->assign("cfg", $arr);

	
	return $arr;
	}



    function moban_get_lang()
    {
	require(ROOT_PATH.'lang/'."".$this->langvs['langpack']."".'/common.php');

	return $_LANG;

    }



    function module()
    {
      
	 
      $this->langvs = $this->moban_get_langvs();
   
	  $module_list = $GLOBALS['db']->getAll("SELECT * FROM " . $GLOBALS['ecs']->table('module') . " WHERE  1  ");
	  foreach($module_list as $module) {
      $contentarray = array();


	 

	
	$contentarray = json_decode($module['data'."".$this->langvs['aftefix'].""], true);
	


	$arr = array();
	if (is_array($contentarray)) {
		foreach ($contentarray as $key => $row) {
			$listarray            = json_decode($row, true);
			$arr[$key]            = $listarray;
			$arr[$key]['content'] = stripslashes($listarray["content"]);
			
		}
	}
	
    switch($module['module_type']) {
                    case "menus":
						$GLOBALS['smarty']->assign("moban_get_navigator_list()",$this->moban_get_navigator_list(0,0));
                        break;
                    case "articlelist":
						$GLOBALS['smarty']->assign("moban_get_cat_article_list(". $module['module_id'] . ")", $this->moban_get_cat_article_list($module));
                        break;
                    case "catlist":
						
						$GLOBALS['smarty']->assign("moban_get_relative_cat_list(". $module['module_id'] . ")", $this->moban_get_relative_cat_list($module));
                        break;
                    case "infolist":
						$GLOBALS['smarty']->assign("moban_get_cat_article_list(". $module['module_id'] . ")", $this->moban_get_cat_article_list($module));
                        break;
					case "form":
						$base = json_decode($module['base'], true);
						$form_id  = isset($base['dataid'])?$base['dataid']:0;
						$GLOBALS['smarty']->assign("moban_get_form_fields_list(". $module['module_id'] . ")", $this->moban_get_form_fields_list($form_id));
                        break;
					case "video":
                        $module['base']                                         = json_decode($module['base'], true);
				     	$module['base']['videosrc']=html_entity_decode(stripslashes($module['base']['videosrc']));
						$GLOBALS['smarty']->assign("moban_get_video(". $module['module_id'] . ")", array($module['base']));
                        break;
					case "map":
                        $module['base']                                         = json_decode($module['base'], true);
						$GLOBALS['smarty']->assign("moban_get_map(". $module['module_id'] . ")", array($module['base']));
                        break;
					case "location":
						 $module['base']                                         = json_decode($module['base'], true);
						$GLOBALS['smarty']->assign("moban_get_location(". $module['module_id'] . ")", $this->moban_get_location($module));
					    break;
					default:
					    $GLOBALS['smarty']->assign("moban_get_module_data_list(". $module['module_id'] . ")", $arr);	
				        break;       
                }
     }	

	
	
	}

    function moban_get_pager($url, $param, $record_count, $page = 1, $size = 10)
    {
        $size = intval($size);
        if ($size < 1) {
            $size = 10;
        }
        
        $page = intval($page);
        if ($page < 1) {
            $page = 1;
        }
        
        $record_count = intval($record_count);
        
        $page_count = $record_count > 0 ? intval(ceil($record_count / $size)) : 1;
        if ($page > $page_count) {
            $page = $page_count;
        }
        /* 分页样式 */
        $pager['styleid'] = isset($GLOBALS['_CFG']['page_style']) ? intval($GLOBALS['_CFG']['page_style']) : 0;
        
        $page_prev = ($page > 1) ? $page - 1 : 1;
        $page_next = ($page < $page_count) ? $page + 1 : $page_count;
        
        /* 将参数合成url字串 */
        $param_url = '?';
        foreach ($param AS $key => $value) {
            $param_url .= $key . '=' . $value . '&';
        }
        
        $pager['url']          = $url;
        $pager['start']        = ($page - 1) * $size;
        $pager['page']         = $page;
        $pager['size']         = $size;
        $pager['record_count'] = $record_count;
        $pager['page_count']   = $page_count;
        
        if ($pager['styleid'] == 0) {
            //$pager['page_first']   = $url . $param_url . 'page=1';
            //$pager['page_prev']    = $url . $param_url . 'page=' . $page_prev;
            // $pager['page_next']    = $url . $param_url . 'page=' . $page_next;
            // $pager['page_last']    = $url . $param_url . 'page=' . $page_count;
            
            $pager['page_first'] = $param['defurl'] . '-1/';
            $pager['page_prev']  = $param['defurl'] . '-' . $page_prev . '/';
            $pager['page_next']  = $param['defurl'] . '-' . $page_next . '/';
            $pager['page_last']  = $param['defurl'] . '-' . $page_count . '/';
            
            $pager['array'] = array();
            for ($i = 1; $i <= $page_count; $i++) {
                $pager['array'][$i] = $i;
            }
        } else {
            $_pagenum = 10; // 显示的页码
            $_offset  = 2; // 当前页偏移值
            $_from    = $_to = 0; // 开始页, 结束页
            if ($_pagenum > $page_count) {
                $_from = 1;
                $_to   = $page_count;
            } else {
                $_from = $page - $_offset;
                $_to   = $_from + $_pagenum - 1;
                if ($_from < 1) {
                    $_to   = $page + 1 - $_from;
                    $_from = 1;
                    if ($_to - $_from < $_pagenum) {
                        $_to = $_pagenum;
                    }
                } elseif ($_to > $page_count) {
                    $_from = $page_count - $_pagenum + 1;
                    $_to   = $page_count;
                }
            }
            $url_format           = $url . $param_url . 'page=';
            $pager['page_first']  = ($page - $_offset > 1 && $_pagenum < $page_count) ? $url_format . 1 : '';
            $pager['page_prev']   = ($page > 1) ? $url_format . $page_prev : '';
            $pager['page_next']   = ($page < $page_count) ? $url_format . $page_next : '';
            $pager['page_last']   = ($_to < $page_count) ? $url_format . $page_count : '';
            $pager['page_kbd']    = ($_pagenum < $page_count) ? true : false;
            $pager['page_number'] = array();
            for ($i = $_from; $i <= $_to; ++$i) {
                $pager['page_number'][$i] = $url_format . $i;
            }
        }
        $pager['search'] = $param;
        
        return $pager;
    }
   function moban_get_module($module_id)
    {
      
	 


	  $module = $GLOBALS['db']->getRow("SELECT * FROM " . $GLOBALS['ecs']->table('module') . " WHERE module_id='".$module_id."'  ");
	  

	  
	  $module['data']=$module['data'."".$this->langvs['aftefix'].""];
	  $module['base']=json_decode($module['base'], true);


return $module;


	  }
	
    
    /**
	 * 获取菜单列表
	 *
	 * @param   string      cat_id       分类id
	 *
	 */ 

	function moban_get_navigator_list($pid,$level) {
	$filter = array();
	$where = " where  is_nav_show=1 and is_show=1 ";
	$sql = " SELECT c.*  FROM ".$GLOBALS['ecs']->table('article_cat'). " as c ".
	" $where  order by sort_order asc";
	$res = $GLOBALS['db']->getAll($sql);
    
	foreach ($res AS $idx => $row) {
    
	$arrs[$idx]['cat_name']    = $row['cat_name'."".$this->langvs['aftefix'].""];
	$arrs[$idx]['cat_id']    = $row['cat_id'];
	$arrs[$idx]['ifxiala']    = $row['ifxiala'];
	$arrs[$idx]['url']       = !empty($row['jump_url'])?$row['jump_url']:$this->build_uri('article_cat', array('acid' => $row['cat_id']));
	$arrs[$idx]['catlist']     = $this->moban_get_cat_list($row['cat_id'], 0);
	$arrs[$idx]['articlelist'] = $this->get_relative_cat_article_list($row['cat_id']);

	
	}


   return $arrs;
			
	}

    
   
    
    
    /**
	 * 获取当前分类的文章列表（不包含子分类的文章列表,分类id必须存在）
	 *
	 * @param   string      cat_id       分类id
	 *
	 */      
    function get_relative_cat_article_list($cat_id)
    {
        $sql = " SELECT *   FROM " . $GLOBALS['ecs']->table("article") . " where cat_id=" . $cat_id . "   order by add_time desc";
        $res = $GLOBALS['db']->getAll($sql);
        $arr = array();
        foreach ($res AS $idx => $row) {
            $arr[$idx]          = $row;
            $arr[$idx]['id']    = $row['article_id'];
            $arr[$idx]['title'] = $row['title'."".$this->langvs['aftefix'].""];
            
            $arr[$idx]['add_time'] = $row['add_time'];
            $arr[$idx]['url']      = module::build_uri('article', array('aid' => $row['article_id']), $row['title']);
            $arr[$idx]['cat_url']  = module::build_uri('article_cat', array( 'acid' => $row['cat_id'] ));

        }
        return $arr;
    }


    /**
	 * 获取当前分类的文章列表（包含子分类的文章列表，id可以为空，根据页面判断）
	 *
	 * @param   string      cat_id       分类id
	 *
	 */ 

function moban_get_cat_article_list($module)
    {
	//重要重要 
	    $base = json_decode($module['base'], true);
	    
	    
	   
			
			
		$cat_id  = isset($base['dataid'])?$base['dataid']:0;
        $size=!empty($base['number'])?intval($base['number']):10;
		$page = @$_REQUEST['page']? intval(@$_REQUEST['page']) : 1;
		
	    $arr = array();
		$cat_id=module::moban_get_now_cat_id($cat_id);
       

	   $_REQUEST['Keyword']=empty($_REQUEST['Keyword'])?'':$_REQUEST['Keyword'];

        
        $where=' where 1 ';
		if($cat_id)
		{
		$sql = "SELECT * "." FROM " .$GLOBALS['ecs']->table("article_cat") ." where parent_id='".$cat_id."'   ";
        $cat_child=$GLOBALS['db']->getCol($sql);
		$cat_child[]=$cat_id;//追加当前ID
		$s_cat_child_obj[]=$cat_child;
		foreach($cat_child as $val)
		{
		if($val){
		$sql = "SELECT * "." FROM " .$GLOBALS['ecs']->table("article_cat") ." where parent_id='".$val."'  ";
		$s_cat_child_arr=$GLOBALS['db']->getCol($sql);
		$s_cat_child_obj[]=  $s_cat_child_arr;
		}
		}
		$s_cat_child=array_reduce($s_cat_child_obj, 'array_merge', array());

	

        
		$cat_child_str=is_array($s_cat_child)?join($s_cat_child,','):$s_cat_child;
		$where.=empty($cat_child_str)? '' : " and cat_id in(".$cat_child_str.") " ;



		}
		else
		{
		$where.= " and cat_id = 0 " ;
		}
		if($_REQUEST['Keyword'])
		{
			
		$where.=' and title'.$this->langvs['aftefix'].' like \'%' . trim($_REQUEST['Keyword']) . '%\' ';
		
		}

        $page  = $page;

		
        
		$count = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('article') . $where . " AND is_show = 1");
        $pages = ($count > 0) ? ceil($count / $size) : 1;
		

        if ($page > $pages) {
            $page = $pages;
        }
		
		
        $pager['search']['id'] = $cat_id;
        $keywords              = '';
        $go_keywords           = ''; //继续传递的搜索关键词
        
        
        $sql = 'SELECT * ' . ' FROM ' . $GLOBALS['ecs']->table('article') .  $where . ' and is_show = 1  ORDER BY  add_time DESC';
       
        
        $res = $GLOBALS['db']->selectLimit($sql, $size, ($page - 1) * $size);
               
         
        if ($res) {
            while ($row = $GLOBALS['db']->fetchRow($res)) {
                
                $article_id                      = $row['article_id'];
                $arr[$article_id]                = $row;
                $arr[$article_id]['id']          = $article_id;
                $arr[$article_id]['title']       = $row['title'."".$this->langvs['aftefix'].""];
                $arr[$article_id]['thumb']       = $row['article_thumb'];
                $arr[$article_id]['url']         = module::build_uri('article', array('aid' => $article_id));
                $arr[$article_id]['add_time']    = date('Y-m-d', $row['add_time']);
                $arr[$article_id]['description'] = $row['description'."".$this->langvs['aftefix'].""];
                $arr[$article_id]['spcdesc']     = $row['spcdesc'."".$this->langvs['aftefix'].""];
                $arr[$article_id]['click_count'] = $row['click_count'];
                $arr[$article_id]['keywords']    = $row['keywords'."".$this->langvs['aftefix'].""];
                $arr[$article_id]['add_time_d']  = date('d', $row['add_time']);
                $arr[$article_id]['add_time_ym'] = date('Y-m', $row['add_time']);
            }
        }

      
	$GLOBALS['smarty']->assign("pager".$module['module_id'], module::moban_get_pager('article_cat.php', array('defurl' => $cat_id ), $count, $page, $size));


                       



        
        return $arr;
    }

    function moban_get_location($module)
    {
	
		$cur_url = basename($_SERVER["PHP_SELF"]);
		$filename = strpos($cur_url,'-') ? substr($cur_url, 0, strpos($cur_url,'-')) : substr($cur_url, 0, -4);
		$location_arr=array();
	    $lang=$this->moban_get_lang();
		
		switch($filename)
		{
		case "search":
				$loc_arr[0]['index']='搜索结果';
				$loc_arr[0]['href']='javascript:;';
				break;
				case "index":
				$loc_arr[0]['index']=$lang['home'];
				$loc_arr[0]['href']='javascript:;';
				break;
				case "article_cat":
				$loc_arr[0]['index']=$lang['home'];
				$loc_arr[0]['href']='javascript:;';
				$cat_id = intval($_GET['defurl']);
				$parent_cat_list=array_reverse($this->moban_get_article_cat_parent_cats($cat_id));
				foreach($parent_cat_list as $key=>$cat)
				{ 
				$loc_arr['list'][$key]['separatormark']=$module['base']['separatormark'];
				
				$loc_arr['list'][$key]['name']=$cat['cat_name'];
				}
				$loc_arr[]=$loc_arr;
				break;
				case "article":
				$article_id = intval($_GET['id']);
				$article=$this->moban_get_article_info($article_id);
				$cat_id = $article['cat_id'];
				$parent_cat_list=array_reverse($this->moban_get_article_cat_parent_cats($cat_id));
				$icount=0;
				foreach($parent_cat_list as $key=>$cat)
				{ 
				$base_arr['list'][$key]['separatormark']=$module['base']['separatormark'];
				$base_arr['list'][$key]['name']=$cat['cat_name'];
				$icount++;
				}
				$loc_arr[0]['index']=$lang['home'];
				$loc_arr[0]['href']='javascript:;';
                $loc_arr[]=$base_arr;
				$loc_arr[$icount]['href']='javascript:;';
				$loc_arr[$icount]['title']=$article['title'];

				$loc_arr[$module['module_id']]=$loc_arr;
				
				break;
				default:
				$loc_arr[0]['index']=$lang['home'];
				$loc_arr[0]['href']='javascript:;';
				break;
		}
		
		return $loc_arr;


			

        
    }

/**
	 * 模板当前分类列表
	 *
	 * @param   string      cat_id      分类id
	 *
	 */
    
   function moban_get_now_cat_id($cat_id)
    {
	if(!in_array($cat_id,module::moban_get_all_cat_id()))
	{
	$cat_id=empty($_REQUEST['defurl'])?0:$_REQUEST['defurl'];//当前ID
	}
	else
	{
	$cat_id=$cat_id;
	}

	return $cat_id;
    }


	function moban_get_top_cat_id($cat_id)
    {
	if(in_array($cat_id,module::moban_get_all_cat_id()))
	{
	$current_cat_id=$cat_id;
	}
	else
	{
	$cat_id=empty($_REQUEST['defurl'])?0:$_REQUEST['defurl'];//当前ID
	
	$catlist = array();
    $current_cat_id=0;

	if($cat_id){
	foreach(module::moban_get_article_cat_parent_cats($cat_id) as $k=>$v)
	{
	$catlist[] = $v['cat_id'];
	}
	$current_cat_id=$catlist[count($catlist)-1];//获取顶级分类
	}
	}

	return $current_cat_id;
    }





	function moban_get_relative_cat_list($module)
    {
    
	 $base = json_decode($module['base'], true);
		$cat_id  = isset($base['dataid'])?$base['dataid']:0;


    $where=" where 1 ";

 
	if(!in_array($cat_id,module::moban_get_all_cat_id()))
	{
	$cur_url = basename($_SERVER["PHP_SELF"]);
	$filename = strpos($cur_url,'-') ? substr($cur_url, 0, strpos($cur_url,'-')) : substr($cur_url, 0, -4);
		//当页面是详情内页时
		if($filename=='article')
		{
		 $article_id=empty($_REQUEST['id'])?0:intval($_REQUEST['id']);
         $article=module::moban_get_article_info($article_id);
		 $cat_id=$article['cat_id'];
				if($cat_id){
				foreach(module::moban_get_article_cat_parent_cats($cat_id) as $k=>$v)
				{
				$catlist[] = $v['cat_id'];
				}
				$cat_id=$catlist[count($catlist)-1];//获取顶级分类
				}
				
		}
		//当页面是列表内页时
		if($filename=='article_cat')
		{
		 $cat_id=empty($_REQUEST['defurl'])?0:intval($_REQUEST['defurl']);
				if($cat_id){
				foreach(module::moban_get_article_cat_parent_cats($cat_id) as $k=>$v)
				{
				$catlist[] = $v['cat_id'];
				}
				$cat_id=$catlist[count($catlist)-1];//获取顶级分类
				}
				
		}

		//当页面是首页时
		if($filename=='index')
		{
		 $cat_id=0;		
		}

		//当页面是搜索时
		if($filename=='search')
		{
		 
		//当执行搜索时
		$_REQUEST['search_id']=empty($_REQUEST['search_id'])?'':$_REQUEST['search_id'];
		$where=" where 1 ";
		if($_REQUEST['search_id'])
		{
		$module=module::moban_get_module($_REQUEST['search_id']);
		$cat_id=$module['base']['dataid'];
		}
		
		}




	}




	$current_cat_id=module::moban_get_top_cat_id($cat_id);



    $res = $GLOBALS['db']->getAll(" SELECT *   FROM " . $GLOBALS['ecs']->table('article_cat') . " where  cat_id=" . $current_cat_id . " ");

	$arrs=array();
	foreach ($res AS $idx => $row) {
	$arrs[$idx]['cat_name']         = $row['cat_name'."".$this->langvs['aftefix'].""];
	$arrs[$idx]['url']              = module::build_uri('article_cat', array('acid' => $row['cat_id']));
	$arrs[$idx]['article_cat_list'] = module::moban_get_cat_list($current_cat_id,0);
	$arrs[$idx]['article_list'] = module::get_relative_cat_article_list($current_cat_id);
	}   
   
	return $arrs;        
    }

	function moban_get_all_cat_id()
    {   
    return $GLOBALS['db'] -> getCol(" SELECT cat_id   FROM " . $GLOBALS['ecs']->table('article_cat') . " where   1   ");
	}

    function moban_get_child_cat_list($cat_id) {
	$arrs = array();

	$where = ' where  parent_id='.$cat_id.' ';
	$sql = " SELECT c.*  FROM ".$GLOBALS['ecs']->table('article_cat'). " as c ".
	" $where  order by sort_order asc";
	$res = $GLOBALS['db']->getAll($sql);
	foreach ($res AS $idx => $row) {
	$arrs[$idx]['cat_name']         = $row['cat_name'."".$this->langvs['aftefix'].""];
    $arrs[$idx]['url']       = !empty($row['jump_url'])?$row['jump_url']:module::build_uri('article_cat', array('acid' => $row['cat_id']));

	}
	
	return $arrs;
			
	}

	function moban_get_cat_list($pid,$level) {
	$filter = array();
	$where = ' where  1 ';
	$sql = " SELECT c.*  FROM ".$GLOBALS['ecs']->table('article_cat'). " as c ".
	" $where  order by sort_order asc";
	$res = $GLOBALS['db']->getAll($sql);
			$list = array();    //定义静态数组
			foreach ($res as $key => $value){        //第一次遍历,找到pid=0的节点
	 
				if ($value['parent_id'] == $pid){            //pid为0的节点，是第一级，也就是顶级分类
					$flg = str_repeat('',$level);
					$nbsp = str_repeat('&nbsp;&nbsp;',$level);
					$value['level'] = $level;
					$value['level_nbsp']= $nbsp; 
					$value['cat_name']= $value['cat_name'."".$this->langvs['aftefix'].""];
					$value['url']              = module::build_uri('article_cat', array('acid' => $value['cat_id']));
                    $value['article_cat_list'] = module::moban_get_cat_list($value['cat_id'],$level+1);
					
					$list[] = $value;                    //把数组放到list中
					
					unset($res[$key]);                //把这个节点从数组中移除,减少后续递归消耗
					
					module::moban_get_cat_list($value['cat_id'], $level+1);            //开始递归,查找父id为该节点id的节点,级别则为原级别+1
	 
				}
			}
			return $list;
			
	}

	function moban_get_article_cat_parent_cats($cat)
    {
        
        
        if ($cat == 0) {
            return array();
        }
        
        $arr = $GLOBALS['db']->GetAll('SELECT cat_id, cat_name'.$this->langvs['aftefix'].', parent_id FROM ' . $GLOBALS['ecs']->table('article_cat') . ' where 1 ');
        
        if (empty($arr)) {
            return array();
        }
        
        $index = 0;
        $cats  = array();
        
        while (1) {
            foreach ($arr AS $row) {
                if ($cat == $row['cat_id']) {
                    $cat = $row['parent_id'];
                    
                    $cats[$index]['cat_id']   = $row['cat_id'];
                    $cats[$index]['cat_name'] = $row['cat_name'."".$this->langvs['aftefix'].""];
                    
                    $index++;
                    break;
                }
            }
            
            if ($index == 0 || $cat == 0) {
                break;
            }
        }
        
        return $cats;
    }

 function build_uri($app, $params, $append = '', $page = 0, $keywords = '', $size = 0)
    {
        static $rewrite = NULL;
        
        if ($rewrite === NULL) {
            $rewrite = 2;
            
        }
        
        $args = array(
            'acid' => 0,
            'aid' => 0,
            'sort' => '',
            'order' => ''
        );
        
        extract(array_merge($args, $params));
        
        $uri = '';


		$build_lang=($this->langvs['buildlang'])?$this->langvs['buildlang'].'-':'';

        
        switch ($app) {
            
            case 'article_cat':
                if (empty($acid)) {
                    return false;
                } else {
                    if ($rewrite) {
                        
                        $define_url_article_cat = $GLOBALS['db']->getOne("select define_url from " . $GLOBALS['ecs']->table('article_cat') . " where cat_id='$acid' limit 0,1");
                        $uri                    = $define_url_article_cat ? $build_lang.trim($define_url_article_cat) : '' .$build_lang. $acid;
                        
                        if (!empty($page)) {
                            if ($page == 1) {
                                $uri .= '';
                            } else {
                                $uri .= '-' . $page;
                            }
                            
                        }
                        if (!empty($sort)) {
                            $uri .= '-' . $sort;
                        }
                        if (!empty($order)) {
                            $uri .= '-' . $order;
                        }
                        if (!empty($keywords)) {
                            $uri .= '-' . $keywords;
                        }
                    } else {
                        $uri = 'article_cat.php?id=' . $acid;
                        if (!empty($page)) {
                            $uri .= '&amp;page=' . $page;
                        }
                        if (!empty($sort)) {
                            $uri .= '&amp;sort=' . $sort;
                        }
                        if (!empty($order)) {
                            $uri .= '&amp;order=' . $order;
                        }
                        if (!empty($keywords)) {
                            $uri .= '&amp;keywords=' . $keywords;
                        }
                    }
                }
                
                break;
            
            case 'article':
                if (empty($aid)) {
                    return false;
                } else {
                    
                    $sql               = "select ac.cat_id, ac.define_url from " . $GLOBALS['ecs']->table('article') . "	AS a left join " . $GLOBALS['ecs']->table('article_cat') . " AS ac on a.cat_id=ac.cat_id where a.article_id='$aid' limit 0,1";
                    $cat_array         = $GLOBALS['db']->getRow($sql);
                    $define_url_artcat = $cat_array['define_url'] ? $build_lang.$cat_array['define_url'] . '/' : '' .$build_lang. $cat_array['cat_id'] . '/';
                    $uri               = $rewrite ? $define_url_artcat . $aid : 'article.php?id=' . $aid;
                    
                }
                
                break;
            
            
            case 'article_search':
                break;
            default:
                return false;
                break;
        }
        
        if ($rewrite) {
            if ($rewrite == 2 && !empty($append)) {
                $uri .= '-' . urlencode(preg_replace('/[\.|\/|\?|&|\+|\\\|\'|"|,]+/', '', $append));
            }
            
            
            if ($app == 'article_cat') {
                $uri .= '/';
            } else {
                $uri .= '.html';
            }
            
        }
        if (($rewrite == 2) && (strpos(strtolower(EC_CHARSET), 'utf') !== 0)) {
            $uri = urlencode($uri);
        }
        return $uri;
    }

function get_upload_img($files,$dirpathname){
    $article_thumb='';
	$upFile = $files;

    $re_type = module::check_file_type($upFile['tmp_name'], $upFile['name'], '|png|jpg|jpeg|gif|doc|xls|txt|zip|ppt|pdf|rar|docx|xlsx|pptx|');
	if($re_type){
	if ($upFile) {
	//判断文件是否为空或者出错
		if($upFile['error']=='0'&&!empty($upFile)){
		$file_name=module::unique_name($dirpathname);
		$ex_name=module::get_filetype($upFile['name']);
		$dir_move_name=$dirpathname.$file_name.$ex_name;;
			if(move_uploaded_file($upFile['tmp_name'],$dir_move_name))
			{
			$article_thumb = str_replace(ROOT_PATH,'/',$dir_move_name);
			}
		}
	}	
	}
    return $article_thumb;

	}


	function get_upload_imglist($files,$thumb_arr,$dirpathname){
	
    $upFile = $files;
	$array_thumb=array();
    $article_array_thumb='';
	$list['thumb_list']=is_array(json_decode($thumb_arr,true))?json_decode($thumb_arr,true):array();
	for ($i=0;$i<count($upFile['tmp_name']);$i++)
	{
	$re_type = module::check_file_type($upFile['tmp_name'][$i], $upFile['name'][$i], '|png|jpg|jpeg|gif|doc|xls|txt|zip|ppt|pdf|rar|docx|xlsx|pptx|');
	$ex_name=module::get_filetype($upFile['name'][$i]);
	if($ex_name)
	{
		if($re_type){
		$file_name=module::unique_name($dirpathname);
		$dir_move_name=$dirpathname.$file_name.$ex_name;
		move_uploaded_file($upFile['tmp_name'][$i],$dir_move_name);
		$array_thumb[$i]['article_array_thumb'] =str_replace(ROOT_PATH,'/',$dir_move_name);
		}
		else
		{
		$array_thumb[$i]['article_array_thumb'] =!empty($list['thumb_list'][$i]["article_array_thumb"])?$list['thumb_list'][$i]["article_array_thumb"]:'';
		}
	}
	else
	{
	$array_thumb[$i]['article_array_thumb'] =!empty($list['thumb_list'][$i]["article_array_thumb"])?$list['thumb_list'][$i]["article_array_thumb"]:'';
	}
    
	}
	$article_array_thumb=json_encode($array_thumb);
    return $article_array_thumb;
	}


    function random_filename()
    {
        $str = '';
        for($i = 0; $i < 9; $i++)
        {
            $str .= mt_rand(0, 9);
        }

        return gmtime() . $str;
    }
	function get_filetype($path)
    {
        $pos = strrpos($path, '.');
        if ($pos !== false)
        {
            return substr($path, $pos);
        }
        else
        {
            return '';
        }
    }
	function unique_name($dir)
    {
        $filename = '';
        while (empty($filename))
        {
            $filename = module::random_filename();
            if (file_exists($dir . $filename . '.jpg') || file_exists($dir . $filename . '.gif') || file_exists($dir . $filename . '.png'))
            {
                $filename = '';
            }
        }

        return $filename;
    }
	function check_file_type($filename, $realname = '', $limit_ext_types = '')
{
    if ($realname)
    {
        $extname = strtolower(substr($realname, strrpos($realname, '.') + 1));
    }
    else
    {
        $extname = strtolower(substr($filename, strrpos($filename, '.') + 1));
    }

    if ($limit_ext_types && stristr($limit_ext_types, '|' . $extname . '|') === false)
    {
        return '';
    }

    $str = $format = '';

    $file = @fopen($filename, 'rb');
    if ($file)
    {
        $str = @fread($file, 0x400); // 读取前 1024 个字节
        @fclose($file);
    }
    else
    {
        if (stristr($filename, ROOT_PATH) === false)
        {
            if ($extname == 'jpg' || $extname == 'jpeg' || $extname == 'gif' || $extname == 'png' || $extname == 'doc' ||
                $extname == 'xls' || $extname == 'txt'  || $extname == 'zip' || $extname == 'rar' || $extname == 'ppt' ||
                $extname == 'pdf' || $extname == 'rm'   || $extname == 'mid' || $extname == 'wav' || $extname == 'bmp' ||
                $extname == 'swf' || $extname == 'chm'  || $extname == 'sql' || $extname == 'cert'|| $extname == 'pptx' || 
                $extname == 'xlsx' || $extname == 'docx')
            {
                $format = $extname;
            }
        }
        else
        {
            return '';
        }
    }

    if ($format == '' && strlen($str) >= 2 )
    {
        if (substr($str, 0, 4) == 'MThd' && $extname != 'txt')
        {
            $format = 'mid';
        }
        elseif (substr($str, 0, 4) == 'RIFF' && $extname == 'wav')
        {
            $format = 'wav';
        }
        elseif (substr($str ,0, 3) == "\xFF\xD8\xFF")
        {
            $format = 'jpg';
        }
        elseif (substr($str ,0, 4) == 'GIF8' && $extname != 'txt')
        {
            $format = 'gif';
        }
        elseif (substr($str ,0, 8) == "\x89\x50\x4E\x47\x0D\x0A\x1A\x0A")
        {
            $format = 'png';
        }
        elseif (substr($str ,0, 2) == 'BM' && $extname != 'txt')
        {
            $format = 'bmp';
        }
        elseif ((substr($str ,0, 3) == 'CWS' || substr($str ,0, 3) == 'FWS') && $extname != 'txt')
        {
            $format = 'swf';
        }
        elseif (substr($str ,0, 4) == "\xD0\xCF\x11\xE0")
        {   // D0CF11E == DOCFILE == Microsoft Office Document
            if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'doc')
            {
                $format = 'doc';
            }
            elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xls')
            {
                $format = 'xls';
            } elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'ppt')
            {
                $format = 'ppt';
            }
        } elseif (substr($str ,0, 4) == "PK\x03\x04")
        {
            if (substr($str,0x200,4) == "\xEC\xA5\xC1\x00" || $extname == 'docx')
            {
                $format = 'docx';
            }
            elseif (substr($str,0x200,2) == "\x09\x08" || $extname == 'xlsx')
            {
                $format = 'xlsx';
            } elseif (substr($str,0x200,4) == "\xFD\xFF\xFF\xFF" || $extname == 'pptx')
            {
                $format = 'pptx';
            }else
            {
                $format = 'zip';
            }
        } elseif (substr($str ,0, 4) == 'Rar!' && $extname != 'txt')
        {
            $format = 'rar';
        } elseif (substr($str ,0, 4) == "\x25PDF")
        {
            $format = 'pdf';
        } elseif (substr($str ,0, 3) == "\x30\x82\x0A")
        {
            $format = 'cert';
        } elseif (substr($str ,0, 4) == 'ITSF' && $extname != 'txt')
        {
            $format = 'chm';
        } elseif (substr($str ,0, 4) == "\x2ERMF")
        {
            $format = 'rm';
        } elseif ($extname == 'sql')
        {
            $format = 'sql';
        } elseif ($extname == 'txt')
        {
            $format = 'txt';
        }
    }

    if ($limit_ext_types && stristr($limit_ext_types, '|' . $format . '|') === false)
    {
        $format = '';
    }

    return $format;
}


function moban_get_article_info($article_id)
    {
	
        $sql = "SELECT a.* " . "FROM " . $GLOBALS['ecs']->table('article') . " AS a " . "WHERE a.is_show = 1 and  a.article_id = '$article_id'  GROUP BY a.article_id";
        $row = $GLOBALS['db']->getRow($sql);
        if ($row !== false) {
            
            $row['description'] = $row['description'."".$this->langvs['aftefix'].""];
            $row['keywords']    = $row['keywords'."".$this->langvs['aftefix'].""];
          
			$row['add_time']    = date('Y-m-d', $row['add_time']);
			 $row['click_count']      = $row['click_count'];
            $row['cat_id']      = $row['cat_id'];
            $row['url']         = module::build_uri('article', array('aid' => $row['article_id']), $row['title']);
            $row['thumb']       = $row['article_thumb'];
            $row['other_thumb']       = $row['article_other_thumb'];
			$row['spcdesc']     = html_entity_decode($row['spcdesc'."".$this->langvs['aftefix'].""]);

            $row['title']       = $row['title'."".$this->langvs['aftefix'].""];

			$row['click_count']     = $row['click_count'];
            $row['content']     = $row['content'."".$this->langvs['aftefix'].""];
			            $row['thumb_list']=json_decode($row['article_array_thumb'],true);

        }
        return $row;
    }

function moban_get_article_cat_info($cat_id)
    {
	
        $sql = "SELECT a.* " . "FROM " . $GLOBALS['ecs']->table('article_cat') . " AS a " . "WHERE  a.cat_id = '$cat_id'  GROUP BY a.cat_id";
        $row = $GLOBALS['db']->getRow($sql);
        if ($row !== false) {
            
            $row['description'] = $row['description'."".$this->langvs['aftefix'].""];
            $row['keywords']    = $row['keywords'."".$this->langvs['aftefix'].""];
           
            $row['cat_id']      = $row['cat_id'];
			$row['moban']      = $row['moban'];
			$row['smallmoban']   = $row['smallmoban'];
            $row['url']         = module::build_uri('article_cat', array('acid' => $row['cat_id']), $row['cat_name']);

            $row['cat_name']       = $row['cat_name'."".$this->langvs['aftefix'].""];
        }
        return $row;
    }


function moban_get_form_fields_list($form_id)
{





$sf_sql ="SELECT * FROM " . $GLOBALS['ecs']->table('fields') . " WHERE display = 1   and form_id='$form_id' ORDER BY dis_order";

$form_fields_list = $GLOBALS['db']->getAll($sf_sql);
foreach($form_fields_list as $key=>$val)
{   
	$form_fields_list[$key]['reg_field_name']=$val['reg_field_name'."".$this->langvs['aftefix'].""];
	$val['select_options']=$val['select_options'."".$this->langvs['aftefix'].""];

	if(!empty($val['select_options'])&&($val['type']=='select'||$val['type']=='checked'||$val['type']=='radio'))
	{
		$arr=explode("\n",$val['select_options']);
		
		foreach($arr as $key2=>$val2){
		  if(!$val2)
		  {
			  unset($arr[$key2]);
		  }
		  $arr[$key2]=trim($val2);
		}
	
		$form_fields_list[$key]['select_options']=$arr;
	}
}

return $form_fields_list;
}

}

?>