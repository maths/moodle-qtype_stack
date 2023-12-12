<?php

namespace api\dtos;

class StackRenderResponse
{
    /** @var string */
    public $QuestionRender;
    /** @var string */
    public $QuestionSampleSolutionText;
    /** @var StackRenderInput[]  */
    public $QuestionInputs;
    public $QuestionAssets;
    /** @var int */
    public $QuestionSeed;
    /** @var int[]  */
    public $QuestionVariants;
}

class StackRenderInput {
    public int $ValidationType;
    public $SampleSolution;
    public string $SampleSolutionRender;
    public array $Configuration;
}
