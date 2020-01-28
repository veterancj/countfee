<?php
class Sprule extends CActiveRecord{
	/**
	 * Returns the static model of the specified AR class.
	 * @return User the static model class
	 */
	public static function model($className=__CLASS__)
	{
		return parent::model($className);
	}

	/**
	 * @return string the associated database table name
	 */
	public function tableName()
	{
		return 'basespruleinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('spid,operators,state,feeway,feetype,payment', 'required'),//必填
			array('ID,spid,operators,allday,allmonth,userday,usermonth,uptime,downtime,state,type,feeway,feetype' , 'numerical', 'integerOnly'=>true),//数字类型
			//array('name, area, tel', 'length', 'max'=>200),
			array('canprovince,provincename,shieldcity,cityname,smskey,smstel,twosmskey,twosmstel,directcon,startcon,endcon,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,spid,operators,allday,allmonth,provincename,cityname,userday,usermonth,uptime,downtime,state,type,feeway,feetype,payment,canprovince,shieldcity,smskey,smstel,twosmskey,twosmstel,directcon,startcon,endcon,cdate', 'safe', 'on'=>'search'),
		);
	}

	/**
	 * @return array relational rules.
	 */
	public function relations()//关系
	{
		// NOTE: you may need to adjust the relation name and the related
		// class name for the relations automatically generated below.
		return array(
			
		);
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()//
	{
		/*return array(
			'id' => 'Id',
			'name' => '项目名',
			'state' => '显示状态',
			'ctime' => '创建时间',
			'utime' => '修改时间',
		);*/
	}
	
	public function getSprulelist($pageSize=10){
		$criteria=new CDbCriteria();
		$wherestr = '1';
	    $criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Sprule::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Sprule::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	static public function getProvinceinfo(){//省列表
		$sql = 'select * from baseprovinceinfo order by id';
		$flist = Yii::app()->db->createCommand($sql)->queryAll();
		return $flist;
	}
	
	static public function getCityinfo(){//市列表
		$sql = 'select * from basecityinfo order by CityName asc';
		$flist = Yii::app()->db->createCommand($sql)->queryAll();
		return $flist;
	}
	
	static public function getOperators($state=0){//运营商
		if($state == 0){
			$str = '所有';
		}elseif($state == 1){
			$str = '联通';
		}elseif($state == 2){
			$str = '移动';
		}else {
			$str = '电信';
		}
		return $str;
	}
	
	static public function getCanprovince($str=''){//支持省列表
		$province = '';
		if($str != ''){
			$str = substr($str,0,-1);
			$sql = 'select * from baseprovinceinfo where ID in('.$str.')';
			$flist = Yii::app()->db->createCommand($sql)->queryAll();
			if(count($flist)>0){
				foreach ($flist as $key=>$val){
					$province .= $val['ProvinceName'].',';
				}
				$province = substr($province,0,-1);
			}
		}
		return $province;
	}
	
	static public function getShieldcity($str=''){//屏蔽市列表
		$city = '';
		if($str != ''){
			$str = substr($str,0,-1);
			$sql = 'select * from basecityinfo where ID in('.$str.')';
			$flist = Yii::app()->db->createCommand($sql)->queryAll();
			if(count($flist)>0){
				foreach ($flist as $key=>$val){
					$city .= $val['CityName'].',';
				}
				$city = substr($city,0,-1);
			}
		}
		return $city;
	}
	
	static public function getFeeway($state=1){//计费方式
		if($state == 1){
			$str = '短信';
		}elseif($state == 2){
			$str = 'wap';
		}else {
			$str = 'IVR';
		}
		return $str;
	}
	
	static public function getFeetype($state=0){//计费类型
		if($state == 0){
			$str = '点播';
		}elseif($state == 1){
			$str = '包月';
		}else {
			$str = '包年';
		}
		return $str;
	}
	
	static public function getPayment($state=''){//通道状态
		if($state == 'N'){
			$str = '关闭';
		}elseif($state == 'Y'){
			$str = '上线';
		}else {
			$str = '测试';
		}
		return $str;
	}
	
	static public function getState($state=''){//是否免费配置
		if($state == 0){
			$str = '否';
		}else {
			$str = '是';
		}
		return $str;
	}
	
	static public function getType($state=''){//是否二次确认
		if($state == 0){
			$str = '无';
		}else {
			$str = '有';
		}
		return $str;
	}
	
	static public function getSpinfo($type=''){//得到所有SP的相关信息
		
		if($type == 1){//胡波
			$sql = 'select i.* from basespruleinfo i left join basespinfo ii on i.spid=ii.spid where ii.sptype=1 and ii.display!=1'; 
		}else{
			$sql = 'select * from basespruleinfo';
		}
//		echo $sql;exit;
		$flist = Yii::app()->db->createCommand($sql)->queryAll();
		return $flist;
	}
	
	static public function getSpcon($spid){
		$fobj = Sprule::model()->find('spid='.$spid);
		return $fobj;
	}
	

	
	
}


