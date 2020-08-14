<?php
// TODO 現状正しく動作しない？

error_reporting(E_ALL);

{ // == エラーログ出力の
  set_error_handler(function (int $severity, string $message, string $file, int $line) {
    var_dump([$message, 0, $severity, $file, $line]);
  });
  register_shutdown_function(function () {
    $error = error_get_last();
    if (!is_array($error) || !($error['type'] & (E_ERROR | E_PARSE | E_USER_ERROR | E_RECOVERABLE_ERROR))) {
      return; # 正常終了系
    }

    # 異常終了系
    # Error Logging
    echo("Uncaught Fatal Error: {$error['type']}:{$error['message']} in {$error['file']}:{$error['line']}");
  });
} // ==

require_once(__DIR__."/../vendor/autoload.php");

if ((string)getenv("FC2_CONFIG_FROM_ENV") === "1") {
  require(__DIR__ . '/../config_read_from_env.php');
} else {
  require(__DIR__ . '/../config.php');
}

\Fc2blog\Config::read('cron.php');    // cron用の環境設定読み込み

\Fc2blog\Request::getInstance()->setCronParams($argv);

list($classFile, $className, $methodName) = getRouting(\Fc2blog\Config::get('DEFAULT_CLASS_NAME'), \Fc2blog\Config::get('DEFAULT_METHOD_NAME'), \Fc2blog\Config::get('APP_PREFIX'));
require($classFile);
$controller = new $className($methodName);

