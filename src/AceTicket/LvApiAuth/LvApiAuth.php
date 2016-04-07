<?php  namespace AceTicket\LvApiAuth;

class LvApiAuth {
	
	public function __construct() {
		$this->LvApiAuthUserProvider = new LvApiAuthUserProvider();
	}
}