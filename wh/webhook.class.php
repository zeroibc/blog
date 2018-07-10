<?php
/**
 * fanhaobai.com 网站 webhooks 类
 * @author fanhaobai & i@fanhaobai.com
 * @date 2017/1/20
 */

class Webhook
{
    public $config;
    public $start;
    public $end;
    public $post;

    /**
     * 构造方法
     */
    public function __construct($config)
    {
        //注册异常处理
        set_exception_handler([$this, 'exceptionHandler']);

        if (!isset($config['token_field'])) {
            throw new Exception('token field not be set');
        }

        if (!isset($config['access_token'])) {
            throw new Exception('access token not be set');
        }

        if (!file_exists($config['bash_path'])) {
            throw new Exception('access token not be set');
        }
        //默认配置
        $config['log_path'] = isset($config['log_path']) ? : __DIR__ . '/';
        $config['log_name'] = isset($config['log_name']) ? : 'update_git.log';
        $config['branch'] = isset($config['branch']) ? : 'master';
        $this->config = $config;

        $this->start = $this->microtime();
        $this->accessLog('-');
        $this->accessLog('start');

        $this->post = json_decode($_POST['payload'] ?: '[]', true);
    }

    /**
     * 入口
     */
    public function run()
    {
        //校验token
        if ($this->checkToken()) {
            echo 'ok';
        } else {
            echo 'error';
        }
        //提前返回响应
        fastcgi_finish_request();

        if ($this->checkBranch()) {
            $this->exec();
        }
    }

    /**
     * 校验access_token
     */
    public function checkToken()
    {
        $field = 'HTTP_' . str_replace('-', '_', strtoupper($this->config['token_field']));
        //获取token
        if (!isset($_SERVER[$field])) {
            throw new Exception('access token not be in header');
        }

        $payload = file_get_contents('php://input');
        list($algo, $hash) = explode('=', $_SERVER[$field], 2);
        //计算签名
        $payloadHash = hash_hmac($algo, $payload, $this->config['access_token']);
        //token错误
        if (strcmp($hash, $payloadHash) != 0) {
            throw new Exception('access token check failed');
        }

        $this->accessLog('access token is ok');

        return true;
    }

    /**
     * 校验分支
     */
    public function checkBranch()
    {
        $current = substr(strrchr($this->post['ref'], "/"), 1);
        $this->accessLog("branch is $current");

        return $current == $this->config['branch'];
    }

    /**
     * 执行shell
     */
    public function exec()
    {
        $path = $this->config['bash_path'];

        $result = shell_exec("sh $path 2>&1");
        $this->accessLog($result);

        return $result;
    }

    /**
     * 异常处理
     * @param $exception
     */
    public function exceptionHandler($exception)
    {
        $msg = $exception->getMessage();
        $this->errorLog($msg);

        exit($msg);
    }

    /**
     * 运行日志,记录运行步骤
     */
    private function accessLog($accessMessage)
    {
        //添加数据
        $accessMessage = date(DATE_RFC822) . " -access- " . $accessMessage . "\r\n";
        //调用写入函数
        $this->addToFile($this->config['log_path'] . $this->config['log_name'], $accessMessage);
    }

    /**
     * 错误日志
     */
    private function errorLog($errorMessage)
    {
        //添加必要数据
        $errorMessage = date(DATE_RFC822) . " -error- " . $errorMessage . "\r\n";
        //调用写入函数
        $this->addToFile($this->config['log_path'] . $this->config['log_name'], $errorMessage);
    }

    /**
     * 写入文件函数
     */
    private function addToFile($filePath, $logMessage, $replace = false)
    {
        //判断存储文件策略
        $this->fileConf($filePath);
        //更新文件内容
        if ($replace) {
            $result = file_put_contents($filePath, $logMessage);
        } else {
            $result = file_put_contents($filePath, $logMessage, FILE_APPEND);
        }
        return $result;
    }

    /**
     * 析构函数
     */
    public function __destruct()
    {
        $this->end = $this->microtime();
        $time = $this->end - $this->start;
        $this->accessLog('used time:' . $time);
        $memory = $this->memory();
        $this->accessLog('used memory:' . $memory);
        $this->accessLog('end');
    }

    /**
     * 时间计算
     */
    private function microtime()
    {
        list($usec, $sec) = explode(" ", microtime(), 2);
        return ((float)$usec + (float)$sec);
    }

    /**
     * 内存计算
     */
    private function memory()
    {
        $memory = (!function_exists('memory_get_usage')) ? '0' : round(memory_get_usage() / 1024 / 1024, 5) . 'MB';
        return $memory;
    }

    /**
     * 存储文件策略
     */
    private function fileConf($filePath)
    {
        $path = pathinfo($filePath);
        //路径中目录是否存在
        if (!file_exists($path['dirname'])) {
            mkdir($path['dirname'], 0777, true);
        }
        //文件是否存在
        if (!file_exists($filePath)) {
            touch($filePath);
        }
    }
}
