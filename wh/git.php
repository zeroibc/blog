<?php
include './webhook.class.php';

set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');

$config = array(
    'token_field' => 'X-Hub-Signature',
    'access_token' => 'sha1=17c5e4d35ddeea7ef065aa0b501fe445878e003a',
    'bash_path' => './sh/hexo-pull.sh',
);

$webhook = new Webhook($config);
$webhook->run();
