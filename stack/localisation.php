<?php
    function transformDisplayOutput ($studentAnswer){
                if($studentAnswer === null){
                    return $studentAnswer;
                }
                $decimalSeperator = get_string('decsep', 'langconfig');
            
                if(strcmp($decimalSeperator,",") === 0){
                    return str_replace( array(",","."),array(";","{,}"),$studentAnswer);
                }
                return $studentAnswer;
    }

    function transformOutput ($studentAnswer){
        if($studentAnswer === null){
            return $studentAnswer;
        }
        $decimalSeperator = get_string('decsep', 'langconfig');
    
        if(strcmp($decimalSeperator,",") === 0){
            return str_replace( array(",","."),array(";",","),$studentAnswer);
        }
        return $studentAnswer;
    }

    

?>