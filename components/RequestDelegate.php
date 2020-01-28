<?php
class RequestDelegate
{/*{{{*/
	const TIME_OUT = 10;
	const MAX_RETRY_TIME = 3;
	
	private $errlogPath;
	private $headers = array();
	
	public function __construct($errlogPath=null)
	{/*{{{*/
		$this->errlogPath = $errlogPath;
	}/*}}}*/
	
	public function requestByProxy($proxy,$request,$optionParames=array())
	{
		$header = array('Cache-Control: no-cache');
		$timeout = empty($optionParames['timeout']) ? 10 : $optionParames['timeout'];
		if(isset($optionParames['header'])){
			$header = array_merge($header,$optionParames['header']);
		}
		$proxyUrl = 'http://'.$proxy['host'].':'.$proxy['port'];
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_URL, $request);
//		curl_setopt($ch, CURLOPT_COOKIE, $cookie);//需要cookie请写在header里
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);//获取跳转后的页面
		$response = curl_exec($ch);
		$res[curl_getinfo($ch,CURLINFO_EFFECTIVE_URL)] = array(
					curl_multi_getcontent($ch),//正文
					curl_getinfo($ch,CURLINFO_CONTENT_TYPE),//类型
					curl_getinfo($ch,CURLINFO_HTTP_CODE),//状态码
				);
		curl_close($ch);
		return $res;
	}
	
	public function setHeaders($header)
	{
		$this->headers[] = $header;
	}
	
	public function request($hosts, $method='get', $args='', $cookie='', $timeout=self::TIME_OUT, $noRetry=false)
	{/*{{{*/
		assert(false==empty($hosts));
		$url = $this->pickupHost($hosts);
		$ch = curl_init();
		$data = '';
		if ( ('get' == $method) && ('' != $args) )
		{
			$url = $this->preGetData($url, $args);
		}
		else
		{
			$data = $this->convert($args);
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		}
		curl_setopt($ch, CURLOPT_URL, $url);
		
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_USERAGENT, 'User-AgentMozilla/5.0 (Windows; U; Windows NT 5.1; en-US; rv:1.8.1.3) Gecko/20070309 Firefox/2.0.0.3');
		curl_setopt($ch, CURLOPT_COOKIE, $cookie);
		if (false == empty($this->headers))
		{
			curl_setopt($ch, CURLOPT_HTTPHEADER, $this->headers);
			curl_setopt($ch, CURLOPT_HEADER, true);
			curl_setopt($ch, CURLINFO_HEADER_OUT, true);
		
		}
		
		$result = array();
		$result = curl_exec($ch);
		$this->processErr($ch, $url, $data);
		
		//retry
		$tries = 0;
		while ( (false == $noRetry) && (false === $result) && (false == empty($hosts)) && ($tries <= self::MAX_RETRY_TIME) )
		{
			++$tries;
			$result = $this->request($hosts, $method, $args, $cookie, $timeout, true);
		
			if ($this->errlogPath)
			{
				$msg = 'retry('.$tries.'): url:'.$url."\n";
				error_log($msg, 3, $this->errlogPath);
			}
		}
		curl_close($ch);
		return $result;
	}/*}}}*/
	
	private function processErr($ch, $url, $data)
	{/*{{{*/
		$msg = curl_error($ch);
		if ('' != $msg && $this->errlogPath)
		{
			$time = date('Y-m-d H:i:s');
			$msg = '['.$time.'] '.$msg." | ".$url.' | '.$data."\n";
		
			error_log($msg, 3, $this->errlogPath);
		}
	}/*}}}*/
	
	private function preGetData($url, $args)
	{/*{{{*/
		$data = $this->convert($args);
		if (false === strstr($url, '?'))
		{
			$url = $url.'?'.$data;
		}
		else
		{
			$url = $url.'&'.$data;
		}
		return $url;
	}/*}}}*/
	
	private function microtimeFloat()
	{/*{{{*/
		list($usec, $sec) = explode(" ", microtime());
		return ((float)$usec + (float)$sec);
	}/*}}}*/
	
	private function pickupHost(&$hosts)
	{/*{{{*/
		if (false == is_array($hosts))
		{
			$tries = array();
			array_push($tries, $hosts);
			array_push($tries, $hosts);
			$hosts = $tries;
		}
		$key = array_rand($hosts);
		if (null !== $key)
		{
			$url = $hosts[$key];
			unset($hosts[$key]);
			return $url;
		}
		//url is none!;
		assert(false);
	}/*}}}*/
	
	private function convert($args)
	{/*{{{*/
		$data = '';
		if ('' != $args && is_array($args))
		{
			foreach ($args as $key=>$val)
			{
				if (is_array($val))
				{
					foreach ($val as $k=>$v)
					{
						$data .= $key.'['.$k.']='.rawurlencode($v).'&';
					}
				}
				else
				{
					$data .="$key=".rawurlencode($val)."&";
				}
			}
			return $data;
		}
		return $args;
	}/*}}}*/
	
	public function multiRequest($requests, $timeout=self::TIME_OUT)
	{/*{{{*/
		$mh = curl_multi_init();
		foreach($requests as $request)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $request);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);//获取跳转后的页面
			curl_multi_add_handle($mh, $ch);
			$conn[] = $ch;
		}
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while($mrc == CURLM_CALL_MULTI_PERFORM);
		
		while ($active and $mrc == CURLM_OK)
		{
			if (curl_multi_select($mh) != -1)
			{
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		
		$cnt = count($requests);
		for($i =0; $i < $cnt; $i++)
		{
			if(!curl_errno($conn[$i]))
			{
//				$res[$i] = curl_multi_getcontent($conn[$i]);
				$res[curl_getinfo($conn[$i],CURLINFO_EFFECTIVE_URL)] = array(
					curl_multi_getcontent($conn[$i]),//正文
					curl_getinfo($conn[$i],CURLINFO_CONTENT_TYPE),//类型
					curl_getinfo($conn[$i],CURLINFO_HTTP_CODE),//状态码
//					curl_getinfo($conn[$i],CURLINFO_SIZE_UPLOAD),
//					curl_getinfo($conn[$i],CURLINFO_SIZE_DOWNLOAD),
//					curl_getinfo($conn[$i],CURLINFO_SPEED_DOWNLOAD),
//					curl_getinfo($conn[$i],CURLINFO_SPEED_UPLOAD),
//					curl_getinfo($conn[$i],CURLINFO_HEADER_SIZE),
//					curl_getinfo($conn[$i],CURLINFO_HEADER_OUT),
//					curl_getinfo($conn[$i],CURLINFO_CONTENT_LENGTH_UPLOAD),
//					curl_getinfo($conn[$i],CURLOPT_PROXY),
				);
			}
			curl_multi_remove_handle($mh, $conn[$i]);
			curl_close($conn[$i]);
		}
		curl_multi_close($mh);
		
		return $res;
	}/*}}}*/
	
	public function multiRequestByProxy($proxy, $requests,$cookie='',$header=array(), $timeout=self::TIME_OUT)
	{/*{{{*/
		$proxyUrl = $this->parseUrl($proxy);
		$header = array_merge(array('Cache-Control: no-cache'),$header);
		$mh = curl_multi_init();
		
		foreach($requests as $request)
		{
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_URL, $request);
			curl_setopt($ch, CURLOPT_COOKIE, $cookie);

			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_multi_add_handle($mh, $ch);
			$conn[] = $ch;
		}
		
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while($mrc == CURLM_CALL_MULTI_PERFORM);
		
		while ($active and $mrc == CURLM_OK)
		{
			if (curl_multi_select($mh) != -1)
			{
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		
		$cnt = count($requests);
		for($i =0; $i < $cnt; $i++)
		{
			if(!curl_errno($conn[$i]))
			{
				$res[$i] = curl_multi_getcontent($conn[$i]);
				$type[$i] = curl_getinfo($conn[$i],CURLINFO_CONTENT_TYPE);//类型
			}
			curl_multi_remove_handle($mh, $conn[$i]);
			curl_close($conn[$i]);
		}
		curl_multi_close($mh);
		
		return $res;
	}/*}}}*/
	
	public function multiRequestByProxies($proxies,$requests,$optionParames=array())
	{/*{{{*/
		$header = array('Cache-Control: no-cache');
		$timeout = empty($optionParames['timeout']) ? self::TIME_OUT : $optionParames['timeout'];
		if(isset($optionParames['header'])){
			$header = array_merge($header,$optionParames['header']);
		}
		$mh = curl_multi_init();
		foreach($requests as $request)
		{
			$proxy = $proxies[array_rand($proxies,1)];
			$proxyUrl = $this->parseUrl($proxy);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_URL, $request);
//			curl_setopt($ch, CURLOPT_COOKIE, $cookie);//需要cookie请写在header里
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);//获取跳转后的页面
			curl_multi_add_handle($mh, $ch);
			$conn[] = $ch;
		}
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while($mrc == CURLM_CALL_MULTI_PERFORM);
		while ($active and $mrc == CURLM_OK)
		{
			if (curl_multi_select($mh) != -1)
			{
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		$cnt = count($requests);
		for($i =0; $i < $cnt; $i++)
		{
			if(!curl_errno($conn[$i]))
			{
				$res[curl_getinfo($conn[$i],CURLINFO_EFFECTIVE_URL)] = array(
					curl_multi_getcontent($conn[$i]),//正文
					curl_getinfo($conn[$i],CURLINFO_CONTENT_TYPE),//类型
					curl_getinfo($conn[$i],CURLINFO_HTTP_CODE),//状态码
//					curl_getinfo($conn[$i],CURLINFO_SIZE_UPLOAD),
//					curl_getinfo($conn[$i],CURLINFO_SIZE_DOWNLOAD),
//					curl_getinfo($conn[$i],CURLINFO_SPEED_DOWNLOAD),
//					curl_getinfo($conn[$i],CURLINFO_SPEED_UPLOAD),
//					curl_getinfo($conn[$i],CURLINFO_HEADER_SIZE),
//					curl_getinfo($conn[$i],CURLINFO_HEADER_OUT),
//					curl_getinfo($conn[$i],CURLINFO_CONTENT_LENGTH_UPLOAD),
//					curl_getinfo($conn[$i],CURLOPT_PROXY),
				);
			}
			curl_multi_remove_handle($mh, $conn[$i]);
			curl_close($conn[$i]);
		}
		curl_multi_close($mh);
		return $res;
	}/*}}}*/

	public function multiProxiesByRequest($proxys, $host, $timeout=self::TIME_OUT)
	{/*{{{*/
		$header = array('Cache-Control: no-cache');
		$mh = curl_multi_init();
		foreach($proxys as $proxy)
		{
			$proxyUrl = $this->parseUrl($proxy);
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_PROXY, $proxyUrl);
			curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
			curl_setopt($ch, CURLOPT_URL, $host);
			curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_multi_add_handle($mh, $ch);
			$conn[] = $ch;
		}
		
		do {
			$mrc = curl_multi_exec($mh, $active);
		} while($mrc == CURLM_CALL_MULTI_PERFORM);
		
		while ($active and $mrc == CURLM_OK)
		{
			if (curl_multi_select($mh) != -1)
			{
				do {
					$mrc = curl_multi_exec($mh, $active);
				} while ($mrc == CURLM_CALL_MULTI_PERFORM);
			}
		}
		
		$cnt = count($proxys);
		for($i =0; $i < $cnt; $i++)
		{
			if(!curl_errno($conn[$i]))
			{
				$res[$i] = curl_multi_getcontent($conn[$i]);
			}
			curl_multi_remove_handle($mh, $conn[$i]);
			curl_close($conn[$i]);
		}
		curl_multi_close($mh);
		
		return $res;
	}/*}}}*/
	
	public function parseUrl($urlInfo)
	{/*{{{*/
		$scheme = isset($urlInfo['scheme']) ? $urlInfo['scheme'] : 'http';
		$port = isset($urlInfo['port']) ? $urlInfo['port'] : 80;
		$path = isset($urlInfo['path']) ? $urlInfo['path'] : '';
		
		$request = $scheme . '://'. $urlInfo['host'] .':'. $port . $path;
		return $request;
	}/*}}}*/
	
	public function parseParams($params)
	{/*{{{*/
		$paramString = '';
		$pairs = array();
		foreach($params as $key => $value)
		{
			$pair = $key .'='. $value;
			array_push($pairs, $pair);
		}
		if($query = implode('&', $pairs))
		{
			$paramString .= '?' . $query;
		}
		
		return $paramString;
	}/*}}}*/
}/*}}}*/
