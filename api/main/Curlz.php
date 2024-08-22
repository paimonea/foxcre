<?php

/**
 * @class Curlz
 * @version 1.0
 * @author Raizel_&_Unique By Thryssa
 */
class Curlz
{
    /** Static Properties */
    public static $req, $response;
    public static string $cookies_dir, $cookie_path;
    private static object $headersCallback;

    public function __construct()
    {
        self::$req = curl_init();
    }

    /**
     * @method setOptions
     * @access public
     * @param string $url
     * @param array|null $headers
     * @param bool $proxified
     * @return void
     * @example Set Curl Options
     */
    private static function setOptions(string $url, array $headers = NULL, bool $proxified): void
    {
        curl_setopt_array(
            self::$req,
            [
                CURLOPT_URL => $url,
                CURLINFO_HEADER_OUT => TRUE,
                CURLOPT_RETURNTRANSFER => TRUE,
                CURLOPT_FOLLOWLOCATION => TRUE,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => 2,
                CURLOPT_AUTOREFERER => TRUE,
                CURLOPT_FRESH_CONNECT => TRUE,
                CURLOPT_TIMEOUT => 120,
                CURLOPT_CONNECTTIMEOUT => 120,
                CURLOPT_USERAGENT => Init::randomUserAgents(),
            ]
        );
        if ($headers && array($headers)) curl_setopt(self::$req, CURLOPT_HTTPHEADER, $headers);
        self::setResponseHeaders();
        self::createCookie();
        if ($proxified && Init::$proxy) self::setProxy(Init::$proxy);
    }

    /**
     * @method setProxy
     * @access private
     * @param string $proxy
     * @return void
     * @example Set Proxy Options
     */
    private static function setProxy(string $proxy): void
    {
        [$host, $port, $username, $password] = explode(':', $proxy);
        if ($host && $port) {
            curl_setopt(self::$req, CURLOPT_PROXY, $host);
            curl_setopt(self::$req, CURLOPT_PROXYPORT, $port);

            if ($username && $password) {
                curl_setopt(self::$req, CURLOPT_PROXYUSERPWD, "$username:$password");
            }
        }
    }

    /**
     * @method createCookie
     * @access private
     * @return void
     * @example Create Cookie File
     */
    public static function createCookie()
    {
        if (!isset(self::$cookie_path)) {
            self::$cookies_dir = __DIR__ . '/cookies';
            if (!is_dir(self::$cookies_dir)) mkdir(self::$cookies_dir, 0755, TRUE);
            self::$cookie_path = self::$cookies_dir . DIRECTORY_SEPARATOR . uniqid('cookie_') . ".txt";
        }
        curl_setopt(self::$req, CURLOPT_COOKIEJAR, self::$cookie_path);
        curl_setopt(self::$req, CURLOPT_COOKIEFILE, self::$cookie_path);
    }

    /**
     * @method deleteCookie
     * @access private
     * @return void
     * @example Delete Cookie Directory And Files
     */
    public static function deleteCookie()
    {
        $dir = self::$cookies_dir;
        if (is_dir($dir)) {
            $objects = scandir($dir);
            if ($objects === FALSE) {
                return;
            }
            foreach ($objects as $object) {
                if ($object != "." && $object != "..") {
                    if (filetype($dir . "/" . $object) == "dir")
                        rmdir($dir . "/" . $object);
                    else unlink($dir . "/" . $object);
                }
            }
            reset($objects);
            rmdir($dir);
        }
    }

    /**
     * @method GET
     * @access public
     * @param string $url
     * @param array|null $headers
     * @param bool $proxified @default TRUE
     * @return string
     * @example GET Request
     */
    public static function GET(string $url, array $headers = NULL, bool $proxified = TRUE)
    {
        self::setOptions($url, $headers, $proxified);
        self::$response = curl_exec(self::$req);
        curl_close(self::$req);
        return self::$response;
    }

    /**
     * @method POST
     * @access public
     * @param string $url
     * @param string|null $data
     * @param array|null $headers
     * @param bool $proxified @default TRUE
     * @return string
     * @example POST Request
     */
    public static function POST(string $url, string $data = NULL, array $headers = NULL, bool $proxified = TRUE)
    {
        self::setOptions($url, $headers, $proxified);
        curl_setopt(self::$req, CURLOPT_POST, TRUE);
        curl_setopt(self::$req, CURLOPT_POSTFIELDS, $data);
        self::$response = curl_exec(self::$req);
        curl_close(self::$req);
        return self::$response;
    }

    /**
     * @method setResponse
     * @access public
     * @param string $status
     * @param string|null $response
     * @return void
     * @example echo Response
     */
    public static function setResponse(string $status, string|null $response, bool $is3dResult = TRUE): void
    {
        /** Get Bin Details */
        $binDetails = Init::binDetails();
        if (isset($binDetails)) {
            $binDetails = $binDetails['brand'] . ' - ' . $binDetails['type'] . ' - ' . $binDetails['card_sub_type'] . ' - ' . $binDetails['bank'] . ' - ' . $binDetails['country'];
        }

        /** Dump Approved And Approved-CCN */
        if ($status == 'Approved' || $status == 'Approved-CCN') {
            $folderPath = 'dumps/';
            if (!is_dir($folderPath))
                mkdir($folderPath, 0777, TRUE);

            file_put_contents($folderPath . '/' . Init::$gateName . '.txt', '' . $status . ' - ' . Init::$card . ' Response - ' . $response . ' - ' . $binDetails . '' . PHP_EOL, FILE_APPEND);
        }

        /** Generate Response Based On The Status */
        $responseArray = [
            "card" => Init::$card,
            "status" => $status,
            "response" => empty($response) ? 'No Response Found.' : $response,
            "binData" => $binDetails ?? NULL,
            "gateway" => Init::$gateName ?? NULL
        ];

        if ($is3dResult && ($status == 'Approved' || $status == 'Approved-CCN')) {
            $responseArray["threeDResult"] = Init::get3DResult() ?? NULL;
        }

        /** Echo Response Based On The Status */
        echo json_encode((object) $responseArray);
    }

    /**
     * @method setResponseHeaders
     * @access private
     * @return void
     * @example Set Response Headers
     */
    private static function setResponseHeaders(): void
    {
        /** Store Temporary HeaderCallback Data */
        $stdClass = new \stdClass();
        $stdClass->rawResponseHeaders = "";
        self::$headersCallback = $stdClass;

        /** Set HeaderCallback */
        curl_setopt(
            self::$req,
            CURLOPT_HEADERFUNCTION,
            function ($curl, $header) {
                self::$headersCallback->rawResponseHeaders .= $header;
                return strlen($header);
            }
        );
    }

    /**
     * @method getResponseHeaders
     * @access public
     * @return array
     * @example Get Response Headers
     */
    public static function getResponseHeaders(): array
    {
        return self::parseHeaders(self::$headersCallback->rawResponseHeaders);
    }

    /**
     * @method getRequestHeaders
     * @access public
     * @return array
     * @example Get Request Headers
     */
    public static function getRequestHeaders(): array
    {
        return self::parseHeaders(curl_getinfo(self::$req, CURLINFO_HEADER_OUT));
    }

    /**
     * @method parseHeaders
     * @access private
     * @param string $rawHeaders
     * @return array
     * @example Parse Headers
     */
    public static function parseHeaders(string $rawHeaders): array
    {
        /** Initialize Headers Array */
        $headers = [];

        /** Split Headers Into NewLine */
        $header_lines = explode("\n", trim($rawHeaders));

        /** Parse Each Header Line And Populate Array */
        foreach ($header_lines as $line) {
            if (!empty($line) && strpos($line, ':') !== FALSE) {
                [$key, $value] = explode(':', $line, 2);
                $headers[$key] = trim($value);
            } else if (preg_match('#HTTP/[0-9\.]+\s+([0-9]+)#', $line, $out)) {
                $headers['http_code'] = intval($out[1]);
            }
        }

        /** Return Headers Array */
        return $headers;
    }

    /**
     * @method resetSession
     * @access public
     * @return void
     * @example Reset Curl Session
     */
    public static function resetSession(): void
    {
        if (isset(self::$req)) {
            curl_close(self::$req);
        }
        self::$req = curl_init();
    }

    /**
     * @method debug
     * @access public
     * @return void
     * @example Debug Last Request
     */
    public static function debug(): void
    {
        echo "===================== DEBUG =====================<br>";
        /** Debug Site Response */
        echo "<h2>[ Response ] :</h2>";
        echo "<code>" . nl2br(htmlentities(self::$response)) . "</code>";

        /** Debug Curl_Info */
        $curl_getinfo = curl_getinfo(self::$req);
        unset($curl_getinfo['request_header']);
        unset($curl_getinfo['certinfo']);
        echo sprintf("<h2>[ Curl Info ] :</h2> <pre>%s</pre>", print_r($curl_getinfo, TRUE));

        /** Debug Response And Request Headers */
        echo "<h2>[ Headers ] :</h2>";
        echo "<b>Request Headers</b>";
        echo "<pre>";
        print_r(self::getRequestHeaders());
        echo "</pre>";
        echo "<b>Response Headers</b>";
        echo "<pre>";
        print_r(self::getResponseHeaders());
        echo "</pre>";

        /** Debug Curl_Error */
        echo "<h2>[ Errors ] :</h2>";
        echo "<code>" . nl2br(htmlentities(curl_error(self::$req))) . "</code>";
        echo "<br>===================== DEBUG =====================<br>";
    }
}
