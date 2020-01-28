<?php
class Factory extends CActiveRecord{
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
		return 'basefactoryinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('FactoryName,number', 'required'),//必填
			array('ID,sonid' , 'numerical', 'integerOnly'=>true),//数字类型
			array('cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,sonid,FactoryName,cdate', 'safe', 'on'=>'search'),
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
	
	public function getFactorylist($pageSize=10){
		$criteria=new CDbCriteria();
		$wherestr = 'sonid = 0';
	    $criteria->condition = $wherestr;
	    $criteria->order = 'number ';
	    $count = Factory::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Factory::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	static public function getFactoryinfo(){
		$sql = 'select * from basefactoryinfo where sonid =0 order by cdate asc';
		$flist = Yii::app()->db->createCommand($sql)->queryAll();
		return $flist;
	
	}
	
	static public function getFactoryname($id){
		if(empty($id)|| !is_numeric($id)) return '';
		$fobj = Factory::model()->findbyPk($id);
		return $fobj->FactoryName;
	}
	
	static public function getSonfactory($id){
		if(empty($id)|| !is_numeric($id)) return '';
		$infoobj = Factory::model()->findAll('sonid='.$id);
		return $infoobj;
	
	}
	static public function getNumber($id){
		if(empty($id)|| !is_numeric($id)) return '';
		$infoobj = Factory::model()->find('ID='.$id);
		return $infoobj['fnumber'];
	}

}


