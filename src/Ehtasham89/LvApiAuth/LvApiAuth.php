<?php  

namespace Ehtasham89\LvApiAuth;

class LvApiAuth {
	
	public function __construct() {
		$this->LvApiAuthUserProvider = new LvApiAuthUserProvider();
	}
}