<?php
include './webhook.class.php';

set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');

echo 123;

$config = array(
    'token_field' => 'X-Hub-Signature',
    'access_token' => '1501671226@qq.com',
    'bash_path' => './sh/hexo-pull.sh',
);

$webhook = new Webhook($config);
$webhook->run();
