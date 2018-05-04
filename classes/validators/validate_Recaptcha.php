<?php
class validate_Recaptcha extends validate_AbstractValidator {
	private $privateKey;
	public function __construct($privateKey) {
		$this->privateKey = $privateKey;
	}
	public function getUserIp () {
	
		// cloudflare
		if (array_key_exists('HTTP_CF_CONNECTING_IP', $_SERVER))
			return $_SERVER['HTTP_CF_CONNECTING_IP'];
		
		// proxy
		elseif  (array_key_exists('HTTP_X_FORWARDED_FOR', $_SERVER))
			return $_SERVER['HTTP_X_FORWARDED_FOR'];
		
		// direct IP
		elseif  (array_key_exists('REMOTE_ADDR', $_SERVER))
			return $_SERVER['REMOTE_ADDR'];
		
		else return false;
		
	}
	public function validate($data) {
        

//        $response = $_POST["g-recaptcha-response"];
//        dump($response);
        
        $url = 'https://www.google.com/recaptcha/api/siteverify';
        $data = array(
            'secret' => $this->privateKey,
            'remoteip' => $this->getUserIp(),
            'response' => $_POST["g-recaptcha-response"]
        );
        $options = array(
            'http' => array (
                'method' => 'POST',
                'header' => "Content-Type: application/x-www-form-urlencoded\r\n",
                'content' => http_build_query($data)
            )
        );
        $context  = stream_context_create($options);
        $verify = file_get_contents($url, false, $context);
        $captcha_success=json_decode($verify);
        if ($captcha_success->success==false) {
           throw new Exception('Invalid Captcha');
        } else if ($captcha_success->success==true) {
            return true;
        }

        
	}	

}

?>
