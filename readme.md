## _Important notes to keep in mind before using._

### Gates Selector :-

- `appConfig.js` located inside `root directory`. this file contains information regarding your checker.

- Following structure should be used inside `appConfig.js` :-

```js
export const config = {
  gates_data: [
    {
      name: 'Gate Name', // Can be any name
      api_path: 'api/__api_file_name__.php',
    },
    {
      name: 'Gate Name', // Can be any name
      api_path: 'api/__api_file_name__.php',
    },
    {
      name: 'Gate Name', // Can be any name
      api_path: 'api/__api_file_name__.php',
    },
    {
      name: 'Gate Name', // Can be any name
      api_path: 'api/__api_file_name__.php',
    },
  ],
};

/**
 *
 * `api_path` should be correct including full folder path with file extension (.php)
 *
 * and by putting multiple object inside array we can make multiple gates.
 */
```

### Returning Proper Response From You Api :-

- Following structure should be used inside php api.
- `status` key should only be `Approved`, `Approved-CCN`, `Declined`.

```php
/**
 * To handle card response from api.
 *
 * We need to use `Curlz` function `setResponse()` And pass the following params
 * @param string `Approved` or `Approved-CCN` or `Declined`
 * @param string message you want to return.
 * 
 * To return response from api and see in the browser.
 * Example is given below and also in `sample.php` file.
*/

if(result == 'Approved'){
  $curl->setResponse('Approved', 'CVV Matched');
} elseif (result == 'Your Security code is incorrect.'){
  $curl->setResponse('Approved-CCN', 'CCN Matched');
} else {
  $curl->setResponse('Declined', 'CVV or CCN Not Matched');
}
```

### How to use Proxy :-

- Proxy should be in following format `host:port:username:password` (username and password are optional unless authentication is required).
- First, you need to add proxy inside `settings -> proxy-Textarea` in website. from there it can be handled automatically by `Curlz.php` function `setProxy()` and set inside your curl request automatically.

```php
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
```

- If you wanna disable proxy you can pass argument to your request like below.

```php
# GET REQUEST
$curl->get($url, $headers, FALSE);

# OR

# POST REQUEST
$curl->post($url, $data, $headers, FALSE);
```
