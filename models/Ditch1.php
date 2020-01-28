<?php
class Ditch extends CActiveRecord{
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
		return 'baseditchinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('DitchName,number', 'required'),//必填
			array('ID' , 'numerical', 'integerOnly'=>true),//数字类型
			array('cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID, DitchName,cdate', 'safe', 'on'=>'search'),
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
	
	public function getDitchlist($pageSize=10){
		$criteria=new CDbCriteria();
		$wherestr = '1';
	    $criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Ditch::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		
		$infoobj = Ditch::model()->findAll($criteria);
	
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	static public function getDitchinfo(){
		$sql = 'select * from baseditchinfo order by cdate desc';
		$dlist = Yii::app()->db->createCommand($sql)->queryAll();
		return $dlist;
	
	}
	
	static public function getDitchname($id){
		$fobj = Ditch::model()->findbyPk($id);
		return $fobj->DitchName;
	}
	static public function getNumber($id){
		if(empty($id)) return '';
		$infoobj = Ditch::model()->find('ID='.$id);
		return $infoobj['number'];
	}
	

}


