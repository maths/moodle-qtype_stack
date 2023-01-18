<?php

namespace api\dtos;

class StackGradingResponse
{
    public float $Score;
    public string $SpecificFeedback;
    public array $Prts;
    public array $GradingAssets;
}
