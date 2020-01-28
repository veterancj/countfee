<?php
class Channel extends CActiveRecord{
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
		return 't_channel_info';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('chan_name,chan_id', 'required'),//必填
			array('id' , 'numerical', 'integerOnly'=>true),//数字类型
			array('rmk', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id,chan_name,chan_id,rmk', 'safe', 'on'=>'search'),
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
	
	public function getActivelist($pageSize=10,$pid='',$start='',$end=''){
		$criteria=new CDbCriteria();
		$wherestr = '1';
		if($pid != ''){
			$wherestr .= ' AND pid in('.$pid.')';
		}
		if($start != '' && $end != ''){
			$wherestr .= ' AND date_format(cdate,\'%Y-%m-%d\')>="'.$start.'" AND date_format(cdate,\'%Y-%m-%d\')<="'.$end.'"';
		}
//		echo $wherestr;exit;
		$criteria->condition = $wherestr;
		if($pid !=''){
			$criteria->order = 'pid ASC';
		}else{
	    	$criteria->order = 'cdate DESC';
		}
	    $count = Active::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$resobj = Active::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'resobj'=>$resobj,
		);
	}
	
	public function getQdlist(){//渠道列表
		$sql = 'select chan_name from t_channel_info group by chan_name order by id';
		$qdlist = Yii::app()->db->createCommand($sql)->queryAll();
		return $qdlist;
	}
	
	public function getPidlist($name=''){//渠道PID集合
		$pidlist = '';
		if($name !=''){
			$resobj = Channel::model()->findAll('chan_name="'.$name.'"');
			foreach ($resobj as $key=>$val){
				$pidlist .=$val->chan_id.',';
			}
			$pidlist = substr($pidlist,0,-1);
		}else{
			$pidlist = 0;
		}
		return $pidlist;
	}

}


