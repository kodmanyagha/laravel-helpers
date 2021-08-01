<?php

use Illuminate\Support\Facades\Log;


if (!function_exists('getOnlyNumbers')) {
    /**
     * @param $string
     * @param int $length
     * @return false|string
     *
     * Extract numbers from a string.
     * 0(555) 444 33 22 -> 05554443322
     */
    function getOnlyNumbers($string, $length = 0)
    {
        preg_match_all('!\d+!', $string, $matches);
        $string = implode("", $matches[0]);
        unset($matches);
        return substr($string, $length);
    }
}


if (!function_exists('priceFormat')) {
    /**
     * @param $number
     * @return string
     *
     * Return price as english format.
     */
    function priceFormat($number)
    {
        return number_format((float)$number, 2, '.', '');
    }
}


if (!function_exists('makeApiCall')) {
    /**
     * @param $url
     * @param string $method
     * @param null $postData
     * @param null $username
     * @param null $password
     * @param null $cookieFile
     * @return array
     *
     * Return result as array which contains headers and body.
     */
    function makeApiCall($url, $method = 'get', $postData = null, $username = null, $password = null, $cookieFile = null)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, 1);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch, CURLOPT_POST, intval($method == "post"));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
        curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);

        if (!is_null($username)) curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);

        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        curl_setopt($ch, CURLOPT_TIMEOUT, 90);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36');

        $result = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header      = substr($result, 0, $header_size);
        $body        = substr($result, $header_size);

        curl_close($ch);

        return array(
            "header" => $header,
            "body"   => $body
        );
    }
}


if (!function_exists('var_dump_str')) {
    /**
     * @return false|string
     * Return var_dump result as string.
     */
    function var_dump_str(...$vars)
    {
        $argc = func_num_args();

        if ($argc > 0) {
            ob_start();
            call_user_func_array('var_dump', $vars);
            $result = ob_get_contents();
            ob_end_clean();
            return $result;
        }
        return '';
    }
}


if (!function_exists('pr2json')) {
    /**
     * Convert print_r result to json string.
     * @note Exceptions are always there i tried myself best to get it done. Here $array can be array of arrays or arrays of objects or both
     * @param string $string Result of `print_r($array, true)`
     * @param bool $returnAsObj
     * @return string Json string (transformed version of print_r result)
     */
    function pr2json($string, $returnAsObj = false)
    {
        // replacing `stdClass Objects (` to  `{`
        $string = preg_replace("/stdClass Object\s*\(/s", '{  ', $string);

        // replacing `Array (` to  `{`
        $string = preg_replace("/Array\s*\(/s", '{  ', $string);

        // replacing `)\n` to  `},\n` @note This might append , at the last of string as well which we will trim later on.
        $string = preg_replace("/\)\n/", "},\n", $string);

        // replacing `)` to  `}` at the last of string
        $string = preg_replace("/\)$/", '}', $string);

        // replacing `[ somevalue ]` to "somevalue"
        $string = preg_replace("/\[\s*([^\s\]]+)\s*\](?=\s*\=>)/", '"\1" ', $string);

        // replacing `=> {`  to `: {`
        $string = preg_replace("/=>\s*{/", ': {', $string);

        // replacing empty last values of array special case `=> \n }` to : "" \n
        $string = preg_replace("/=>\s*[\n\s]*\}/s", ":\"\"\n}", $string);

        // replacing `=> somevalue`  to `: "somevalue",`
        $string = preg_replace("/=>\s*([^\n\"]*)/", ':"\1",', $string);

        // replacing last mistakes `, }` to `}`
        $string = preg_replace("/,\s*}/s", '}', $string);

        // replacing `} ,` at the end to `}`
        $string = preg_replace("/}\s*,$/s", '}', $string);

        if ($returnAsObj === true)
            return s2o($string);
        return $string;
    }
}


if (!function_exists('isLocal')) {
    /**
     * @return bool
     *
     * Is current environment is local?
     */
    function isLocal()
    {
        return env('APP_ENV') == 'local';
    }
}


if (!function_exists('sw')) {

    /********************
     * sw: starts with
     *
     * @param $string string
     * @param $startString string
     * @return bool
     */
    function sw($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}

if (!function_exists('ew')) {

    /********************
     * ew: ends with
     *
     * @param $string string
     * @param $endString string
     * @return bool
     */
    function ew($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }
}


if (!function_exists('startsWith')) {
    /**
     * @param $string
     * @param $startString
     * @return bool
     *
     * Function to check string starting
     * with given substring
     */
    function startsWith($string, $startString)
    {
        $len = strlen($startString);
        return (substr($string, 0, $len) === $startString);
    }
}


if (!function_exists('endsWith')) {
    /**
     * @param $string
     * @param $endString
     * @return bool
     *
     * Function to check the string is ends
     * with given substring or not
     */
    function endsWith($string, $endString)
    {
        $len = strlen($endString);
        if ($len == 0) {
            return true;
        }
        return (substr($string, -$len) === $endString);
    }
}


if (!function_exists('gp')) {

    /********************
     * gp: Get Params from route.
     *
     * @return array
     */
    function gp()
    {
        $params = request()->route()->parameters();
        return array_values($params);
    }
}


if (!function_exists('randomDate')) {
    /**
     * @param $minDay
     * @param $maxDay
     * @return false|string
     *
     * Parameters can be negative.
     */
    function randomDate($minDay, $maxDay)
    {
        if ((int)$maxDay < (int)$minDay) throw  new \InvalidArgumentException('maxDay has to be greater or equal then minDay');

        $randomDay = mt_rand($minDay, $maxDay);

        if ($randomDay < 0) $randomDay = '-' . abs($randomDay);
        else $randomDay = '+' . $randomDay;

        return date('Y-m-d', strtotime($randomDay . ' days'));
    }
}


if (!function_exists('randomDateTime')) {
    /**
     * @param $startDate
     * @param $endDate
     * @return false|string
     */
    function randomDateTime($startDate, $endDate)
    {
        if (gettype($startDate) == 'string') $startDate = strtotime($startDate);
        if (gettype($endDate) == 'string') $endDate = strtotime($endDate);

        $dateInt = mt_rand($startDate, $endDate);
        return date("Y-m-d H:i:s", $dateInt);
    }
}


if (!function_exists('mysqlNow')) {
    /**
     * @return string
     */
    function mysqlNow()
    {
        return date("Y-m-d H:i:s");
    }
}


if (!function_exists('println')) {
    /**
     * @param $data
     * Print data with EOL.
     */
    function println($data)
    {
        echo $data . PHP_EOL;
    }
}


if (!function_exists('pe')) {
    /**
     * @param $data
     * print and exit
     */
    function pe($data)
    {
        echo "<pre>";
        print_r($data);
        exit;
    }
}


if (!function_exists('password')) {
    /**
     * @param $password
     * @return string
     *
     * Create hash from password with salt (APP_KEY).
     */
    function password($password)
    {
        return sha1(md5($password . env('APP_KEY')));
    }
}


if (!function_exists('mo')) {
    /**
     * @param $data
     * @return mixed
     *
     * Make object.
     */
    function mo($data)
    {
        return json_decode(json_encode($data));
    }
}


if (!function_exists('ma')) {
    /**
     * @param mixed $data
     * @param int $depth
     * @return mixed
     *
     * Make array.
     */
    function ma($data, $depth = 512)
    {
        return json_decode(json_encode($data), true, $depth);
    }
}


if (!function_exists('s2o')) {

    /**
     * @param string $str
     * @param boolean $assoc
     * @return mixed
     *
     * String to object (json)
     */
    function s2o($str, $assoc = false)
    {
        return json_decode($str, $assoc);
    }
}


if (!function_exists('o2s')) {

    /**
     * @param mixed $obj
     * @return string
     *
     * Object to string
     */
    function o2s($obj, $pretty = false)
    {
        if ($pretty)
            return json_encode($obj, JSON_PRETTY_PRINT);

        return json_encode($obj);
    }
}


if (!function_exists('lang')) {
    /**
     * @param string $key
     * @param array $params
     * @param string|null $langCode
     * @return string
     *
     * Example usage:
     *   lang('Hello World!'); // Hello World!
     *   lang('Welcome, {{firstname}}!', ['firstname' => 'John']); // Welcome, John!
     *   lang('Your 2FA password: {{0}}', ['123456'], 'tr-TR'); // 2 Aşamalı Doğrulama kodunuz: 123456
     */
    function lang(string $key, array $params = [], string $langCode = null)
    {
        // detect langCode if null
        if (is_null($langCode)) {
            // Example value: `Accept-Language: fr-CH, fr;q=0.9, en;q=0.8, de;q=0.7, *;q=0.5`
            $headerAcceptLanguages = explode(',', request()->header('accept-language'));
            foreach ($headerAcceptLanguages as $headerAcceptLanguage) {
                $headerAcceptLanguage = trim($headerAcceptLanguage);
                $headerAcceptLanguage = explode(';', $headerAcceptLanguage);

                if (isset(app('languages')[$headerAcceptLanguage[0]])) {
                    $langCode = $headerAcceptLanguage[0];
                    break;
                }
            }

            // this is important then http header
            $inputLangCode = substr(trim(request()->input('lang')), 0, 8);
            if (isset(app('languages')[$inputLangCode])) {
                $langCode = $inputLangCode;
            }

            // if we still didn't recognize client's preferred lang then we set it default lang
            if (strlen($langCode) == 0)
                $langCode = env('APP_LANG', 'en-US');
        }

        if (!isset(app('languages')[$langCode]))
            lgi('Lang not found: ' . $langCode . ' ' . $key);
        if (!isset(app('languages')[$langCode][$key]))
            lgi('Key not found: ' . $langCode . ' ' . $key);

        $key = (isset(app('languages')[$langCode][$key])) ? app('languages')[$langCode][$key] : $key;
        if (count($params) > 0) {
            foreach ($params as $k => $v) {
                $key = str_replace('{{' . $k . '}}', $v, $key);
            }
        }

        return $key;
    }
}


if (!function_exists('lg')) {

    /**
     * lg: log with file name and line number
     */
    function lg($anything, $type = 'debug')
    {
        if (!in_array(gettype($anything), [
            'string',
            'number'
        ])) {
            $anything = o2s($anything, true);
        }
        $bt = debug_backtrace();

        foreach ($bt as $i => $b) {
            if ($i > 10) break;
        }
        $cwd = base_path();

        $caller = array_shift($bt);
        $file   = $caller['file'];
        $file   = substr($file, strlen($cwd));

        // pass the current file
        if ($file == '/vendor/kodmanyagha/laravel-helpers/src/Helper.php')
            $caller = array_shift($bt);

        $file    = $caller['file'];
        $line    = $caller['line'];
        $file    = substr($file, strlen($cwd));
        $message = $file . ':' . $line . ' ' . $anything;

        // force output to stdout: config(['logging.default' => 'stdout']);
        $logChannel = Log::channel(config('logging.default'));

        if (strtolower($type) == 'debug')
            $logChannel->debug($message);
        else if (strtolower($type) == 'info')
            $logChannel->info($message);
        else if (strtolower($type) == 'warning')
            $logChannel->warning($message);
        else if (strtolower($type) == 'error')
            $logChannel->error($message);
    }

    /**
     * @param mixed $message
     * Log DEBUG.
     */
    function lgd($message)
    {
        $args = func_get_args();
        if (count($args) == 1)
            lg($args[0], 'debug');
        else
            lg($args, 'debug');
    }

    /**
     * @param mixed $message
     * Log INFO.
     */
    function lgi($message)
    {
        $args = func_get_args();
        if (count($args) == 1)
            lg($args[0], 'info');
        else
            lg($args, 'info');
    }

    /**
     * @param mixed $message
     * Log WARNING.
     */
    function lgw($message)
    {
        $args = func_get_args();
        if (count($args) == 1)
            lg($args[0], 'warning');
        else
            lg($args, 'warning');
    }

    /**
     * @param mixed $message
     * Log ERROR.
     */
    function lge($message)
    {
        $args = func_get_args();
        if (count($args) == 1)
            lg($args[0], 'error');
        else
            lg($args, 'error');
    }
}
