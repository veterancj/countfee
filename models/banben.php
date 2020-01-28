<?php
class Banben extends CActiveRecord{
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
		return '{{verinfo}}';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('name,vernum', 'required'),//必填
			array('id, uid, itemid, state, is_submit' , 'numerical', 'integerOnly'=>true),//数字类型
			array('name, vernum, inpointver', 'length', 'max'=>50),
			array('img, downurl', 'length', 'max'=>125),
			array('content', 'length', 'min'=>5),
			array('ctime, utime', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, uid, itemid, name, vernum, inpointver, img, downurl, content, state, is_submit, ctime, utime', 'safe', 'on'=>'search'),
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
		return array(
			'id' => 'Id',
			'uid' => '用户ID',
			'itemid' => '项目ID',
			'name' => '产品名',
			'vernum' => '版本号',
			'inpointver'=>'适用系统的版本号',
			'img'=>'图片',
			'downurl'=>'下载地址',
			'content'=>'详情',
			'state' => '显示状态',
			'is_submit'=>'发布状态',
			'ctime' => '创建时间',
			'utime' => '修改时间',
		);
	}
	
	public static function getItemname($ids){
		$itemobj = Item::model()->findbyPk($ids);
		return $itemobj->name;
	}


}


