<?php
class Bindfee extends CActiveRecord{
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
		return 'basepfcontact';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('sid,pid', 'required'),//必填
			array('ID,ProductID,cid' , 'numerical', 'integerOnly'=>true),//数字类型
			//array('name, area, tel', 'length', 'max'=>200),
			//array('companyname,businessname,cdate', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('ID,ProductID,pid,cid,sid', 'safe', 'on'=>'search'),
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
	
	public function getBindfeelist($pageSize=10){
		$criteria=new CDbCriteria();
		$wherestr = '1';
	    $criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Bindfee::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = Bindfee::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	static public function getFeecon($ProductID){//得到已绑定的计费点信息
		$feeinfo = Bindfee::model()->findAll('ProductID='.$ProductID.' group by sid');
		if(count($feeinfo)>0){
			foreach ($feeinfo as $key=>$vals){
				$sid[] = $vals->sid;
				$name[] = Fee::getFeesname($vals->sid);
			}
			$num = count($sid);
			if($num == 1){
				$con = $name[0].'-'.$sid[0];
			}else{
				$con = '<select name="feetype" style="width:100px;" id="feetype">';
				for($i=0;$i<$num;$i++){
			    	$con .= '<option value="">'.$name[$i].'-'.$sid[$i].'</option>';
				}
				$con .= '</select>';
			}
		}else{
			$con = '无计费点';
		}
		return $con;
	}

	static public function getFeelistres($sid){//得到计费点sid已经绑定的pid
		$sql = 'select * from basepfcontact where sid='.$sid;
		$flist = Yii::app()->db->createCommand($sql)->queryAll();
		$pidarr = array();
		foreach ($flist as $key=>$val){
			$pidarr[$key+1] = $val['pid'];
		}
		return $pidarr;
	
	}
	
	static public function getAllhbSid(){//得到胡波运营商事业部项目id是12的PID所绑定的所有SID
		$sql = 'select s.sid from basepqrelatinfo m left join baseiteminfo i on m.cid = i.cid left join baseditchinfo d on i.did = d.ID left join basepfcontact s on s.pid = m.pid where d.ID=12';
		$flist = Yii::app()->db->createCommand($sql)->queryAll();
		$sidstr = '';
		if(count($flist)>1){
			foreach ($flist as $key=>$val){
				if($val['sid'] == NULL) continue;
				$sidstr .= $val['sid'].',';
			}
			$sidstr = substr($sidstr,0,-1);
		}
		
		return $sidstr;
	
	}
	
	
}


