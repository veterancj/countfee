<?php
class User extends CActiveRecord{
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
		return '{{user}}';//对应数据库表
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		// NOTE: you should only define rules for those attributes that
		// will receive user inputs.
		return array(
			array('username, password', 'required'),//必填
			//array('username','message'=>'{attribute}已存在'),//唯一不能重复
			array('id, state' , 'numerical', 'integerOnly'=>true),//数字类型
			array('ctime, utime', 'safe'),//安全类型
			// The following rule is used by search().
			// Please remove those attributes that should not be searched.
			array('id, username, password, state, ctime, utime', 'safe', 'on'=>'search'),
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
			'username' => '账号',
			'password' => '密码',
			'state' => '权限标示',
			'ctime' => '创建时间',
			'utime' => '修改时间',
		);
	}
	
	
	public function validatePassword($password,$dbpassword)
	{
		//为了兼容以前的密码
		return $password == $dbpassword ? true : false;
//		return self::hashPassword($password)==$this->password ? true : false ;
	}
	public static function hashPassword($password,$salt='cms')
	{
		return md5($salt.$password);
	}
	public function userPassword(){
		$userinfo = User::model()->find('username="'.Yii::app()->user->name.'"');
		return $userinfo->password;
	}
	
	public static function userItemid($username){
		$userinfo = User::model()->find('username="'.$username.'"');
		return $userinfo->limitid;
	}
	
	public function userInfo($username){
		if($username != ''){
			$userinfo = User::model()->find('username="'.$username.'"');
		}else{
			$userinfo = array();
		}
		return $userinfo;
	}
	
	public function userList($pageSize=10){
		$criteria=new CDbCriteria();
		$wherestr = '1';
	    $criteria->condition = $wherestr;
	    $criteria->order = 'ctime DESC';
	    $count = User::model()->count($criteria);
	    $allnum = $count/$pageSize;
	    $pages=new CPagination($count);
		$pages->pageSize=$pageSize;
		$pages->applyLimit($criteria);
		$infoobj = User::model()->findAll($criteria);
		return (object)array(
			'count'=>$count,
			'allnum'=>$allnum,
			'pages'=>$pages,
			'infoobj'=>$infoobj,
		);
	}
	
	public function userrank($state=1){
		if($state == 1){
			$str = '管理员';
		}elseif($state == 2){
			$str = '运营';
		}elseif($state == 3){
			$str = '产品';
		}elseif($state == 4){
			$str = '商务';
		}elseif($state == 6){//胡波运营商事业部
			$str = '运营商事业部';
		}elseif($state == 5){
			$str = '测试';
		}
		return $str;
	}
	
	public function myItem($id){
		$namelist = '';
		$myitem = User::model()->findbyPk($id);
		$itemidarr = explode(',',$myitem->limitid);
		if($myitem->limitid != ''){
			foreach ($itemidarr as $val){
				$namelist .= Item::getItemname($val).' ';
			}
		}
		return $namelist;
	}
	public static function includeItem($itemstr,$itemid){//项目是否选中
		if($itemstr != ''){
			$itemarr = explode(',',$itemstr);
			if(in_array($itemid, $itemarr)){
				$checked = 'checked';
			}else{
				$checked = '';
			}
		}else{
			$checked = '';
		}
		return $checked;
	}
	
	static public function isAdmin(){
		$flag = false;
		if(!Yii::app()->user->isGuest){
			$user = new User();
			$userinfo = $user->userInfo(Yii::app()->user->name);
			$state = $userinfo->state;
			if($state == 1){//管理员
				$flag = true;
			}else{
				$flag = false;
			}
			
		}else{
			$flag = false;
		}
		
		return $flag;
	
	}
	
}


