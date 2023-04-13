<?php



if (!defined('IN_ECS'))
{
die('Hacking attempt');
}


function get_lang($sitelang)
{
switch ($sitelang) 
{
case "lang1" :
$sitelang='lang1';
break;
case "lang2" :
$sitelang='lang2';
break;
case "lang3" :
$sitelang='lang3';
break;
default:
$sitelang='';
}
return $sitelang;
}

function get_lang_code($sitelang)
{
switch ($sitelang) 
{
case "lang1" :
$sitecode='_1';
break;
case "lang2" :
$sitecode='_2';
break;
case "lang3" :
$sitecode='_3';
break;
default:
$sitecode='';
}
return $sitecode;
}


function get_lang_pac($sitelang)
{
switch ($sitelang) 
{
case "lang1" :
$sitelang='lang1';
break;
case "lang2" :
$sitelang='lang2';
break;
case "lang3" :
$sitelang='lang3';
break;
default:
$sitelang='lang';
}
return $sitelang;
}





?>