<?php

declare(strict_types=1);

return [
    /*
    |--------------------------------------------------------------------------
    | API Specification
    |--------------------------------------------------------------------------
    |
    | The file path must be absolute in order to resolve references in api spec
    |
    */
    'api_spec_file' => base_path() . '/api/openapi.yaml',

    /*
    |--------------------------------------------------------------------------
    | Validation Error Response Customisation
    |--------------------------------------------------------------------------
    |
    | Set the response code to the status code you want to return on error
    | Set the url to the url you want to return on error
    |
    */
    'validation_error_response_code' => 400,
    'validation_error_response_type' => 'about:blank',
    'api_problem_response_types' => [],

];
