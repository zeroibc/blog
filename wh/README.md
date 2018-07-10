Webhook工具
===========

git.php 文件较简洁，只对外提供 web 请求服务，git 事件触发的 webhook 将请求到这个脚本，脚本接受到请求后会立即响应，处理任务将在请求结束后继续。

## 配置

日志默认路径为`update_git.log`，工具默认只监听 master 分支变更，因此建议发布前合并修改到 master 分支。
 
```PHP
include './webhook.class.php';
set_time_limit(0);
$config = array(
   'token_field'   =>  'X-Hub-Signature',
   'access_token'  =>  'xxxxxxxxx',   //token
   'bash_path'     =>  '/data/html/git-update/sh/hexo-pull.sh',  //shell脚本路径
   'branch'        =>  'master'
);
```

## 使用

```PHP
$webhook = new Webhook($config);
$webhook->run();
```

一切就绪后配置 web 服务，webhook 地址为 [localhost/git.php]()，请求内容示例为：

Headers 信息：
```PHP
Request URL: localhost/git.php
Request method: POST
content-type: application/x-www-form-urlencoded
Expect: 
User-Agent: GitHub-Hookshot/a322ef4
X-GitHub-Delivery: 16a9f30a-dbdc-11e7-9b49-2790340a0f6c
X-GitHub-Event: push
X-Hub-Signature: sha1=xxxxxxxxxxxxxxxxxx
```

POST 参数 Payload：
```PHP
{
  "ref": "refs/heads/feature",
  "before": "0e1e7127406dda7b46f411ea298092e35cf2d62a",
  "after": "fd79bed8e3b70fda06ae2393755f47f878b3c766",
  "created": false,
  "deleted": false,
  "forced": false,
  "base_ref": "refs/heads/master",
  "compare": "https://github.com/fan-haobai/blog/compare/0e1e7127406d...fd79bed8e3b7",
  //...
}
```

## 代码规范检测

PHP_CodeSniffer 是一款自动化的 PHP 代码规范检查工具，详细见 [自动化代码规范检测 — PHP_CodeSniffer](https://www.fanhaobai.com/2018/04/php-code-sniffer.html)。

hook 脚本为：
* [Bash — Linux&Max 系统](https://github.com/fan-haobai/webhook/blob/master/sh/pre-commit-phpcs/pre-commit)
* [Python — Win 系统](https://github.com/fan-haobai/webhook/blob/master/sh/pre-commit-phpcs/pre-commit.py)
