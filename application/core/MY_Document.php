<?php
class MY_Document {
	public $title;
	public $description;
	public $keywords;
	public $scripts = array();
	public $styles = array();

	public function addScript($href)
	{
		$this->scripts[] = $href;
	}

	public function addStyle($href)
	{
		$this->styles[] = $href;
	}
}