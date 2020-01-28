<?php
class Sidchannel extends CActiveRecord{ 
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
		return 'basesidchannel';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('modelID,modelName,provinceid', 'required'),//必填
			array('ID,modelID,provinceid' , 'numerical', 'integerOnly'=>true),//数字类型
			//array('name, area, tel', 'length', 'max'=>200),
			array('spid,priority,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,modelID,provinceid,spid,priority,cdate', 'safe', 'on'=>'search'),
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
	
	public function getFeechannellist($pageSize=10){
		$criteria=new CDbCriteria();
		$wherestr = '1';
	    $criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Feechannel::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Feechannel::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
//	static public function getSidchannel($sid,$provinceid){//sid通道列表
//		echo 'aaa';exit;
//		if($sid != '' && $provinceid != ''){
//			$fobj = Sidchannel::model()->find('sid='.$sid.' and provinceid='.$provinceid);
//		}else{
//			$fobj = array();
//		}
//		return $fobj;
//	}
	static public function getSidchannel($modelID,$provinceid){//sid通道列表
		if($modelID != '' && $provinceid != ''){
			$fobj = Sidchannel::model()->find('modelID='.$modelID.' and provinceid='.$provinceid);
		}else{
			$fobj = array();
		}
		return $fobj;
	}
	

	static public function getSpmodel($pageSize=10){//sp模板列表
		$criteria=new CDbCriteria();
		$wherestr = '1';
	    $criteria->condition = $wherestr;
	    $criteria->group = 'modelID';
	    $criteria->order = 'cdate DESC';
	    $count = Sidchannel::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Sidchannel::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	static public function DeleteSp($modelID,$diqu=''){//删除某一个模板的通道信息
		$sql = 'delete from basesidchannel where modelID='.$modelID;
		if($diqu != ''){
			$sql .= ' and provinceid='.$diqu;
		}
//		echo $sql;exit;
		Yii::app()->db->createCommand($sql)->query();
	}
	
	static public function GetModellist(){
		$sql = 'select * from basesidchannel group by modelID desc';
		$flist = Yii::app()->db->createCommand($sql)->queryAll();
		return $flist;
	}
	
	static public function GetModelname($modelID){//得到模板名
		$resinfo = Sidchannel::model()->find('modelID='.$modelID);
		if(count($resinfo)){
			$modelName = $resinfo->modelName;
		}else{
			$modelName = '';
		}
		return  $modelName;
	}
	
}


