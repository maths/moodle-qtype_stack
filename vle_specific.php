<?php
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
 * A collection of things that are a bit VLE specific and have been
 * extracted from the general logic.
 *
 * If you are porting to another platform you should check these out
 * these are not going to stop you from progressing but you will need
 * these at some point.
 *
 * There are two main things here:
 *
 *   1. Permission checking, the future error message system will tune its
 *      verbosity based on whether the user is a teacher or not.
 *
 *   2. Attached file management, any links that need rewriting to
 *      access attached fiels should be handled by this. This is relevant
 *      for all bits of CASText.
 *
 *
 * Elsewhere there are other major things:
 *
 *   1. The JSXGraph block in the CASText system uses JavaScript and loads
 *      it through the system, you will probably need to replace the block,
 *      should be enough to replace the portion of the block pushing out
 *      the script and the script itself may require some tuning related
 *      to the JavaScript Module system. If you don't want to support
 *      binding of inputs to JSXGraphs, just throw the block away.
 *
 *      CASText blocks can be replaced during execution so you do not even
 *      need to touch the original file. Simply use the `register`-function
 *      in the block-factory to replace the class handling that particular
 *      block. Same logic can be used to add blocks if for example your file
 *      management would need a new one.
 *
 *   2. The inputs and their related JavaScripts, these are the difficult
 *      ones. Again replacing scripts and the loading logic for them can
 *      prove to be hard and you may even choose to live without
 *      the instant validation feature those scripts provide. Other than
 *      that the recommended way is to map whatever way you deal with
 *      $_POST or even $_GET data so that those inputs receive similar
 *      $response arrays as they would in Moodle. Mapping functions for
 *      dealing with the script handling would be a good idea, or dummy
 *      functions if one does not care about those.
 *
 *   3. Storage of the question, you can freely store thigns as you wish
 *      but it would be nice to have unique identifiers for all
 *      the things that the original Moodle database model has separated
 *      to tables. And naturally mapping to similar arrays/objects on
 *      on the code side will help.
 *
 */

/**
 * This answers the question whether the currently active user is
 * able to edit this question. Basically, editing user.
 *
 * If you are unable to answer this question simply return FALSE.
 */
function stack_user_can_edit_question($question): bool {
    // In Moodle we can get this directly from the question itself.
    return $question->has_cap('edit');
}

/**
 * This answers the question whether the currently active user is
 * able to view this question. Basically, have it present in something
 * that they are supposed to see.
 *
 * If you are unable to answer this question simply return TRUE.
 * This is currently used for [[textdownload]] and being able to figure
 * out a link to some other persons attempt is not really a problem.
 */
function stack_user_can_view_question($question): bool {
    // In Moodle we can get this directly from the question itself.
    return $question->has_cap('view');
}

/**
 * Attachement files and CASText2 compilation note:
 *  1. If your attacment url is entirelly static after the question
 *     has received its database IDs please write it open here.
 *  2. In Moodle the url includes usage specific identifiers and must
 *     therefore be written open at the point of usage.
 *  3. Due to that we use the [[pfs]]-block to carry relevant details
 *     around in the code so that the writing open step can access these
 *     details when need be and in Moodle the [[pfs]]-block does that.
 *  4. If you have simillar needs for urls being specific to the user
 *     or usage crate your own block like [[pfs]] and register it to
 *     the CASText system to do the rewriting.
 *  5! If you have that type of variance in handling you must not
 *     rewrite at this point as the result of these is stored as compiled
 *     CASText and will be used for all future users of this question.
 *  6. You may need to deal with permissions here as well if you track
 *     access separately based on the part of the question the file
 *     exists in.
 *
 *  7. Note that rewriting to static urls during question import is also
 *     an option but it means that one needs to do more complex things
 *     during export if one wants to export those questiosn with those
 *     files.
 */

/**
 * Rewrites or wraps in rewriting logic a given CASText string if it
 * includes placeholders for urls that need to be rewritten.
 *
 * If your system does not support any such urls just return the string
 * as is.
 */
function stack_castext_file_filter(string $castext, array $identifiers): string {
    if ($castext === '') {
        // Nothing to do with empty strings.
        return $castext;
    }

    // In Moodle these are easy to spot.
    if (mb_strpos($castext, '@@PLUGINFILE@@') !== false) {
        // We use the PFS block that has been specicifally
        // built for Moodle to pass on the relevant details.
        $block = '[[pfs';
        switch ($identifiers['field']) {
            case 'questiontext':
            case 'generalfeedback':
                $block .= ' component="question"';
                $block .= ' filearea="' . $identifiers['field'] . '"';
                $block .= ' itemid="' . $identifiers['questionid'] . '"';
                break;
            case 'specificfeedback':
            case 'prtcorrect': // These three are not in actual use.
            case 'prtpartiallycorrect':
            case 'prtincorrect':
                $block .= ' component="qtype_stack"';
                $block .= ' filearea="' . $identifiers['field'] . '"';
                $block .= ' itemid="' . $identifiers['questionid'] . '"';
                break;
            case 'prtnodetruefeedback':
            case 'prtnodefalsefeedback':
                $block .= ' component="qtype_stack"';
                $block .= ' filearea="' . $identifiers['field'] . '"';
                $block .= ' itemid="' . $identifiers['prtnodeid'] . '"';
                break;
        }
        $block .= ']]';
        return $block . $castext . '[[/pfs]]';
    }
    return $castext;
}

/*
 * This function returns the version number of the current Moodle.
 */
function stack_determine_moodle_version() {
    $v = get_config('moodle');
    return($v->branch);
}
