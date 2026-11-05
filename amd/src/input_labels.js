// This file is part of STACK, licensed under GNU GPL v3 or later.
/**
 * Associate visible author-supplied mathematical context with answer controls.
 * @module qtype_stack/input_labels
 * @copyright 2026 Oleksandr Kulkov
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */
define([], function() {
    "use strict";
    return {
        /**
         * Labels stay in question text so CASText and language filters apply normally.
         * @param {String} questionId outer question element
         * @param {String} prefix attempt-specific input prefix
         */
        init: function(questionId, prefix) {
            const question = document.getElementById(questionId);
            if (!question) {
                return;
            }
            question.querySelectorAll('[data-stack-input-label]').forEach((label, index) => {
                const name = label.dataset.stackInputLabel;
                if (!/^[a-zA-Z][a-zA-Z0-9_]*$/.test(name)) {
                    return;
                }
                if (!label.id) {
                    label.id = prefix + name + '_label_' + index;
                }
                question.querySelectorAll('input, select, textarea').forEach(control => {
                    const matrix = control.dataset.stackInputType === 'matrix' &&
                        control.name.startsWith(prefix + name + '_sub_');
                    const simple = control.name === prefix + name &&
                        (control.tagName === 'SELECT' || control.tagName === 'TEXTAREA' || control.type === 'text');
                    if (!matrix && !simple) {
                        return;
                    }
                    // Preserve explicit labels supplied by another supported authoring mechanism.
                    if (!control.dataset.stackLabelManaged &&
                            (control.hasAttribute('aria-labelledby') || (control.labels && control.labels.length) ||
                            (!matrix && control.hasAttribute('aria-label')))) {
                        return;
                    }
                    const ids = control.dataset.stackLabelManaged ? control.getAttribute('aria-labelledby').split(' ') : [];
                    if (!ids.includes(label.id)) {
                        ids.unshift(label.id);
                    }
                    if (matrix) {
                        const positionId = control.id + '_position';
                        let position = document.getElementById(positionId);
                        if (!position) {
                            position = document.createElement('span');
                            position.id = positionId;
                            position.className = 'accesshide';
                            position.textContent = control.getAttribute('aria-label') || '';
                            control.after(position);
                        }
                        if (!ids.includes(positionId)) {
                            ids.push(positionId);
                        }
                    }
                    control.setAttribute('aria-labelledby', ids.join(' '));
                    control.dataset.stackLabelManaged = 'true';
                });
            });
        }
    };
});
