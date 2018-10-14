define(['jquery', 'qtype_stack/tex2max', 'qtype_stack/visual-math-input'], function ($, Tex2Max, VisualMath) {

    // Constants
    const FEEDBACK_ERROR_DELAY = 1000;
    const WAITING_TIMER_DELAY = 1000;

    let errorTimer;
    let waitingTimer;
    let converters = new Map();

    function convert(latex, options, stackInputID) {
        let result = '';

        let converter = converters.get(stackInputID);
        if (typeof converter === "undefined") {
            try {
                converter = new Tex2Max.TeX2Max(options);
                converters.set(stackInputID, converter);
            } catch (error) {
                renderErrorFeedback(error.message, stackInputID);
                return;
            }
        }

        clearTimeout(errorTimer);

        if (!latex) {
            hideTeX2MaXFeedback(stackInputID);

            let stackValidationFeedback = document.getElementById(stackInputID + '_val');
            let $stackValidationFeedback = $(stackValidationFeedback);
            $stackValidationFeedback.hide();

            return result;
        }

        try {
            result = converter.toMaxima(latex);
            hideTeX2MaXFeedback(stackInputID);

        } catch (error) {
            renderErrorFeedback(error.message, stackInputID);
        }

        return result;
    }

    function removeAllValidationClasses(selector) {
        let validationFeedback = document.getElementById(selector);
        let $validationFeedback = $(validationFeedback);
        $validationFeedback.removeClass('empty');
        $validationFeedback.removeClass('error');
        $validationFeedback.removeClass('loading');
        $validationFeedback.removeClass('waiting');
    }

    function resetStackValidation(stackInputID) {
        let stackValidationFeedback = document.getElementById(stackInputID + '_val');
        let $stackValidationFeedback = $(stackValidationFeedback);

        $stackValidationFeedback.removeAttr("style");
    }

    function hideTeX2MaXFeedback(stackInputID) {
        let existingFeedback = document.getElementById(stackInputID + '_tex2max');
        let $existingFeedback = $(existingFeedback);

        let stackValidationFeedback = document.getElementById(stackInputID + '_val');

        if (stackValidationFeedback.style.display !== "") {
            $existingFeedback.toggleClass('waiting', true);
            waitingTimer = setTimeout(() => {
                removeAllValidationClasses(stackInputID + '_tex2max');
                $existingFeedback.toggleClass('empty', true);
                resetStackValidation(stackInputID);
            }, WAITING_TIMER_DELAY);

            setTimeout(function () {
                removeAllValidationClasses(stackInputID + '_tex2max');
                $existingFeedback.toggleClass('waiting', true);
            }, 0);

        } else {
            $existingFeedback.toggleClass('empty', true);
        }
    }

    function renderErrorFeedback(errorMessage, stackInputID) {
        clearTimeout(waitingTimer);

        let existingFeedback = document.getElementById(stackInputID + '_tex2max');
        let $existingFeedback = $(existingFeedback);
        if (existingFeedback && !$existingFeedback.hasClass('empty')) {
                removeAllValidationClasses(stackInputID + '_tex2max');
                $existingFeedback.toggleClass('waiting', true);
        }

        errorTimer = setTimeout(() => {
            renderTeX2MaXFeedback(errorMessage, stackInputID)
        }, FEEDBACK_ERROR_DELAY);
    }

    function renderTeX2MaXFeedback(errorMessage, stackInputID) {
        if (!errorMessage) errorMessage = "";

        let feedbackMessage = "This answer is invalid.";
        let stackValidationFeedback = document.getElementById(stackInputID + '_val');
        let $stackValidationFeedback = $(stackValidationFeedback);
        $stackValidationFeedback.hide();

        let existingFeedback = document.getElementById(stackInputID + '_tex2max');
        if (existingFeedback) {
            removeAllValidationClasses(stackInputID + '_tex2max');

            let existingErrorMessageParagraph = document.getElementById(stackInputID + '_errormessage');
            existingErrorMessageParagraph.innerHTML = errorMessage;

        } else {
            let feedbackWrapper = document.createElement('div');
            feedbackWrapper.setAttribute('class', 'tex2max-feedback-wrapper');
            feedbackWrapper.setAttribute('id', stackInputID + '_tex2max');

            let feedbackMessageParagraph = document.createElement('p');
            let errorMessageParagraph = document.createElement('p');
            errorMessageParagraph.setAttribute('id', stackInputID + '_errormessage');

            feedbackMessageParagraph.innerHTML = feedbackMessage;
            errorMessageParagraph.innerHTML = errorMessage;

            feedbackWrapper.append(feedbackMessageParagraph);
            feedbackWrapper.append(errorMessageParagraph);

            $stackValidationFeedback.after(feedbackWrapper);
        }
    }

    function showOrHideCheckButton(inputIDs, prefix) {
        for (let i = 0; i < inputIDs.length; i++) {
            let $outerdiv = $(document.getElementById(inputIDs[i])).parents('div.que.stack').first();
            if ($outerdiv && ($outerdiv.hasClass('dfexplicitvaildate') || $outerdiv.hasClass('dfcbmexplicitvaildate'))) {
                // With instant validation, we don't need the Check button, so hide it.
                let button = $outerdiv.find('.im-controls input.submit').first();
                if (button.attr('id') === prefix + '-submit') {
                    button.hide();
                }
            }
        }
    }

    const DEFAULT_TEX2MAX_OPTIONS = {
        onlySingleVariables: false,
        handleEquation: false,
        addTimesSign: true,
        onlyGreekName: true,
        onlyGreekSymbol: false,
        debugging: true
    };

    function formatOptionsObj(rawOptions) {
        let options = {};

        for (let key in rawOptions) {
            if (!rawOptions.hasOwnProperty(key)) continue;

            let value = rawOptions[key];
            switch (key) {
                case "singlevars":
                    if (value === '1') {
                        options.onlySingleVariables = true;
                    } else {
                        options.onlySingleVariables = false;
                    }
                    break;
                case "addtimessign":
                    if (value === '1') {
                        options.addTimesSign = true;
                    } else {
                        options.addTimesSign = false;
                    }
                    break;

                default :
                    break;
            }
        }

        options = Object.assign(DEFAULT_TEX2MAX_OPTIONS, options);
        return options;
    }

    function buildInputControls(mode) {
        if (!mode) throw new Error('No mathinputmode is set');

        let controls = new VisualMath.ControlList('#controls_wrapper');
        let controlNames = [];

        let caret = controls.define('caret', '^', field => field.cmd('^'))

        switch (mode) {
            case 'simple':
                controlNames = ['sqrt', 'divide', 'pi', 'caret'];
                controls.enable(controlNames);
                break;
            case 'normal':
                controlNames = ['sqrt', 'divide', 'nchoosek', 'pi', 'caret'];
                controls.enable(controlNames);
                break;
            case 'experimental':
                controls.enableAll();
                break;
            case 'none':
                break;
            default:
                break;
        }
    }


    return {
        initialize: (debug, prefix, stackInputIDs, latexInputIDs, latexResponses, questionOptions) => {

            if (!stackInputIDs.length > 0) return;

            let options = formatOptionsObj(questionOptions);
            let readOnly = false;

            showOrHideCheckButton(stackInputIDs, prefix);

            for (let i = 0; i < stackInputIDs.length; i++) {
                let $stackInputDebug, $latexInputDebug;

                let latexInput = document.getElementById(latexInputIDs[i]);
                let $latexInput = $(latexInput);

                let stackInput = document.getElementById(stackInputIDs[i]);
                let $stackInput = $(stackInput);

                let wrapper = document.createElement('div');
                $stackInput.wrap(wrapper);
                let $parent = $stackInput.parent();

                if (debug) {
                    let stackInputDebug = document.getElementById(stackInputIDs[i] + '_debug');
                    $stackInputDebug = $(stackInputDebug);

                    let latexInputDebug = document.getElementById(latexInputIDs[i] + '_debug');
                    $latexInputDebug = $(latexInputDebug);
                }

                let input = new VisualMath.Input('#' + $.escapeSelector(stackInputIDs[i]), $parent);
                input.$input.hide();

                if (!input.$input.prop('readonly')) {
                    input.onEdit = ($input, field) => {
                        $input.val(convert(field.latex(), options, stackInputIDs[i]));
                        $latexInput.val(field.latex());
                        $input.get(0).dispatchEvent(new Event('change')); // Event firing needs to be on a vanilla dom object.

                        if (debug) {
                            $stackInputDebug.html(convert(field.latex(), options, stackInputIDs[i]));
                            $latexInputDebug.html(field.latex());
                        }
                    };

                } else {
                    readOnly = true;
                    input.disable();
                }

                // Set the previous step attempt data or autosaved (mod_quiz) value to the MathQuill field.
                if ($latexInput.val()) {
                    input.field.write($latexInput.val());
                    convert($latexInput.val(), options, stackInputIDs[i])

                } else if (latexResponses[i] !== null && latexResponses[i] !== "") {
                    input.field.write(latexResponses[i]);
                    convert(latexResponses[i], options, stackInputIDs[i])
                }
            }


            if (!readOnly) {
                buildInputControls(questionOptions['mathinputmode']);
            }

        }
    };

});