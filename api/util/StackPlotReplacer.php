<?php

namespace api\util;

class StackPlotReplacer
{
    public static function replace_plots(&$text, $pluginFilePrefix) {
        $plots = [];

        preg_match_all('/["\']!ploturl!([\w\-\.]*?)["\']/m', $text, $matches);
        array_push($plots, ...$matches[1]);
        preg_match_all('/["\']@@PLUGINFILE@@\/([\w\-\.]*?)["\']/m', $text, $matches);
        //Add Pluginfiles with prefix
        array_push($plots, ...preg_filter('/^/', $pluginFilePrefix . '-', $matches[1]));

        $text = str_replace('!ploturl!', '', $text);
        $text = str_replace('@@PLUGINFILE@@/', $pluginFilePrefix . '-', $text);

        return $plots;
    }

    public static function persistPluginfiles(\qtype_stack_question $question, $pluginFilePrefix) {
        global $CFG;
        foreach ($question->pluginfiles as $name => $content) {
            file_put_contents($CFG->dataroot . '/stack/plots/' . $pluginFilePrefix . '-' . $name, base64_decode($content));
        }
    }

}
