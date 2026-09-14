// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.

const maxVisibleQuestions = 100;
let questions = [];
let page = 0;

$(document).ready(function () {
    fetch('demo/questions')
        .then((response) => response.json())
        .then((catalogue) => {
            questions = catalogue;
            document.getElementById('question-search').addEventListener('input', renderQuestionList);
            document.getElementById('question-list').addEventListener('change', (event) => {
                goToPage(Number(event.target.value));
            });
            renderQuestionList();
            if (questions.length > 0) {
                goToPage(0);
            }
        })
        .catch(() => {
            document.getElementById('errors').innerText = 'There was an error loading the question list.';
        });
});

function renderQuestionList() {
    const list = document.getElementById('question-list');
    const count = document.getElementById('question-count');
    const search = document.getElementById('question-search').value.trim().toLowerCase();
    const matches = questions
        .map((question, index) => ({question, index}))
        .filter(({question}) => {
            if (search === '') {
                return true;
            }
            return question.name.toLowerCase().includes(search) ||
                question.filename.toLowerCase().includes(search);
        });
    const visibleMatches = matches.slice(0, maxVisibleQuestions);

    list.innerHTML = '';
    for (const {question, index} of visibleMatches) {
        const option = document.createElement('option');
        option.value = index;
        option.text = `${question.name} - ${question.filename}`;
        option.title = question.category;
        if (index === page) {
            option.selected = true;
        }
        list.appendChild(option);
    }

    count.innerText = visibleMatches.length === matches.length ?
        `${matches.length} matching questions` :
        `Showing ${visibleMatches.length} of ${matches.length} matching questions`;
}

function goToPage(targetPage) {
    page = targetPage;
    document.getElementById('question-list').value = String(page);
    document.getElementById('question-frame').src =
        `/embed?questionId=${encodeURIComponent(questions[page].questionId)}`;
}
