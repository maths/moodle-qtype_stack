// This file is part of Stack - http://stack.maths.ed.ac.uk/
//
// Stack is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Stack is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Stack.  If not, see <http://www.gnu.org/licenses/>.

/**
 * Javascript shared between API frontend pages.
 *
 * @package    qtype_stack
 * @copyright  2025 University of Edinburgh
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later.
 */

const timeOutHandler = new Object();
const inputPrefix = 'stackapi_input_';
const feedbackPrefix = 'stackapi_fb_';
const validationPrefix = 'stackapi_val_';
const FULLDISPLAY = 'FULL';
const SAMPLEDISPLAY = 'SAMPLE';
let displayType = FULLDISPLAY;

// Get the different input elements by tag and return object with values.
function collectAnswer() {
    const inputs = document.getElementsByTagName('input');
    const textareas = document.getElementsByTagName('textarea');
    const selects = document.getElementsByTagName('select');
    let res = {};
    res = processNodes(res, inputs);
    res = processNodes(res, textareas);
    res = processNodes(res, selects);
    return res;
}

// Return object of values of valid entries in an HTMLCollection.
function processNodes(res, nodes) {
    for (let i = 0; i < nodes.length; i++) {
        const element = nodes[i];
        if (element.name.indexOf(inputPrefix) === 0 && !element.name.endsWith('_val')) {
            if (element.type === 'checkbox' || element.type === 'radio') {
                if (element.checked) {
                    res[element.name.slice(inputPrefix.length)] = element.value;
                }
            } else {
                res[element.name.slice(inputPrefix.length)] = element.value;
            }
        }
        if (element.name.endsWith('_val')) {
            res[element.name] = element.value;
        }
    }
    return res;
}

// Display rendered question and solution.
function send() {
    loading(true);
    const http = new XMLHttpRequest();
    const url = window.location.origin + '/render';
    http.open("POST", url, true);
    http.setRequestHeader('Content-Type', 'application/json');
    http.onreadystatechange = function () {
        if (http.readyState == 4) {
            try {
                loading(false);
                const json = JSON.parse(http.responseText);
                if (json.message) {
                    document.getElementById('errors').innerText = json.message;
                    return;
                } else {
                    document.getElementById('errors').innerText = '';
                }
                renameIframeHolders();
                let question = json.questionrender;
                const inputs = json.questioninputs;
                let correctAnswers = '';
                // Show correct answers.
                const placeholders = question.matchAll(/\[\[input:([a-zA-Z][a-zA-Z0-9_]*)\]\]/g);
                for (const holder of placeholders) {
                    const name = holder[1];
                    const input = inputs[name];
                    question = question.replace(`[[input:${name}]]`, input.render);
                    question = question.replace(`[[validation:${name}]]`, `<span name='${validationPrefix + name}'></span>`);
                    if (input.samplesolutionrender && name !== 'remember') {
                        // Display render of answer and matching user input to produce the answer.
                        correctAnswers += `<p>A correct answer is: `;
                        // Is the solution fully rendered? If not we need to surround with LaTeX.
                        if (input.samplesolutionrender.substring(0, 1) === '<') {
                            correctAnswers += input.samplesolutionrender;
                        } else {
                            correctAnswers += `\\[{${input.samplesolutionrender}}\\]`;
                        }
                        if (input.samplesolution) {
                            correctAnswers += `, which can be typed as follows: `;
                            for (const [name, solution] of Object.entries(input.samplesolution)) {
                                if (name.indexOf('_val') === -1) {
                                    correctAnswers += `<span class='correct-answer'>${solution.replace(/\n/g, '<br>')}</span>`;
                                }
                            }
                        }
                        correctAnswers += '.</p>';
                    } else if (name !== 'remember' && input.samplesolution) {
                        // For dropdowns, radio buttons, etc, only the correct option is displayed.
                        for (const solution of Object.values(input.samplesolution)) {
                            if (input.configuration.options) {
                                correctAnswers += `<p class='correct-answer'>${input.configuration.options[solution]}</p>`;
                            }
                        }
                    }
                }
                const elementsRequiringInputs = document.getElementsByClassName('noninfo');
                if (Object.keys(inputs).length) {
                    for (let el of elementsRequiringInputs) {
                        el.style.display = 'inline-block';
                    }
                } else {
                    for (let el of elementsRequiringInputs) {
                        el.style.display = 'none';
                    }
                }
                // Convert Moodle plot filenames to API filenames.
                for (const [name, file] of Object.entries(json.questionassets)) {
                    question = question.replace(name, `plots/${file}`);
                    json.questionsamplesolutiontext = json.questionsamplesolutiontext.replace(name, `plots/${file}`);
                    correctAnswers = correctAnswers.replace(name, `plots/${file}`);
                }
                question = replaceFeedbackTags(question);

                document.getElementById('output').innerHTML = question;
                // Only display results sections once question retrieved.
                document.getElementById('stackapi_qtext').style.display = 'block';
                if (displayType === FULLDISPLAY) {
                    document.getElementById('stackapi_correct').style.display = 'block';
                }

                // Setup a validation call on inputs. Timeout length is reset if the input is updated
                // before the validation call is made.
                for (const inputName of Object.keys(inputs)) {
                    const inputElements = document.querySelectorAll(`[name^=${inputPrefix + inputName}]`);
                    for (const inputElement of Object.values(inputElements)) {
                        inputElement.oninput = (event) => {
                            const currentTimeout = timeOutHandler[event.target.id];
                            if (currentTimeout) {
                                window.clearTimeout(currentTimeout);
                            }
                            timeOutHandler[event.target.id] = window.setTimeout(validate.bind(null, event.target), 1000);
                        };
                    }
                }
                let sampleText = json.questionsamplesolutiontext;
                if (displayType === FULLDISPLAY) {
                    if (sampleText) {
                        sampleText = replaceFeedbackTags(sampleText);
                        document.getElementById('stackapi_generalfeedback').style.display = 'block';
                        document.getElementById('generalfeedback').innerHTML = sampleText;
                    } else {
                        // If the question is updated, there may no longer be general feedback.
                        document.getElementById('stackapi_generalfeedback').style.display = 'none';
                    }
                    document.getElementById('stackapi_score').style.display = 'none';
                } else {
                    if (sampleText) {
                        sampleText = replaceFeedbackTags(sampleText);
                        document.getElementById('generalfeedback').innerHTML = sampleText;
                    } else {
                        document.getElementById('generalfeedback').innerHTML = '';
                    }
                    document.getElementById('stackapi_combinedfeedback').style.display = 'none';
                    document.getElementById('stackapi_name').innerText = questions[page].name;
                }
                document.getElementById('stackapi_validity').innerText = '';
                const innerFeedback = document.getElementById('specificfeedback');
                innerFeedback.innerHTML = '';
                innerFeedback.classList.remove('feedback');
                document.getElementById('formatcorrectresponse').innerHTML = correctAnswers;
                createIframes(json.iframes);
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
            catch (e) {
                document.getElementById('errors').innerText = 'There was an error attempting to display the request. Please try again or reload the page.';
                return;
            }
        }
    };
    const data = collectData();
    delete data.answers;
    http.send(JSON.stringify(data));
}

// Validate an input. Called a set amount of time after an input is last updated.
function validate(element) {
    const http = new XMLHttpRequest();
    const url = window.location.origin + '/validate';
    http.open("POST", url, true);
    // Remove API prefix and subanswer id.
    const answerName = element.name.slice(15).split('_', 1)[0];
    http.setRequestHeader('Content-Type', 'application/json');
    http.onreadystatechange = function () {
        if (http.readyState == 4) {
            try {
                const json = JSON.parse(http.responseText);
                if (json.message) {
                    document.getElementById('errors').innerText = json.message;
                    return;
                } else {
                    document.getElementById('errors').innerText = '';
                }
                renameIframeHolders();
                const validationHTML = json.validation;
                const element = document.getElementsByName(`${validationPrefix + answerName}`)[0];
                element.innerHTML = validationHTML;
                if (validationHTML) {
                    element.classList.add('validation');
                } else {
                    element.classList.remove('validation');
                }
                createIframes(json.iframes);
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
            catch (e) {
                document.getElementById('errors').innerText = 'There was an error attempting to display the request. Please try again or reload the page.';
                return;
            }
        }
    };

    const data = collectData();
    data.inputName = answerName;
    http.send(JSON.stringify(data));
}

// Submit answers.
function answer() {
    loading(true);
    const http = new XMLHttpRequest();
    const url = window.location.origin + '/grade';
    http.open("POST", url, true);

    if (!document.getElementById('output').innerText) {
        return;
    }

    http.setRequestHeader('Content-Type', 'application/json');
    http.onreadystatechange = function () {
        if (http.readyState == 4) {
            try {
                loading(false);
                const json = JSON.parse(http.responseText);
                if (json.message) {
                    document.getElementById('errors').innerText = json.message;
                    return;
                } else {
                    document.getElementById('errors').innerText = '';
                }
                if (!json.isgradable) {
                    document.getElementById('stackapi_validity').innerText
                        = ' Please enter valid answers for all parts of the question.';
                    return;
                }
                renameIframeHolders();
                if (displayType === FULLDISPLAY) {
                    document.getElementById('score').innerText
                        = (json.score * json.scoreweights.total).toFixed(2) +
                        ' out of ' + json.scoreweights.total;
                    document.getElementById('stackapi_score').style.display = 'block';
                    document.getElementById('response_summary').innerText = json.responsesummary;
                    document.getElementById('stackapi_summary').style.display = 'block';
                } else {
                    document.getElementById('stackapi_combinedfeedback').style.display = 'block';
                }
                const feedback = json.prts;
                const specificFeedbackElement = document.getElementById('specificfeedback');
                // Replace tags and plots in specific feedback and then display.
                if (json.specificfeedback) {
                    for (const [name, file] of Object.entries(json.gradingassets)) {
                        json.specificfeedback = json.specificfeedback.replace(name, `plots/${file}`);
                    }
                    json.specificfeedback = replaceFeedbackTags(json.specificfeedback);
                    specificFeedbackElement.innerHTML = json.specificfeedback;
                    if (displayType === FULLDISPLAY) {
                        specificFeedbackElement.classList.add('feedback');
                    }
                } else if (displayType === FULLDISPLAY) {
                    specificFeedbackElement.classList.remove('feedback');
                }
                // Replace plots in tagged feedback and then display.
                for (let [name, fb] of Object.entries(feedback)) {
                    for (const [name, file] of Object.entries(json.gradingassets)) {
                        fb = fb.replace(name, `plots/${file}`);
                    }
                    const elements = document.getElementsByName(`${feedbackPrefix + name}`);
                    if (elements.length > 0) {
                        const element = elements[0];
                        if (json.scores[name] !== undefined) {
                            fb = fb + `<div>Marks for this submission:
                ${(json.scores[name] * json.scoreweights[name] * json.scoreweights.total).toFixed(2)}
                / ${(json.scoreweights[name] * json.scoreweights.total).toFixed(2)}.</div>`;
                        }
                        element.innerHTML = fb;
                        if (fb) {
                            element.classList.add('feedback');
                        } else {
                            element.classList.remove('feedback');
                        }
                    }
                }
                createIframes(json.iframes);
                MathJax.Hub.Queue(["Typeset", MathJax.Hub]);
            }
            catch (e) {
                document.getElementById('errors').innerText = 'There was an error attempting to display the request. Please try again or reload the page.';
                loading(false);
                return;
            }
        }
    };
    // Clear previous answers and score.
    const specificFeedbackElement = document.getElementById('specificfeedback');
    specificFeedbackElement.innerHTML = "";
    specificFeedbackElement.classList.remove('feedback');
    if (displayType === FULLDISPLAY) {
        document.getElementById('response_summary').innerText = "";
        document.getElementById('stackapi_summary').style.display = 'none';
        document.getElementById('stackapi_score').style.display = 'none';
    } else {
        document.getElementById('stackapi_combinedfeedback').style.display = 'none';
    }
    const inputElements = document.querySelectorAll(`[name^=${feedbackPrefix}]`);
    for (const inputElement of Object.values(inputElements)) {
        inputElement.innerHTML = "";
        inputElement.classList.remove('feedback');
    }
    document.getElementById('stackapi_validity').innerText = '';
    http.send(JSON.stringify(collectData()));
}

function renameIframeHolders() {
    // Each call to STACK restarts numbering of iframe holders so we need to rename
    // any old ones to make sure new iframes end up in the correct place.
    for (const iframe of document.querySelectorAll(`[id^=stack-iframe-holder]:not([id$=old]`)) {
        iframe.id = iframe.id + '_old';
    }
}

function createIframes(iframes) {
    for (const iframe of iframes) {
        create_iframe(...iframe);
    }
}

// Replace feedback tags in some text with an approproately named HTML div.
function replaceFeedbackTags(text) {
    let result = text;
    const feedbackTags = text.match(/\[\[feedback:.*?\]\]/g);
    if (feedbackTags) {
        for (const tag of feedbackTags) {
            // Part name is between '[[feedback:' and ']]'.
            result = result.replace(tag, `<div name='${feedbackPrefix + tag.slice(11, -2)}'></div>`);
        }
    }
    return result;
}

function loading(isLoading) {
    if (isLoading) {
    $('.main-content .btn-primary').prop('disabled', true);
    $('[id$="stackapi-nav"]').addClass('link-disabled');
    $('#stackapi_spinner').show();
    } else {
    $('.main-content .btn-primary').prop('disabled', false);
    $('[id$="stackapi-nav"]').removeClass('link-disabled');
    $('#stackapi_spinner').hide();
    }
}

function download(filename, fileid) {
    const http = new XMLHttpRequest();
    const url = window.location.origin + '/download';
    http.open("POST", url, true);
    http.setRequestHeader('Content-Type', 'application/json');
    http.onreadystatechange = function() {
        if(http.readyState == 4) {
        try {
            // Only download the file once. Replace call to download controller with link
            // to downloaded file.
            const blob = new Blob([http.responseText], {type: 'application/octet-binary', endings: 'native'});
            const selector = CSS.escape(`javascript\:download\(\'${http.filename}\'\, ${http.fileid}\)`)
            const linkElements = document.querySelectorAll(`a[href^=${selector}]`);

            const link = linkElements[0];
            link.setAttribute('href', URL.createObjectURL(blob));
            link.setAttribute('download', filename);
            link.click();
        }
        catch(e) {
            document.getElementById('errors').innerText = http.responseText;
            return;
        }
        }
    };
    const data = collectData();
    data.filename = filename;
    data.fileid = fileid;
    delete data.answers;
    http.send(JSON.stringify(data));
}

function diff() {
    const http = new XMLHttpRequest();
    const url = window.location.origin + '/diff';
    http.open("POST", url, true);
    http.setRequestHeader('Content-Type', 'application/json');
    http.onreadystatechange = function() {
        if(http.readyState == 4) {
            try {
                const json = JSON.parse(http.responseText);
                if (json.message) {
                    document.getElementById('errors').innerText = json.message;
                    return;
                } else {
                    document.getElementById('errors').innerText = '';
                }
                document.getElementById('stackapi_difference').style.display = 'block';
                document.getElementById('difference').innerText = json.diff;
            } catch (e) {
                document.getElementById('errors').innerText = 'There was an error attempting to display the request. Please try again or reload the page.';
                return;
            }
        }
    };
    const data = collectData();
    const request = {
        questionDefinition: data.questionDefinition
    }
    http.send(JSON.stringify(request));
}
