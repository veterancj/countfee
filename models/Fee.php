<?php
class Fee extends CActiveRecord{
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
		return 'basefeeinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sid,feename,feeinfo,price,switch,state,feetype,feeway', 'required'),//必填
			array('ID,sid,price,switch,state,circle,ankou,nostart,noend,hart,restart,display' , 'numerical', 'integerOnly'=>true),//数字类型
			//array('name, area, tel', 'length', 'max'=>200),
			array('startcon,endcon,rstartcon,rendcon,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,display,sid,restart,feename,feeinfo,price,switch,state,startcon,endcon,rstartcon,rendcon,feeway,feetype,circle,ankou,nostart,noend,hart,cdate', 'safe', 'on'=>'search'),
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
	
	public function getFeelist($pageSize=10,$type=''){
		$criteria=new CDbCriteria();
		$wherestr = 'display=0';
		if($type == 1){//胡波
			$sidstr = Bindfee::getAllhbSid();
			if($sidstr !=''){
				$wherestr .= ' and sid in('.$sidstr.')';
			}
		}
	    $criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Fee::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Fee::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	
	static public function getSwitch($state=0){//提示开关
		if($state == 0){
			$str = '关闭';
		}elseif($state == 2){
			$str = '一点多上';
		}else {
			$str = '一点一上';
		}
		return $str;
	}
	
	static public function getStatemark($state=0){//是否拦截短信
		if($state == 0){
			$str = '不拦截';
		}else {
			$str = '拦截';
		}
		return $str;
	}
	
	static  public function getFeesname($sid){//得到计费点名字
		$feelist = fee::model()->find('sid='.$sid);
		if(count($feelist)>0){
			$feename = $feelist->feename;
		}else{
			$feename = '';
		}
		return $feename;
	}
}


