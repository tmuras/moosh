<?php
class tmLineParser
{
  private $re;
  private $elements;
  
  public function __construct($re, $params)
  {
    
    if(! is_array($params) || !count($re) || !count($params)) {
      throw new tmApacheLogParserException("You must provide arrays to the constructor",tmApacheLogParserException::$INVALID_ARGUMENT);
    }
    
    $all = array (
      'remote_ip' => array (
      'format' => 'a', 'parser' => new tmStringElement(), 're' => '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})' ), 'local_ip' => array (
      'format' => 'A', 'parser' => new tmStringElement(), 're' => '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})' ), 'response_size' => array (
      'format' => 'B', 'parser' => new tmIntegerElement(), 're' => '(\d+)' ), 'response_size_clf' => array (
      'format' => 'b', 'parser' => new tmIntegerElement(), 're' => '(\d+|-)' ), 'cookie' => array (
      'format' => 'C', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'serving_time' => array (
      'format' => 'D', 'parser' => new tmIntegerElement(), 're' => '(d+)' ), 'env_variable' => array (
      'format' => 'e', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'filename' => array (
      'format' => 'f', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'remote_host' => array (
      'format' => 'h', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'request_protocol' => array (
      'format' => 'H', 'parser' => new tmStringElement(), 're' => '(.+)' ), 'request_header_line' => array (
      'format' => 'i', 'parser' => new tmStringElement(), 're' => '(.+)' ), 'remote_logname' => array (
      'format' => 'l', 'parser' => new tmStringElement(), 're' => '([^ ]+)' ), 'request_method' => array (
      'format' => 'm', 'parser' => new tmStringElement(), 're' => '(options|get|head|post|put|delete|trace|connect)' ), 'note' => array (
      'format' => 'n', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'remote_user' => array (
      'format' => 'u', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'reply_header_line' => array (
      'format' => 'o', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'port' => array (
      'format' => 'p', 'parser' => new tmIntegerElement(), 're' => '(\d+)' ), 'pid' => array (
      'format' => 'P', 'parser' => new tmIntegerElement(), 're' => '\d+' ), 'query' => array (
      'format' => 'q', 'parser' => new tmStringElement(), 're' => '(|\?.*)' ), 'request_first_line' => array (
      'format' => 'r', 'parser' => new tmStringElement(), 're' => '(.+)' ), 'status' => array (
      'format' => 's', 'parser' => new tmIntegerElement(), 're' => '(\d+)' ), 'time' => array (
      'format' => 't', 'parser' => new tmTimeElement('%d/%b/%Y:%T %z'), 're' => '(\[.+\])' ), 'serving_time' => array (
      'format' => 'T', 'parser' => new tmIntegerElement(), 're' => '(\d+)' ), 'remote_user' => array (
      'format' => 'u', 'parser' => new tmStringElement(), 're' => '([^ ]+)' ), 'url' => array (
      'format' => 'U', 'parser' => new tmStringElement(), 're' => '([a-zA-Z0-9\-\.\?\,\'\/\\\+&;:=@%\$#_]*)' ), 'server_name' => array (
      'format' => 'v', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'server_name_usecanonical' => array (
      'format' => 'V', 'parser' => new tmStringElement(), 're' => '(.*)' ), 'connection_after_response' => array (
      'format' => 'X', 'parser' => new tmStringElement(), 're' => '(X|\+|-)' ), 'bytes_received' => array (
      'format' => 'I', 'parser' => new tmIntegerElement(), 're' => '(\d+)' ), 'bytes_sent' => array (
      'format' => 'O', 'parser' => new tmIntegerElement(), 're' => '(\d+)' ) );
    
    $this->elements = array ();
    foreach($params as $param) {
      if(! is_array($param)) {
        if(! array_key_exists($param, $all))
          throw new tmApacheLogParserException("Unknown element: '$param'");
        $this->elements[] = $all[$param] + array (
          'name' => $param );
      } else {
        if(! array_key_exists($param['element'], $all))
          throw new tmApacheLogParserException("Unknown element or element not set");
        $this->elements[] = array_merge(array (
          'name' => $param['element'] ), $all[$param['element']], $param);
      }
    }
    //generate regexp
    $reParams = array ();
    foreach($this->elements as $element) {
      $reParams[] = $element['re'];
    }
    $this->re = vsprintf($re, $reParams);
    
    if(! $this->re) {
      throw new tmApacheLogParserException("Invalid argument(s)",tmApacheLogParserException::$INVALID_ARGUMENT);
    }
  }
  
  public function parse(&$line)
  {
    $matches = array ();
    $ret = preg_match('/' . $this->re . '/', $line, $matches);
    
    if(! $ret) {
      //ok, we didn't match the line - that's too bad!
      return false;
    }
    
    $result = array ();
    //put results in a hash
    //number of matches must be the same as number of elements
    $i = 1;
    foreach($this->elements as $element) {
      $result[$element['name']] = $element['parser']->parse($matches[$i ++]);
    }
    
    return $result;
  }
  
  /**
   * Returns a regular expression that is used to parse each line.
   *
   * @return string
   */
  public function getRE()
  {
    return '/' . $this->re . '/';
  }
}
