<?php
/**
 * Plugin Name: GitHub Grubber
 * Plugin URI: http://whoisowenbyrne.com/github-grubber
 * Description: Display a users public GitHub repositories
 * Author: Owen Byrne
 * Author URI: http://whoisowenbyrne.com
 * Version: 1.0
 */
require(dirname(__FILE__) . '/lib/grubber.php');
class GitHubGrubber extends WP_Widget {

	function GitHubGrubber() {
		$wops = array('description' =>  __('Display a GitHub users public repositories!'));
		parent::WP_Widget(false, $name = __('GitHub Grubber'), $wops);	
	}

	function form($instance) {
		$title = self::get_title($instance);
		$username = self::get_username($instance);
		$project_count = self::get_project_count($instance);

		?>
	    	<p>
				<label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?> </label>
					<input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" 
					name="<?php echo $this->get_field_name('title'); ?>" type="text" 
					value="<?php echo $title; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('GitHub Username:'); ?> </label>
					<input class="widefat" id="<?php echo $this->get_field_id('username'); ?>" 
					name="<?php echo $this->get_field_name('username'); ?>" type="text" 
					value="<?php echo $username; ?>" />
			</p>
			<p>
				<label for="<?php echo $this->get_field_id('project_count'); ?>"><?php _e('Number of projects to show:'); ?> </label>
					<input id="<?php echo $this->get_field_id('project_count'); ?>" 
					name="<?php echo $this->get_field_name('project_count'); ?>" type="text" 
					value="<?php echo $project_count; ?>" size="3" />
			</p>
	    <?php
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		
		$clean_title = strip_tags(trim($new_instance['title']));
		if (strlen($clean_title) > 0)  
			$instance['title'] = $clean_title;
	
		$clean_username = str_replace(' ', '', strip_tags(trim($new_instance['username'])));
		if (strlen($clean_username) > 0)  
			$instance['username'] = $clean_username;
		
		$clean_project_count = strip_tags(trim($new_instance['project_count']));
		if(is_numeric($clean_project_count))
			$instance['project_count'] = $clean_project_count;
		
		$grubber = new Grubber($clean_username);
		$grubber->update();
		return $instance;
	}
	
	
	function widget($args, $instance) {
		extract($args);	
		
		$title = self::get_title($instance);
		$username = self::get_username($instance);
		
		echo $before_widget;
		echo $before_title . $title . $after_title;
		
		$grubby = new Grubber($username);
		$repositories = $grubby->get_repositories();
		if($repositories == null || count($repositories) == 0) {
			echo $username . ' does not have any public repositories.';
		} else {
			$projs_to_disp = min(count($repositories), self::get_project_count($instance));
			echo '<div class"block"><ul>';
			for ($i=0; $i < $projs_to_disp; $i++) { 
		 		echo '<li><a href="'. $repositories[$i]['url'] . '">' . $repositories[$i]['name'] . '</a></li>';
			}
			echo '</ul></div>';
		}
		
		echo '<small><a href="https://github.com/' . $username . '">Follow '. $username  . ' on GitHub</a></small>';
		echo $after_widget;
	}
	
	private function get_title($instance) {
		return empty($instance['title']) ? 'My GitHub Projects' : apply_filters('widget_title', $instance['title']);
	}
	
	private function get_username($instance) {
		return empty($instance['username']) ? 'owenbyrne' : $instance['username'];
	}
	
	private function get_project_count($instance) {
		return empty($instance['project_count']) ? 5 : $instance['project_count'];
	}
}
add_action('widgets_init', create_function('', 'return register_widget("GitHubGrubber");'));
