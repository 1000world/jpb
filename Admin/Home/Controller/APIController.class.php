<?php
namespace Home\Controller;
use Think\Controller;

class APIController extends Controller {
	private function fprint_f($str)
	{
		echo "<pre>";
		print_r($str);
		echo "</pre>";
	}

	private function throw_error($error_id,$desc)
	{
		/*

		*/
		return array('status'=>0,'error'=>$error_id,'desc'=>$desc);
	}

	private function save_login_token($pid,$token)
	{
		//change to redis later,use the db for now
		$tokens = M();
		$sql = "insert into jpb_tokens (pid,token) value (".$pid.",'".$token."') on duplicate key update token='".$token."'";
		$tokens->execute($sql);
	}


	private function login($data){
		if(!isset($data["account"]) || !isset($data["password"]))
		{
			return $this->throw_error(1,'account or password is empty');
		}
		$key = $data["account"];
		$pwd = $data["password"];

		$users = M('users');
		$result = $users->where(' (pid = '. $key .' AND password= "' . $pwd . '") OR (cellphone = "'. $key .'" AND password= "'. $pwd .'")')->find();
		if(empty($result)){
			$token = $this->throw_error(1,'invalid account or password');
		}else
		{
			$salt_md5 = md5($result['pid'] . $result['password'] . 'JinPingBao!' . time());
			$token = array('status'=>1,'token'=>$salt_md5,'cellphone'=>$result['cellphone'],'pid'=>$result['pid']);

			$this->save_login_token($result['pid'],$salt_md5);

		}
		return $token;
	}

	private function register($data){
		$user = array();
		if(!isset($data["cellphone"]) || !isset($data["password"]) || !isset($data["code"]))
		{
			return $this->throw_error(1,'cellphone or password is empty');
		}

		$cellphone = $data["cellphone"];
		$code = $data["code"];
		$table = M('verify_code');
		$result = $table->where('cellphone = '.$cellphone.' AND code = '.$code. ' AND create_at >' .(time() - 600))->find();
		if(empty($result)) return $this->throw_error(1,'invalid code');


		$user["cellphone"] = $data["cellphone"];
		$user["password"] = $data["password"];
		$user["sex"] = isset($data["sex"])?$data["sex"]:0;
		$user["age_id"] = isset($data["age_id"])?$data["age_id"]:6;
		$user["ref_pid"] = isset($data["ref_pid"])?$data["ref_pid"]:0;
		$user["created_at"] = $user["updated_at"] = time();
		$users = D('users');
		$users->create();

		for($i = 0; $i < 5; $i++){
			$user["pid"] = rand(10000000,99999999);
			$result = $users->add($user);
			if($result) break;
		}
		//fix it later
		if($i == 5) return $this->throw_error(1,'fail to create user');

		$salt_md5 = md5($user['pid'] . $user['password'] . 'JinPingBao!' . time());
		$token = array('status'=>1,'token'=>$salt_md5,'cellphone'=>$user['cellphone'],'pid'=>$user['pid']);
		$this->save_login_token($user['pid'],$salt_md5);

		return $token;
	}

	private function verify_code($data){
		if(!isset($data["cellphone"])  || !isset($data["code"]))
		{
			return $this->throw_error(1,'cellphone or code is empty');
		}

		$cellphone = $data["cellphone"];
		$code = $data["code"];
		$table = M('verify_code');
		$result = $table->where('cellphone = '.$cellphone.' AND code = '.$code. ' AND create_at >' .(time() - 600))->find();
		if(empty($result)) return $this->throw_error(1,'invalid code');

		$result["status"] = 1;
		$table->save($result);
		return array('status'=>1);
	}

	private function apply_verify($data){
		if(!isset($data["cellphone"]))
		{
			return $this->throw_error(1,'cellphone is empty');
		}
		$verify = array();
		$verify["cellphone"] = $data["cellphone"];
		$verify["code"] = 1111; //rand(1000,9999);
		$verify["create_at"] = time();
		$table = M('verify_code');
		$table->create();
		$result = $table->add($verify);
		if(!$result) return $this->throw_error(2,'create code failed');
		return array('status'=>1);
	}

	public function request(){
		if(isset($_POST["action"])) $data = $_POST;
		else if(isset($_GET["action"])) $data = $_GET;

		$action = $data["action"];
		$response = "";
		switch($action)
		{
			case "apply_verify": //http://localhost/jpb/admin.php/API/request?action=apply_verify&cellphone=CELLPHONE
				$response = json_encode($this->apply_verify($data));
				break;
			case "verify_code": //http://localhost/jpb/admin.php/API/request?action=verify_code&cellphone=CELLPHONE&code=CODE
				$response = json_encode($this->verify_code($data));
				break;
			case "register": //http://localhost/jpb/admin.php/API/request?action=register&cellphone=CELLPHONE&password=PASSWORD&code=1111
				$response = json_encode($this->register($data));
				break;
			case "login": //http://localhost/jpb/admin.php/API/request?action=login&account=CELLPHONE_OR_PID&password=PASSWORD
				$response = json_encode($this->login($data));
				break;
			default:
				$response = json_encode($this->throw_error(2,'invalid action'));
		}
		echo $response;
		return true;
	}


}
?>