<?php
/**
* 
*/
require dirname(__FILE__) . '/cache.php';
class Grubber
{
	const github_api_base = 'http://github.com/api/v1/xml/';
	private $username = null;
	private $cache = null;
	public function Grubber($uname = 'owenbyrne') {
		$this->username = $uname;
		$this->cache = new Cache();
	}
	
	public function grub() {
		if((($contents = @file_get_contents($this->github_api_url())) == true)) {
		 	$response = new SimpleXMLElement($contents);
			return $response->repositories;
		}
		return null;
	}
	
	public function update() {
		$this->cache->clear();
	}
	
	public function get_repositories() {
		$repositories = null;
		
		if(($repositories = $this->cache->get()) == null) {
			$repositories = $this->grub();
			
			$clean_repos = array();
			foreach ($repositories->repository as $repository) {
				$repo = array();
				foreach ($repository->children() as $key => $value) {
					$repo[$key] = (string) $value;
				}
				$clean_repos[] = $repo;
			}
			
			$this->cache->set($clean_repos);
			$repositories = $clean_repos;
		}
		return $repositories;
	}
	
	public function get_username() {
		return $this->username;
	}
	
	public function github_api_url() {
		return self::github_api_base . $this->username;
	}	
}
?>