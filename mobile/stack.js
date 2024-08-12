var that = this;
var result = {

    componentInit: function() {
        // This.question should be provided to us here.
        // This.question.html (string) is the main source of data, presumably prepared by the renderer.
        // There are also other useful objects with question like infoHtml which is used by the
        // page to display the question state, but with which we need do nothing.
        // This code just prepares bits of this.question.html storing it in the question object ready for
        // passing to the template (stack.html).
        // Note this is written in 'standard' javascript rather than ES6. Both work.

        if (!this.question) {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }

        // Create a temporary div to ease extraction of parts of the provided html.
        var div = this.CoreDomUtilsProvider.convertToElement(this.question.html);
        div.innerHTML = this.question.html;

        // Replace Moodle's correct/incorrect classes, feedback and icons with mobile versions.
        that.CoreQuestionHelperProvider.replaceCorrectnessClasses(div);
        that.CoreQuestionHelperProvider.replaceFeedbackClasses(div);
        that.CoreQuestionHelperProvider.treatCorrectnessIcons(div);

        // Get useful parts of the provided question html data.
        var questiontext = div.querySelector('.content');
        const answers = questiontext.querySelectorAll('.answer');
        const dashLink = questiontext.querySelector('.questiontestslink');
        if (dashLink) {
            dashLink.parentNode.removeChild(dashLink);
        }
        var prompt = div.querySelector('.prompt');

        // Add the useful parts back into the question object ready for rendering in the template.
        this.question.text = questiontext.innerHTML;
        // Without the question text there is no point in proceeding.
        if (typeof this.question.text === 'undefined') {
            return that.CoreQuestionHelperProvider.showComponentError(that.onAbort);
        }
        if (prompt !== null) {
            this.question.prompt = prompt.innerHTML;
        }
        var checkboxsets = [];

        answers.forEach(function(checkboxset, i) {
            var options = checkboxset.querySelectorAll('.option');
            const o = [];
            options.forEach(function(option) {
                // Each answer option contains all the data for presentation, it just needs extracting.
                var label = option.querySelector('label').innerHTML;
                var name = option.querySelector('label').getAttribute('for');
                var checked = (option.querySelector('input[type=checkbox]').getAttribute('checked') ? true : false);
                var disabled = (option.querySelector('input').getAttribute('disabled') === 'disabled' ? true : false);
                var qclass = option.getAttribute('class');
                o.push({text: label, name: name, checked: checked, disabled: disabled, qclass: qclass});
            });
            checkboxsets.push(o);
            checkboxset.replaceWith('~~!!~~Checkbox:' + i + '~~!!');
        });
        var questionHTML = questiontext.innerHTML;
        var sectionsHTML = questionHTML.split('~~!!');
        const sections = [];
        sectionsHTML.forEach(function(sectionHTML) {
            const section = {};
            if (!sectionHTML.startsWith('~~')) {
                section.type = 'Text';
                section.content = sectionHTML;
            } else {
                const sectionInfo = sectionHTML.split(':');
                switch (sectionInfo[0]) {
                    case ('~~Checkbox'):
                        section.type = 'Checkbox';
                        section.options = checkboxsets[Number(sectionInfo[1])];
                        break;
                }
            }
            sections.push(section);
        });
        this.question.sections = sections;
        return true;
    }
};

// This next line is required as is (because of an eval step that puts this result object into the global scope).
result;
