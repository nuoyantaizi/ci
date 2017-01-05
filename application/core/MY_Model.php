<?php
class MY_Model extends CI_Model {
	protected $model;
	public $route = NULL;

	public function __construct($model)
	{
		parent::__construct();

		$this->model = $model;
	}

	public function __call($method, $args)
	{
		$output = NULL;

		$result = $this->event->trigger('model/'.$this->route.'/'.$method.'/before', array(&$args, &$output));
		if($result) {
			return $result;
		}

		if(method_exists($this->model, $method)) {
			$output = call_user_func_array(array($this->model, $method), $args);
		}

		$result = $this->event->trigger('model/'.$this->route.'/'.$method.'/after', array(&$args, &$output));
		if($result) {
			return $result;
		}

		return $output;
	}
}