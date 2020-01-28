<?

class PageList
{
	//-------------------------定义类中用到的全局变量----------------------/
	var $PerPage;				//当前页数
	var $PerLimit;				//当前每页显示条数
	var $PerPageLimit;			//当前每页显示页数
	var $TotalNums;				//当前分页中的总条数
	var $TotalPage;				//当前分页中总页数
	var $PageUrl;				//定义当前网页路径
	var $PageStart;				// 定义当前开始末ID
	var $PageEnd;				//定义当前结束ID
	var $PageStyle;				//定主当前分页显示样式
	var $PageHeader;			//定义显示头部分
	var $PageBody;				//定义显示主体部分
	var $PageBottom;			//定义显示尾部分
	var $imagePath;

	function  SetVar($pageParameter=array(1,1,1,0),$styleParameter=array("false","true","true","true")){
		$this->TotalNums=$pageParameter[0];
		$this->PerLimit=$pageParameter[1];
		$this->PerPageLimit=$pageParameter[2];
		$this->PageStyle=$styleParameter;
		$this->SetToTalPage();
		if($pageParameter[3]>0){
			$this->PerPage=$pageParameter[3];	
		}elseif($pageParameter[3]<0 && !isset($_GET["page"])){
			$this->PerPage=$this->TotalPage;	
		}else{
			$this->PerPage=$_GET["page"];
		}
		$this->imagePath="images/";
		$this->SetDefaultStyle();
		$this->SetPageUrl();
		$this->SetPerPage();
		$this->SetStaticDynamic();
		$this->SetDisplayPageInfo();
	}

	//------------定义设置强制风格函数----------------------------------------/
	function SetDefaultStyle()
	{
		if($this->PageStyle[0]=="false")
			if($this->PageStyle[1]<>"no")
				$this->PageStyle[1]="true";
		if($this->PageStyle[3]=="no")
			$this->PageStyle[1]="false";
	}
	  
	//-------------定认取得当前网页路径函数----------------------------------/
	function SetPageUrl()
	{
		$tmp_arr = array();
		$queryString=$_SERVER["QUERY_STRING"];
		parse_str($queryString,$array);
		foreach($array as $key=>$value)
		{
			if($key=="page"){
				continue;
			}else{
				$tmp_arr[$key].=$array[$key];
			}
		}
		if(!is_array($tmp_arr))
			$tmp_arr=array();
		$tmp_str=http_build_query($tmp_arr);
		$this->PageUrl="http://".$_SERVER["HTTP_HOST"].$_SERVER["PHP_SELF"]."?".$tmp_str;
	}
	//-------------定义设置当前分页类中总页数函数------------------------------/
	function SetTotalPage(){
		$this->TotalPage=ceil($this->TotalNums/$this->PerLimit);
	}
	//--------------定义设置当前分页类中当前页数------------------------------/
	function SetPerPage()
	{
		if($this->PerPage<=0)
			$this->PerPage=1;
		elseif($this->PerPage>$this->TotalPage)
			$this->PerPage=$this->TotalPage;
	}
	//-------------定义取得当前分页中的当前页函数------------------------------/
	function GetPerPage()
	{
		return $this->PerPage;
	}
	//-------------定义取得当前分页类中总页数函数------------------------------/
	function GetTotalPage()
	{
		return $this->TotalPage;
	}
	function GetStart()
	{
		return ($this->PerPage-1)*$this->PerLimit;
	}
	//-----------------定义取得当前页中信息首尾号函数---------------------------/
	function GetStartEndInfo()
	{
		$Start=($this->PerPage-1)*$this->PerLimit+1;
		$End=$Start+$this->PerLimit-1;
		if($End>$this->TotalNums)
			$End=$this->TotalNums;
		return array($Start,$End);
	}
	//----------定义取得是否有上下页函数-----------------------------------/
	function GetPerNextInfo()
	{
		if($this->PerPage>1)
			$per=$this->PerPage-1;
		else
			$per=false;
		if($this->PerPage<$this->TotalPage)
			$next=$this->PerPage+1;
		else 
			$next=false;
		return array($per,$next);
	}
	//-----------------定义取得是否有上几页和下几页函数-----------------/
	function GetPerNextMoreInfo()
	{
		if($this->PerPage>$this->PerPageLimit)
			$start=$this->PageStart-1;
		else
			$start=false;
		if($this->PageEnd<$this->TotalPage)
			$end=$this->PageEnd+1;
		else
			$end=false;
		return array($start,$end);
	}
	//-----------------定义取得当前步进动态分页信息函数----------------------------/
	function SetDynamicPageInfo()
	{	
		$this->PageStart=$this->PerPage;
		if($this->PageStart<=0)
			$this->PageStart=1;
		$this->PageEnd=$this->PerPageLimit+$this->PageStart-1;
		if($this->PageEnd>$this->TotalPage)
			$this->PageEnd=$this->TotalPage;
	}
	//-----------------定义取得当前中间动态分页信息函数----------------------------/
	function SetMidDynamicPageInfo()
	{	
		$add=floor($this->PerPageLimit/2);
		$this->PageStart=$this->PerPage-$add;
		if($this->PageStart<=0)
			$this->PageStart=1;
		$this->PageEnd=$this->PerPageLimit+$this->PageStart-1;
		if($this->PageEnd>$this->TotalPage)
			$this->PageEnd=$this->TotalPage;
	}
	//-------------定义取得当前静态分页信息函数-------------------------------/
	function SetStaticPageInfo()
	{
		$starts=$this->PerPage/$this->PerPageLimit;
		if(is_int($starts))
			$starts=$starts-1;
		else
			$starts=floor($starts);
		$this->PageStart=$starts*$this->PerPageLimit+1;
		$this->PageEnd=$this->PageStart+$this->PerPageLimit-1;
		if($this->PageEnd>$this->TotalPage)
			$this->PageEnd=$this->TotalPage;
	}

	//------------定义通过判断是静动态来设置首尾号----------------/
	function SetStaticDynamic()
	{
		if($this->PageStyle[0]=="false")
			$this->SetStaticPageInfo();
		elseif($this->PageStyle[0]=="true")
		{
			if($this->PageStyle[1]=="true")
				$this->SetDynamicPageInfo();
			else
				$this->SetMidDynamicPageInfo();	
		}
		else
			$this->SetStaticPageInfo();
	}
	function getFooter(){
		$totpage = $this -> GetTotalPage();
	}

	//-------------定义显示前几页和后几页函数----------------------------/
	function GetPerNextMore($mark=true){
		$tmp_str = '';
		if($mark=="true" and $this->PerPage<>1)
			$tmp_str="<a href=$this->PageUrl&page=1 title=\"首页\">首页</a>";
		if($this->PageStyle[1]=="no" || $this->PageStyle[1]=="false")
		{
			$this->PageBody.="";
			return;
		}
		$ifPerNextArray=$this->GetPerNextMoreInfo();
		if($this->PageStyle[0]=="true" and $this->PageStyle[0]=="true")
		{
			$ifPerNextArray[0]=$ifPerNextArray[0]+1-$this->PerPageLimit;
			if($ifPerNextArray[0]<=0)
			{
				$ifPerNextArray[0]=1;	
			}
			if($this->PerPage==1)
				$ifPerNextArray[0]=false;
		}
		if($ifPerNextArray[0] && $mark=="true")
			$tmp_str.="<a href=$this->PageUrl&page=$ifPerNextArray[0] title= 前".$this->PerPageLimit."页><img src=\"".$this->imagePath."ico_page_first.gif\" border=\"0\" align='absmiddle'></a>\n";
		if($ifPerNextArray[1] && $mark<>"true")
			$tmp_str.="<font face=\"Webdings\"><a href=$this->PageUrl&page=$ifPerNextArray[1] title=后".$this->PerPageLimit."页><img src=\"".$this->imagePath."ico_page_end.gif\" border=\"0\" align='absmiddle'></a></font>\n";
		if($mark<>"true" and $this->PerPage<>$this->TotalPage)
			$tmp_str.="<a href=$this->PageUrl&page=$this->TotalPage title=\"尾页\">尾页</a>";

		$this->PageBody.=$tmp_str;
		return $tmp_str;
	}

		//-------------定义显示前一页和后一页函数----------------------------/
	function GetPerNext($mark="true"){
		if($mark=="false")
			$tmp_str="<a href=$this->PageUrl&page=$this->TotalPage title=\"尾页\">尾页</a>";
		if($this->PageStyle[1]=="no" || $this->PageStyle[1]=="true")
		{
			return;
		}
		$ifPerNextArray=$this->GetPerNextInfo();
		if($ifPerNextArray[0] && $mark=="true")
			$tmp_str="<a href=$this->PageUrl&page=$ifPerNextArray[0] title= 前一页><img src=\"".$this->imagePath."ico_page_forward.gif\" border=\"0\" align='absmiddle'></a> ";
		if($ifPerNextArray[1] && $mark<>"true")
			$tmp_str="<a href=$this->PageUrl&page=$ifPerNextArray[1] title=后一页><img src=\"".$this->imagePath."ico_page_next.gif\" border=\"0\" align='absmiddle'></a>";
		$this->PageBody.=$tmp_str;
		return $tmp_str;
	}

	//-------------------定义显示分页头函数-------------------/
	function GetPageHeader(){
		if($this->PageStyle[0]=="no")
		{
			return;
		}
		$tmp_str="第".$this->PerPage."页/共".$this->GetTotalPage()."页 共".$this->TotalNums."条";
		$this->PageHeader="<div style=\"float:left;display:inline;width:30%;\">".$tmp_str."</div>\n";
		return $tmp_str;
	}

	//-------------------定义显示分页主体函数-------------------/
	function GetPageBody(){

		if($this->PageStyle[3]=="no")
		{
			return;
		}	
		for($this->PageStart;$this->PageStart<=$this->PageEnd;$this->PageStart++)
		{
			if($this->PerPage==$this->PageStart)

				$this->PageBody.="<a href=$this->PageUrl&page=$this->PageStart title=第".$this->PageStart."页><span style=\"color:#F00\"><b>[".$this->PageStart."]</b></span></a>\n ";
			else
				$this->PageBody.="<a href=$this->PageUrl&page=$this->PageStart title=第".$this->PageStart."页>[".$this->PageStart."]</a>\n ";
		}
		return $this->PageBody;
	}

	//-------------------定义显示分页主体函数-------------------/
	function SetPageBody(){
		$this->GetPerNextMore();
		$this->GetPerNext();
		$this->GetPageBody();			
		$this->GetPerNextMore(false);
		$this->GetPerNext(false);
		$this->PageBody="<div style=\"float:left;display:inline;width:40%;text-align:center\">".$this->PageBody."</div>\n";
	}

	//-------------------定义显示分页尾函数-------------------/
	function GetPageEnd(){
		if($this->PageStyle[2]=="no")
		{
			$this->PageBottom="";
			return;
		}
		elseif($this->PageStyle[2]=="true")
		{
			$tmp_str="跳到<select name=\"pageSelect\" onChange=\"document.location=this.value\">";
			for($i=1;$i<=$this->GetTotalPage();$i++)
			{
				if($i==$this->PerPage)
					$tmp_str.="<option value=$this->PageUrl&page=$i selected>$i</option>";
				else
					$tmp_str.="<option value=$this->PageUrl&page=$i>$i</option>";
			}
			$tmp_str.="</select>页";
		}
		elseif($this->PageStyle[2]=="false")
		{
			$tmp_str="<input type=\"text\" name=\"pageSelect\" id=\"pageSelect\" size=\"3\" maxlength=\"5\" value=\"$this->PerPage\"/>";
			$tmp_str.="&nbsp;<input type=\"button\" value=\"GO\" onClick=\"document.location='$this->PageUrl&page='+ pageSelect.value\">";
		}
		$this->PageBottom="<div style=\"float:right;display:inline;width:20%\">".$tmp_str."</div>\n";
		return $tmp_str;
	}

	//-------------定义得到前台显示变量函数---------------/
	function SetDisplayPageInfo()
	{
		$this->GetPageHeader();
		$this->SetPageBody();
		$this->GetPageEnd();
		$this->DisplayInfo="<div style=\"margin:5px;width:100%; \">\n".$this->PageHeader.$this->PageBody.$this->PageBottom."</div>";
	}
	//-------------定义得到前台显示变量函数---------------/
	function DisplayPageInfo()
	{
		return $this->DisplayInfo;
	}
	
	//计算select返回结果总数,2006-12-19添加
	function getCountBySql($sql) {
		/*global $db;
		$tempRs = $db->Execute($sql);
		if($tempRs) {
			return $tempRs->RecordCount();
		}
		else {
			return false;
		}*/
		$tempRs = mysql_query($sql);
		if($tempRs) {
			return mysql_num_rows($tempRs);
		}
		else {
			return false;
		}
	}
}
?>