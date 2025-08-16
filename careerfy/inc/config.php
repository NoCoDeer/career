<?php

/**
 * Careerfy Theme Config.
 *
 * @package Careerfy
 */
define("CAREERFY_VERSION", "10.1.1");
define("WP_JOBSEARCH_VERSION", "3.0.3");

function careerfy_framework_options() {
    global $careerfy_framework_options;
    if (empty($careerfy_framework_options)) {
        $careerfy_framework_options = get_option('careerfy_framework_options');
    }
    return $careerfy_framework_options;
}