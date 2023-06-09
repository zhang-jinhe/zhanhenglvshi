<?php



if (!defined('IN_ECS'))
{
    die('Hacking attempt');
}

/**
 * 获得查询次数以及查询时间
 *
 * @access  public
 * @return  string
 */
function insert_query_info()
{
    if ($GLOBALS['db']->queryTime == '')
    {
        $query_time = 0;
    }
    else
    {
        if (PHP_VERSION >= '5.0.0')
        {
            $query_time = number_format(microtime(true) - $GLOBALS['db']->queryTime, 6);
        }
        else
        {
            list($now_usec, $now_sec)     = explode(' ', microtime());
            list($start_usec, $start_sec) = explode(' ', $GLOBALS['db']->queryTime);
            $query_time = number_format(($now_sec - $start_sec) + ($now_usec - $start_usec), 6);
        }
    }

    /* 内存占用情况 */
    if ($GLOBALS['_LANG']['memory_info'] && function_exists('memory_get_usage'))
    {
        $memory_usage = sprintf($GLOBALS['_LANG']['memory_info'], memory_get_usage() / 1048576);
    }
    else
    {
        $memory_usage = '';
    }

    /* 是否启用了 gzip */
    $gzip_enabled = gzip_enabled() ? $GLOBALS['_LANG']['gzip_enabled'] : $GLOBALS['_LANG']['gzip_disabled'];

    $online_count = $GLOBALS['db']->getOne("SELECT COUNT(*) FROM " . $GLOBALS['ecs']->table('sessions'));

    /* 加入触发cron代码 */
    $cron_method = empty($GLOBALS['_CFG']['cron_method']) ? '<img src="api/cron.php?t=' . gmtime() . '" alt="" style="width:0px;height:0px;" />' : '';

    return sprintf($GLOBALS['_LANG']['query_info'], $GLOBALS['db']->queryCount, $query_time, $online_count) . $gzip_enabled . $memory_usage . $cron_method;
}

/**
 * 调用浏览历史
 *
 * @access  public
 * @return  string
 */
function insert_history()
{
    $str = '';
    if (!empty($_COOKIE['ECS']['history']))
    {
        $where = db_create_in($_COOKIE['ECS']['history'], 'goods_id');
        $sql   = 'SELECT goods_id, goods_name, goods_thumb, shop_price FROM ' . $GLOBALS['ecs']->table('goods') .
                " WHERE $where AND is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0";
        $query = $GLOBALS['db']->query($sql);
        $res = array();
		
        while ($row = $GLOBALS['db']->fetch_array($query))
        {
            $goods['goods_id'] = $row['goods_id'];
            $goods['goods_name'] = $row['goods_name'];
            $goods['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $goods['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
            $goods['shop_price'] = price_format($row['shop_price']);
            $goods['url'] = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);
            $str.='<li class="goodsimg"><a href="'.$goods['url'].'" target="_blank" class="history_pic"><img src="'.$goods['goods_thumb'].'" alt="'.$goods['goods_name'].'" /></a><a href="'.$goods['url'].'" target="_blank"  title="'.$goods['goods_name'].'" class="history_name">'.$goods['short_name'].'</a>'.'<p class="history_price">'.'<a  href="'.$goods['url'].'">'.$goods['shop_price'].'</a>'.'</p></li>';
        }
        $str .= '<ul id="clear_history"><a onclick="clear_history()">' . $GLOBALS['_LANG']['clear_history'] . '</a></ul>';
    }
    return $str;
}


function insert_index_history()
{
    $str = '';
    if (!empty($_COOKIE['ECS']['history']))
    {
        $where = db_create_in($_COOKIE['ECS']['history'], 'goods_id');
        $sql   = 'SELECT goods_id, goods_name, goods_thumb, shop_price ,market_price FROM ' . $GLOBALS['ecs']->table('goods') .
                " WHERE $where AND is_on_sale = 1 AND is_alone_sale = 1 AND is_delete = 0";
        $res = $GLOBALS['db']->getAll($sql);
     	
		foreach($res as $idx => $row)
		{
			$goods[$idx]['goods_id'] = $row['goods_id'];
            $goods[$idx]['goods_name'] = $row['goods_name'];
            $goods[$idx]['short_name'] = $GLOBALS['_CFG']['goods_name_length'] > 0 ? sub_str($row['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $row['goods_name'];
            $goods[$idx]['goods_thumb'] = get_image_path($row['goods_id'], $row['goods_thumb'], true);
            $goods[$idx]['shop_price'] = price_format($row['shop_price']);
			$goods[$idx]['market_price'] = price_format($row['market_price']);
            $goods[$idx]['url'] = build_uri('goods', array('gid'=>$row['goods_id']), $row['goods_name']);	
		}
		
		$GLOBALS['smarty']->assign('history_goods',$goods);
	    $output = $GLOBALS['smarty']->fetch('library/history_info.lbi');
		$GLOBALS['smarty']->caching = $need_cache;
		return $output;
    }
   // return $str;
}	
		
/**
 * 调用购物车信息
 *
 * @access  public
 * @return  string
 */
function insert_cart_info()
{
    $sql = 'SELECT c.*,g.goods_name,g.goods_thumb,g.goods_id,c.goods_number,c.goods_price' .
           ' FROM ' . $GLOBALS['ecs']->table('cart') ." AS c ".
					 " LEFT JOIN ".$GLOBALS['ecs']->table('goods')." AS g ON g.goods_id=c.goods_id ".
           " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $row = $GLOBALS['db']->GetAll($sql);
		$arr = array();
		foreach($row AS $k=>$v)
		{
				$arr[$k]['goods_thumb']  =get_image_path($v['goods_id'], $v['goods_thumb'], true);
        $arr[$k]['short_name']   = $GLOBALS['_CFG']['goods_name_length'] > 0 ?
                                               sub_str($v['goods_name'], $GLOBALS['_CFG']['goods_name_length']) : $v['goods_name'];
				$arr[$k]['url']          = build_uri('goods', array('gid' => $v['goods_id']), $v['goods_name']);
				$arr[$k]['goods_number'] = $v['goods_number'];
				$arr[$k]['goods_name']   = $v['goods_name'];
				$arr[$k]['goods_price']  = price_format($v['goods_price']);
				$arr[$k]['rec_id']       = $v['rec_id'];
		}		
    $sql = 'SELECT SUM(goods_number) AS number, SUM(goods_price * goods_number) AS amount' .
           ' FROM ' . $GLOBALS['ecs']->table('cart') .
           " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $row = $GLOBALS['db']->GetRow($sql);

    if ($row)
    {
        $number = intval($row['number']);
        $amount = floatval($row['amount']);
    }
    else
    {
        $number = 0;
        $amount = 0;
    }
	$GLOBALS['smarty']->assign('number',$number);
	$GLOBALS['smarty']->assign('amount',$amount);
	
    $GLOBALS['smarty']->assign('str',sprintf($GLOBALS['_LANG']['cart_info'], $number, price_format($amount, false)));
	$GLOBALS['smarty']->assign('goods',$arr);
		
    $output = $GLOBALS['smarty']->fetch('library/cart_info.lbi');
    return $output;
}

/**
 * 调用购物车加减返回信息
 *
 * @access  public
 * @return  string
 */
function insert_flow_info($goods_price,$market_price,$saving,$save_rate,$goods_amount,$real_goods_count)
{
    $GLOBALS['smarty']->assign('goods_price',$goods_price);
	$GLOBALS['smarty']->assign('market_price',$market_price);
	$GLOBALS['smarty']->assign('saving',$saving);
	$GLOBALS['smarty']->assign('save_rate',$save_rate);
	$GLOBALS['smarty']->assign('goods_amount',$goods_amount);
	$GLOBALS['smarty']->assign('real_goods_count',$real_goods_count);
		
    $output = $GLOBALS['smarty']->fetch('library/flow_info.lbi');
    return $output;
}

/**
 * 购物车弹出框返回信息
 *
 * @access  public
 * @return  string
 */
function insert_show_div_info($goods_number,$script_name,$goods_id,$goods_recommend,$goods_amount,$real_goods_count)
{
    $GLOBALS['smarty']->assign('goods_number',$goods_number);
	$GLOBALS['smarty']->assign('script_name',$script_name);
	$GLOBALS['smarty']->assign('goods_id',$goods_id);
	$GLOBALS['smarty']->assign('goods_recommend',$goods_recommend);
	$GLOBALS['smarty']->assign('goods_amount',$goods_amount);
	$GLOBALS['smarty']->assign('real_goods_count',$real_goods_count);

    $output = $GLOBALS['smarty']->fetch('library/show_div_info.lbi');
    return $output;
}
function insert_subcontainer($arr)
{
$module_id=$arr['tab_id'];
$module = $GLOBALS['db'] -> getRow("SELECT * FROM " . $GLOBALS['ecs'] -> table('module') . " WHERE   module_id='".$module_id."' ");
$module_area_arr=json_decode($module['data'],true);
$webhtml='';
$html='';
$html.= $GLOBALS['smarty']->fetch('tab_'.$module['module_id'].'.shtml');
$webhtml .= $html;
$out=$GLOBALS['smarty']->_eval($GLOBALS['smarty']->fetch_str($webhtml));
return $out;	
}









/**
 * 调用指定的广告位的广告
 *
 * @access  public
 * @param   integer $id     广告位ID
 * @param   integer $num    广告数量
 * @return  string
 */
function insert_ads($arr)
{
    static $static_res = NULL;

    $time = gmtime();
    if (!empty($arr['num']) && $arr['num'] != 1)
    {
        $sql  = 'SELECT a.ad_id, a.position_id, a.media_type, a.ad_link, a.ad_code, a.ad_name, p.ad_width, ' .
                    'p.ad_height, p.position_style, RAND() AS rnd ' .
                'FROM ' . $GLOBALS['ecs']->table('ad') . ' AS a '.
                'LEFT JOIN ' . $GLOBALS['ecs']->table('ad_position') . ' AS p ON a.position_id = p.position_id ' .
                "WHERE enabled = 1 AND start_time <= '" . $time . "' AND end_time >= '" . $time . "' ".
                    "AND a.position_id = '" . $arr['id'] . "' " .
                'ORDER BY rnd LIMIT ' . $arr['num'];
        $res = $GLOBALS['db']->GetAll($sql);
    }
    else
    {
        if ($static_res[$arr['id']] === NULL)
        {
            $sql  = 'SELECT a.ad_id, a.position_id, a.media_type, a.ad_link, a.ad_code, a.ad_name, p.ad_width, '.
                        'p.ad_height, p.position_style, RAND() AS rnd ' .
                    'FROM ' . $GLOBALS['ecs']->table('ad') . ' AS a '.
                    'LEFT JOIN ' . $GLOBALS['ecs']->table('ad_position') . ' AS p ON a.position_id = p.position_id ' .
                    "WHERE enabled = 1 AND a.position_id = '" . $arr['id'] .
                        "' AND start_time <= '" . $time . "' AND end_time >= '" . $time . "' " .
                    'ORDER BY rnd LIMIT 1';
            $static_res[$arr['id']] = $GLOBALS['db']->GetAll($sql);
        }
        $res = $static_res[$arr['id']];
    }
    $ads = array();
    $position_style = '';

    foreach ($res AS $row)
    {
        if ($row['position_id'] != $arr['id'])
        {
            continue;
        }
        $position_style = $row['position_style'];
        switch ($row['media_type'])
        {
            case 0: // 图片广告
                $src = (strpos($row['ad_code'], 'http://') === false && strpos($row['ad_code'], 'https://') === false) ?
                        DATA_DIR . "/afficheimg/$row[ad_code]" : $row['ad_code'];
                $ads[] = "<a href='affiche.php?ad_id=$row[ad_id]&amp;uri=" .urlencode($row["ad_link"]). "'
                target='_blank'><img src='$src' width='" .$row['ad_width']. "' height='$row[ad_height]'
                border='0' /></a>";
                break;
            case 1: // Flash
                $src = (strpos($row['ad_code'], 'http://') === false && strpos($row['ad_code'], 'https://') === false) ?
                        DATA_DIR . "/afficheimg/$row[ad_code]" : $row['ad_code'];
                $ads[] = "<object classid=\"clsid:d27cdb6e-ae6d-11cf-96b8-444553540000\" " .
                         "codebase=\"http://fpdownload.macromedia.com/pub/shockwave/cabs/flash/swflash.cab#version=8,0,0,0\"  " .
                           "width='$row[ad_width]' height='$row[ad_height]'>
                           <param name='movie' value='$src'>
                           <param name='quality' value='high'>
                           <embed src='$src' quality='high'
                           pluginspage='http://www.macromedia.com/go/getflashplayer'
                           type='application/x-shockwave-flash' width='$row[ad_width]'
                           height='$row[ad_height]'></embed>
                         </object>";
                break;
            case 2: // CODE
                $ads[] = $row['ad_code'];
                break;
            case 3: // TEXT
                $ads[] = "<a href='affiche.php?ad_id=$row[ad_id]&amp;uri=" .urlencode($row["ad_link"]). "'
                target='_blank'>" .htmlspecialchars($row['ad_code']). '</a>';
                break;
        }
    }
    $position_style = 'str:' . $position_style;

    $need_cache = $GLOBALS['smarty']->caching;
    $GLOBALS['smarty']->caching = false;

    $GLOBALS['smarty']->assign('ads', $ads);
    $val = $GLOBALS['smarty']->fetch($position_style);

    $GLOBALS['smarty']->caching = $need_cache;

    return $val;
}

/**
 * 调用会员信息
 *
 * @access  public
 * @return  string
 */
function insert_member_info()
{
    $need_cache = $GLOBALS['smarty']->caching;
    $GLOBALS['smarty']->caching = false;

    if ($_SESSION['user_id'] > 0)
    {
        $GLOBALS['smarty']->assign('user_info', get_user_info());
    }
    else
    {
        if (!empty($_COOKIE['ECS']['username']))
        {
            $GLOBALS['smarty']->assign('ecs_username', stripslashes($_COOKIE['ECS']['username']));
        }
        $captcha = intval($GLOBALS['_CFG']['captcha']);
        if (($captcha & CAPTCHA_LOGIN) && (!($captcha & CAPTCHA_LOGIN_FAIL) || (($captcha & CAPTCHA_LOGIN_FAIL) && $_SESSION['login_fail'] > 2)) && gd_version() > 0)
        {
            $GLOBALS['smarty']->assign('enabled_captcha', 1);
            $GLOBALS['smarty']->assign('rand', mt_rand());
        }
    }

	//获取微信登录配置信息
	$ret = $GLOBALS['db']->getRow("SELECT * FROM ".$GLOBALS['ecs']->table('config')." WHERE `id` = 1");
	$GLOBALS['smarty']->assign('wx_appid', $ret['appid']);
	$GLOBALS['smarty']->assign('wx_uri', $ret['redirect_uri']);

    $output = $GLOBALS['smarty']->fetch('library/member_info.lbi');

    $GLOBALS['smarty']->caching = $need_cache;

    return $output;
}

/**
 * 调用评论信息
 *
 * @access  public
 * @return  string
 */
function insert_comments($arr)
{
    $need_cache = $GLOBALS['smarty']->caching;
    $need_compile = $GLOBALS['smarty']->force_compile;

    $GLOBALS['smarty']->caching = false;
    $GLOBALS['smarty']->force_compile = true;

    /* 验证码相关设置 */
    if ((intval($GLOBALS['_CFG']['captcha']) & CAPTCHA_COMMENT) && gd_version() > 0)
    {
        $GLOBALS['smarty']->assign('enabled_captcha', 1);
        $GLOBALS['smarty']->assign('rand', mt_rand());
    }
    $GLOBALS['smarty']->assign('username',     stripslashes($_SESSION['user_name']));
    $GLOBALS['smarty']->assign('email',        $_SESSION['email']);
    $GLOBALS['smarty']->assign('comment_type', $arr['type']);
    $GLOBALS['smarty']->assign('id',           $arr['id']);
    $cmt = assign_comment($arr['id'],          $arr['type']);
    $GLOBALS['smarty']->assign('comments',     $cmt['comments']);
    $GLOBALS['smarty']->assign('pager',        $cmt['pager']);


    $val = $GLOBALS['smarty']->fetch('library/comments_list.lbi');

    $GLOBALS['smarty']->caching = $need_cache;
    $GLOBALS['smarty']->force_compile = $need_compile;

    return $val;
}

function insert_indexcomments($arr)
{
    $need_cache = $GLOBALS['smarty']->caching;
    $need_compile = $GLOBALS['smarty']->force_compile;

    $GLOBALS['smarty']->caching = false;
    $GLOBALS['smarty']->force_compile = true;

    /* 验证码相关设置 */
    if ((intval($GLOBALS['_CFG']['captcha']) & CAPTCHA_COMMENT) && gd_version() > 0)
    {
        $GLOBALS['smarty']->assign('enabled_captcha', 1);
        $GLOBALS['smarty']->assign('rand', mt_rand());
    }
    $GLOBALS['smarty']->assign('username',     stripslashes($_SESSION['user_name']));
    $GLOBALS['smarty']->assign('email',        $_SESSION['email']);
    $GLOBALS['smarty']->assign('comment_type', $arr['type']);
    $GLOBALS['smarty']->assign('id',           $arr['id']);
    $cmt = assign_comment($arr['id'],          $arr['type']);
    $GLOBALS['smarty']->assign('comments',     $cmt['comments']);
    $GLOBALS['smarty']->assign('pager',        $cmt['pager']);


    $val = $GLOBALS['smarty']->fetch('library/indexcomments.lbi');

    $GLOBALS['smarty']->caching = $need_cache;
    $GLOBALS['smarty']->force_compile = $need_compile;

    return $val;
}
/**
 * 调用商品购买记录
 *
 * @access  public
 * @return  string
 */
function insert_bought_notes($arr)
{
    $need_cache = $GLOBALS['smarty']->caching;
    $need_compile = $GLOBALS['smarty']->force_compile;

    $GLOBALS['smarty']->caching = false;
    $GLOBALS['smarty']->force_compile = true;

    /* 商品购买记录 */
    $sql = 'SELECT u.user_name, og.goods_number, oi.add_time, IF(oi.order_status IN (2, 3, 4), 0, 1) AS order_status ' .
           'FROM ' . $GLOBALS['ecs']->table('order_info') . ' AS oi LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON oi.user_id = u.user_id, ' . $GLOBALS['ecs']->table('order_goods') . ' AS og ' .
           'WHERE oi.order_id = og.order_id AND ' . time() . ' - oi.add_time < 2592000 AND og.goods_id = ' . $arr['id'] . ' ORDER BY oi.add_time DESC LIMIT 5';
    $bought_notes = $GLOBALS['db']->getAll($sql);

    foreach ($bought_notes as $key => $val)
    {
        $bought_notes[$key]['add_time'] = local_date("Y-m-d G:i:s", $val['add_time']);
    }

    $sql = 'SELECT count(*) ' .
           'FROM ' . $GLOBALS['ecs']->table('order_info') . ' AS oi LEFT JOIN ' . $GLOBALS['ecs']->table('users') . ' AS u ON oi.user_id = u.user_id, ' . $GLOBALS['ecs']->table('order_goods') . ' AS og ' .
           'WHERE oi.order_id = og.order_id AND ' . time() . ' - oi.add_time < 2592000 AND og.goods_id = ' . $arr['id'];
    $count = $GLOBALS['db']->getOne($sql);


    /* 商品购买记录分页样式 */
    $pager = array();
    $pager['page']         = $page = 1;
    $pager['size']         = $size = 5;
    $pager['record_count'] = $count;
    $pager['page_count']   = $page_count = ($count > 0) ? intval(ceil($count / $size)) : 1;;
    $pager['page_first']   = "javascript:gotoBuyPage(1,$arr[id])";
    $pager['page_prev']    = $page > 1 ? "javascript:gotoBuyPage(" .($page-1). ",$arr[id])" : 'javascript:;';
    $pager['page_next']    = $page < $page_count ? 'javascript:gotoBuyPage(' .($page + 1) . ",$arr[id])" : 'javascript:;';
    $pager['page_last']    = $page < $page_count ? 'javascript:gotoBuyPage(' .$page_count. ",$arr[id])"  : 'javascript:;';

    $GLOBALS['smarty']->assign('notes', $bought_notes);
    $GLOBALS['smarty']->assign('pager', $pager);


    $val= $GLOBALS['smarty']->fetch('library/bought_notes.lbi');

    $GLOBALS['smarty']->caching = $need_cache;
    $GLOBALS['smarty']->force_compile = $need_compile;

    return $val;
}

/**
 * 调用购物车信息
 *
 * @access  public
 * @return  string
 */
function insert_cart_info1()
{
    $sql = 'SELECT SUM(goods_number) AS number, SUM(goods_price * goods_number) AS amount' .
           ' FROM ' . $GLOBALS['ecs']->table('cart') .
           " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $row = $GLOBALS['db']->GetRow($sql);

    if ($row)
    {
        $number = intval($row['number']);
        $amount = floatval($row['amount']);
    }
    else
    {
        $number = 0;
        $amount = 0;
    }

    $str = $number;

    return $str ;
}

/**
 * 调用购物车信息
 *
 * @access  public
 * @return  string
 */
function insert_cart_info2()
{
    $sql = 'SELECT SUM(goods_number) AS number, SUM(goods_price * goods_number) AS amount' .
           ' FROM ' . $GLOBALS['ecs']->table('cart') .
           " WHERE session_id = '" . SESS_ID . "' AND rec_type = '" . CART_GENERAL_GOODS . "'";
    $row = $GLOBALS['db']->GetRow($sql);

    if ($row)
    {
        $number = intval($row['number']);
        $amount = floatval($row['amount']);
    }
    else
    {
        $number = 0;
        $amount = 0;
    }

    $str = $amount;

    return $str ;
}



/**
 * 调用在线调查信息
 *
 * @access  public
 * @return  string
 */
function insert_vote()
{
    $vote = get_vote();
    if (!empty($vote))
    {
        $GLOBALS['smarty']->assign('vote_id',     $vote['id']);
        $GLOBALS['smarty']->assign('vote',        $vote['content']);
    }
    $val = $GLOBALS['smarty']->fetch('library/vote.lbi');

    return $val;
}

//广告位小图
function insert_get_adv_child($arr)
{
	$need_cache = $GLOBALS['smarty']->caching;
    $need_compile = $GLOBALS['smarty']->force_compile;

    $GLOBALS['smarty']->caching = false;
    $GLOBALS['smarty']->force_compile = true;

    /* 验证码相关设置 */
    if ((intval($GLOBALS['_CFG']['captcha']) & CAPTCHA_COMMENT) && gd_version() > 0)
    {
        $GLOBALS['smarty']->assign('enabled_captcha', 1);
        $GLOBALS['smarty']->assign('rand', mt_rand());
    }
	$id_name = '_'.$arr['id']."',";
	$str_ad = str_replace(',',$id_name,$arr['ad_arr']);
	$in_ad_arr = substr($str_ad,0,strlen($str_ad)-1);

	$GLOBALS['smarty']->assign('ad_child', get_ad_posti_child($in_ad_arr));
	  
	$val = $GLOBALS['smarty']->fetch('library/position_get_adv_small.lbi');  
	  
	$GLOBALS['smarty']->caching = $need_cache;
    $GLOBALS['smarty']->force_compile = $need_compile;
	
	return $val;
}
function get_ad_posti_child($cat_n_child = ''){

	$cat_child = " ad.ad_name in($cat_n_child) and ";
	
	$sql = "select ap.ad_width,ap.ad_height,ad.ad_name,ad.ad_code,ad.ad_link from ".$GLOBALS['ecs']->table('ad_position')." as ap left join ".$GLOBALS['ecs']->table('ad')." as ad on ad.position_id = ap.position_id where " .$cat_child. " ad.media_type=0 and UNIX_TIMESTAMP()>ad.start_time and UNIX_TIMESTAMP()<ad.end_time and ad.enabled=1";
     $res = $GLOBALS['db']->getAll($sql);
	 
	 foreach($res as $key=>$row){
		 $key = $key + 1; 
		 $arr[$key]['ad_code'] = $row['ad_code'];
		 $arr[$key]['ad_link'] = $row['ad_link'];
	 }
	 
	 return $arr;
}


function insert_kefu()
{
$sql = 'SELECT * FROM ' . $GLOBALS['ecs']->table('kefu');
$row = $GLOBALS['db']->getRow($sql);
//售前客服qq
$kfqq_arr   = explode(",",$row['kfqq']);
foreach ($kfqq_arr AS $key =>$qqrow)
{
$qqrow_arr=explode(":",$qqrow);
$row['qqone'][$key]['name']   = $qqrow_arr[0];
$row['qqone'][$key]['qq']   = $qqrow_arr[1];
}
//售前客服旺旺
$kfim_arr   = explode(",",$row['im']);
foreach ($kfim_arr AS $key =>$imrow)
{
$imrow_arr=explode(":",$imrow);
$row['imone'][$key]['name']   = $imrow_arr[0];
$row['imone'][$key]['im']   = $imrow_arr[1];
}
$row['typetwo']                = !empty($row['typetwo']) ? $row['typetwo'] : '';
//售后客服qq
$kfqq_arr   = explode(",",$row['kfqqtwo']);
foreach ($kfqq_arr AS $key =>$qqrow)
{
$qqrow_arr=explode(":",$qqrow);
$row['qqtwo'][$key]['name']   = $qqrow_arr[0];
$row['qqtwo'][$key]['qq']   = $qqrow_arr[1];
}
//售后客服旺旺
$kfim_arr   = explode(",",$row['imtwo']);
foreach ($kfim_arr AS $key =>$imrow)
{
$imrow_arr=explode(":",$imrow);
$row['imqqtwo'][$key]['name']   = $imrow_arr[0];
$row['imqqtwo'][$key]['im']   = $imrow_arr[1];
}
$row['qqqun']=$row['qqqun']?explode(",",$row['qqqun']):$row['qqqun'];
$row['wwqun']=$row['wwqun']?explode(",",$row['wwqun']):$row['wwqun'];
$row['kf53']=$row['kf53']?explode(",",$row['kf53']):$row['kf53'];
$row['fenxiang']=$row['fenxiang']?explode(",",$row['fenxiang']):$row['fenxiang'];
$GLOBALS['smarty']->assign('kefu',$row);
$webhtml= $GLOBALS['smarty']->fetch('kefu.shtml');
$out=$GLOBALS['smarty']->_eval($GLOBALS['smarty']->fetch_str($webhtml));


return $out;	
}


?>