<?php
	require_once("Rest.inc.php");
	
	class API extends REST {
		public $data = "";
		public function __construct(){
			parent::__construct();				// Init parent contructor
		}

		protected function register(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			if(!empty($this->_request['email']) && !empty($this->_request['password'])){

				$check_info = array(
						'fields'=>'user_id,email',
						'where'=>'email like "'.$this->_request['email'].'"'
					);
				$exist_email = $this->GetSingleRecord("user_master",$check_info);

				if(count($exist_email)>0) {
					$response_array['status']='fail';
					$response_array['message']='Email already exists.';
					$response_array['data']='';
					$this->response($this->json($response_array), 200);
 				} else {
					$info_array = array(
							'firstname'=>$this->_request['firstname'],
							'lastname'=>$this->_request['lastname'],
							'email'=>$this->_request['email'],
							'password'=>$this->MakePassword($this->_request['password']),
							'register_date'=>date("Y-m-d H:i:s"),
							'register_ipaddress'=>$_SERVER['REMOTE_ADDR']
						);
					//$this->response($this->json($info_array), 200);		
					$user_id = $this->InsertRecord("user_master",$info_array);

					if($user_id>0) {
						$response_array['status']='success';
						$response_array['message']='register successfully.';
						$response_array['data']=array('user_id'=>$user_id);
						$this->response($this->json($response_array), 200);
					} else {
						$response_array['status']='fail';
						$response_array['message']='insufficient data.';
						$response_array['data']='';
						$this->response($this->json($response_array), 204);
					}
				}
			}
		}

		protected function login(){
			// Cross validation if the request method is POST else it will return "Not Acceptable" status
			if($this->get_request_method() != "POST"){
				$this->response('',406);
			}
			
			$email = $this->_request['email'];		
			$password = $this->_request['password'];

			if(!empty($email) && !empty($password) && $this->validate($email,'email')){

				$info_array = array(
						"fields"=>"user_id,firstname,lastname,email,active_status",
						"where"=>"email = '".$email."' and password = '".$this->MakePassword($password)."'"
					);
				$user_data = $this->GetSingleRecord("user_master",$info_array);

				if(count($user_data)>0) {
					$response_array['status']='success';
					$response_array['message']='logged in successfully.';
					$response_array['data']=$user_data;
					$this->response($this->json($response_array), 200);
				} else {
					$response_array['status']='fail';
					$response_array['message']='invalid email or password.';
					$response_array['data']='';
					$this->response($this->json($response_array));
				}
			}
			
			// If invalid inputs "Bad Request" status message and reason
			$error = array('status' => "Failed", "msg" => "Invalid data");
			$this->response($this->json($error), 400);
		}
		
		protected function users(){	
			// Cross validation if the request method is GET else it will return "Not Acceptable" status
			if($this->get_request_method() != "GET"){
				$this->response('',406);
			}

			$info_array = array(
						"fields"=>"user_id,firstname,lastname,email,active_status"
					);
			$user_data = $this->GetRecord("user_master",$info_array);

			if(count($user_data)>0) {
				$response_array['status']='success';
				$response_array['message']='Total '.count($user_data).' record(s) found.';
				$response_array['total_record']= count($user_data);
				$response_array['data']=$user_data;
				$this->response($this->json($response_array), 200);
			} else {
				$response_array['status']='fail';
				$response_array['message']='Record not found.';
				$response_array['data']='';
				$this->response($this->json($response_array), 204);
			}
		}

		protected function deleteuser(){
			// Cross validation if the request method is DELETE else it will return "Not Acceptable" status
			if($this->get_request_method() != "DELETE"){
				$this->response('',406);
			}
			$id = (int)$this->_request['id'];
			if($id > 0){
				$where = "user_id = '".$id."'";
				$delete = $this->DeleteRecord("user_master",$where);

				if($delete>0) {
					$response_array['status']='success';
					$response_array['message']='Total '.count($delete).' record(s) Deleted.';
					$response_array['data']=$delete;
					$this->response($this->json($response_array), 200);
				} else {
					$response_array['status']='fail';
					$response_array['message']='no record deleted';
					$response_array['data']='';
					$this->response($this->json($response_array), 200);
				}
			} else {
				$this->response('',204);	// If no records "No Content" status
			}
		}
	}
	// Initiiate Library
	$api = new API();
	$api->processApi();
?>