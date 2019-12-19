<?php

namespace Moosh\ApacheLogParser;

class LineParser
{
    private $re;
    private $elements;

    public function __construct($re, $params)
    {

        if (!is_array($params) || !count($re) || !count($params)) {
            throw new \Exception("You must provide arrays to the constructor");
        }

        $all = array(
            'remote_ip' => array(
                'format' => 'a', 'parser' => new StringElement(), 're' => '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})'),
            'local_ip' => array(
                'format' => 'A', 'parser' => new StringElement(), 're' => '(\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3})'),
            'response_size' => array(
                'format' => 'B', 'parser' => new IntegerElement(), 're' => '(\d+)'),
            'response_size_clf' => array(
                'format' => 'b', 'parser' => new IntegerElement(), 're' => '(\d+|-)'),
            'cookie' => array(
                'format' => 'C', 'parser' => new StringElement(), 're' => '(.*)'),
            'serving_time_microseconds' => array(
                'format' => 'D', 'parser' => new IntegerElement(), 're' => '(\d+)'),
            'env_variable' => array(
                'format' => 'e', 'parser' => new StringElement(), 're' => '(.*)'),
            'filename' => array(
                'format' => 'f', 'parser' => new StringElement(), 're' => '(.*)'),
            'remote_host' => array(
                'format' => 'h', 'parser' => new StringElement(), 're' => '(.*)'),
            'request_protocol' => array(
                'format' => 'H', 'parser' => new StringElement(), 're' => '(.+)'),
            'request_header_line' => array(
                'format' => 'i', 'parser' => new StringElement(), 're' => '(.+)'),
            'remote_logname' => array(
                'format' => 'l', 'parser' => new StringElement(), 're' => '([^ ]+)'),
            'request_method' => array(
                'format' => 'm', 'parser' => new StringElement(), 're' => '(options|get|head|post|put|delete|trace|connect)'),
            'note' => array(
                'format' => 'n', 'parser' => new StringElement(), 're' => '(.*)'),
            'remote_user' => array(
                'format' => 'u', 'parser' => new StringElement(), 're' => '(.*)'),
            'reply_header_line' => array(
                'format' => 'o', 'parser' => new StringElement(), 're' => '(.*)'),
            'port' => array(
                'format' => 'p', 'parser' => new IntegerElement(), 're' => '(\d+)'),
            'pid' => array(
                'format' => 'P', 'parser' => new IntegerElement(), 're' => '\d+'),
            'query' => array(
                'format' => 'q', 'parser' => new StringElement(), 're' => '(|\?.*)'),
            'request_first_line' => array(
                'format' => 'r', 'parser' => new StringElement(), 're' => '(.+)'),
            'status' => array(
                'format' => 's', 'parser' => new IntegerElement(), 're' => '(\d+)'),
            'time' => array(
                'format' => 't', 'parser' => new TimeElement('%d/%b/%Y:%T %z'), 're' => '(\[.+\])'),
            'serving_time' => array(
                'format' => 'T', 'parser' => new IntegerElement(), 're' => '(\d+)'),
            'remote_user' => array(
                'format' => 'u', 'parser' => new StringElement(), 're' => '([^ ]+)'),
            'url' => array(
                'format' => 'U', 'parser' => new StringElement(), 're' => '([a-zA-Z0-9\-\.\?\,\'\/\\\+&;:=@%\$#_]*)'),
            'server_name' => array(
                'format' => 'v', 'parser' => new StringElement(), 're' => '(.*)'),
            'server_name_usecanonical' => array(
                'format' => 'V', 'parser' => new StringElement(), 're' => '(.*)'),
            'connection_after_response' => array(
                'format' => 'X', 'parser' => new StringElement(), 're' => '(X|\+|-)'),
            'bytes_received' => array(
                'format' => 'I', 'parser' => new IntegerElement(), 're' => '(\d+)'),
            'bytes_sent' => array(
                'format' => 'O', 'parser' => new IntegerElement(), 're' => '(\d+)'));

        $this->elements = array();
        foreach ($params as $param) {
            if (!is_array($param)) {
                if (!array_key_exists($param, $all))
                    throw new \Exception("Unknown element: '$param'");
                $this->elements[] = $all[$param] + array(
                        'name' => $param);
            } else {
                if (!array_key_exists($param['element'], $all))
                    throw new \Exception("Unknown element or element not set");
                $this->elements[] = array_merge(array(
                    'name' => $param['element']), $all[$param['element']], $param);
            }
        }
        //generate regexp
        $reParams = array();
        foreach ($this->elements as $element) {
            $reParams[] = $element['re'];
        }
        $this->re = vsprintf($re, $reParams);

        if (!$this->re) {
            throw new \Exception("Invalid argument(s)");
        }
    }

    public function parse(&$line)
    {
        $matches = array();
//        var_dump($this->re);
        $ret = preg_match('/' . $this->re . '/', $line, $matches);
//        var_dump($line);
//var_dump($matches);
        if (!$ret) {
            //ok, we didn't match the line - that's too bad!
            return false;
        }

        $result = array();
        //put results in a hash
        //number of matches must be the same as number of elements
        $i = 1;
        foreach ($this->elements as $element) {
            $result[$element['name']] = $element['parser']->parse($matches[$i++]);
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
