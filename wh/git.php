<?php
include './webhook.class.php';

set_time_limit(0);
date_default_timezone_set('Asia/Shanghai');

echo 123;

$config = array(
    'token_field' => 'X-Hub-Signature',
    'access_token' => '850c4c27dcb252b81e4b783f83a8a19be44db6ee',
    'bash_path' => './sh/hexo-pull.sh',
);

$webhook = new Webhook($config);
$webhook->run();
