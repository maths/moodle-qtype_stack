<?php

/*
 This file is part of Stack - http://stack.bham.ac.uk//

 Stack is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 Stack is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with Stack.  If not, see <http://www.gnu.org/licenses/>.
*/


class stack_cas_maxima_connector {

    private $options;
    private $security;
    private $seed;

    private $config;

    public function __construct($options, $security='s', $seed= null) {
        $this->options  = $options;
        $this->security = $security;
        if ($seed != null) {
            $this->seed = $seed;
        } else {
            $this->seed = time();
        }

        $path = 'C:\xampp\data\moodledata\stack';
        $initCommand = "load(\"".$path."\maximalocal.mac\");";
        $initCommand = str_replace("\\", "/", $initCommand);
        $initCommand .= "\n";
                    
        $this->config['platform']       = 'win';
        $this->config['logs']           = $path;
        $this->config['CASCommand']     = $path.'\maxima.bat';
        $this->config['CASInitCommand'] = $initCommand;
        $this->config['CASTimeout']     = 5;
        $this->config['CASDebug']       = false;
        $this->config['CASVersion']     = '5.21.1';
    }

    public function sendAnsTest($student, $teacher, $anstest) {
        return $result;
    }

}
