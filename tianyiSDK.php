<?php
class tianyiSDK{
	private $AppId;
	private $AppSecret;
	private $Batch;
	private $AuthCode;
	private $AccessToken;
	public function __construct($aid,$sid){
		$this->AppId = $aid;
		$this->AppSecret = $sid;
		$this->Batch = date('Y-m-d H:i:s');
		echo $this->AccessToken = $this->getAT();
	}
	public function curl_post($url,$postdata){
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_POST,1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,$postdata);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
        $result = curl_exec($ch);		
		return $result;	
		curl_close($ch);
	}
	public function curl_get($url){
		$ch = curl_init($url);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION,1);
        curl_setopt($ch, CURLOPT_HEADER,0);  // DO NOT RETURN HTTP HEADERS
        curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);  // RETURN THE CONTENTS OF THE CALL
        $result = curl_exec($ch);		
		return $result;	
		curl_close($ch);
	}
	public function getSign($param){
		$param_values = explode("&",$param);
		print_r($param_values);
		$param_keys = array();
		foreach($param_values as $val){
			$t = explode("=",$val);
			array_push($param_keys,$t[0]);
		}
		$param_array = array_combine($param_keys,$param_values);
		ksort($param_array);
		$param = join("&",$param_array);
		$param_array['sign'] = "sign=".rawurlencode(base64_encode(hash_hmac("sha1",$param,$this->AppSecret,$raw_output=True)));
		ksort($param_array);
		$signed_param = join("&",$param_array);
		return $signed_param;
	}
	public function getAT(){
		$url = 'https://oauth.api.189.cn/emp/oauth2/v3/access_token';
		$postdata = 'grant_type=client_credentials&app_id='.$this->AppId.'&app_secret='.$this->AppSecret;
		$result = $this->curl_post($url,$postdata);
		$raw = json_decode($result);
		//print_r($raw);
		if ($raw->res_code == "0") {
			return $raw->access_token;
		}elseif ($raw->res_code == "10000") {
			return $raw->res_message;
		}
	}
	public function getToken(){
		$url = "http://api.189.cn/v2/dm/randcode/token?";
		$params = "app_id=".$this->AppId;
		$params .= "&access_token=".$this->AccessToken;
		$params .= "&timestamp=".$this->Batch;
		$params = $this->getSign($params);
		$result = $this->curl_get($url.$params);
		$raw = json_decode($result);
		if($raw->res_code == 0){
			return $raw->token;
		}
		else{
			return $raw->res_message;
		}
	}
	public function SendTemplateSMS($to,$data,$tempId){
		$url = "http://api.189.cn/v2/emp/templateSms/sendSms";
		$paramdata = json_encode($data);
		$postdata = 'acceptor_tel='.$to.'&template_id='.$tempId.
					'&template_param='.$paramdata.
					'&app_id='.$this->AppId.'&access_token='.$this->AccessToken.
					'&timestamp='.$this->Batch;
		$result = $this->curl_post($url,$postdata);
		$raw = json_decode($result);
		if($raw->res_code == '0'){
			return $raw->idertifier;
		}
		else{
			return $raw->res_message;
		}
	}
}
?>
