<?php
/*
 * This file is part of the GetOptionKit package.
 *
 * (c) Yo-An Lin <cornelius.howl@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 */
namespace GetOptionKit;
use GetOptionKit\OptionSpec;
use GetOptionKit\OptionSpecCollection;
use GetOptionKit\OptionResult;
use GetOptionKit\OptionParser;
use Exception;

class GetOptionKit 
{
    public $parser;
    public $specs;

    function __construct()
    {
        $this->specs = new OptionSpecCollection;
        $this->parser = new OptionParser( $this->specs );
    }

    /* 
     * return current parser 
     * */
    function getParser()
    {
        return $this->parser;
    }

    /* get all option specification */
    function getSpecs()
    {
        return $this->specs;
    }

    /* a helper to build option specification object from string spec 
     *
     * @param $specString string
     * @param $description string
     * @param $key
     *
     * */
    function add( $specString, $description , $key = null ) 
    {
        $spec = $this->specs->add($specString,$description,$key);
        return $spec;
    }

    /* get option specification by Id */
    function get($id)
    {
        return $this->specs->get($id);
    }


    function parse( $argv ) 
    {
        return $this->parser->parse( $argv );
    }

    function printOptions( $class = 'GetOptionKit\OptionPrinter' )
    {
        $this->specs->printOptions( $class );
    }

}

