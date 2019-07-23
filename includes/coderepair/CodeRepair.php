<?php

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
            $this->fixInlineComments($file);

            $file_lines = file($file);
            $fixed_file_lines = array();
            foreach ($file_lines as $line) {
                $line = $this->fixUnderscoreVariables($line);

                $fixed_file_lines[] = $line;
            }

            $fixed_file = implode(PHP_EOL, $fixed_file_lines);
            file_put_contents($file, $fixed_file);
        }
    }

    // Whole File fixers

    public function addHeadlines($file) {
        $file_content = file_get_contents($file);
        $pattern = '/(<\\?(?:php)?)(?:\\s*\\/\\/.*$)*/im';
        $file_fixed_headlines = preg_replace($pattern,
                     $this->headlines, $file_content, 1);
        file_put_contents($file, $file_fixed_headlines);

    } 

    public function fixInlineComments($line) {
        $f = file_get_contents($file);

        $f = preg_replace_callback(
            '!/\*(.+?)\*/!',
            function($matches) {
                $comment = $matches[1];
                $comment = trim($comment);
                $comment = rtrim($comment, '.');
                return '// ' . ucfirst($comment) . '.';
            },
            $f
        );

        file_put_contents($file, $f);
    }

    // One code line fixers

    public function fixUnderscoreVariables($line) {

    }
}
