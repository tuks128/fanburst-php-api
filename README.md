# ***Fanburst - PHP API*** #

Complex PHP5 CURL library. See also official documentation: https://developers.fanburst.com/

## Install (Composer):

```
composer require tuks128/fanburst-php-api
```

## Initialization: ##
```
<?php

use WaProduction\Fanburst\FanburstApi;

$fanburstApi = new FanburstApi('CLIENT_ID', 'CLIENT_SECRET', 'AUTH_CALLBACK_URI');
```

## Auth: ##

auth.php
```
<?php

$options = [
  'state' => json_encode([
      'custom_param' => 'custom_param_value',
   ]),
];
  
header('Location: '.$fanburstApi->getOauthLoginUrl($options)); // get URL for login
```

authCallback.php
```
<?php

$accessToken = $fanburstApi->exachangeCodeForAccessToken($_GET['code']);
$fanburstApi->setAccessToken($accessToken);
```

## Other methods: ##
```
<?php

$fanburstApi->getAccessToken();
$fanburstApi->followUser('CHANNEL_ID')
$fanburstApi->searchUser('CHANNEL_NAME');
$fanburstApi->multipleCallTargets('CHANNEL_ID', function($target) { });
```
