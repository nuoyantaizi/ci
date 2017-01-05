<?php
class MY_Controller extends CI_Controller {

	public $route = NULL;

	public function __construct()
	{
		if(! is_object(get_instance())) {
			parent::__construct();
		}
		
		foreach (is_loaded() as $var => $class)
		{
			$this->$var =& load_class($class);
		}

		$this->load =& load_class('Loader', 'core');
		$this->load->initialize();

		$this->event = load_class('Event', 'core');
	}

	public function _remap($method, $args=array())
	{
		if(is_null($this->route)) {
			$ci_route = & load_class('Router', 'core');
			$this->route = $ci_route->fetch_directory().$ci_route->fetch_class();
		}
		
		$output = NULL;

		$result = $this->event->trigger('controller/'.$this->route.'/'.$method.'/before', array(&$args, &$output));
		if($result) return $result;

		$output = call_user_func_array(array($this, $method), $args);

		$result = $this->event->trigger('controller/'.$this->route.'/'.$method.'/after', array(&$args, &$output));
		if($result) return $result;

		return $output;
	}
}