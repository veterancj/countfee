<?php
class Errorlog extends CActiveRecord{
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
		return 'android_error_log';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('IMSI,type,XML,ip', 'required'),//必填
			array('id' , 'numerical', 'integerOnly'=>true),//数字类型
			array('cdate,pid', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id,pid,IMSI,type,ip,XML,cdate', 'safe', 'on'=>'search'),
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
	
	public function getErrorlog($pageSize=10,$pid='',$imsi='',$start='',$end=''){
		$criteria=new CDbCriteria();
		$wherestr = '1';
		if($pid != ''){
			$wherestr .= ' AND pid = "'.$pid.'"';
		}
		if($start != '' && $end != ''){
			$wherestr .= ' AND date_format(cdate,\'%Y-%m-%d\')>="'.$start.'" AND date_format(cdate,\'%Y-%m-%d\')<="'.$end.'"';
		}
		if($imsi != ''){
			$wherestr .= ' AND IMSI = "'.$imsi.'"';
		}
//		echo $wherestr;exit;
		$criteria->condition = $wherestr;
	    $criteria->order = 'cdate DESC';
	    $count = Errorlog::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$resobj = Errorlog::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'resobj'=>$resobj,
		);
	}
	
	public function errorExplain($type){//错误日志说明
		//记录下发日志  $type:reg-注册下发XML、pidnofree-渠道号不能计费、jcpb-进程屏蔽(杀毒软件)、dqpb-地区屏蔽、citypb-市屏蔽、
		//xfnum-下发次数超额、yysno-运营商不支持、hmd-黑名单、free-免费配置、inmonth在包月期间、allday-总日限、userday-单用户日限
		//allmonth-总月限    usermonth-单用户月限
		if($type == 'reg'){
			$res = '注册下发';
		}elseif ($type == 'pidnofree'){
			$res = '渠道不能计费';
		}elseif ($type == 'jcpb'){
			$res = '进程屏蔽';
		}elseif ($type == 'dqpb'){
			$res = '地区屏蔽';
		}elseif($type == 'citypb'){
			$res = '市屏蔽';
		}elseif ($type == 'xfnum'){
			$res = '下发次数超额';
		}elseif ($type == 'yysno'){
			$res = '运营商不支持';
		}elseif ($type == 'hmd'){
			$res = '黑名单';
		}elseif ($type == 'free'){
			$res = '免费配置';
		}elseif ($type == 'inmonth'){
			$res = '包月期间';
		}elseif ($type == 'allday'){
			$res = '超总日限';
		}elseif ($type == 'userday'){
			$res = '单用户超日限';
		}elseif ($type == 'allmonth'){
			$res = '超总月限';
		}elseif ($type == 'usermonth'){
			$res = '单用户超月限';
		}elseif ($type == 'nofree'){
			$res = '没有符合的通道';
		}else {
			$res = '无';
		}
		return $res;
	}


}


