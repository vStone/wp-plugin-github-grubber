<?php
/**
* 
*/
class Grubber
{
	const github_api_base = 'http://github.com/api/v1/xml/';
	private $username = null;
	private $simXML = null;
	
	public function Grubber($uname = 'owenbyrne') {
		$this->username = $uname;
	}
	
	public function grub() {
		if($this->simXML == null && 
			(($contents = @file_get_contents($this->github_api_url())) == true))
				$this->simXML = new SimpleXMLElement($contents);
	}
	
	public function get_repositories() {
		if ($this->simXML == null) {
			return null;
		} else {
			return $this->simXML->repositories;
		}
	}
	
	public function get_username() {
		return $this->username;
	}
	
	public function github_api_url() {
		return self::github_api_base . $this->username;
	}	
}
?>