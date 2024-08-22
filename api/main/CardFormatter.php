<?php

/**
 * @class CardFormatter
 * @version 1.0
 * @author Raizel_&_Unique By Thryssa
 */
class CardFormatter
{
    /** Static Property */
    public static ?string $card = NULL;

    public function __construct(string|null $data)
    {
        if (empty($data)) {
            echo "Empty Card Data Provided.";
            return;
        } elseif (self::validateCardFormat($data)) {
            self::$card = $data;
            return;
        } else {
            $data = preg_replace("/(\d{4})\s*(\d{4})\s*(\d{4})\s*(\d{4})/", "$1$2$3$4", $data);
            $data = preg_replace("/(\d{4})\s*(\d{6})\s*(\d{5})/", "$1$2$3", $data);
            $data = preg_replace('/\n+|\s+|\D+|~|\.|`|,|!|@|#|£|€|\$|¢|¥|\/|§|%|°|\^|&|\*|\(|\)|-|\+|=|\{|}|\[|]|\||\\|:|;|\"|\'|<|>|\?/m', "|", $data);
            $data = preg_replace("/\|+/m", "|", $data);
            $data = preg_replace("/\|1\|/m", "|01|", $data);
            $data = preg_replace("/\|2\|/m", "|02|", $data);
            $data = preg_replace("/\|3\|/m", "|03|", $data);
            $data = preg_replace("/\|4\|/m", "|04|", $data);
            $data = preg_replace("/\|5\|/m", "|05|", $data);
            $data = preg_replace("/\|6\|/m", "|06|", $data);
            $data = preg_replace("/\|7\|/m", "|07|", $data);
            $data = preg_replace("/\|8\|/m", "|08|", $data);
            $data = preg_replace("/\|9\|/m", "|09|", $data);

            if (preg_match_all('/(\d{15,16})\|(\d{4})\|(\d{2})\|(\d{4})\|(\d{2})\|(\d{3,4})/', $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = $key[1];
                    $year = $key[2];
                    $month = $key[3];
                    $cvv = $key[6];
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(01|02|03|04|05|06|07|08|09|10|11|12)\|(22|2022|23|2023|24|2024|25|2025|26|2026|27|2027|28|2028|29|2029|30|2030|31|2031|32|2032|33|2033|34|2034|35|2035)\|[0-9]{3,4}/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    self::$card = "$key[0]";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(22|23|24|25|26|27|28|29|30|31|32|33|34|35)\|(01|02|03|04|05|06|07|08|09|10|11|12)\|[0-9]{3,4}/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    [$cc, $yy, $mm, $cvv] = explode('|', $key[0]);
                    self::$card = "$cc|$mm|$yy|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(22|23|24|25|26|27|28|29|30|31|32|33|34|35)\|(01|02|03|04|05|06|07|08|09|10|11|12)\|[0-9]{3,4}/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 20, -4);
                    $year = substr($key[0], 17, -7);
                    $cvv = substr($key[0], 23, 25);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(2022|2023|2024|2025|2026|2027|2028|2029|2030|2031|2032|2033|2034|2035)\|(01|02|03|04|05|06|07|08|09|10|11|12)\|[0-9]{3,4}/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 22, -4);
                    $year = substr($key[0], 17, -7);
                    $cvv = substr($key[0], 25, 27);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|[0-9]{3,4}\|(01|02|03|04|05|06|07|08|09|10|11|12)(22|23|24|25|26|27|28|29|30|31|32|33|34|35)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 21, -2);
                    $year = substr($key[0], 23, 25);
                    $cvv = substr($key[0], 17, -5);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|[0-9]{3,4}\|(01|02|03|04|05|06|07|08|09|10|11|12)(2022|2023|2024|2025|2026|2027|2028|2029|2030|2031|2032|2033|2034|2035)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 21, -4);
                    $year = substr($key[0], 23, 27);
                    $cvv = substr($key[0], 17, -7);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(01|02|03|04|05|06|07|08|09|10|11|12)(22|23|24|25|26|27|28|29|30|31|32|33|34|35)\|[0-9]{3,4}/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 17, -6);
                    $year = substr($key[0], 19, -4);
                    $cvv = substr($key[0], 22, 25);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(01|02|03|04|05|06|07|08|09|10|11|12)(2022|2023|2024|2025|2026|2027|2028|2029|2030|2031|2032|2033|2034|2035)\|[0-9]{3,4}/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 17, -8);
                    $year = substr($key[0], 19, -4);
                    $cvv = substr($key[0], 24, 27);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|[0-9]{3,4}\|(01|02|03|04|05|06|07|08|09|10|11|12)\|(22|23|24|25|26|27|28|29|30|31|32|33|34|35)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 21, -3);
                    $year = substr($key[0], 24, 26);
                    $cvv = substr($key[0], 17, -6);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|[0-9]{3,4}\|(2022|2023|2024|2025|2026|2027|2028|2029|2030|2031|2032|2033|2034|2035)\|(01|02|03|04|05|06|07|08|09|10|11|12)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], -2);
                    $year = substr($key[0], 21, -3);
                    $cvv = substr($key[0], 17, -8);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|[0-9]{3,4}\|(22|23|24|25|26|27|28|29|30|31|32|33|34|35)\|(01|02|03|04|05|06|07|08|09|10|11|12)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], -2);
                    $year = substr($key[0], 21, -3);
                    $cvv = substr($key[0], 17, -6);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(22|23|24|25|26|27|28|29|30|31|32|33|34|35)(01|02|03|04|05|06|07|08|09|10|11|12)\|[0-9]{3,4}/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 19, -4);
                    $year = substr($key[0], 17, -6);
                    $cvv = substr($key[0], -3);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(2022|2023|2024|2025|2026|2027|2028|2029|2030|2031|2032|2033|2034|2035)(01|02|03|04|05|06|07|08|09|10|11|12)\|[0-9]{3,4}/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 21, -4);
                    $year = substr($key[0], 17, -6);
                    $cvv = substr($key[0], -3);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(22|23|24|25|26|27|28|29|30|31|32|33|34|35)\|[0-9]{3,4}\|(01|02|03|04|05|06|07|08|09|10|11|12)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], -2);
                    $year = substr($key[0], 17, -7);
                    $cvv = substr($key[0], 20, -3);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(2022|2023|2024|2025|2026|2027|2028|2029|2030|2031|2032|2033|2034|2035)\|[0-9]{3,4}\|(01|02|03|04|05|06|07|08|09|10|11|12)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], -2);
                    $year = substr($key[0], 17, -7);
                    $cvv = substr($key[0], 22, -3);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(01|02|03|04|05|06|07|08|09|10|11|12)\|[0-9]{3,4}\|(22|23|24|25|26|27|28|29|30|31|32|33|34|35)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 17, -7);
                    $year = substr($key[0], -2);
                    $cvv = substr($key[0], 20, -3);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }

            if (preg_match_all("/[0-9]{15,16}\|(01|02|03|04|05|06|07|08|09|10|11|12)\|[0-9]{3,4}\|(2022|2023|2024|2025|2026|2027|2028|2029|2030|2031|2032|2033|2034|2035)/", $data, $matches, PREG_SET_ORDER, 0)) {
                foreach ($matches as $key) {
                    $cc = substr($key[0], 0, 16);
                    $month = substr($key[0], 17, -9);
                    $year = substr($key[0], -4);
                    $cvv = substr($key[0], 20, -5);
                    self::$card = "$cc|$month|$year|$cvv";
                }
                return;
            }
            self::$card = NULL;
        }
    }

    /**
     * @method validateCardFormat
     * @access public
     * @param string|null $card
     * @return bool
     * @example Validate Card Format
     */
    public static function validateCardFormat(string|null $card): bool
    {
        if (str_starts_with($card, "3") && preg_match('/^[0-9]{15}\|(01|02|03|04|05|06|07|08|09|10|11|12)\|(22|2022|23|2023|24|2024|25|2025|26|2026|27|2027|28|2028|29|2029|30|2030|31|2031|32|2032|33|2033|34|2034|35|2035)\|[0-9]{4}$/', $card)) {
            return TRUE;
        } elseif (preg_match('/^[0-9]{16}\|(01|02|03|04|05|06|07|08|09|10|11|12)\|(22|2022|23|2023|24|2024|25|2025|26|2026|27|2027|28|2028|29|2029|30|2030|31|2031|32|2032|33|2033|34|2034|35|2035)\|[0-9]{3}$/', $card)) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    /**
     * @method getInitializedValue
     * @access public
     * @return string|null
     * @example Returns Card Value
     */
    public static function getInitializedValue(): string|null
    {
        return self::$card;
    }
}


