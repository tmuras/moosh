<?php

class tmStringElement
{
	private $format;
	public function __construct($format = null)
	{
		$this->format = $format;
	}
	
  public function parse($str) {
  	if($str == '-') {
  		return null;
  	}
      return $str;
  }
}