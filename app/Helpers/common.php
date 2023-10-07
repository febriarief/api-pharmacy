<?php

/**
 * Return success response in json form
 *
 * @author Febri Arief<febriarief661@gmail.com>
 * @param  int  $code
 * @param  string  $message
 * @param  object|array|\Illuminate\Support\Collection  $data
 * @return \Illuminate\Http\JsonResponse
 */
if (!function_exists('json_success_response')) {
    function json_success_response($code = 200, $message = '', $data = []) {
        return response()->json([
            'status'  => $code,
			'message' => $message,
			'data'    => $data
		], $code);
    }
}

/**
 * Return error response in json form
 *
 * @author Febri Arief<febriarief661@gmail.com>
 * @param  int  $code
 * @param  string  $message
 * @param  object|array|\Illuminate\Support\Collection  $data
 * @return \Illuminate\Http\JsonResponse
 */
if (!function_exists('json_error_response')) {
    function json_error_response($code = 422, $message = '', $errors = []) {
        return response()->json([
			'errors'    => $errors,
			'message' => $message
		], $code);
    }
}

/**
 * Convert camel case to human readable case
 *
 * @author Febri Arief<febri.arief001@gmail.com>
 * @param  string $string
 * @return string
 */
if (!function_exists('camel_to_human_case')) {
    function camel_to_human_case($string) {
        return preg_replace('/(?:[a-z]|[A-Z]+)\K(?=[A-Z]|\d+)/', ' ', $string);
    }
}

/**
 * Convert phone number with/without country code
 *
 * @author Febri Arief <febriarief6661@gmail.com>
 * @param  string|int  $phonenumber
 * @param  bool  $countryCode
 * @return string
 */
if (!function_exists('format_phonenumber')) {
    function format_phonenumber($phonenumber, $countryCode = false) {
        if ($phonenumber) {
            if ($countryCode) {
                if ($phonenumber[0] == '0') {
                    $phonenumber = '+62' . substr($phonenumber, 1);
                } else if ($phonenumber[0] == '8') {
                    $phonenumber = '+62' . $phonenumber;
                } else if (preg_match('/^62/', $phonenumber)) {
                    $phonenumber = '+62' . substr($phonenumber, 2);
                }
            } else {
                if (preg_match('/\+62/', $phonenumber)) {
                    $phonenumber = '0' . substr($phonenumber, 3);
                } else if (preg_match('/^62/', $phonenumber)) {
                    $phonenumber = '0' . substr($phonenumber, 2);
                } else if ($phonenumber[0] != '0') {
                    $phonenumber = '0' . preg_replace('/[^0-9\-]/', '', $phonenumber);
                }
            }
        }

        return $phonenumber;
    }
}
