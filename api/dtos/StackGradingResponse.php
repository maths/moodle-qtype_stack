<?php

namespace api\dtos;

class StackGradingResponse
{
    public bool $isGradable;
    public float $Score;
    public string $SpecificFeedback;
    public array $Prts;
    public $GradingAssets;
}
