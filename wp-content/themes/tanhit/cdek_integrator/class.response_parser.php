<?php  

abstract class response_parser {
	
	protected $data;
	
	public function setData($data) {
		$this->data = $data;
	}
	
	abstract public function getData();
}
?>