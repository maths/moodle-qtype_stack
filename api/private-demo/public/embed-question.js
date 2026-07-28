// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

const embeddedQuestion = window.stackEmbeddedQuestion || {};
let questions = [{name: embeddedQuestion.name || 'Practice question'}];
let page = 0;
let seed = null;

$(document).ready(function () {
    document.getElementById('stackapi_variant').style.display = 'none';
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
    seed = seed === null ? 1 : seed + 1;
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
