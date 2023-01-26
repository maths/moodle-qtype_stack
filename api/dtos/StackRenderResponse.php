<?php

namespace api\dtos;

class StackRenderResponse
{
    public string $QuestionRender;
    public string $QuestionSampleSolutionText;
    /** @var StackRenderInput[]  */
    public array $QuestionInputs;
    /** @var string[]  */
    public array $QuestionAssets;
    public int $QuestionSeed;
    /** @var int[]  */
    public array $QuestionVariants;
}

class StackRenderInput {
    public int $ValidationType;
    public $SampleSolution;
    public string $SampleSolutionRender;
    public array $Configuration;
}
