<?php

namespace api\util;

class StackSeedHelper
{
    static public function initializeSeed($question, $seed) {
        if($question->has_random_variants()) {
            //We require the xml to include deployed variants
            if(count($question->deployedseeds) === 0) {
                throw new \Exception(get_string('api_no_deployed_variants', null));
            }

            //If no seed has been specified, use the first deployed variant
            if(!$seed) {
                $seed = $question->deployedseeds[0];
            }

            if(!in_array($seed, $question->deployedseeds)) {
                throw new \Exception(get_string('api_seed_not_in_variants', null));
            }

            $question->seed = $seed;
        } else {
            //We just set any seed here
            $question->seed = -1;
        }
    }
}
