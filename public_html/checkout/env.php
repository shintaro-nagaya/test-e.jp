<?php
// stg checkout URI - ステージングのチェックアウトPGのURL　基本的にこれだけ変更する必要がある
// ステージングでBasic認証がかかっている場合URLに認証情報を含めるのを忘れずに
define('STG_CHECKOUT_ENDPOINT', "https://stg.example.com/checkout/stg.php");

// プロジェクトディレクトリ
define('PROJECT_ROOT', realpath(__DIR__. "/../../"));
// リポジトリのパス
define('REPOSITORY', PROJECT_ROOT. "/.git");
// ビルドシェル
define('BUILD_SH', PROJECT_ROOT. "/build.sh");

// prodでのチェックアウトブランチ
define('PROD_BRUNCH', "master");
define('PROD_REF', "refs/heads/". PROD_BRUNCH);

// stgでのチェックアウトブランチ
define('STG_BRUNCH', "stg");
define('STG_REF', "refs/heads/". STG_BRUNCH);

// GIT Command
define('GIT_COMM', "/usr/bin/git");
// PHP Command
define('PHP_COMM', "/usr/bin/php8.1");
// SH Command
define('SH_COMM', "/usr/bin/sh");
// Asset build dir
define('BUILD_DIR', realpath(__DIR__. "/../build"));
// LOG Dir
const LOG_DIR = __DIR__ . "/log/";

// LogDir作成
if(!file_exists(LOG_DIR)) {
    mkdir(LOG_DIR);
    file_put_contents(LOG_DIR.".htaccess", "deny from all");
}
// 1ヶ月前のログは消す
$logKill = strtotime("-30 days");
foreach(glob(LOG_DIR. "*.log") as $logFile) {
    if(filemtime($logFile) < $logKill) {
        unlink($logFile);
    }
}
// ログ書き込み
function logging(string $message):void {
    $file = LOG_DIR. date('Ymd'). ".log";
    file_put_contents(
        $file,
        date('H:i:s'). " : ". $message. "\r\n",
        FILE_APPEND
    );
}
// チェックアウト実行
function checkout(string $brunch): void
{
    // fetch
    exec(GIT_COMM. " --git-dir=". REPOSITORY. " fetch");
    // checkout
    exec(GIT_COMM. " --git-dir=". REPOSITORY. " --work-tree=". PROJECT_ROOT. " checkout ". $brunch. " -f");
    // com hash
    $commit_hash = shell_exec(GIT_COMM. " --git-dir=". REPOSITORY. " rev-parse --verify HEAD");
    logging($brunch. " checkout. hash: ". $commit_hash);

    // symfony cache clear
    exec(PHP_COMM. " ". PROJECT_ROOT. "/bin/console cache:clear");

    $build_res = shell_exec(SH_COMM. " ". BUILD_SH);
    logging($build_res);
}