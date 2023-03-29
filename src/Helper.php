<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;


if (!function_exists('heredocCall')) {

    /**
     * Use this in `HEREDOC` strings. Example:
     *
     * $call = heredocCall()
     *
     * $text = <<<HEREDOC
     * Hello, {$call(ucfirst('world'))}
     * HEREDOC;
     *
     * $text = <<<HTML
     * <li class="list-group-item p-1">
     * {$c(__('app.permissions.' . $row->name))}
     * </li>
     * HTML;
     *
     */
    function heredocCall(): Closure
    {
        return fn($fn) => $fn;
    }
}

if (!function_exists('cpuRandomStr')) {
    function cpuRandomStr()
    {
        return md5(json_encode(getrusage()));
    }
}

if (!function_exists('getOnlyNumbers')) {
    /**
     * @param $string
     * @param int $length
     *
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
     *
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
     * @param array|null $headers
     * @param null $username
     * @param null $password
     * @param null $cookieFile
     * @param null $bearerToken
     *
     * @return array
     *
     * Return result as array which contains headers and body.
     */
    function makeApiCall(
        $url, string $method = 'get',
        $postData = null, ?array $headers = [],
        $username = null, $password = null,
        $cookieFile = null, $bearerToken = null
    )
    {
        $headers   = !is_array($headers) ? array_values(makeArray($headers)) : array_values($headers);
        $headers[] = 'Connection: close';

        $host = parse_url($url, PHP_URL_HOST);
        $port = (int)parse_url($url, PHP_URL_PORT);
        $port = $port > 0 ? $port : 80;

        $method = strtolower($method);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_VERBOSE, config('app.curl_verbose', false));
        curl_setopt($ch, CURLOPT_HEADER, 1);

        curl_setopt($ch, CURLOPT_RESOLVE, [sprintf("%s:%d:%s", $host, $port, '8.8.8.8')]);

        curl_setopt($ch, CURLOPT_FORBID_REUSE, 1);
        curl_setopt($ch, CURLOPT_TCP_KEEPALIVE, 0);

        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);

        if ((int)config('app.allow_insecure_curl') === 1) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        }

        if (strlen($cookieFile) > 0) {
            curl_setopt($ch, CURLOPT_COOKIEJAR, $cookieFile);
            curl_setopt($ch, CURLOPT_COOKIEFILE, $cookieFile);
        }

        if (in_array($method, [
            'post',
            'put',
            'patch',
        ])) {
            if ($method == 'post') {
                curl_setopt($ch, CURLOPT_POST, 1);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, strtoupper($method));
            }

            if (is_string($postData)) {
                $headers[] = 'Content-Type: application/x-www-form-urlencoded';
                curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            } elseif (is_array($postData) || is_object($postData)) {
                //$headers[] = 'Content-Type: multipart/form-data';
                $headers[] = 'Content-Type: application/json';
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
            }
        }

        if (!is_null($username)) {
            curl_setopt($ch, CURLOPT_USERPWD, $username . ":" . $password);
        }

        if (!is_null($bearerToken)) {
            $headers[] = "Authorization: Bearer " . $bearerToken;
        }

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_TIMEOUT, 30);
        curl_setopt($ch, CURLOPT_USERAGENT,
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.93 Safari/537.36');

        $result = curl_exec($ch);

        $header_size = curl_getinfo($ch, CURLINFO_HEADER_SIZE);
        $header      = substr($result, 0, $header_size);
        $body        = substr($result, $header_size);

        curl_close($ch);
        unset($ch);

        return [
            "header" => $header,
            "body"   => $body,
        ];
    }
}

if (!function_exists('msleep')) {

    /********************
     * millisecond sleep
     */
    function msleep(float $secondFloat)
    {
        $secondFloat = (int)($secondFloat * 1000000);
        usleep($secondFloat);
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

if (!function_exists('arrayToObject')) {
    /**
     */
    function arrayToObject(array $array, string $class, string $case = 'pascal')
    {
        $obj = new $class();
        foreach ($array as $key => $val) {
            if (is_numeric($key)) {
                $key = '_numeric_' . $key;
            }

            if ($case == 'snake') {
                $key = Str::of($key)->snake();
            } elseif ($case == 'camel') {
                $key = Str::of($key)->camel();
            }

            $obj->$key = $val;
        }

        return $obj;
    }
}

if (!function_exists('arrayToObjectKeys')) {
    /**
     * @throws ReflectionException
     */
    function arrayToObjectKeys(array $array, string $class)
    {
        $reflectionClass = new ReflectionClass($class);
        $object          = $reflectionClass->newInstanceWithoutConstructor();
        $properties      = $reflectionClass->getProperties();

        foreach ($properties as $property) {
            $property->setAccessible(true);

            if (isset($array[$property->name])) {
                $property->setValue($object, $array[$property->name]);
            }
        }

        return $object;
    }
}

if (!function_exists('printr2json')) {
    /**
     * Convert print_r result to json string.
     * @note Exceptions are always there i tried myself best to get it done. Here $array can be array of arrays or arrays of objects or both
     *
     * @param string $string Result of `print_r($array, true)`
     * @param bool $returnAsObj
     *
     * @return string Json string (transformed version of print_r result)
     */
    function printr2json($string, $returnAsObj = false)
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

        if ($returnAsObj === true) {
            return stringToObject($string);
        }

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
        return config('app.env') == 'local';
    }
}

if (!function_exists('slug')) {
    /**
     * @param string $str
     *
     * @return string
     *
     * Convert string to english slug.
     */
    function slug($str)
    {
        return Str::slug($str);
    }
}

if (!function_exists('startsWith')) {
    /**
     * @param $string
     * @param $startString
     *
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
     *
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

if (!function_exists('getParams')) {

    /********************
     * gp: Get Params from route.
     *
     * @return array
     */
    function getParams()
    {
        $params = request()->route()->parameters();
        return array_values($params);
    }
}

if (!function_exists('randomDate')) {
    /**
     * @param $minDay
     * @param $maxDay
     *
     * @return false|string
     *
     * Parameters can be negative.
     */
    function randomDate($minDay, $maxDay)
    {
        if ((int)$maxDay < (int)$minDay) {
            throw  new \InvalidArgumentException('maxDay has to be greater or equal then minDay');
        }

        $randomDay = mt_rand($minDay, $maxDay);

        if ($randomDay < 0) {
            $randomDay = '-' . abs($randomDay);
        } else {
            $randomDay = '+' . $randomDay;
        }

        return date('Y-m-d', strtotime($randomDay . ' days'));
    }
}

if (!function_exists('randomDateTime')) {
    /**
     * @param $startDate
     * @param $endDate
     *
     * @return false|string
     */
    function randomDateTime($startDate, $endDate)
    {
        if (gettype($startDate) == 'string') {
            $startDate = strtotime($startDate);
        }
        if (gettype($endDate) == 'string') {
            $endDate = strtotime($endDate);
        }

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

if (!function_exists('strToDate')) {
    /**
     * @param string $str
     * @return string
     */
    function strToDate(string $str)
    {
        return date("Y-m-d H:i:s", strtotime($str));
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

if (!function_exists('printrExit')) {
    /**
     * @param $data
     * print and exit
     */
    function printrExit($data, $varDump = false)
    {
        echo "<pre>";
        if ($varDump === true) {
            var_dump($data);
        } else {
            print_r($data);
        }

        exit;
    }
}

if (!function_exists('flushContinue')) {
    function flushContinue($data)
    {
        continueExecution($data);
    }
}

if (!function_exists('continueExecution')) {
    function continueExecution($data)
    {
        // check if fastcgi_finish_request is callable
        if (is_callable('fastcgi_finish_request')) {
            echo $data;
            /*
             * http://stackoverflow.com/a/38918192
             * This works in Nginx but the next approach not
             */
            session_write_close();
            fastcgi_finish_request();

            return;
        }

        ignore_user_abort(true);
        ob_start();

        echo $data;

        $serverProtocol = filter_input(INPUT_SERVER, 'SERVER_PROTOCOL', FILTER_SANITIZE_STRING);
        header($serverProtocol . ' 200 OK');
        // Disable compression (in case content length is compressed).
        header('Content-Encoding: none');
        header('Content-Length: ' . ob_get_length());

        // Close the connection.
        header('Connection: close');

        ob_end_flush();
        ob_flush();
        flush();
    }
}

if (!function_exists('password')) {
    /**
     * @param $password
     *
     * @return string
     *
     * Create hash from password with salt (APP_KEY).
     */
    function password($password)
    {
        return sha1(sha1($password . config('app.key')));
    }
}

if (!function_exists('makeObject')) {
    /**
     * @param $data
     *
     * @return mixed
     *
     * Make object.
     */
    function makeObject($data = stdClass::class)
    {
        if ($data === stdClass::class) {
            $data = new stdClass();
        }

        return json_decode(json_encode($data));
    }
}

if (!function_exists('makeArray')) {
    /**
     * @param mixed $data
     * @param int $depth
     *
     * @return mixed
     *
     * Make array.
     */
    function makeArray($data = [], $depth = 512)
    {
        return json_decode(json_encode($data), true, $depth);
    }
}

if (!function_exists('stringToObject')) {

    /**
     * @param string $str
     * @param boolean $assoc
     *
     * @return mixed
     *
     * String to object (json)
     */
    function stringToObject($str, $assoc = false)
    {
        return json_decode($str, $assoc);
    }
}

if (!function_exists('objectToString')) {

    /**
     * @param mixed $obj
     *
     * @return string
     *
     * Object to string
     */
    function objectToString($obj, $pretty = false)
    {
        if ($pretty) {
            // Set default indentation from 4 to 2 spaces.
            return preg_replace_callback('/^ +/m', function ($m) {
                return str_repeat(' ', strlen($m[0]) / 2);
            }, json_encode($obj, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
        }

        return json_encode($obj, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}

if (!function_exists('lang')) {
    /**
     * @param string $key
     * @param array $params
     * @param string|null $langCode
     *
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
            if (strlen($langCode) == 0) {
                $langCode = config('app.locale', 'en');
            }
        }

        if (!isset(app('languages')[$langCode])) {
            logInfo('Lang not found: ' . $langCode . ' ' . $key);
        }
        if (!isset(app('languages')[$langCode][$key])) {
            logInfo('Key not found: ' . $langCode . ' ' . $key);
        }

        $key = (isset(app('languages')[$langCode][$key])) ? app('languages')[$langCode][$key] : $key;
        if (count($params) > 0) {
            foreach ($params as $k => $v) {
                $key = str_replace('{{' . $k . '}}', $v, $key);
            }
        }

        return $key;
    }
}

if (!function_exists('currentTimeStamp')) {

    /********************
     * cts: current time stamp
     * Normally laravel doesn't set created_at and updated_at when you call static insert() method.
     * But if you set default() parameter as CURRENT_TIMESTAMP then Mysql can set these columns.
     *
     * Usage:
     * Schema::create('password_resets', function (Blueprint $table) {
     *     $table->string('email')->index();
     *     $table->string('token');
     *     //$table->timestamp('created_at')->nullable();
     *     cts($table);
     * });
     *
     */
    function currentTimeStamp(&$table)
    {
        $table->timestamp('created_at')->default(DB::raw('CURRENT_TIMESTAMP'));
        $table->timestamp('updated_at')->default(DB::raw('CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP'));
    }
}

if (!function_exists('randomQuery')) {

    /********************
     * rq: random query
     *
     * @return float|object
     */
    function randomQuery()
    {
        if (config('app.env') == 'local') {
            return microtime(true);
        }

        return date('i');
    }
}

if (!function_exists('exportExit')) {

    /********************
     * ee: export and exit
     */
    function exportExit()
    {
        echo "<pre>";
        $args = func_get_args();

        if (count($args) == 1) {
            print_r($args[0]);
        } else {
            foreach ($args as $arg) {
                print_r($arg);
            }
        }

        exit();
    }
}

if (!function_exists('varDumpExit')) {

    /********************
     * vde: var_dump and exit
     */
    function varDumpExit()
    {
        echo "<pre>";
        $args = func_get_args();

        if (count($args) == 1) {
            var_dump($args[0]);
        } else {
            foreach ($args as $arg) {
                var_dump($arg);
            }
        }

        exit();
    }
}

if (!function_exists('runTimeDetect')) {

    /**
     * Example usage:
     *     [$totalTime, $result] = runTimeDetect(fn() => longExecFunction($param1, $param2));
     *
     * @param Closure $closure
     * @param int $decimal
     *
     * @return array
     */
    function runTimeDetect(Closure $closure, $decimal = 4)
    {
        $startTime = microtime(true);
        $result    = $closure();
        $totalTime = microtime(true) - $startTime;
        return [(float)number_format($totalTime, $decimal), $result];
    }
}

if (!function_exists('getPrivateProperty')) {

    function getPrivateProperty($object, string $property)
    {
        return Closure::bind(
            function () use ($property) {
                return $this->$property;
            },
            $object,
            $object
        )();
    }
}

if (!function_exists('uniqidReal')) {

    /**
     * PHP's uniqid() function is not creating unique id in loop.
     * Detailed explanation:  https://www.php.net/manual/tr/function.uniqid.php#120123
     *
     * @throws Exception
     */
    function uniqidReal($lenght = 13)
    {
        // uniqid gives 13 chars, but you could adjust it to your needs.
        if (function_exists("random_bytes")) {
            $bytes = random_bytes(ceil($lenght / 2));
        } elseif (function_exists("openssl_random_pseudo_bytes")) {
            $bytes = openssl_random_pseudo_bytes(ceil($lenght / 2));
        } else {
            throw new Exception("No cryptographically secure random function available.");
        }
        return substr(bin2hex($bytes), 0, $lenght);
    }
}

if (!function_exists('getProtectedProperty')) {
    function getProtectedProperty($obj, $prop)
    {
        $reflection = new ReflectionClass($obj);
        $property   = $reflection->getProperty($prop);
        $property->setAccessible(true);
        return $property->getValue($obj);
    }
}

if (!function_exists('getClassShortName')) {
    /**
     * @throws ReflectionException
     */
    function getClassShortName(mixed $classOrObject): string
    {
        $reflection = is_string($classOrObject)
            ? new ReflectionClass($classOrObject)
            : new ReflectionObject($classOrObject);

        return $reflection->getShortName();
    }
}

if (!function_exists('logWithFileAndLine')) {
    /**
     * lg: log with file name and line number
     */
    function logWithFileAndLine($anything, $type = 'debug')
    {
        if (!in_array($type, ['debug', 'info', 'warning', 'error'])) {
            throw new InvalidArgumentException('type value not valid.');
        }

        if (!in_array(gettype($anything), [
            'string',
            'number',
        ])) {
            $anything = objectToString($anything, true);
        }
        $bt = debug_backtrace();

        foreach ($bt as $i => $b) {
            if ($i > 10) {
                break;
            }
        }

        $cwd    = base_path();
        $cellar = null;
        $file   = null;
        for ($i = 0; $i < 5; $i++) {
            $caller = array_shift($bt);
            $file   = $caller['file'];
            $file   = substr($file, strlen($cwd));

            if ($file != '/vendor/kodmanyagha/laravel-helpers/src/Helper.php') {
                break;
            }
        }

        $file    = $caller['file'];
        $line    = $caller['line'];
        $file    = substr($file, strlen($cwd));
        $message = $file . ':' . $line . ' ' . $anything;

        // force output to stdout: config(['logging.default' => 'stdout']);
        $logChannel = Log::channel(config('logging.default'));
        $logChannel->log($type, $message);
    }

    /**
     * @param mixed $message
     * Log DEBUG.
     */
    function logDebug($message)
    {
        $args = func_get_args();
        if (count($args) == 1) {
            logWithFileAndLine($args[0], 'debug');
        } else {
            logWithFileAndLine($args, 'debug');
        }
    }

    /**
     * @param mixed $message
     * Log INFO.
     */
    function logInfo($message)
    {
        $args = func_get_args();
        if (count($args) == 1) {
            logWithFileAndLine($args[0], 'info');
        } else {
            logWithFileAndLine($args, 'info');
        }
    }

    /**
     * @param mixed $message
     * Log WARNING.
     */
    function logWarning($message)
    {
        $args = func_get_args();
        if (count($args) == 1) {
            logWithFileAndLine($args[0], 'warning');
        } else {
            logWithFileAndLine($args, 'warning');
        }
    }

    /**
     * @param mixed $message
     * Log ERROR.
     */
    function logError($message)
    {
        $args = func_get_args();
        if (count($args) == 1) {
            logWithFileAndLine($args[0], 'error');
        } else {
            logWithFileAndLine($args, 'error');
        }
    }
}
