<?php
class Product extends CActiveRecord{
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
		return 'baseproductinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('uid,ProductName,number,rid,tid,cpid', 'required'),//必填
			array('ID,display' , 'numerical', 'integerOnly'=>true),//数字类型
			array('type,cpname,usetype,ratio,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,display,ProductID,ProductName,type,cpname,usetype,ratio,cdate', 'safe', 'on'=>'search'),
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
	
	public function getProductlist($pageSize=10,$ProductID='',$ProductName='',$type='',$cpname='',$usetype='',$ratio=''){
		
		
	    $sql = " from baseproductinfo p left join baseusetypeinfo u on p.uid=u.ID left join baseratioinfo r on p.rid=r.ID left join basetypeinfo t on p.tid = t.ID left join basecpnameinfo c on p.cpid = c.ID " .
	    		" where  p.display=0 ";
	    
	    if(!empty($ProductID)){
	    	$sql .= " and p.ProductID like '{$ProductID}%'";
	    }
	    if(!empty($ProductName)){
	    	$sql .= " and p.ProductName like '{$ProductName}%'";
	    }
	    if(!empty($type)){
	    	$sql .= is_numeric($type) ? " and p.tid = $type" : "and t.name like '{$type}%'";
	    }
	    if(!empty($cpname)){
	    	$sql .= " and c.name like '$cpname%'";
	    }
	    if(!empty($usetype)){
	    	$sql .= is_numeric($usetype) ? " and p.uid = $usetype" : "and u.name like '{$usetype}%'";
	    }
	    if(!empty($ratio)){
	    	$sql .= is_numeric($ratio) ? " and p.rid = $ratio" : "and r.name like '{$ratio}%'";
	    }
	    
	    $sql .=" order by p.cdate DESC ";
	   
	    $count = Yii::app()->db->createCommand("select count(*) as tot ".$sql) -> queryRow();
		$pages=new CPagination($count['tot']);
		$pages->pageSize=$pageSize;
//		var_dump("select p.ID,concat(u.number,r.number,t.number,c.number,p.number) as ProductID , u.name as usetype,r.name as ratio,t.name as cpname,p.ProductName ".$sql." limit ".$pages->getOffset()." , {$pageSize}");
//		
//		exit();
		$infoobj = Yii::app()->db->createCommand("select p.ID,p.ProductID , u.name as usetype,r.name as ratio,t.name as type,c.name as cpname,p.ProductName,p.cdate ".$sql." limit ".$pages->getOffset()." , {$pageSize}") -> queryAll();
	    
	
		return (object)array(
			'count'=>$count['tot'],
			'allnum'=>$pages->getPageCount(),
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	static public function getProductID($id){
		if(empty($id)) return '';
		$sql = "select u.number as unumber,r.number as rnumber,t.number as tnumber,c.number as cnumber ,p.number as nnumber " .
				"from baseproductinfo p,baseusetypeinfo u,baseratioinfo r,basetypeinfo t,basecpnameinfo c " .
				"where p.uid=u.ID and p.rid=r.ID and p.tid=t.ID and p.cpid = c.ID  and p.ID ={$id} ";
		$arr = Yii::app() -> db -> createCommand($sql)->queryRow();
		return "<span class='unumber'>".$arr['unumber']."</span>" .
			   "<span class='rnumber'>".$arr['rnumber']."</span>" .
			   "<span class='tnumber'>".$arr['tnumber']."</span>".
			   "<span class='cnumber'>".$arr['cnumber']."</span>".
			   "<span class='nnumber'>".$arr['nnumber']."</span>";
	}
	
	
	

}


