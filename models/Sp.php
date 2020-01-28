<?php
class Sp extends CActiveRecord{
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
		return 'basespinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
//			array('spid,terracename,phone,order,price', 'required'),//必填
			array('spid', 'required'),//必填
			array('ID,price,ordertype,sptype,display' , 'numerical', 'integerOnly'=>true),//数字类型
			//array('name, area, tel', 'length', 'max'=>200),
			array('companyname,businessname,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,display,spid,companyname,businessname,terracename,phone,order,ordertype,price,cdate', 'safe', 'on'=>'search'),
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
	
	public function getSplist($pageSize=10,$spid='',$terracename='',$companyname='',$businessname='',$sptype=''){
		$criteria=new CDbCriteria();
		$wherestr = 'display=0';
		if($sptype != ''){//胡波运营商事业部
			$wherestr .= ' and sptype =1';
		}
		
		if($spid != ''){//cp名称
			$wherestr .= ' and spid like "%'.$spid.'%"';
		}
		
		if($terracename != ''){
			$wherestr .= ' and terracename like "%'.$terracename.'%"';
		}
		
		if($companyname != ''){
			$wherestr .= ' and companyname like "%'.$companyname.'%"';
		}
		
		if($businessname != ''){
			$wherestr .= ' and businessname like "%'.$businessname.'%"';
		}
		
//		echo $wherestr;exit;
		
	    $criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Sp::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Sp::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	static public function getOrdermark($state=1){
		if($state == 1){
			$str = '精确';
		}else {
			$str = '模糊';
		}
		return $str;
	}
	
	static public function getSpname($spid){//得到通道名
		$fobj = Sp::model()->find('spid='.$spid);
		return $fobj->terracename;
	}
	
	static public function getSporder($spid,$order='order'){//得到指令、长号码
		$fobj = Sp::model()->find('spid='.$spid);
		if($order == 'order'){
			return $fobj->order;
		}else{
			return $fobj->phone;
		}
	}
	
	static public function getDaystate($spid){//通道日限到期状态
		$sql = 'SELECT * FROM basefeereport WHERE DATE_FORMAT( cdate, "%Y%m%d" ) = DATE_FORMAT( CURDATE( ) , "%Y%m%d" ) and spid='.$spid.' and status=0';
		$resobj = Yii::app()->db->createCommand($sql)->queryAll();//对账单数据
		$num = count($resobj);
		$spinfo = Sprule::getSpcon($spid);
		$allday = $spinfo->allday;
		if($num>=$allday){
			return '<span style="color:red;">日限已超</span>';
		}else{
			return '日限没超';
		}
	}
	
	static public function getMonthstate($spid){//通道日限到期状态
		$sql = 'SELECT * FROM basefeereport WHERE DATE_FORMAT( cdate, "%Y%m" ) = DATE_FORMAT( CURDATE( ) , "%Y%m" ) and spid='.$spid.' and status=0';
		$resobj = Yii::app()->db->createCommand($sql)->queryAll();//对账单数据
		$num = count($resobj);
		$spinfo = Sprule::getSpcon($spid);
		$allmonth = $spinfo->allmonth;
		if($num>=$allmonth){
			return '<span style="color:red;">月限已超</span>';
		}else{
			return '月限没超';
		}
	}
	
}


