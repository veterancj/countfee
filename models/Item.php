<?php
class Item extends CActiveRecord{
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
		return 'baseiteminfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('fid,did,fcid,cid', 'required'),//必填
			array('ID,fid,did,fcid' , 'numerical', 'integerOnly'=>true),//数字类型
			array('ItemName,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,ItemName,fid,did,fcid,cdate,cid', 'safe', 'on'=>'search'),
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
	/*
	public function getItemlist($pageSize=10,$cid='',$did='',$fid=''){
		
		$criteria=new CDbCriteria();
		$wherestr = '1';
		if($cid != ''){
			$wherestr .= ' and cid like "%'.$cid.'%"';
		}
		
		if($did != ''){
			$wherestr .= ' and did='.$did;
		}
		
		if($fid != ''){
			$wherestr .= ' and fid ='.$fid;
		}
		
	    $criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Item::model()->count($criteria);
	   
	    
	    
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Item::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	*/
	public function getItemlist($pageSize=10,$did='',$fid='',$fcid='',$cid=''){
		$sql = " from baseiteminfo i left join basefactoryinfo f on i.fid = f.ID left join baseditchinfo d on i.did = d.ID  left join basefactoryinfo fc on i.fcid = fc.ID " .
				"where 1=1 ";
		if(!empty($cid)){
			$sql .= " and i.cid like '{$cid}%'";
		}
		if(!empty($did)){
			$sql .= " and d.ID ={$did}";
		}
		if(!empty($fid)){
			$sql .= " and f.ID ={$fid}";
		}
		if(!empty($fcid)){
			$sql .= " and fc.ID ={$fcid}";
		}
		$sql .= " order by i.cdate DESC";
		$count = Yii::app()->db->createCommand("select count(*) as tot".$sql) -> queryRow();
		$pages=new CPagination($count['tot']);
		$pages->pageSize=$pageSize;
		$infoobj = Yii::app()->db->createCommand("select i.ID,i.cid as cid , f.FactoryName,fc.FactoryName as fcname,i.cdate,d.DitchName ".$sql." limit ".$pages->getOffset()." , {$pageSize}") -> queryAll();
		
		return (object)array(
			'count'=>$count['tot'],
			'allnum'=>$pages->getPageCount() ,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
		
	}
	
	static public function getIteminfo(){
		$sql = 'select * from baseiteminfo order by did desc';
		$flist = Yii::app()->db->createCommand($sql)->queryAll();
		return $flist;
	
	}
	public static function getItem($cid){
		if(empty($cid)) return null;
		$sql = "select f.FactoryName,d.DitchName,i.cid from basefactoryinfo f,baseditchinfo d,baseiteminfo i where f.id = i.fid and i.did=d.id and i.cid = ".$cid;
		$arr = Yii::app() -> db -> createCommand($sql)->queryRow();
		return $arr['FactoryName'].'——'.$arr['DitchName'].'——'.$arr['cid'];
	}
	static public function getCid($id){
		if(empty($id)) return null;
		$sql = "select d.dnumber,f.fnumber,fc.fnumber as fcnumber" .
				" from basefactoryinfo f,baseditchinfo d,baseiteminfo i,basefactoryinfo fc " .
				" where f.ID = i.fid and i.did=d.ID and i.fcid=fc.ID and i.ID = {$id}";
		$arr = Yii::app() -> db -> createCommand($sql)->queryRow();
		return "<span class='dnumber'>".$arr['dnumber']."</span><span class='fnumber'>".$arr['fnumber']."</span><span class='fcnumber'>".$arr['fcnumber']."</span>";
	}
	
}


