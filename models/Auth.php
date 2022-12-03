<?php
    class Auth {
        private $conn;
		private $sql;
		private $data = array();
		private $info = [];
		private $status =array();
		private $failed_stat = array(
			'remarks'=>'failed',
			'message'=>'Failed to retrieve the requested records'
		);
		private $success_stat = array(
			'remarks'=>'success',
			'message'=>'Successfully retrieved the requested records'
		);

		public function __construct($db){
			$this->conn = $db;
        }
        
        function encryptPassword($password): ?string{
            $hashFormat ="$2y$10$";
            $saltLength =22;
            $salt = $this->generateSalt($saltLength);
            return crypt($password, $hashFormat.$salt);
        }

        function generateSalt($len){
            $urs=md5(uniqid(mt_rand(), true));
            $b64string = base64_encode($urs);
            $mb64string = str_replace('+','.', $b64string);
            return substr($mb64string, 0, $len);
        }
        
        function register_user($dt){
            $payload = $dt;
            $encryptedPassword = $this->encryptPassword($dt->password);

            $payload = array(
                'uname'=>$dt->username,
                'password'=>$this->encryptPassword($dt->password)
            );
            
            $this->sql = "INSERT INTO tbl_user(user_fname, user_lname, user_emailadd, user_contact, user_username, user_password, user_role)
                VALUES ('$dt->fname', '$dt->lname', '$dt->email', '$dt->contactnum', '$dt->username', '$encryptedPassword',  1)"; //replace 0 with $dt_role if already fixed in the frontend;
            $this->conn->query($this->sql);

            $this->data = $payload;

            return array(
				'status'=>$this->status,
				'payload'=>$this->data,
				'prepared_by'=>'Trez Bandidos',
				'timestamp'=>date('D M j, Y h:i:s e')
			);
        }

        function update_profile($dt){

            $payload = $dt;
            $encryptedPassword = $this->encryptPassword($dt->password);

            $this->sql = "UPDATE tbl_user SET user_fname='$dt->fname', user_lname='$dt->lname', user_emailadd='$dt->email', user_contact='$dt->contactnum', user_password='$encryptedPassword' WHERE user_id=$dt->userid";
            $this->conn->query($this->sql);
            return $this->select('tbl_user', null);
        }

        function updateCompany($dt){

            $payload = $dt;

            $this->sql = " UPDATE tbl_companies SET company_name='$dt->company_name1', company_location='$dt->company_location1', company_contact='$dt->company_contact1' WHERE company_id='$dt->company_id1' ";
            $this->conn->query($this->sql);
            return $this->select('tbl_companies', null);
        }

        function generalQuery($query){

            $this->result = $this->conn->query($query);
            $rowCount = $this->result->num_rows;
            if ($this->result->num_rows>0) {
                while($res = $this->result->fetch_assoc()){
                    array_push($this->data,$res);
                }
                return $this->info = array(
                        'status'=>array(
                        'remarks'=>true,
                        'message'=>'Data retrieval successful.'
                    ),
                    'data' =>$this->data,
                    'payload'=>$this->data,
                    'dataCount'=>$rowCount,
                    'timestamp'=>date('D M j, Y h:i:s e'),
                    'prepared_by'=>'Trez Bandidos'
                );
    
            } else {
                return $this->info = array('status'=>array(
                    'remarks'=>false,
                    'payload'=>$this->data,
                    'dataCount'=>$rowCount,
                    'message'=>'Data retrieval failed.'),
                    'timestamp'=>date('D M j, Y h:i:s e'),
                    'prepared_by'=>'Trez Bandidos' );
            }

        }

        function checkUsernameEmailExist($dt) {
            $payload = $dt;

            $this->sql = "SELECT * FROM tbl_user WHERE user_username = '$dt->username'";
            // $this->sql = "SELECT * FROM tbl_user WHERE user_emailadd = '$dt->email'";

            if($result = $this->conn->query($this->sql)){
                if($result->num_rows>0){
                    while($res = $result->fetch_assoc()){
                        array_push($this->data, $res);
                    }
                    http_response_code(200);
                    $this->status = array(
                    'remarks'=>'failed',
                    'message'=>'Username is already taken.',
                );
                }
                
                if ($result->num_rows === 0) {
                    http_response_code(200);
                    $this->status = array(
                    'remarks'=>'success',
                    'message'=>'Username is available',
                );
                }   
            } 

            else {
                http_response_code(200);
                $this->status = array(
                    'remarks'=>'success',
                    'message'=>'Username is available.',
                );
                }

            $this->conn->query($this->sql);
            $this->status = $payload;

            return array(
                'status'=>$this->status,
                'payload'=>$this->data,
                'prepared_by'=>'Trez Bandidos',
                'timestamp'=>date('D M j, Y h:i:s e')
            );
        }


        function review_cateringservice($dt) {
            $payload = $dt;

            $payload = array(
                'contname'=>$dt->contname,
                'commentcontent'=>$dt->commentcontent,
                'servicename'=>$dt->servicename
            );
            
            $this->sql = "INSERT INTO tbl_reviews (review_content, review_user, review_service) VALUES 
            ('$dt->commentcontent', '$dt->contname', '$dt->servicename')"; 
            
            $this->conn->query($this->sql);

            $this->data = $payload;

            return array(
                'status'=>$this->status,
                'payload'=>$this->data,
                'prepared_by'=>'Trez Bandidos',
                'timestamp'=>date('D M j, Y h:i:s e')
            );
        }

        function addCompany($dt) {
            $payload = $dt;

            $this->sql = "INSERT INTO tbl_companies (company_name, company_location, company_contact) VALUES 
            ('$dt->company_name', '$dt->company_location', '$dt->company_contact')"; 
            
            $this->conn->query($this->sql);

            $this->data = $payload;

            return array(
                'status'=>$this->status,
                'payload'=>$this->data,
                'prepared_by'=>'Trez Bandidos',
                'timestamp'=>date('D M j, Y h:i:s e')
            );
        }

        function deleteUser($dt) {
            $payload = $dt;

            $this->sql = "DELETE FROM tbl_user WHERE user_id = '$dt->user_id'"; 
            
            $this->conn->query($this->sql);

            $this->data = $payload;

            return array(
                'status'=>$this->status,
                'payload'=>$this->data,
                'prepared_by'=>'Trez Bandidos',
                'timestamp'=>date('D M j, Y h:i:s e')
            );
        }

        function deleteCompany($dt) {
            $payload = $dt;

            $this->sql = "DELETE FROM tbl_companies WHERE company_id = '$dt->company_id'"; 
            
            $this->conn->query($this->sql);

            $this->data = $payload;

            return array(
                'status'=>$this->status,
                'payload'=>$this->data,
                'prepared_by'=>'Trez Bandidos',
                'timestamp'=>date('D M j, Y h:i:s e')
            );
        }

        function select($table, $filter_data) {
            $this->sql = "SELECT * FROM $table";

            if($filter_data!=null){
                $this->sql.=" WHERE review_id='$filter_data'";
                $this->sql.=" WHERE user_id='$filter_data'";
            }

            if($result = $this->conn->query($this->sql)){
                if($result->num_rows>0){
                    while($res = $result->fetch_assoc()){
                        array_push($this->data, $res);
                    }
                    $this->status = $this->success_stat;
                    http_response_code(200);
                }
            }
            return array(
                'status'=>$this->status,
                'payload'=>$this->data,
                'prepared_by'=>'Trez Bandidos',
                'timestamp'=>date('D M j, Y h:i:s e')
            );
        }

        function loadCurrentUser($dt) {
            $this->sql = "SELECT * FROM tbl_user WHERE user_id = '$dt->userid'";

            if($result = $this->conn->query($this->sql)){
                if($result->num_rows>0){
                    while($res = $result->fetch_assoc()){
                        array_push($this->data, $res);
                    }
                    $this->status = $this->success_stat;
                    http_response_code(200);
                }
            }
            return array(
                'status'=>$this->status,
                'payload'=>$this->data,
                'prepared_by'=>'Trez Bandidos',
                'timestamp'=>date('D M j, Y h:i:s e')
            );
        }

        
function login_user($dt){
            $payload = $dt;
            $uname = $this->conn->real_escape_string($dt->uname);
            $pword = $this->conn->real_escape_string($dt->pword);

            $this->sql="SELECT * FROM tbl_user WHERE user_username='$uname' LIMIT 1";

            if($result=$this->conn->query($this->sql)){
                if($result->num_rows>0){
                    $res=$result->fetch_assoc();
                    if($this->pwordCheck($pword, $res['user_password'])){
                http_response_code(200);
                $this->data = array(
                    'uname'=>$res['user_username'],
                    'fname'=>$res['user_fname'],
                    'lname'=>$res['user_lname'],
                    'password'=>$res['user_password'],
                    'emailadd'=>$res['user_emailadd'],
                    'contactnumber'=>$res['user_contact'],
                    'role'=>$res['user_role'],
                    'uid'=>$res['user_id'],
                );
                $this->status = array(
                    'remarks'=>'success',
                    'message'=>'Successfully logged in',
                );


            }else{
                http_response_code(200);
                $this->status = array(
                    'remarks'=>'failed',
                    'message'=>'Incorrect username or password',
                );
            }
            }else{
                http_response_code(200);
                $this->status = array(
                    'remarks'=>'failed',
                    'message'=>'Incorrect username or password',
                );
            }
        }else{
                http_response_code(200);
                $this->status = array(
                    'remarks'=>'failed',
                    'message'=>'Incorrect username or password',
                );
            }
            $this->status = $payload;
            return array(
                'status'=>$this->status,
                'payload'=>$this->data,
                'prepared_by'=>'Trez Bandidos',
                'timestamp'=>date('D M j, Y h:i:s e')
            );
        }
        
        function pwordCheck($password, $existingpw){
            $hash=crypt($password, $existingpw);
            if($hash === $existingpw){return true;} else {return false;}
        }
        



    }
?>