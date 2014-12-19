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

//status : 0 success 1 error 2 token fail
	private function throw_error($error_id,$desc,$status = 0)
	{
		/*

		*/
		return array('status'=>$status,'error'=>$error_id,'desc'=>$desc);
	}

	private function save_login_token($pid,$token)
	{
		//change to redis later,use the db for now
		$tokens = M();
		$sql = "insert into jpb_tokens (pid,token) value (".$pid.",'".$token."') on duplicate key update token='".$token."'";
		$tokens->execute($sql);
	}

	private function initUser($pid)
	{
		$ed_ads = M("ed_ads");
		$data = array();
		$data["pid"] = $pid;
		$data["ad_id"] = 1;
		$data["left_status"] = 0;
		$data["right_status"] = 0;
		$data["created_at"] = $data["updated_at"] = time();
		$ed_ads->add($data);
		$data["ad_id"] = 2;
		$ed_ads->add($data);
		$data["ad_id"] = 3;
		$ed_ads->add($data);

		$ed_campaign = M("ed_campaign");
		$campaign = array();
		$campaign["pid"] = $pid;
		$campaign["created_at"] = $campaign["updated_at"] = time();
		$campaign["cid"] = 1;
		$ed_campaign->add($campaign);
	}

	private function get_pid_from_token($token){
		$table = M('tokens');
		$result = $table->where('token = "'.$token.'"')->find();
		if(empty($result)) return NULL;		
		$pid = $result["pid"];
		return $pid;
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
		if(!isset($data["cellphone"]) || !isset($data["password"]))
		{
			return $this->throw_error(1,'cellphone or password is empty');
		}

		$cellphone = $data["cellphone"];
		$code = $data["code"];
		$table = M('verify_code');
		$result = $table->where('cellphone = '.$cellphone.' AND status = 1')->find();
		if(empty($result)) return $this->throw_error(1,'invalid code');


		$user["cellphone"] = $data["cellphone"];
		$user["password"] = $data["password"];
		$user["sex"] = isset($data["sex"])?$data["sex"]:0;
		$user["age_id"] = isset($data["age_id"])?$data["age_id"]:0;
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

		$this->initUser($user["pid"]);

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

	private function left_action($data){
		if(!isset($data["token"])  || !isset($data["ad_id"]))
		{
			return $this->throw_error(1,'token or ad_id is missing');
		}
		$token = $data["token"];
		$ad_id = $data["ad_id"];

		$pid = $this->get_pid_from_token($token);
		if(is_null($pid)){
			return $this->throw_error(1,'access denied',2);
		}

		$ads = M('ed_ads');
		$result = $ads->where('pid = '.$pid. ' AND ad_id = '.$ad_id )->find();
		if(empty($result)) return $this->throw_error(1,'miss matched ad_id');
		$result["left_status"] = 1;
		$result["updated_at"] = time();
		if($ads->save($result))
			return array('status'=>1);
		else
			return $this->throw_error(1,'operation failed');
	}

	private function right_action($data){
		if(!isset($data["token"])  || !isset($data["ad_id"]))
		{
			return $this->throw_error(1,'token or ad_id is missing');
		}
		$token = $data["token"];
		$ad_id = $data["ad_id"];

		$pid = $this->get_pid_from_token($token);
		if(is_null($pid)){
			return $this->throw_error(1,'access denied',2);
		}

		$ads = M('ed_ads');
		$result = $ads->where('pid = '.$pid. ' AND ad_id = '.$ad_id )->find();
		if(empty($result)) return $this->throw_error(1,'miss matched ad_id');
		$result["right_status"] = 1;
		$result["updated_at"] = time();
		if($ads->save($result))
			return array('status'=>1);
		else
			return $this->throw_error(1,'operation failed');
	}

	private function page_home($data){
		if(!isset($data["token"]))
		{
			return $this->throw_error(1,'token is missing');
		}
		$token = $data["token"];

		$pid = $this->get_pid_from_token($token);
		if(is_null($pid)){
			return $this->throw_error(1,'access denied',2);
		}

		$all_ads = M('all_ads');
		$all_camp = M('all_campaign');
		$today_time = strtotime('today');
		$total = $all_ads->where('pid='.$pid)->sum('left_total') + $all_ads->where('pid='.$pid)->sum('right_total') + $all_camp->where('pid='.$pid)->sum('price');
		$today = $all_ads->where('pid='.$pid.' AND updated_at >'.$today_time)->sum('left_total') + $all_ads->where('pid='.$pid.' AND updated_at >'.$today_time)->sum('right_total') + $all_camp->where('pid='.$pid.' AND updated_at >'.$today_time)->sum('price');
		
		return array('status'=>1,'total'=>$total,'today'=>$today,'left'=>$total);
				
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
			case "register": //http://localhost/jpb/admin.php/API/request?action=register&cellphone=CELLPHONE&password=PASSWORD&ref_pid=PID
				$response = json_encode($this->register($data));
				break;
			case "login": //http://localhost/jpb/admin.php/API/request?action=login&account=CELLPHONE_OR_PID&password=PASSWORD
				$response = json_encode($this->login($data));
				break;
			case "right_action": //http://localhost/jpb/admin.php/API/request?action=right_action&token=TOKEN&ad_id=AD_ID
				$response = json_encode($this->right_action($data));
				break;
			case "left_action": //http://localhost/jpb/admin.php/API/request?action=left_action&token=TOKEN&ad_id=AD_ID
				$response = json_encode($this->left_action($data));
				break;
			case "page_home": //http://localhost/jpb/admin.php/API/request?action=page_home&token=TOKEN
				$response = json_encode($this->page_home($data));
				break;
			default:
				$response = json_encode($this->throw_error(2,'invalid action'));
		}
		echo $response;
		return true;
	}


}
?>