<?php
class Rightlog extends CActiveRecord{
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
		return 'android_right_log';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('IMSI,XML,ip', 'required'),//必填
			array('id,sid' , 'numerical', 'integerOnly'=>true),//数字类型
			array('cdate, pid', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id,pid,sid,IMSI,XML,cdate', 'safe', 'on'=>'search'),
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
	
	public function getRightlog($pageSize=10,$imsi='',$start='',$end=''){
		$criteria=new CDbCriteria();
		$wherestr = '1';
		if($start != '' && $end != ''){
			$wherestr .= ' AND date_format(cdate,\'%Y-%m-%d\')>="'.$start.'" AND date_format(cdate,\'%Y-%m-%d\')<="'.$end.'"';
		}
		if($imsi != ''){
			$wherestr .= ' AND IMSI = "'.$imsi.'"';
		}
//		echo $wherestr;exit;
		$criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Rightlog::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$resobj = Rightlog::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'resobj'=>$resobj,
		);
	}


}


