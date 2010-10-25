<?php
/**
 * Plugin Name: Git Grubber
 * Plugin URI: http://whoisowenbyrne.com/test-plugin
 * Description: Display a users public GitHub repositories
 * Author: Owen Byrne
 * Author URI: http://whoisowenbyrne.com
 * Version: 1.0
 */
require(dirname(__FILE__) . '/lib/grubber.php');
class GitGrubber extends WP_Widget {

	function GitGrubber() {
		parent::WP_Widget(false, $name = 'Git Grubber');	
	}

	function form($instance) {
		$title = self::get_title($instance);
		$username = self::get_username($instance);
		?>
	    	<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> 
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
					name="<?php echo $this->get_field_name('title'); ?>" type="text" 
					value="<?php echo $title; ?>" />
				</label>
				<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Username:'); ?> 
					<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" 
					name="<?php echo $this->get_field_name('username'); ?>" type="text" 
					value="<?php echo $username; ?>" />
				</label>
			</p>
	    <?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['title'] = strip_tags($new_instance['title']);
		$instance['username'] = strip_tags($new_instance['username']);
		return $instance;
	}

	function widget($args, $instance) {
		extract($args);	
		
		$title = self::get_title($instance);
		$username = self::get_username($instance);
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		
		$grubby = new Grubber($username);
		$grubby->grub();
		$repositories = $grubby->get_repositories();

		if($repositories == null) {
			echo $username . ' does not have any public repositories.';
		} else {
			foreach ($repositories->repository as $repository) {
			  echo '<a href="'. $repository->url . '">' . $repository->name . '</a><br />';
			}
		}
		echo $after_widget;
	}
	
	private function get_title($instance) {
		return empty($instance['title']) ? 'My GitHub Repositories' : apply_filters('widget_title', $instance['title']);
	}
	
	private function get_username($instance) {
		return empty($instance['username']) ? '' : $instance['username'];
	}

}
add_action('widgets_init', create_function('', 'return register_widget("GitGrubber");'));
