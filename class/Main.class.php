<?php
@include("../config.php");
class Main {
	
	/* BEGIN __CONSTRUNCT FUNCITON FOR THE MAIN CLASS */
		public function __construct(){
			/*BEGIN SETTING PAGE PER RECORD */
				if(isset($_GET['record']) && is_numeric($_GET['record'])){
					$_SESSION['pagerecords_limit']=$_GET['record'];
				}
			/* END SETTING PAGE PER RECORD */		
			$this->pagefilename = strtolower(basename($_SERVER['PHP_SELF']));

			$info_array = array();
			$this->sitedata = $this->GetSingleRecord("site_settings",$info_array);

			define("SITE_TITLE",stripslashes($this->sitedata['site_title']));	
			define("SITE_EMAIL",stripslashes($this->sitedata['site_email']));	
			
		}
	/* END __CONSTRUNCT FUNCITON FOR THE MAIN CLASS */	

	/*BEGIN DATABASE CONNECTION FUNCTION WITH MYSQLI*/
		private function DBConnection(){
			@include("../config.php");

			$con = mysqli_connect($db_hostname,$db_username,$db_password,$db_name);

			if (mysqli_connect_errno()){
		  		echo "Failed to connect to MySQL: " . mysqli_connect_error();exit;
		  	} else {
		  		return $con;
		  	}
		}
	/*BEGIN DATABASE CONNECTION FUNCTION WITH MYSQLI*/

	/*BEGIN PAGE REDIRECTION FUNCTION */
		public function RedirectPage($url){
			if($url!=""){
				echo '<script type="text/javascript">window.location="'.$url.'";</script>';
				exit;
			}
		}
	/*END PAGE REDIRECTION FUNCTION : RedirectPage()*/

	/*BEGIN VIEW LINK CREATION FUNCTION */
		public function AddLink(){
			echo $this->pagefilename.'?action=add';
		}
	/*END EDIT LINK CREATION FUNCTION */

	/*BEGIN VIEW LINK CREATION FUNCTION */
		public function ViewLink($id){
			echo $this->pagefilename.'?action=view&id='.$id;
		}
	/*END EDIT LINK CREATION FUNCTION */

	/*BEGIN EDIT LINK CREATION FUNCTION */
		public function EditLink($id){
			echo $this->pagefilename.'?action=edit&id='.$id;
		}
	/*END EDIT LINK CREATION FUNCTION */

	/*BEGIN STATUS CHANGE LINK CREATION FUNCTION */
		public function StatusChangeLink($id,$currentstatus){
			if($currentstatus=="0"){
				echo '<a href="'.$this->pagefilename.'?action=status&status=1&id='.$id.'"><span  class="label label-danger">Inactive</span></a>';
			} else {
				echo '<a href="'.$this->pagefilename.'?action=status&status=0&id='.$id.'"><span  class="label label-success">Active</span></a>';
			}
		}

		public function StatusLink($id,$status=0){
			echo $this->pagefilename.'?action=status&status='.$status.'&id='.$id;
		}
	/*END STATUS CHANGE LINK CREATION FUNCTION */

	/*BEGIN DELETE LINK CREATION FUNCTION */
		public function DeleteLink($id){
			echo $this->pagefilename.'?action=delete&id='.$id;
		}
	/*END DELETE LINK CREATION FUNCTION */


	/*BEGIN VALIDATE FUNCTION */
		public function validate($value,$function="require"){
			$response = false;
			/*BEGIN REQUIRE VALIDAITON */
				if($function=="require" && trim($value)!=""){
					$response = true;
				}
			/*END REQUIRE VALIDAITON */

			/*BEGIN NUMBER VALIDAITON */
				if(trim($value)!="" && $function=="numeric" && is_numeric($value)){
					$response = true;
				}	
			/*END NUMBER VALIDAITON */

			/*BEGIN STRING VALIDAITON */
				if(trim($value)!="" && $function=="alpha" && preg_match("/^[a-zA-Z ]*$/",$value)){
					$response = true;
				}	
			/*END STRING VALIDAITON */

			/*BEGIN ALPHA-NUMERIC VALIDAITON */
				if(trim($value)!="" && $function=="alphanumeric" && preg_match("/^[a-zA-Z0-9 ]*$/",$value)){
					$response = true;
				}	
			/*END ALPHA-NUMERIC VALIDAITON */

			/*BEGIN EMAIL VALIDAITON */
				if(trim($value)!="" && $function=="email" && filter_var($value, FILTER_VALIDATE_EMAIL)){
					$response = true;
				}	
			/*END EMAIL VALIDAITON */

			/*BEGIN WEBSITE URL VALIDAITON */
				else if(trim($value)!="" && preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=~_|!:,.;]*[-a-z0-9+&@#\/%=~_|]/i",$value)){
					$response = true;
				}	
			/*END WEBSITE VALIDAITON */

			return $response;
			
		}
	/*END VALIDATE FUNCTION */

	/*BEGIN SEND MAIL FUNCTION */
		public function SendMail($mailto,$subject,$message,$attachments=""){
			// To send HTML mail, the Content-type header must be set
			$headers  = 'MIME-Version: 1.0' . "\r\n";
			$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";

			// Additional headers
			//$headers .= 'To: User1 <bharatparmar@example.com>, User2 <user2@example.com>' . "\r\n";
			$headers .= 'From: '.SITE_TITLE.' <'.SITE_EMAIL.'>' . "\r\n";
			//$headers .= 'Cc: user3@example.com' . "\r\n";
			//$headers .= 'Bcc: user4@example.com' . "\r\n";	
			@mail($mailto,$subject,$message,$headers);
			
		}
	/*END SEND MAIL FUNCTION : SendMail()  */

	/*BEGIN GET RANDOM STRING FUNCTION */
		public function GetRandomString($length,$type=0){
		    $key = '';
		    if($type==1)		//NUMERIC ONLY
		    {
		    	$keys = range(0, 9);
		    }
		    else if($type==2)	//APHA ONLY
		    {
		    	$keys = range('a', 'z');
		    }
		    else
		    {
		    	$keys = array_merge(range(0, 9), range('a', 'z'));
		    }

		    for ($i = 0; $i < $length; $i++) 
		    {
		        $key .= $keys[array_rand($keys)];
		    }

	    	return $key;
		}
	/*END GET RANDOM STRING FUNCTION : GetRandomString() */

	/*BEGIN DATABASE RECORD FUNCTION */

		/*BEGIN INSERT RECORD FUNCTION */
			public function InsertRecord($tablename, array $values){
				/*
					REQUIREMENT : 
						$tablename : Table Name where data will be inserted
						$values :  array 
									field_1 => value_1,field_2 => value_2,field_3 => value_3,field_n => value_n

									fields name and values which will be added for the record

					RETURN : LAST INSERTED IDs
				*/

				$last_inserted_id=0;	

				if(TABLE_PREFIX!=""){
					$tablename = TABLE_PREFIX.$tablename;
				}

				if(!empty($values)){

					$con = $this->DBConnection();

					$query_string = "insert into ".$tablename." set ";

					foreach($values as $key=>$value)
					{
						$query_string.=$key." = '".addslashes(mysqli_real_escape_string($con,$value))."' , ";	
					}

					$query_string = trim($query_string," , ");

					/* TO CHECK QUERY REMOVE ENABLE BELOW CODE */
					//echo $query_string;exit;
					
					mysqli_query($con,$query_string);
					$last_inserted_id = mysqli_insert_id($con);
					mysqli_close($con);
				}

				return $last_inserted_id;
			}
		/*END INSERT RECORD FUNCTION : InsertRecord()*/

		/*BEGIN INSERT MULTIPLE RECORD FUNCTION */
			public function InsertMultipleRecord($tablename,$fieldarray,$valuearray){
				if(TABLE_PREFIX!=""){
					$tablename = TABLE_PREFIX.$tablename;
				}

				$query_string = "insert into ".$tablename." (";

				foreach ($fieldarray as $key => $value) {
					$query_string.="`".$value."` ,";
				}
				
				$query_string = trim($query_string," ,");

				$query_string.=" ) values ";
				
				$con = $this->DBConnection();		
				foreach ($valuearray as $key => $value) {
					$query_string.=" ( ";
					foreach ($value as $k => $v) 
					{
						$query_string.="'".mysqli_real_escape_string($con,$v)."' ,";	
					}
					$query_string = trim($query_string," ,");
					$query_string.=" ) ,";
					
				}

				$query_string = trim($query_string," ,");


				/* TO CHECK QUERY REMOVE ENABLE BELOW CODE */
				//echo $query_string;exit;
				mysqli_query($con,$query_string);
				$totalnewrecord = mysqli_affected_rows($con);
				mysqli_close($con);

				return $totalnewrecord;				

			}
		/*END INSERT MULTIPLE RECORD FUNCTION */

		/*BEGIN GET SINGLE RECORD FUNCTION */
			public function GetSingleRecord($tablename,array $array){
				/*
					REQUIREMENT : 
						$tablename : Table Name where data will be inserted
						$array :  array 
								fields : * or field_1, field_2, field_n #BY DEFAULT *	
								where : where condition as per your requirement 
					RETURN : RECORD ARRAY
				*/

				if(TABLE_PREFIX!=""){
					$tablename = TABLE_PREFIX.$tablename;
				}

				$record = array();

				if(!isset($array['fields']) || $array['fields']=="") {$array['fields']="*";}

				$query_string = "select ".$array['fields']." from ".$tablename." where 1=1 "; 	

				if(@$array['where']!=""){
					$query_string.=" and ".$array['where']." ";
				}

				//setting group by
				if(@$array['groupby']!="") {
					$query_string.=" group by ".$array['groupby'];
				}

				//seeting order by
				if(@$array['orderby']=="") {
					$array['orderby']=1;
				}

				//setting order type
				if(@$array['ordertype']=="") {
					$array['ordertype']="desc";
				}

				$query_string.=" order by ".$array['orderby']." ".$array['ordertype'];				

				//setting record start limit
				if(@$array['startfrom']=="") {
					$array['startfrom']=0;
				}

				/* TO CHECK QUERY REMOVE ENABLE BELOW CODE */
				//echo $query_string;exit;

				$con = $this->DBConnection();
				$query = mysqli_query($con,$query_string);
				if(@mysqli_num_rows($query)>0){
					$record = mysqli_fetch_assoc($query);
					mysqli_free_result($query);
				}
				mysqli_close($con);
				return $record;
			}
		/*END GET SINGLE RECORD FUNCTION */
		
		/*BEGIN GET RECORD FUNCTION */
			public function GetRecord($tablename,array $array){
				
				/*
					REQUIREMENT : 
						$tablename : Table Name where data will be inserted
						$array :  array 
								fields : * or field_1, field_2, field_n #BY DEFAULT *	
								where : where condition as per your requirement 
								orderby	: order by parameter : #BY DEFAULT PRIMARY KEY
								ordertype	: order type parameter : #BY DEFAULT PRIMARY KEY desc
								limit : limit of the record, 10 or 20 or n...
								startfrom : record starts from
								groupby : group by
					RETURN : RECORD ARRAY
				*/

				if(TABLE_PREFIX!=""){
					$tablename = TABLE_PREFIX.$tablename;
				}

				$record = array();

				if(!isset($array['fields']) || $array['fields']=="") {$array['fields']="*";}

				$query_string = "select ".$array['fields']." from ".$tablename." where 1=1 "; 	

				if(@$array['where']!=""){
					$query_string.=" and ".$array['where']." ";
				}

				//setting group by
				if(@$array['groupby']!=""){
					$query_string.=" group by ".$array['groupby'];
				}

				//seeting order by
				if(@$array['orderby']==""){
					$array['orderby']=1;
				}

				//setting order type
				if(@$array['ordertype']==""){
					$array['ordertype']="desc";
				}

				$query_string.=" order by ".$array['orderby']." ".$array['ordertype'];				

				//setting record start limit
				if(@$array['startfrom']==""){
					$array['startfrom']=0;
				}

				//setting record limit
				if(@$array['limit']>0 && is_numeric(@$array['limit'])){
					$query_string.=" limit ".$array['startfrom'].", ".$array['limit'];
				}

				/* TO CHECK QUERY REMOVE ENABLE BELOW CODE */
				//echo $query_string;exit;

				$con = $this->DBConnection();
				$query = mysqli_query($con,$query_string);
				if(@mysqli_num_rows($query)>0){

					while($data=mysqli_fetch_assoc($query)){
						$record[] = $data;
					}
					mysqli_free_result($query);

				}

				mysqli_close($con); 
				return $record;
			}
		/*END GET RECORD FUNCTION GetRecord()*/	

		/*BEGIN PAGINATION FUNCTION */
			public function PagiNation($tablename,$array= array()){
				
				/*
					REQUIREMENT : 
					$query : By defualt it will be generated from getrecord functions. You can also provide with custom query. 
					RETURN : display the pagination number in list 
				*/

				if(@$array['query']!=""){
					$query = $array['query'];
				} else {
					if(TABLE_PREFIX!=""){$tablename = TABLE_PREFIX.$tablename;}	

					$query = "select count(1) as total from ".$tablename." where 1=1 "; 	
					if(@$array['where']!="") {
						$query.=" and ".$array['where']." ";
					}
					
					if(@$array['groupby']!="") {
						$query.=" group by ".$array['groupby'];	
					}
				}

				

				echo '<div class="row">';	
					echo '<div class="col-md-6">';
						$data = $this->GetCustom($query); 
						$totalrecord_data=$data[0];
						if($totalrecord_data['total']>0){
							$lastpage = ceil($totalrecord_data['total']/$_SESSION['pagerecords_limit']);

							if($lastpage>1){

								echo '<ul class="pagination">';

									if(@$_GET['page']>1){
										echo '<li><a href="'.$_SERVER['PHP_SELF'].'?'.$this->PagingQueryString(1).'">&laquo;</a></li>';
									}

									$maxpage_display = 5;
									$startpage=1;
									if($lastpage>$maxpage_display){
										$endpage=$maxpage_display;
									} else {
										$endpage=$lastpage;	
									}

									if(@$_GET['page']<$maxpage_display){
										$startpage = 1;
									} else {
										$startpage = abs($_GET['page']-2);

										if(($_GET['page']+2)<$lastpage) {
											$endpage = abs($_GET['page']+2);
										} else {
											$endpage = $lastpage;
										}
									}

									if($startpage>4){

										echo '<li><a href="'.$_SERVER['PHP_SELF'].'?'.$this->PagingQueryString($startpage-2).'">&larr; Prev</a></li>';
									}

									for($a=$startpage;$a<=$endpage;$a++){

										if(@$_GET['page']==$a) {
											echo '<li class="active"><a href="'.$_SERVER['PHP_SELF'].'?'.$this->PagingQueryString($a).'"  style="background:#ddd !important;">'.$a.'</a></li>';
										} else {
											echo '<li><a href="'.$_SERVER['PHP_SELF'].'?'.$this->PagingQueryString($a).'">'.$a.'</a></li>';
										}
									}

									if($endpage<($lastpage-3)){
										echo '<li><a href="'.$_SERVER['PHP_SELF'].'?'.$this->PagingQueryString($endpage-2).'">Next &rarr;</a></li>';
									}
									
									if(@$_GET['page']<$lastpage){
										echo '<li><a href="'.$_SERVER['PHP_SELF'].'?'.$this->PagingQueryString($lastpage).'">&raquo;</a></li>';
									}

								echo '</ul>';
							}	
						}

					echo '</div>';		

					echo '<div class="col-md-4">';
						if($totalrecord_data['total']>20){

							echo '<ul class="pagination">';
								echo "<li><a>Records/Page : </a></li>";
								$record_array = array(20,50,100,200);
								foreach ($record_array as $key => $value){

									if($_SESSION['pagerecords_limit']==$value){
										$activerecord='style="background:#ddd !important;"';
									} else{
										$activerecord="";
									}

									echo '<li><a href="'.$_SERVER['PHP_SELF'].'?record='.$value.'" '.$activerecord.'>'.$value.'</a></li>';	
								}
							echo '</ul>';
						}
					echo '</div>';
				echo '</div>';				
			}

			private function PagingQueryString($pagevalue=1)
			{
				parse_str($_SERVER['QUERY_STRING'],$getarray);
				@$getarray['page'] = $pagevalue;
				return http_build_query($getarray);
			}
		/*END PAGINATION FUNCTION : PagiNation() */	

		/*BEGIN UPDATE RECORD FUNCTION */
			public function UpdateRecord($tablename,array $values,$where="")
			{

				/*
					REQUIREMENT : 
						$tablename : Table Name where data will be inserted
						$values :  	array 
									field_1 => value_1,field_2 => value_2,field_3 => value_3,field_n => value_n

									field names and values which will be updated.
						$where : 	Where condition in string : id = 1, id =1 and status=1

					RETURN : number of updated records 
				*/

				if(TABLE_PREFIX!=""){
					$tablename = TABLE_PREFIX.$tablename;
				}	

				if(!empty($values)){

					$con = $this->DBConnection();
					$query_string = "update ".$tablename." set ";

					foreach($values as $key=>$value){
						$query_string.=$key." = '".addslashes(mysqli_real_escape_string($con,$value))."' , ";
					}
					
					$query_string = trim($query_string," , ");

					if($where!=""){
						$query_string.=" where ".$where;
					}

					/* TO CHECK QUERY REMOVE ENABLE BELOW CODE */
					//echo $query_string;exit;
					
					mysqli_query($con,$query_string);
					$totalupdated = mysqli_affected_rows($con);
					mysqli_close($con);
					
				}

				//return mysql_affected_rows();
				return $totalupdated;
			}
		/*END UPDATE RECORD FUNCTION : UpdateRecord()*/	

		/*BEGIN DELETE RECORD FUNCTION*/	
			public function DeleteRecord($tablename, $where, $limit=0){

				/*
					REQUIREMENT : 
						$tablename : Table Name where data will be inserted
						$where : 	Where condition in string : id = 1, id =1 and status=1

					RETURN : number of deleted records 
				*/

				if(TABLE_PREFIX!=""){
					$tablename = TABLE_PREFIX.$tablename;
				}	

				$query_string = "delete from ".$tablename." ";

				if($where!=""){
					$query_string.=" where ".$where;
				}

				if($limit>0){
					$query_string.=" limit ".$limit;	
				}

				/* TO CHECK QUERY REMOVE ENABLE BELOW CODE */
				//echo $query_string;exit;

				$con = $this->DBConnection();
				mysqli_query($con,$query_string);
				$totaldeleted = mysqli_affected_rows($con);
				mysqli_close($con);

				return $totaldeleted;
			}
		/*END DELETE RECORD FUNCTION : DeleteRecord() */		

		/*BEGIN GET CUSTOM FUNCTION */
			public function GetCustom($query_string){

				/*
					REQUIREMENT : 
							$query = query string as per your requirements
				*/	

				$con = $this->DBConnection();
				$query = mysqli_query($con,$query_string);
				if(@mysqli_num_rows($query)>0){

					while($data=mysqli_fetch_assoc($query)){
						$record_array[] = $data;
					}

					mysqli_free_result($query);
				}

				mysqli_close($con); 

				return $record_array;
			}
		/*END GET CUSTOM FUNCTION : GetCustom() */	

	/*END DATABASE BASED NORMAL FUNCTION */

	/*BEGIN FILE MANAGEMENT FUNCTOIN */
		/*BEGIN UPLOAD FILE FUNCTION */
			public function UploadFile($files,array $array){

				$uploaded_files = array();

				if(isset($files) && $files['name']!=""){

					//CHANGING PERMISSION OF THE DIRECTORY
					@chmod($array['uploadpath'], 0755);

					if($array['limit']==0 || $array['limit']>@count($files['name'])){
						$array['limit']=@count($files['name']);
					}

					for($a=0;$a<$array['limit'];$a++){

						if(@$array['maxsize']<=0) {$array['maxsize']=5000;}
						$allowedfiletypes = $array['filetype'];
						$max_size = $array['maxsize']*1000;	//in KB

						$filename="";
						if($array['limit']>1){

							$currentfile_extension = end(@explode(".",$files['name'][$a]));

							if(in_array(strtolower($currentfile_extension),$allowedfiletypes)){

								$filename = date("YmdHis").rand(1000,9999).".".$currentfile_extension;

								if($files['size'][$a]<$max_size){	

									if(@move_uploaded_file($files['tmp_name'][$a], $array['uploadpath'].$filename)){

										$uploaded_files[]=$filename;

										//CHANGIN FILE PERMISSION
										@chmod($array['uploadpath'].$filename, 0755);
									}
								}
							}
						} else {

							$currentfile_extension = end(@explode(".",$files['name']));

							if(in_array(strtolower($currentfile_extension),$allowedfiletypes)){

								$filename = date("YmdHis").rand(1000,9999).".".$currentfile_extension;

								if($files['size'][$a]<$max_size){

									if(@move_uploaded_file($files['tmp_name'], $array['uploadpath'].$filename)){

										$uploaded_files[]=$filename;

										//CHANGIN FILE PERMISSION
										@chmod($array['uploadpath'].$filename, 0755);
									}
								}
							}
						}
					}
				}

				return $uploaded_files;
			}
		/*END UPLOAD FILE FUNCTION : UploadFile() */

		/*BEGIN DELETE FILE FUNCTION */
			public function DeleteFile(array $array){

				foreach ($array['files'] as $key => $value){
					@unlink($array['uploadpath'].$value);
				}
			}
		/*DELETE FILE FUNCTION*/

	/*END FILE MANAGEMENT FUNCTION */

	/*BEGIN DATE DIFFERENCE FUNCTION*/
		public function DateDifference($date1,$date2){

			$difference = abs(strtotime($date1)-strtotime($date2));
	        $difference_array['hours'] = ceil($difference/(60*60));
	        $difference_array['days'] = floor($difference_array['hours']/24);
	        $difference_array['extra_hours'] = abs($difference_array['hours']%24);

	        return $difference_array;
		}
	/*END DATE DIFFERENCE FUNCTION : DateDifference()*/

	/*BEGIN PASSWORD ENCRYPTION FUNCTION*/
		protected function MakePassword($string){

			if(!empty($string)){

				$string=SECRET_KEY.$string;		//SECRET_KEY IS DEFINED IN THE config.php FILE.
				return base64_encode($string);	
				//return sha1($string);	
				//return hash("sha256", $string);	
				//return hash("sha512", $string);
			}
		}
	/*END PASSWORD ENCRYPTION FUNCTION*/

	/*BEGIN PASSWORD ENCRYPTION FUNCTION*/
		protected function GetPassword($string){

			if(!empty($string)){
				
				$string=SECRET_KEY.$string;		//SECRET_KEY IS DEFINED IN THE config.php FILE.
				$plain_text = base64_decode($string);
				return str_replace(SECRET_KEY, "", $plain_text);
				//return sha1($string);	
				//return hash("sha256", $string);	
				//return hash("sha512", $string);
			}
		}
	/*END PASSWORD ENCRYPTION FUNCTION*/

}

?>
