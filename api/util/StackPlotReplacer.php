<?php

namespace api\util;

class StackPlotReplacer
{
    public static function replace_plots(&$plots, &$text, $namePrefix, $storePrefix) {
        $i = 0;

        $text = preg_replace_callback('/["\']!ploturl!([\w\-\.]*?)\.(\w+)["\']/m', function ($matches) use (&$i, $namePrefix, &$plots) {
            $name = $namePrefix . "-" . $i . "." . $matches[2];
            $plots[$name] = $matches[1]. "." . $matches[2];
            $i++;
            return "\"".$name."\"";
        }, $text);

        $text = preg_replace_callback('/["\']@@PLUGINFILE@@\/([\w\-\.]*?)["\']/m', function ($matches) use ($storePrefix, &$plots) {
            $plots[$matches[1]] = $storePrefix . "-" . $matches[1];
            return "\"".$matches[1]."\"";
        }, $text);
    }

    public static function persistPluginfiles(\qtype_stack_question $question, $storePrefix) {
        global $CFG;
        foreach ($question->pluginfiles as $name => $content) {
            file_put_contents($CFG->dataroot . '/stack/plots/' . $storePrefix . '-' . $name, base64_decode($content));
        }
    }

}
