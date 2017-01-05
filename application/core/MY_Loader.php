<?php
class MY_Loader extends CI_Loader {
	public function model($model, $name = '', $db_conn = FALSE)
	{
		if(empty($model)) return $this;

		parent::model($model, $name, $db_conn);
		$name = end($this->_ci_models);

		$CI =& get_instance();

		if(! is_a($CI->$name, 'MY_Model')) $CI->$name = new My_model(clone $CI->$name);

		$CI->$name->route = $model;

		return $this;
	}

	public function view($view, $vars = array(), $return = FALSE)
	{
		$CI = & get_instance();

		$output = '';

		$result = $CI->event->trigger('view/'.$view.'/before', array(&$vars, &$return));
		if($return) $output .= $result;

		$result = parent::view($view, $vars, $return);
		if($return) $output .= $result;

		$result = $CI->event->trigger('view/'.$view.'/after', array(&$vars, &$return));
		if($return) $output .= $result;

		return $output;
	}

	public function controller($controller, $name)
	{
		$split = explode('/', $controller);
		$class = array_pop($split);
		$path = implode('/', $split);
		if(empty($name)) $name = $class;

		require_once(APPPATH.'controllers/'.ltrim($path.'/'.ucfirst($class), '/').'.php');

		$CI =& get_instance();
	
		$CI->$name = new $class();
		$CI->$name->route = $controller;
	
		return $this;
	}
}