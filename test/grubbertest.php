<?php
/**
 * GrubberTest
 * Author: Owen Byrne
 * Author URI: http://whoisowenbyrne.com
 */
require dirname(__FILE__) . '/../lib/grubber.php';

class GrubberTest extends PHPUnit_Framework_TestCase {
	
	public function test_constructor_sets_default_username() {
		$grubber = new Grubber();
		$this->assertEquals('owenbyrne', $grubber->get_username());
	}
	
	public function test_constructor_sets_specified_username() {
		$grubber = new Grubber('chuck_norris');
		$this->assertEquals('chuck_norris', $grubber->get_username());
	}
	
	public function test_github_api_url_well_formed() {
		$grubber = new Grubber();
		$this->assertEquals('http://github.com/api/v1/xml/owenbyrne', $grubber->github_api_url());
	}
}
?>