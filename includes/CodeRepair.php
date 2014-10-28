<?php


/**
* 
*/
class CodeRepair
{
    public $files = array();    
    private $headlines = '<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.';

    function __construct($files)
    {
        $this->files = $files;
    }

    public function start() {
        foreach ($this->files as $file) {
            $this->addHeadlines($file);
        }
    }

    public function addHeadlines($file) {
        $file_content = file_get_contents($file);
        $pattern = '/(<\\?(?:php)?)(?:\\s*\\/\\/.*$)*/im';
        $file_fixed_headlines = preg_replace($pattern,
                     $this->headlines, $file_content, 1);
        file_put_contents($file, $file_fixed_headlines);
        // var_dump($file_content);
        // var_dump($file_fixed_headlines);
    } 
}