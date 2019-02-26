<?php

function posts($where){
    return \Models\Post::select($where);
}

function msubstr($str,$length, $suffix=true, $start=0, $charset="utf-8") {
    if(function_exists("mb_substr")){
        $slice = mb_substr($str, $start, $length, $charset);
        $strlen = mb_strlen($str,$charset);
    }elseif(function_exists('iconv_substr')){
        $slice = iconv_substr($str,$start,$length,$charset);
        $strlen = iconv_strlen($str,$charset);
    }else{
        $re['utf-8']   = "/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|[\xe0-\xef][\x80-\xbf]{2}|[\xf0-\xff][\x80-\xbf]{3}/";
        $re['gb2312'] = "/[\x01-\x7f]|[\xb0-\xf7][\xa0-\xfe]/";
        $re['gbk']    = "/[\x01-\x7f]|[\x81-\xfe][\x40-\xfe]/";
        $re['big5']   = "/[\x01-\x7f]|[\x81-\xfe]([\x40-\x7e]|\xa1-\xfe])/";
        preg_match_all($re[$charset], $str, $match);
        $slice = join("",array_slice($match[0], $start, $length));
        $strlen = count($match[0]);
    }
    if($suffix && $strlen>$length)$slice.='...';
    return $slice;
}

function cutstr_html($string){  
    $string = strip_tags($string);  
    $string = trim($string);  
    $string = ereg_replace("\t","",$string);  
    $string = ereg_replace("\r\n","",$string);  
    $string = ereg_replace("\r","",$string);  
    $string = ereg_replace("\n","",$string);  
    $string = ereg_replace(" ","",$string);  
    return trim($string);  
}