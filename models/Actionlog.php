<?php
class Actionlog extends CActiveRecord{
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
		return 'baseactionlog';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username', 'required'),//必填
			array('ID' , 'numerical', 'integerOnly'=>true),//数字类型
			array('cdate,detail,action', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,username,action,detail,cdate', 'safe', 'on'=>'search'),
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
	
	
	static public function insertLog($username='',$detail='',$action=''){
		$obj = new Actionlog();
		$obj->username = $username;
		$obj->action = $action;
		$obj->detail = $detail;
		$obj->save();
		return true;
	}
	

}


