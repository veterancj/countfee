<?php
class Pqrelat extends CActiveRecord{
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
		return 'basepqrelatinfo';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('ProductID,cid,pid', 'required'),//必填
			array('ID,ProductID,cid,isfree,isuse' , 'numerical', 'integerOnly'=>true),//数字类型
			//array('name, area, tel', 'length', 'max'=>200),
			//array('companyname,businessname,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,ProductID,cid,pid,isfree,isuse', 'safe', 'on'=>'search'),
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
	
	public function getPqrelatlistbak($pageSize=10){
		$criteria=new CDbCriteria();
		$wherestr = '1';
	    $criteria->condition = $wherestr;
	    $criteria->order = 'pid DESC';
	    $count = Pqrelat::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Pqrelat::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	
	public function getPqrelatlist($pageSize=10,$page=1,$pid='',$did='',$fid='',$ProductName='',$type='',$cpname='',$usetype='',$ratio='',$fcname=''){//Pid列表
		
		/*
		 * $sql = 'select i.pid,i.isfree,ii.ProductName,i.isuse,u.name as usetype,r.name as ratio , c.name as cpname ,t.name as type,iii.cid,iii.fid,iii.fcid,iii.did ' .
				'from basepqrelatinfo i , baseproductinfo ii, baseiteminfo iii,baseusetypeinfo u,baseratioinfo r,basecpnameinfo c,basetypeinfo t' .
				' where i.ProductID=ii.ProductID and i.cid=iii.cid and ii.uid = u.ID and ii.rid = r.ID and ii.cpid = c.ID and ii.tid = t.ID and ii.display=0 ';
		*/
		$sql_1 = " m.pid,m.isfree,m.isuse,m.iswww," .
				"u.name as usetype," .
				"r.name as ratio , " .
				"c.name as cpname ," .
				"t.name as type," .
				"p.ProductName," .
				"i.cid,i.fid,i.fcid,i.did, " .
				"f.FactoryName as fname," .
				"fc.FactoryName as fcname," .
				"d.DitchName as dname," .
				"i.cid,p.ProductID";
		$sql =  " from  basepqrelatinfo m " .
				" left join baseiteminfo i on m.cid = i.cid " .
				" left join baseproductinfo p on m.ProductID = p.ProductID " .
				" left join baseusetypeinfo u on p.uid = u.ID " .
				" left join baseratioinfo r on p.rid = r.ID  " .
				" left join basetypeinfo t on p.tid = t.ID " .
				" left join basecpnameinfo c on p.cpid = c.ID " .
				" left join basefactoryinfo fc on fc.id = i.fcid " .
				" left join basefactoryinfo f on f.id = i.fid " .
				" left join baseditchinfo d on i.did = d.ID" .
				" where 1 = 1";
		
		if($pid != ''){
			$sql .= " and m.pid like '{$pid}%' ";
		}
		
		if($did != ''){//项目
			$sql .= " and d.DitchName ='{$did}'";
		}
		
		if($fid != ''){//厂商
			$sql .= " and f.FactoryName='{$fid}'";
		}
		
		if($type != ''){//产品类型
			$sql .= " and t.name = '{$type}'";
		}
		
		if($ProductName != ''){//产品名
			$sql .= " and p.ProductName like '{$ProductName}'";
		}
		
		if($cpname != ''){//cp名称
			$sql .= " and c.name like '{$cpname}%'";
		}
		
		if($usetype != ''){//操作平台
			$sql .= " and u.name = '{$usetype}'";
		}
	
		if($ratio != ''){//分辨率
			$sql .= " and r.name = '{$ratio}'";
		}
		if(!empty($fcname)){
			$sql .= " and fc.FactoryName  ='{$fcname}'";
		}
		
		$criteria=new CDbCriteria();
		$flist = Yii::app()->db->createCommand("select count(m.id) as cous".$sql)->queryAll();
		$count = $flist[0]['cous'];
//	    $count =count($flist);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		
		$sql .= " order by m.cdate desc limit ".($page-1)*$pageSize.",".$pageSize;
		
//				echo $sql;exit;
		$infoobj = Yii::app()->db->createCommand("select ".$sql_1.$sql)->queryAll();
	
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	static public function getCidlist($ProductID=0){
		$sql = 'select cid from basepqrelatinfo where ProductID='.$ProductID;
		$cidlist = Yii::app()->db->createCommand($sql)->queryAll();
		return $cidlist;
	}
	
	static public function getPlistres($cid){//得到渠道已经绑定的产品
		$flist = Pqrelat::model()->findAll('cid='.$cid);
		$pidarr = array();
		foreach ($flist as $key=>$val){
			$pidarr[$key+1] = $val->ProductID;
		}
		return $pidarr;
	
	}
	
}


