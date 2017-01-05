<?php
class MY_Event {
	private $events = array();

	public function __get($key)
	{
		return get_instance()->$key;
	}

	public function bind($route, $event)
	{
		$this->events[$route] = $event;
	}

	public function remove($route)
	{
		$this->events[$route] = null;
	}

	public function trigger($route, $args=array())
	{
		foreach($this->events as $event_route=>$action)
		{
			if(preg_match('|'.str_replace('*', '.*', $event_route).'|', $route)) {

				$action_split = explode('/', $action);
				$action_type = array_shift($action_split);

				if($action_type == 'view') {
					$return = $this->load->view(implode('/', $action_split), $args[0], $args[1]);
					if($args[1]) return $return;

					continue;
				}

				$method = array_pop($action_split);
				$filepath = implode('/', $action_split);
				$class = $filepath;

				if (($last_slash = strrpos($filepath, '/')) !== FALSE)
				{
					$class = substr($filepath, $last_slash);
				}

				$this->load->$action_type($filepath, $class);

				$return = NULL;

				if(is_object($this->$class)) {
					if($action_type == 'controller') 
						$return = call_user_func_array(array($this->$class, '_remap'), array($method, $args));
					else 
						$return = call_user_func_array(array($this->$class, $method), $args);
				} 
				elseif(function_exists($method)) {
					$return = call_user_func_array($method, $args);
				}
				
				if($return) return $return;
			}
		}
	}
}