<?php
class Usetype extends CActiveRecord{
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
		return 'baseusetypeinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('number,name', 'required'),//必填
			array('ID' , 'numerical', 'integerOnly'=>true),//数字类型
			array('name,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,number,name,cdate', 'safe', 'on'=>'search'),
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
	
	static public function getUsetypelist(){
		$sql = 'select * from baseusetypeinfo order by cdate desc';
		$dlist = Yii::app()->db->createCommand($sql)->queryAll();
		return $dlist;
	}
	static public function getPageList($pageSize = 10,$name='',$number=''){
		$criteria=new CDbCriteria();
		$wherestr = array();
		if(!empty($name)){
			$wherestr[] = "name like '{$name}%'";
		}
		if(!empty($number)){
			$wherestr[] = "number like '{$number}%'";
		}

	    $criteria->condition = implode(' and ',$wherestr);
	    $criteria->order = 'number ';
	    $count = Usetype::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Usetype::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
}


