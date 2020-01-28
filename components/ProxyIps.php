<?php
class ProxyIps
{
	public $ips=array(); 
	public function __construct($cache=false){
		$this->ips = (true==$cache) ? $this->init_ips(true) : $this->init_ips();
	}
	protected function init_ips($cache=false){
		$pros = Yii::app()->cache->get('proxyIps');
//		$pros = Yii::app()->cache->delete('proxyIps');
		if(empty($pros) OR $cache==true){
			$rq = new RequestDelegate();
			$html = file_get_contents(dirname(__FILE__).'/../data/ipdatas.php');
			$reg_tag_ip = '/\s*?([^\s]*?):(.+?)@/';
			$result = preg_match_all($reg_tag_ip,$html,$match_result);
			foreach ($match_result[1] as $key=>$rs){
				$allips[$key] = array('host'=>$rs,'port'=>$match_result[2][$key]);
			}
			$conts = $rq->multiProxiesByRequest($allips,'http://www.baidu.com',35);
			foreach ($conts as $key=>$cont){
				if(mb_strstr($cont,'passport.baidu.com')){
					$ips[$key] = $allips[$key];
				}
			}
			Yii::app()->cache->set('proxyIps',$ips,3600*24*1);
		}
//		var_dump(Yii::app()->cache->get('proxyIps'));exit;
		return Yii::app()->cache->get('proxyIps');
	}
	public function getIp(){
		$proxyIps = $this->ips;
		return $proxyIps[array_rand($proxyIps,1)];
	}
}