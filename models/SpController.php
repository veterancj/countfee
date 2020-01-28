<?php
class SpController extends Controller{
	
	public function actionIndex(){
		$user = new User();
		$sp = new Sp();
		$userinfo = $user->userInfo(Yii::app()->user->name);
		if(empty($userinfo)){
			$this->redirect('/site/login');
		}else{
			$color = isset($_GET['color'])?$_GET['color']:'';
			if(isset($_GET['pagesize'])){
				$pagesize = intval($_GET['pagesize']) > 50  ? intval($_GET['pagesize']) : 50;
			}else{
				$pagesize=50;
			}
			$urlparams = $_GET;
			unset($urlparams['pagesize']);
			unset($urlparams['page']);
			
			if(isset($_POST['search'])){//搜索
				$spid = $_POST['spid'];
				$terracename = $_POST['terracename'];
				$companyname = $_POST['companyname'];
				$businessname = $_POST['businessname'];
				$splist = $sp->getSplist($pagesize,$spid,$terracename,$companyname,$businessname);
			}else{
				$spid = '';
				$terracename = '';
				$companyname = '';
				$businessname = '';
				$splist = $sp->getSplist($pagesize);
			}
			
			$this->render('index',array(
				'splist'=>$splist,
				'urlparams' => $urlparams,
				'spid'=> $spid,
				'terracename'=> $terracename,
				'companyname'=> $companyname,
				'businessname'=> $businessname,
				'color'=>$color
			));
		}
	}
	
	public function actionAdd(){//新增sp商
		$user = new User();
		$sp = new Sp();
		$userinfo = $user->userInfo(Yii::app()->user->name);
		if(empty($userinfo)){
			$this->redirect('/site/login');
		}else{
			if(isset($_POST['submit'])){
				if(!isset($_POST['ids'])){
					$spid = $_POST['spid'];
					$terracename = $_POST['terracename'];
					$companyname = $_POST['companyname'];
					$businessname = $_POST['businessname'];
					$price = $_POST['price'];
					$phone = $_POST['phone'];
					$order = $_POST['order'];
					$ordertype = $_POST['ordertype'];

					$sp->spid = $spid;
					$sp->terracename = $terracename;
					$sp->companyname = $companyname;
					$sp->businessname = $businessname;
					$sp->price = $price;
					$sp->phone = $phone;
					$sp->order = $order;
					$sp->ordertype = $ordertype;
					$sp->save();
					Actionlog::insertLog(Yii::app()->user->name,'sp商','add');
					$this->redirect('index');
				}else{
					$ids = $_POST['ids'];
					$url = $_POST['url'];
					$spobj = Sp::model()->findbyPk($ids);
					
					$spid = $_POST['spid'];
					$terracename = $_POST['terracename'];
					$companyname = $_POST['companyname'];
					$businessname = $_POST['businessname'];
					$price = $_POST['price'];
					$phone = $_POST['phone'];
					$order = $_POST['order'];
					$ordertype = $_POST['ordertype'];

					$spobj->spid = $spid;
					$spobj->terracename = $terracename;
					$spobj->companyname = $companyname;
					$spobj->businessname = $businessname;
					$spobj->price = $price;
					$spobj->phone = $phone;
					$spobj->order = $order;
					$spobj->ordertype = $ordertype;
					$spobj->save();
					Actionlog::insertLog(Yii::app()->user->name,'sp商','update');
					$this->redirect("$url");
				}
			}
			if(isset($_GET['ids'])){//修改页面
				$ids = $_GET['ids'];
				$spinfo = Sp::model()->findbyPk($ids);
				$this->render('add',array(
					'spinfo'=>$spinfo,
				));
			}
			
		}
	}
	
	
	public function actionSpdel(){
		if(!Yii::app()->user->isGuest){
			if(isset($_GET['ids'])){
				$ids = $_GET['ids'];
				$objdel = Sp::model()->findbyPk($ids);
				//$objdel->delete();
				$objdel->display=1;//删除
				$objdel->save();
				Actionlog::insertLog(Yii::app()->user->name,'sp商','delete');
			}
			$this->redirect('index');
		}else{
			$this->redirect('/site/login');
		}
	
	}
	
	public function actionCheck(){
		$spid = $_POST['spid'];
		$objdel = Sp::model()->find('spid="'.$spid.'"');
		$num = count($objdel);
		echo $num;exit;
		
	}
	
	public function actionSprule(){//配置规则
		if(!Yii::app()->user->isGuest){
			if(isset($_POST['submit'])){
				$sprule = new Sprule();
				if($_POST['ids'] == ''){//新增
					$spid = $_POST['spid'];
					$operators = $_POST['operators'];
					$canprovince = $_POST['canprovince'];
					$shieldcity = $_POST['shieldcity'];
					$provincename = $_POST['provincename'];
					$cityname = $_POST['cityname'];
					$allday = $_POST['allday'];
					$allmonth = $_POST['allmonth'];
					$userday = $_POST['userday'];
					$usermonth = $_POST['usermonth'];
					$uptime = $_POST['uptime'];
					$downtime = $_POST['downtime'];
					$feeway = $_POST['feeway'];
					$feetype = $_POST['feetype'];
					$payment = $_POST['payment'];
					$state = $_POST['state'];
					$spconstarst = $_POST['spconstarst'];
					$spconend = $_POST['spconend'];
					$smskey = $_POST['smskey'];
					$smstel = $_POST['smstel'];
					$type = $_POST['type'];
					$twosmskey = $_POST['twosmskey'];
					$twosmstel = $_POST['twosmstel'];
					$directcon = $_POST['directcon'];
					$startcon = $_POST['startcon'];
					$endcon = $_POST['endcon'];
					
					$sprule->spid = $spid;
					$sprule->operators = $operators;
					$sprule->canprovince = $canprovince;
					$sprule->shieldcity = $shieldcity;
					$sprule->provincename = $provincename;
					$sprule->cityname = $cityname;
					$sprule->allday = $allday;
					$sprule->allmonth = $allmonth;
					$sprule->userday = $userday;
					$sprule->usermonth = $usermonth;
					$sprule->uptime = $uptime;
					$sprule->downtime = $downtime;
					$sprule->feeway = $feeway;
					$sprule->feetype = $feetype;
					$sprule->payment = $payment;
					$sprule->state = $state;
					$sprule->spconstarst = $spconstarst;
					$sprule->spconend = $spconend;
					$sprule->smskey = $smskey;
					$sprule->smstel = $smstel;
					$sprule->type = $type;
					$sprule->twosmskey = $twosmskey;
					$sprule->twosmstel = $twosmstel;
					$sprule->directcon = $directcon;
					$sprule->startcon = $startcon;
					$sprule->endcon = $endcon;
					$sprule->save();
					Actionlog::insertLog(Yii::app()->user->name,'sp配置规则','add');
					$this->redirect('index');
				}else{//修改
					$ids = $_POST['ids'];
					$url = $_POST['url'];
					$sprule = Sprule::model()->findbyPk($ids);
					
					//$spid = $_POST['spid'];
					$operators = $_POST['operators'];
					$canprovince = $_POST['canprovince'];
					$shieldcity = $_POST['shieldcity'];
					$provincename = $_POST['provincename'];
					$cityname = $_POST['cityname'];
					$allday = $_POST['allday'];
					$allmonth = $_POST['allmonth'];
					$userday = $_POST['userday'];
					$usermonth = $_POST['usermonth'];
					$uptime = $_POST['uptime'];
					$downtime = $_POST['downtime'];
					$feeway = $_POST['feeway'];
					$feetype = $_POST['feetype'];
					$payment = $_POST['payment'];
					$state = $_POST['state'];
					$spconstarst = $_POST['spconstarst'];
					$spconend = $_POST['spconend'];
					$smskey = $_POST['smskey'];
					$smstel = $_POST['smstel'];
					$type = $_POST['type'];
					$twosmskey = $_POST['twosmskey'];
					$twosmstel = $_POST['twosmstel'];
					$directcon = $_POST['directcon'];
					$startcon = $_POST['startcon'];
					$endcon = $_POST['endcon'];
					
					//$sprule->spid = $spid;
					$sprule->operators = $operators;
					$sprule->canprovince = $canprovince;
					$sprule->shieldcity = $shieldcity;
					$sprule->provincename = $provincename;
					$sprule->cityname = $cityname;
					$sprule->allday = $allday;
					$sprule->allmonth = $allmonth;
					$sprule->userday = $userday;
					$sprule->usermonth = $usermonth;
					$sprule->uptime = $uptime;
					$sprule->downtime = $downtime;
					$sprule->feeway = $feeway;
					$sprule->feetype = $feetype;
					$sprule->payment = $payment;
					$sprule->state = $state;
					$sprule->spconstarst = $spconstarst;
					$sprule->spconend = $spconend;
					$sprule->smskey = $smskey;
					$sprule->smstel = $smstel;
					$sprule->type = $type;
					$sprule->twosmskey = $twosmskey;
					$sprule->twosmstel = $twosmstel;
					$sprule->directcon = $directcon;
					$sprule->startcon = $startcon;
					$sprule->endcon = $endcon;
					$sprule->save();
					Actionlog::insertLog(Yii::app()->user->name,'sp配置规则','update');
					$this->redirect("$url");
				}
			}
			
			if(isset($_GET['spid'])){	
				$spid = $_GET['spid'];
				$sprule = Sprule::model()->find('spid='.$spid);
				$provincelist = Sprule::getProvinceinfo();
				$citylist = Sprule::getCityinfo();
				$this->render('sprule',array(
					'sprule'=>$sprule,
					'spid'=>$spid,
					'provincelist'=>$provincelist,
					'citylist'=>$citylist,
				));
			}else{
				$this->redirect('index');
			}
		}else{
			$this->redirect('/site/login');
		}
	
	}
	
	public function actionRulelist(){//通道列表
		$sprule = new Sprule();
		if(!Yii::app()->user->isGuest){
			$color = isset($_GET['color'])?$_GET['color']:'';
			if(isset($_GET['pagesize'])){
				$pagesize = intval($_GET['pagesize']) > 50  ? intval($_GET['pagesize']) : 50;
			}else{
				$pagesize=50;
			}
			$urlparams = $_GET;
			unset($urlparams['pagesize']);
			unset($urlparams['page']);
			$rulelist = $sprule->getSprulelist($pagesize);
			$this->render('rulelist',array(
					'rulelist'=>$rulelist,
					'urlparams' => $urlparams,
					'color' =>$color
				));
		}else{
			$this->redirect('/site/login');
		}
	
	}
	
	public function actionSpruledel(){
		if(!Yii::app()->user->isGuest){
			if(isset($_GET['ids'])){
				$ids = $_GET['ids'];
				$objdel = Sprule::model()->findbyPk($ids);
				$objdel->delete();
				Actionlog::insertLog(Yii::app()->user->name,'sp配置规则','delete');
			}
			$this->redirect('rulelist');
		}else{
			$this->redirect('/site/login');
		}
	
	}
	
	
	public function actionChannel(){//计费通道配置
		$user = new User();
		$channel = new Feechannel();
		$userinfo = $user->userInfo(Yii::app()->user->name);
		if(empty($userinfo)){
			$this->redirect('/site/login');
		}else{
			$color = isset($_GET['color'])?$_GET['color']:'';
			$ditchlist = Ditch::getDitchinfo();//项目列表
			$provincelist = Sprule::getProvinceinfo();//省列表
			$channellist = $channel->getFeechannellist(20);
			$this->render('channel',array(
				'channellist'=>$channellist,
				'ditchlist'=>$ditchlist,
				'provincelist'=>$provincelist,
				'color'=>$color
			));
		}
	}
	
	
	public function actionChadd(){//新增通道配置
		$user = new User();
		$feechannel = new Feechannel();
		$userinfo = $user->userInfo(Yii::app()->user->name);
		if(empty($userinfo)){
			$this->redirect('/site/login');
		}else{
			if(isset($_POST['submit'])){
				if(!isset($_POST['ids'])){
					$iid = $_POST['iid'];
					$provinceid = $_POST['provinceid'];
					$spidarr = isset($_POST['spid'])?$_POST['spid']:array();
					//$spidarr = $_POST['spid'];
					$spid = '';
					$priority = '';
					if(count($spid)>0){
						foreach ($spidarr as $key=>$vals){
							$spid .= $vals.',';
							$priority .= $_POST['priority_'.$vals].',';
						}
					}
					
					$feechannel->iid = $iid;
					$feechannel->provinceid = $provinceid;
					$feechannel->spid = $spid;
					$feechannel->priority = $priority;
					$feechannel->save();
					Actionlog::insertLog(Yii::app()->user->name,'channel计费通道','add');
					$this->redirect('channel');
				}else{
					$ids = $_POST['ids'];
					$url = $_POST['url'];
					$chobj = Feechannel::model()->findbyPk($ids);
					$spidarr = isset($_POST['spid'])?$_POST['spid']:array();
//					$spidarr = $_POST['spid'];
					$spid = '';
					$priority = '';
					if(count($spid)>0){
						foreach ($spidarr as $key=>$vals){
							$spid .= $vals.',';
							$priority .= $_POST['priority_'.$vals].',';
						}
					}
					
					$chobj->spid = $spid;
					$chobj->priority = $priority;
					$chobj->save();
					
					Actionlog::insertLog(Yii::app()->user->name,'channel计费通道切换','update');
					$this->redirect("$url");
				}
			}
			if(isset($_GET['ids'])){//修改页面
				$ids = $_GET['ids'];
				$channelinfo = Feechannel::model()->findbyPk($ids);
				$this->render('chadd',array(
					'channelinfo'=>$channelinfo,
				));
			}
			
		}
	}
	
	public function actionProvince(){
		$this->layout = '//layouts/';
		$provinceid = $_POST['provinceid'];
		$this->render('province',array(
			'provinceid'=>$provinceid,
		));
		
	}
	
	public function actionPlupdate(){//sp批量修改、删除
		if(!Yii::app()->user->isGuest){
			if(!isset($_POST['ids'])){
				if(isset($_POST['plupdate']) && isset($_POST['autoid'])){
					$idsarr = $_POST['autoid'];
					$num = count($idsarr);
					if($num > 0){
						$idstr = implode(',',$idsarr);
						$feelist = Sp::model()->findAll('ID in('.$idstr.')');
					}else{
						$feelist = array();
					}
					$this->render('plupdate',array(
						'feelist'=>$feelist,
					));
				}else if(isset($_POST['pldel']) && isset($_POST['autoid'])){//批量删除
					$idsarr = $_POST['autoid'];
					$num = count($idsarr);
					if($num > 0){
						for($m=0;$m<$num;$m++){
							$objdel = Sp::model()->findbyPk($idsarr[$m]);
							$objdel->display=1;//删除
							$objdel->save();
						}
						Actionlog::insertLog(Yii::app()->user->name,'批量删除产品','delete');
					}
					$this->redirect('index');
				}else{
					$this->render('plupdate',array(
						'feelist'=>array(),
					));
				}
			
			}else{//批量修改
				
				$ids = $_POST['ids'];
				$spid = $_POST['spid'];
				$terracename = $_POST['terracename'];
				$companyname = $_POST['companyname'];
				$businessname = $_POST['businessname'];
				$price = $_POST['price'];
				$phone = $_POST['phone'];
				$order = $_POST['order'];
				$ordertype = $_POST['ordertype'];
				
				$nums = count($ids);
				if($nums >0){
					for($i=0;$i<$nums;$i++){
						$feeobj = Sp::model()->findbyPk($ids[$i]);
						$feeobj->spid = $spid[$i];
						$feeobj->terracename = $terracename[$i];
						$feeobj->companyname = $companyname[$i];
						$feeobj->businessname = $businessname[$i];
						$feeobj->price = $price[$i];
						$feeobj->phone = $phone[$i];
						$feeobj->order = $order[$i];
						$feeobj->ordertype = $ordertype[$i];
						$feeobj->save();
					}
				}
				Actionlog::insertLog(Yii::app()->user->name,'sp管理','plupdate');
				$this->redirect('index');
			
			}
			
		}else{
			$this->redirect('/site/login');
		}
	}
	
	
	
}