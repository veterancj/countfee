<?php
class OracleDbinfo{
	
	public function oraclecon(){//adodb连接oracle
        require_once('adodb5/adodb.inc.php');
		$conn = ADONewConnection('oci8');
		$SID='sztyora';//数据库名
		$conn->connectSID = true;
		$conn->Connect('192.168.5.33', 'lianluo', 'zhrmghgll2011', $SID,'al32utf8');
    	$conn->Execute("set names 'al32utf8'");
		return $conn;
		
	}
	
	public function phpconOracle(){
		$dbconn=oci_connect("groups","sztygroups2011","(DESCRIPTION=(ADDRESS=(PROTOCOL =TCP)(HOST=192.168.5.33)(PORT = 1521))(CONNECT_DATA =(SID=sztyora)))",'al32utf8');
		return $dbconn;
	}
	
	public function phpOracle($sql,$type){//type!=2 select
		
		$dbconn = $this->phpconOracle();
//		var_dump($dbconn);exit;
		if($dbconn!=false){
			$stmt = oci_parse($dbconn, $sql);
			if($type == 2){
				$resobj = oci_execute($stmt);
				$committed = oci_commit($dbconn);//oracle执行sql后必须提交
				if (!$committed) {
			       $error = oci_error($dbconn);
			       $messenge = 'Commit failed. Oracle reports: ' . $error['message'];
			       return $messenge;
			    }
			}else{
				$resobj = oci_execute($stmt,OCI_DEFAULT);
				$num = oci_fetch_all($stmt,$results);
				if($num>0){
					return $results;
				}
			}
		}else{
			return array();
		}
		
	}
	public function phpOraclstore($val,$name){//val:条件值 name 存储过程名字
		$dbconn = $this->phpconOracle();
		$message = '';
		$val = '2012-04-12';
		/** 调用存储过程的sql语句(sql_sp : sql_storeprocedure) 
	　　* 语法： 
	　　* begin 存储过程名([[:]参数]); end; 
	　　* 加上冒号表示该参数是一个位置 
	　　**/
		$sql_sp = "begin ".$name."(:val,-10); end;";
		$stmt = ociparse($dbconn, $sql_sp);
		//执行绑定 
		ocibindbyname($stmt, ":val", $val, 32);//参数说明：绑定php变量$id到位置:id，并设定绑定长度16位
		ociexecute($stmt);
		
		var_dump($message);exit;
		while ($row = oci_fetch_assoc($stmt)) {
		    var_dump($row);
		}
		
		oci_free_statement($stmt);
		exit;
		
//		$committed = oci_commit($dbconn);

	}
	
	
	public function phpCallstore($deptno,$name){//$deptno:条件值 name 存储过程名字
		$dbconn = $this->phpconOracle();
		$cur=oci_new_cursor($dbconn);
        //创建调用语句
        $query="call ".$name."(:deptno,:v_cur)";
        $statement=oci_parse($dbconn,$query);
        //绑定游标句柄，接收返回的游标参数
        oci_bind_by_name($statement,":deptno",$deptno,116);
        oci_bind_by_name($statement,":v_cur",$cur,-1,OCI_B_CURSOR);
        //执行
        oci_execute($statement);
        //获取返回的游标数据到游标句柄
        oci_execute($cur);
        //遍历游标内容
        while ($dat = oci_fetch_row($cur)) {
            $data =$dat;
        }
        oci_free_statement($statement);
        oci_close($dbconn);      
		return $data;
	}

}