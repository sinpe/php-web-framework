<?php

if (!function_exists('snake')) {
    /**
     * Convert a string to snake case.
     *
     * @param  string  $value
     * @param  string  $delimiter
     * @return string
     */
    function snake($value, $delimiter = '_')
    {
        $value = trim(preg_replace_callback(
            '#[A-Z]#',
            function ($matches) use ($delimiter) {
                return $delimiter . strtolower($matches[0]);
            },
            $value
        ), $delimiter);

        return $value;
    }

}

if (!function_exists('camel')) {
    /**
     * Convert a value to camel case.
     *
     * @param  string  $value
     * @return string
     */
    function camel($value)
    {
        return lcfirst(studly($value));
    }
}

if (!function_exists('studly')) {
    /**
     * Convert a value to studly caps case.
     *
     * @param  string  $value
     * @return string
     */
    function studly(string $value, string $delimiter = null)
    {
        if ($delimiter) {
            $values = explode($delimiter, $value);
            $values = array_map(function($value){
                return studly($value);
            }, $values);
            return implode($delimiter, $values);
        }
        $value = ucwords(str_replace(['-', '_'], ' ', $value));
        return str_replace(' ', '', $value);
    }
}