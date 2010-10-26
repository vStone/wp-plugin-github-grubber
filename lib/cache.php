<?php
/**
* 
*/
class Cache 
{
	private $cache_file = null;
	private $timeout = 900; // 15 minute timeout
	function Cache()
	{
		$this->cache_file = dirname(__FILE__) . "/../cache/grubby.cache";
	}
	
	public function get()
	{
		if (file_exists($this->cache_file) && filemtime($this->cache_file) + $this->timeout > time()) {
			$repositories = unserialize(file_get_contents($this->cache_file));
			
			if(is_array($repositories))
				return $repositories;
		}
		return null;
	}
	
	public function set($repositories)
	{
		try {
				file_put_contents($this->cache_file, serialize($repositories));
		} catch (Exception $e) {
			// probably no permission
		}
	}
	
	public function clear()
	{
		try {
			unlink($this->cache_file);
		} catch (Exception $e) {
			// boo! have to wait for the timeout :(
		}
	}
}

?>