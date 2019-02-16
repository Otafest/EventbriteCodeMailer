<?php

/******************** EVENTBRITE SETTINGS ********************/

//Get this from https://www.eventbrite.ca/platform/api-keys
DEFINE("EB_API_KEY", "REPLACETHISTEXTWITHYOURKEY");

//Get this from your Eventbrite URL
//e.g. if your URL is https://www.eventbrite.ca/e/otafest-2019-tickets-45864012496
//Then your event id is 45864012496
DEFINE("EVENT_ID", "REPLACETHISTEXTWITHYOUREVENTID");

//If you want the discount codes generated here to have a
//certain prefix, add it below.
DEFINE("CODE_PREFIX", "");

/******************** EMAIL SETTINGS ********************/
//Email server (e.g. smtp-relay.gmail.com)
DEFINE("MAIL_HOST", "");

//Email username
DEFINE("MAIL_USER", "");

//Email password
DEFINE("MAIL_PASS", "");

//Email port (probably one of 25, 587, 465, or 993)
DEFINE("MAIL_PORT", 587);

//Email encryption. TRUE if using TLS, FALSE if not.
DEFINE("MAIL_TLS", TRUE);

//The friendly "from" name in the e-mail (e.g. Otafest)
DEFINE("MAIL_SENDER_NAME", "");

//Default subject line to appear in the webform
DEFINE("MAIL_DEFAULT_SUBJECT", "[Action Required] Your ticket code");

//The HTML email body. Type %%code%% wherever you want the discount code to appear.
DEFINE("MAIL_DEFAULT_BODY", "Thanks for purchasing your ticket to Otafest!<br /><br /><strong>You're not done yet</strong>, please use the following code to claim your ticket: <a href='https://otafest.eventbrite.ca?discount=%%code%%'>%%code%%</a>");

//Email functionality thanks to https://github.com/snipworks/php-smtp

?>