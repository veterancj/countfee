<?php
class MyFun
{
	public static function substr($string,$start,$length)
	{
		mb_internal_encoding('utf8');
	    return mb_substr($string,$start,$length);
	}
	public static function unicode_decode($code){
		$js = json_decode('{"0":'.$code.'}');
		return $js['0'];
	}
	//发短信
	public static function setSMS($mobile,$content){
		$content = 'param=<?xml version="1.0" encoding="utf-8"?><B><m>'.$mobile.'</m><u>SNS</u><c>'.$content.'</c></B>';
		$url = 'http://211.143.108.6/wap/SMSSendService';  
		$data = 'act=sentcustom&'.$content;
		$set = new RequestDelegate();
		return $set->request($url, 'post',$data);
	}
	//随机数
	public static function randomNum($length=6){
		return rand(100000,999999);  
	}
	//随机字符
	public static function randomChar($length=6)  
	{
	    // 密码字符集，可任意添加你需要的字符  
	    $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_ []{}<>~`+=,.;:/?|';  
	    $password = '';  
	    for ( $i = 0; $i < $length; $i++ )  
	    {  
	        // 这里提供两种字符获取方式  
	        // 第一种是使用 substr 截取$chars中的任意一位字符；  
	        // 第二种是取字符数组 $chars 的任意元素  
	        // $password .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);  
	        $password .= $chars[ mt_rand(0, strlen($chars) - 1) ];  
	    }  
	    return $password;
	}
	public static function getClientIP(){
		return $_SERVER["REMOTE_ADDR"];
	}
	public static function formatText($string)
	{
	    $string = str_replace(
			array('&nbsp;','&amp;','&apos;','&gt;','&lt;','&quot;','&shy;'),
			array(''),
		$string);
		$string = strip_tags($string);
		return $string;
	}
	public static function formatWML($string){
		$string = str_replace(
			array('&nbsp;','&amp;','&apos;','&gt;','&lt;','&quot;','&shy;'),
			array('&#160;','&#38;','&#39;','&#62;','&#60;','&#34;','&#173;'),
		$string);
		$string = strip_tags($string,'<wml><head><meta><card><p><a><br><anchor><go><input><img><table><td><tr><b><big><em><i><small><strong><u><do><onevent><postfield><noop><prev><refresh><fieldset><optgroup><option><select><setvar><timer>');
		return $string;
	}
	public static function showModel($model){
		return mb_substr($model,0,3).'********';
	}
	public static function getUrlsByHtml($html){
		/*
		 * $reg_tag_a = '/<[a|A].*?href=[\'\"]{0,1}([^>\'\"\ ]*).*?>/';
		 */
		$reg_tag_a = '/<[a|A].*?[(href)|(HREF)]=[\'\"]{0,1}([^>\'\"\ ]*).*?[\'\"]\>.*?\<\/[a|A]\>/';
		$result = preg_match_all($reg_tag_a,$html,$match_result);
		if($result){
			return $match_result;
		}else{
			return false;
		} 
	}
	public static function _reviseUrl($base_url,$url_list){
	    $url_info = parse_url($base_url);
	    $port = isset($url_info['port']) ? ':'.$url_info['port'] :'';
		$path = isset($url_info['path']) ? $url_info['path'] : '';
	    if(is_array($url_list)){  
	    	$result=array();
	        foreach ($url_list as $url_item) {  
	        	if(preg_match('/^http/',$url_item)){  
	                $result[] = $url_item;
	            }elseif(preg_match('/^\/.+/',$url_item)){
	            	$result[] = 'http://'.$url_info['host'].$port.$url_item;
	            }else{  
	                $result[] = 'http://'.$url_info['host'].$port.$path;
	            }
	        }
	        return $result;  
	    }else{
	        return false;
	    }
	}
	
	public static function formatTime($sec){
		$hour = floor($sec/3600).'时';
		$minute = round( ($sec-$hour*3600)/60 ).'分';
		return $hour==='0时' ? $minute : $hour.$minute;
	}
	public function getip() { //获取远程ip
		$unknown = 'unknown';
		$ip = '';
		if ( isset($_SERVER['HTTP_X_FORWARDED_FOR']) && $_SERVER['HTTP_X_FORWARDED_FOR'] && strcasecmp($_SERVER['HTTP_X_FORWARDED_FOR'], $unknown) ) {  
			$ip = $_SERVER['HTTP_X_FORWARDED_FOR'];  
		}elseif ( isset($_SERVER['REMOTE_ADDR']) && $_SERVER['REMOTE_ADDR'] && strcasecmp($_SERVER['REMOTE_ADDR'], $unknown) ) {  
			$ip = $_SERVER['REMOTE_ADDR'];  
		}
	
		return $ip;
	}
}
?>
<?php //Yii::app()->request->urlReferrer;上一页?>