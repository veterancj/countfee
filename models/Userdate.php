<?php
class Userdate extends CActiveRecord{
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
		return 'baseclientinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('IMEI,IMSI,PhoneNumber', 'required'),//必填
			array('ID, ProductID,CityID,SimCardTypeID,Blocked' , 'numerical', 'integerOnly'=>true),//数字类型
			array('RegDate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, ProductID,CityID,SimCardTypeID,Blocked,IMEI,IMSI,PhoneNumber,RegDate', 'safe', 'on'=>'search'),
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
	
	public function getUserlist($pageSize=10,$phone='',$imsi='',$pid=''){
		$criteria=new CDbCriteria();
		$wherestr = '1';
		if($phone != ''){
			$wherestr .= ' AND PhoneNumber = "'.$phone.'"';
		}
		if($imsi != ''){
			$wherestr .= ' AND IMSI = "'.$imsi.'"';
		}
		if($pid != ''){
			$wherestr .= ' AND ProductID = '.$pid;
		}
//		echo $wherestr;exit;
		$criteria->condition = $wherestr;
	    $criteria->order = 'RegDate DESC';
	    $count = Userdate::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$resobj = Userdate::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'resobj'=>$resobj,
		);
	}
	
	static public function getProvince($id = -1){//获得地区
		if($id != -1){
			$sql = 'select ProvinceName from baseprovinceinfo where ID='.$id;
			$res = Yii::app()->db->createCommand($sql)->queryAll();
			if(count($res)>0){
				$province = $res[0]['ProvinceName'];
			}else{
				$province = '未知';
			}
		}else{
			$province = '未知';
		}
		return $province;
	}


}


