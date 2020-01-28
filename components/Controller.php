<?php
/**
 * Controller is the customized base controller class.
 * All controller classes for this application should extend from this base class.
 */
class Controller extends CController
{
	/**
	 * @var string the default layout for the controller view. Defaults to '//layouts/column1',
	 * meaning using a single column layout. See 'protected/views/layouts/column1.php'.
	 */
	public $layout='//layouts/column1';
	/**
	 * @var array context menu items. This property will be assigned to {@link CMenu::items}.
	 */
	public $keywords = '前端模块,前端控件,javascript教程,css3,html5,icon,学习手册,web前端开发,移动应用开发,图标,奇遇林,网络建设,网站开发';
	public $description = '奇遇林是一个为方便您在线预览与管理你的前端代码与效果，在这里您可以创作专属于自己的脚本、UI库，同时在线交流开发经验，欣赏他人杰作，在这里我们致力于将前端功能模块化。';
	public $menu=array(
		array('label'=>'发布源码', 'url'=>array('/jscode/create'),'linkOptions'=>array('onclick'=>'return addCode(this)')),
	);
	public $sideTitle = 'RSS';
	public $breadcrumbs=array();
	
	
	public function getAuth(){
		return User::model()->findByPk(Yii::app()->user->id);
	}
	/*
	 * 判断是否是作者
	 */
	protected function isOwner($user){
//		var_dump($user->id,$this->loadModel());exit;
		return ($this->loadModel()->user_id===$user->id);
	}
	/*
	 * 判断是否是超级管理员
	 */
	protected function isSuperAdmin($user){
		return in_array($user->id,array(1));
	}
	public function getUploadDir(){
		return YiiBase::getPathOfAlias('webroot.uploads').'/';
	}
	public function jsonEncode($error=200,$data='',$options=array()){//状态，数据
		$ages = array_merge(array('status'=>$error,'datas'=>$data),$options);
		$json = json_encode($ages);
		return isset($_GET['jsoncallback']) ? $_GET['jsoncallback'].'('.$json.')' : $json;
	}
	
	public function getIsAjax(){
		return (isset($_GET['ajax']) OR isset($_POST['ajax'])) ? true : false;
	}
	
	public function render($view,$data=null,$return=false)
	{
		return $this->isAjax ? $this->renderPartial($view,$data,$return) : parent::render($view,$data,$return);
	}
	
	protected function beforeAction($action)
	{
		return true;
	}
}