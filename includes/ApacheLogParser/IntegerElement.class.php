<?php
namespace Moosh\ApacheLogParser;

class IntegerElement
{
  public function __construct()
  {
  }
  
  public function parse($str)
  {
    if(! $str || $str == '-') {
      return null;
    }
    return intval($str);
  }
}
