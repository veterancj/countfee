<?php
class Active extends CActiveRecord{
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
		return 't_stat_usernum';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('pid,regnum,atvnum', 'required'),//必填
			array('id' , 'numerical', 'integerOnly'=>true),//数字类型
			array('cdate,rmk', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id,pid,regnum,atvnum,cdate,rmk', 'safe', 'on'=>'search'),
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
	
	public function getEmportdate($pid='',$start='',$end=''){//导出excel
		$wherestr = '1'; $strlist = '';$daysum = '';$jdaysum = '';$zcsum = '';$jhsum = '';
		if($pid != ''){
			$wherestr .= ' AND pid in('.$pid.')';
			$pidarr = explode(',',$pid);
		}
		if($start != '' && $end != ''){
			$wherestr .= ' AND date_format(cdate,\'%Y-%m-%d\')>="'.$start.'" AND date_format(cdate,\'%Y-%m-%d\')<="'.$end.'"';
		}
		$wherestr .= 'order by pid,cdate asc';
//		echo $wherestr;exit;
		$resobj = Active::model()->findAll($wherestr);
		$thead = "PID\t注册数\t激活数\t日期\t\n";//标题
		foreach ($pidarr as $key=>$pidval){
			$pidcheck = Active::model()->findAll('pid='.$pidval);
			$num = count($pidcheck);
			foreach ($resobj  as $key=>$val){
				if($pidval == $val->pid){
					$strlist .= "$val->pid\t$val->regnum\t$val->atvnum\t$val->cdate\t\n";
					$daysum +=  $val->regnum;//总计注册
					$jdaysum += $val->atvnum;//总计激活
					
					$zcsum +=  $val->regnum;//单渠道的注册总计
					$jhsum += $val->atvnum;//单渠道的激活总计
				}
			}
			if($num>0){
				$strlist .= "\n总计：\t$zcsum\t$jhsum\t\n\n";
			}
			$zcsum = '';
			$jhsum = '';
		}
		$content = $strlist."合计：\t$daysum\t$jdaysum\t\n";
		$time = time();
		file_put_contents('file/'.$time.'.xls',iconv('utf-8', 'gb2312',$thead.$content));
		$name = 'file/'.$time.'.xls';
		return $name;
	}

}


