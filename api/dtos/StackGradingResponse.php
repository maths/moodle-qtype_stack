<?php

namespace api\dtos;

class StackGradingResponse
{
    /** @var boolean */
    public $isGradable;
    /** @var float */
    public $Score;
    /** @var string */
    public $SpecificFeedback;
    /** @var array */
    public $Prts;
    public $GradingAssets;
}
