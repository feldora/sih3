<?php

use Carbon\Carbon;

/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('convertYmdToMdy')) {
    function convertYmdToMdy($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date)->format('m-d-Y');
    }
}

/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('convertMdyToYmd')) {
    function convertMdyToYmd($date)
    {
        return Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d');
    }
}

/**
 * echo pretty and die dump
 *
 * @return response()
 */
if (! function_exists('pd')) {
    function pd($args, $die = true)
    {
        echo "<pre>";
        print_r($args);
        echo "</pre>";
        if ($die) {
            die;
        }
    }
}

/**
 * response json
 *
 * @return response()
 */
if (! function_exists('pd')) {
    function je($args)
    {
        return json_encode($args);
    }
}
