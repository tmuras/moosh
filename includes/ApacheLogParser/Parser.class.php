<?php
/*
 * @TODO Add building the log format for apache config
 * @TODO Could (posibly) be integrated with Net_UserAgent_Detect
 * @TODO Try to guess where the interesting logs start in the file (jump to predicted location and move formard/backward to the first log entry we want to process).
 */

namespace Moosh\ApacheLogParser;

require_once('LineParser.class.php');
require_once('IntegerElement.class.php');
require_once('StringElement.class.php');
require_once('TimeElement.class.php');

class Parser
{
  public static $FORMAT_COMMON = 1;
  public static $FORMAT_COMMON_VHOST = 2;
  public static $FORMAT_COMBINED = 3;
  public static $FORMAT_VHOST_COMBINED = 4;
  public static $MAX_LINE_LENGTH = 8192;
  
  /**
   * @var LineParser
   */
  private $lineParser;
  private $fhandle;
  private $start;
  private $stop;
  private $stats;
  
  public function __construct($re, $elements)
  {
    $this->lineParser = new LineParser($re, $elements);
  }
  
  /**
   * Only parse one line
   *
   * @param string $line
   */
  public function parseLine(&$line)
  {
    return $this->lineParser->parse($line);
  }
  
  
  /**
   * Returns a regular expression that is used to parse each line.
   *
   * @return string
   */
  public function getRE()
  {
  	return $this->lineParser->getRE();
  }
  
  /**
   * Read the next line from the log file.
   *
   */
  public function next()
  {
    if(! $this->fhandle) {
      throw new \Exception("No file to parse");
    }
    
    while(1) {
      $line = fgets($this->fhandle, self::$MAX_LINE_LENGTH);
      $parsed = $this->parseLine($line);
      
      if(! $parsed) {
        if(feof($this->fhandle)) {
        	return -1;
        } else {
      		return false;
        }
      }
      if($this->stop && $parsed['time'] > $this->stop) {
        //we're too far away
        return false;
      }
      if($this->start && $parsed['time'] >= $this->start || ! $this->start) {
        break;
      }
    }
    
    return $parsed;
  }
  
  public function setFile($filePath)
  {
    if(! is_file($filePath) || ! is_readable($filePath)) {
      throw new \Exception("Can't read a file: $filePath");
    }
    $this->filePath = $filePath;
    $this->fhandle = fopen($this->filePath, 'r');
  }
  
  public function __destruct()
  {
    @fclose($this->fhandle);
  }
  
  /**
   * Process log entries no earlier than a given date
   *
   */
  public function setStart($start)
  {
    $this->start = $start;
  }
  
  /**
   * Don't process log entries older than a date
   *
   */
  public function setStop($stop)
  {
    $this->stop = $stop;
  }
  
  public static function createFormat($format)
  {
    switch($format) {
      case self::$FORMAT_COMBINED:
	     /*"%h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\""*/
	     $re = '%s %s %s %s "%s" %s %s "%s" "%s"';
        $elements = array (
          'remote_host', 'remote_logname', 'remote_user', 'time', 'request_first_line', 'status', 'response_size_clf', array (
          'element' => 'request_header_line', 'name' => 'referer' ), array (
          'element' => 'request_header_line', 'name' => 'user_agent' ) );
        
        break;
      case self::$FORMAT_VHOST_COMBINED :
     /*"%v:%p %h %l %u %t \"%r\" %>s %b \"%{Referer}i\" \"%{User-Agent}i\""*/
      $re = '%s:%s %s %s %s %s "%s" %s %s "%s" "%s"';
        $elements =
            array('server_name', 'port', 'remote_host', 'remote_logname', 'remote_user', 'time', 'request_first_line', 'status', 'response_size_clf',
            array('element' => 'request_header_line', 'name' => 'referer' ),
            array('element' => 'request_header_line', 'name' => 'user_agent' ) );
        
        break;
      case self::$FORMAT_COMMON :
     /*"%h %l %u %t \"%r\" %>s %b"*/
	     $re = '%s %s %s %s "%s" %s %s';
        $elements = array (
          'remote_host', 'remote_logname', 'remote_user', 'time', 'request_first_line', 'status', 'response_size_clf' );
        
        break;
      
      case self::$FORMAT_COMMON_VHOST :
     /*"%v %h %l %u %t \"%r\" %>s %b"*/
	     $re = '%s %s %s %s %s "%s" %s %s';
        $elements = array (
          'server_name', 'remote_host', 'remote_logname', 'remote_user', 'time', 'request_first_line', 'status', 'response_size_clf' );
        
        break;
      
      default :
        throw new \Exception("Not supported format: '$format'");
    }
    
    return new Parser($re, $elements);
  }
  
  private function readlastline()
  {
    $pos = - 1;
    $t = " ";
    while($t != "\n") {
      fseek($this->fhandle, $pos, SEEK_END);
      $t = fgetc($this->fhandle);
      $pos = $pos - 1;
    }
    $t = fgets($this->fhandle);
    return $t;
  }
}
