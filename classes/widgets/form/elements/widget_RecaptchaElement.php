<?php
class widget_RecaptchaElement extends widget_AbstractElement {
	private $options = array();

	private $publicKey = '';

	public function __construct($args) {
		if(!isset($args['apiKey'])) {
			throw new Exception('Recaptcha widget required the apiKey index to be set');
		}
		$this->publicKey = $args['apiKey'];
	}
	function renderElement() {
		$output = sf('
			<div class="g-recaptcha" data-sitekey="%s"></div>', $this->publicKey);
		return $output;
	}
}
?>
