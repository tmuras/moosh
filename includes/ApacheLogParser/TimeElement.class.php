<?php
namespace Moosh\ApacheLogParser;

class TimeElement
{
  private $format;
  public function __construct($format)
  {
    $this->format = $format;
  }
  
  public function parse($str)
  {
    //cut [ and ] if needed
    

    $str = trim($str, '[]');
    
    $time = strptime($str, $this->format);
    if(! $time) {
      return false;
    }
    return mktime($time['tm_hour'], $time['tm_min'], $time['tm_sec'], $time['tm_mon'] + 1, $time['tm_mday'], $time['tm_year'] + 1900);
  }
}
	