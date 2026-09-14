// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

let embeddedQuestion = {};
let questions = [{name: 'Practice question'}];
let page = 0;
let seed = null;
let seedIndex = 0;
let seedSequence = [];

function configureEmbeddedQuestion(question) {
    embeddedQuestion = question || {};
    questions = [{name: embeddedQuestion.name || 'Practice question'}];
    seedSequence = Array.isArray(embeddedQuestion.seeds) ? embeddedQuestion.seeds : [];
    seedIndex = 0;
    seed = seedSequence.length > 0 ? seedSequence[0] : null;
}

$(document).ready(function () {
    document.getElementById('stackapi_variant').style.display = seedSequence.length > 1 ? '' : 'none';
    send();
});

function collectData() {
    const data = {
        answers: collectAnswer(),
        seed: seed,
        renderInputs: inputPrefix,
    };
    if (embeddedQuestion.questionId) {
        data.questionId = embeddedQuestion.questionId;
    } else {
        data.questionPath = embeddedQuestion.questionPath;
    }
    return data;
}

function advanceVariant() {
    if (seedSequence.length < 2) {
        return;
    }
    seedIndex = (seedIndex + 1) % seedSequence.length;
    seed = seedSequence[seedIndex];
    send();
}

function toggleAnswer(button) {
    const element = document.getElementById('stackapi_correct');
    const status = element.style.display;
    if (status === 'block') {
        element.style.display = 'none';
        button.value = 'Display Correct Answers';
    } else {
        element.style.display = 'block';
        button.value = 'Hide Correct Answers';
    }
}
