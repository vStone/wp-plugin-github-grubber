<?php
/**
 * Plugin Name: GitHub Grubber
 * Plugin URI: http://whoisowenbyrne.com/github-grubber
 * Description: Display a users public GitHub repositories
 * Author: Owen Byrne
 * Author URI: http://whoisowenbyrne.com
 * Version: 1.1
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

		$sort_field = self::get_sort_field($instance);
    $sort_order = self::get_sort_order($instance);
		$sortable_fields = self::get_sortable_fields();

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
			<p>
        <label for="<?php echo $this->get_field_id('sort_field'); ?>"><?php _e('Sort projects by:'); ?></label>
        <select class="widefat" id="<?php echo $this->get_field_id('sort_field'); ?>"
          name="<?php echo $this->get_field_name('sort_field'); ?>">
<?php foreach ($sortable_fields as $key => $value) {  ?>
          <option value=<?php echo $key; ?><?php echo ($key == $sort_field) ? ' selected="selected"' : '' ?>><?php echo $value['desc']; ?></option>
<?php } ?>
        </select>
      </p>
      <?php _e('Sort order:'); ?>
      <p>
        <label for="<?php echo $this->get_field_id('sort_order_asc'); ?>"><?php _e('Ascending'); ?></label>
        <input id="<?php echo $this->get_field_id('sort_order_asc'); ?>" 
        type="radio" name="<?php echo $this->get_field_name('sort_order'); ?>"
          <?php echo ($sort_order == 'asc') ? 'checked="checked" ' : ''; ?>value='asc'/>
          <label for="<?php echo $this->get_field_id('sort_order_desc'); ?>"><?php _e('Descending'); ?><label>
          <input id="<?php echo $this->get_field_id('sort_order_desc'); ?>" 
          type="radio" name="<?php echo $this->get_field_name('sort_order'); ?>" 
          <?php echo ($sort_order == 'desc') ? 'checked="checked" ' : '';?>value='desc'/>
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
		if (is_numeric($clean_project_count))
			$instance['project_count'] = $clean_project_count;

		$clean_sort_field = strip_tags(trim($new_instance['sort_field']));
		if (strlen($clean_sort_field) > 0)
      $instance['sort_field'] = $clean_sort_field;
    
		$clean_sort_order = strip_tags(trim($new_instance['sort_order']));
		if (in_array($clean_sort_order, array('asc','desc')))
			$instance['sort_order'] = $clean_sort_order;

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
		echo '<small><a href="http://github.com/' . $username . '">Follow '. $username  . ' on GitHub</a></small>';
		if (current_user_can('manage_options')) {
			printf('<pre>%s</pre>', print_r($repositories,TRUE));
		}
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

	private function get_sort_field($instance) {
    return empty($instance['sort_field']) ? 'name' : $instance['sort_field'];
  }

  private function get_sort_order($instance) {
    return empty($instance['sort_order']) ? 'asc' : $instance['sort_order'];
  }

	private function get_sortable_fields() {
    return array(
      'name'        => array ('desc' => 'Name',                  'sort' => SORT_STRING, ),
      'created-at'  => array ('desc' => 'Project creation date', 'sort' => SORT_REGULAR, ),
      'pushed-at'   => array ('desc' => 'Last push date',        'sort' => SORT_REGULAR, ),
      'open-issues' => array ('desc' => 'Open issue count',      'sort' => SORT_NUMERIC, ),
      'language'    => array ('desc' => 'Project language',      'sort' => SORT_REGULAR, ),
      'size'        => array ('desc' => 'Codebase size',         'sort' => SORT_NUMERIC, ),
      'watchers'    => array ('desc' => 'Number of watchers',    'sort' => SORT_NUMERIC, ),
      'forks'       => array ('desc' => 'Number of forks',       'sort' => SORT_NUMERIC, ),
    );
  }
}
add_action('widgets_init', create_function('', 'return register_widget("GitHubGrubber");'));
