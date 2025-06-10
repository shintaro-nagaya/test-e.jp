<?php

namespace App\Command\Init;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\Filesystem\Filesystem;

#[AsCommand(
    name: 'app:init:env',
    description: '.env.localファイルの作成',
)]
class EnvCommand extends Command
{
    public function __construct(
        private ParameterBagInterface $parameterBag
    )
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $fs = new Filesystem();

        $dest = $this->parameterBag->get('kernel.project_dir'). "/.env.local";
        if($fs->exists($dest)) {
            return Command::SUCCESS;
        }
        $qHelper = $this->getHelper("question");
        $line = ["APP_ENV=dev", ""];

        // DB
        $io->title("DataBase接続設定");
        $io->note('mysql://{user}:{password}@{host}:{port}?serverVersion={version}');

        // User
        $q = new Question("User? [root]: ", "root");
        $q->setAutocompleterValues(["root", "user", "db_user"]);
        $user = $qHelper->ask($input, $output, $q);
        // Password
        $q = new Question("Password? [password]: ", "password");
        $q->setAutocompleterValues(["password", "root", "Vagrant:2019mySql-"]);
        $password = $qHelper->ask($input, $output, $q);
        // Host
        $q = new Question("Host? [localhost]: ", "localhost");
        $q->setAutocompleterValues(["localhost", "127.0.0.1"]);
        $host = $qHelper->ask($input, $output, $q);
        // Port
        $q = new Question("Port? [3306]: ", "3306");
        $q->setAutocompleterValues(["3306"]);
        $port = $qHelper->ask($input, $output, $q);
        // DatabaseName
        $q = new Question("Database name? :", false);
        $dbname = $qHelper->ask($input, $output, $q);
        if(!$dbname) {
            $io->caution("Database名が未入力の場合は、手動設定後に以下コマンドを実行か、phpMyAdminなどで作成 \n php bin/console doctrine:database:create");
        }
        // version
        $q = new Question("Engine version? [10.5.17-MariaDB]: ", "10.5.17-MariaDB");
        $q->setAutocompleterValues(["10.5.17-MariaDB", "5.6", "8.0"]);
        $version = $qHelper->ask($input, $output, $q);

        $line[] = "# database";
        $line[] = sprintf(
            "DATABASE_URL=mysql://%s:%s@%s:%s/%s?serverVersion=%s",
            $user,
            $password,
            $host,
            $port,
            $dbname,
            $version
        );
        $line[] = "";

        $io->title('SMTP設定');
        $io->note('https://symfony.com/doc/current/mailer.html');
        $io->note('smtp://{account}:{password}@{host}:{port}');
        $q = new ConfirmationQuestion("dockerのmailHogを利用し、SymfonyLocalWebServerを使用? [n]", "n", "/^y/i");
        if(!$qHelper->ask($input, $output, $q)) {
            $q = new Question("SMTP account? [dev_test@triple-e.jp]: ", "dev_test@triple-e.jp");
            $account = $qHelper->ask($input, $output, $q);
            $q = new Question("SMTP password? : ", 'GXWs2dHtttcqeaaw');
            $password = $qHelper->ask($input, $output, $q);
            $q = new Question("SMTP host? [sv14665.xserver.jp]: ", "sv14665.xserver.jp");
            $host = $qHelper->ask($input, $output, $q);
            $q = new Question("SMTP Port? [465]: ", "465");
            $port = $qHelper->ask($input, $output, $q);

            $line[] = "# SMTP";
            $line[] = sprintf(
                "MAILER_DSN=smtp://%s:%s@%s:%s",
                $account,
                $password,
                $host,
                $port
            );
            $line[] = "";
        } else {
            $line[] = "MAILER_DSN=smtp://127.0.0.1:1025";
            $line[] = "";
        }
        $io->note('env=dev の場合にメール送信されるデバッグ用アドレス (ご自身のメールアドレスを入力)');
        $q = new Question("メールアドレス? [dev_test@triple-e.jp]: ", "dev_test@triple-e.jp");
        $myMail = $qHelper->ask($input, $output, $q);
        $line[] = "DEV_MAIL=". $myMail;

        // CMS UPLOAD
        $io->title('CMS画像のアップロードディレクトリ');
        $q = new Question("ディレクトリ指定? [var/upload]: ", "var/upload");
        $dir = $qHelper->ask($input, $output, $q);
        $line[] = "APP_CMS_UPLOAD_DIR=". $dir;
        $line[] = "";

        // ReCaptcha
        $io->title('reCaptcha V3 設定');
        $io->note('https://www.google.com/recaptcha/about/');
        $line[] = "# Google reCaptcha";
        $q = new ConfirmationQuestion("Local開発用の設定を使う? [y]: ", "y", "/^y/i");
        if($qHelper->ask($input, $output, $q)) {
            $line[] = "GOOGLE_RECAPTCHA_SITE_KEY=6Lfn5UIfAAAAAHqQaFkf2ItJv2wHhXPxeTPEAgTL";
            $line[] = "GOOGLE_RECAPTCHA_SECRET=6Lfn5UIfAAAAADfDuhhDJa8fMOPP85KbkhYgXg1l";
        } else {
            $q = new Question("サイトキー?: ", false);
            $siteKey = $qHelper->ask($input, $output, $q);
            $line[] = "GOOGLE_RECAPTCHA_SITE_KEY=". $siteKey;

            $q = new Question("シークレット?: ", false);
            $secret = $qHelper->ask($input, $output, $q);
            $line[] = "GOOGLE_RECAPTCHA_SECRET=" . $secret;
        }
        $line[] = "";

        // Slack token
        $line[] = "SLACK_TOKEN=xoxb-3404361154391-3443055718144-sp17GLVtiUHiEB23APThoPal";
        $line[] = "";

        // inquiry validation
        $line[] = "# env=devで以下がfalseの時はお問合せフォームの連続送信チェック・reCaptchaチェックを実行しない";
        $line[] = "INQUIRY_VALIDATION=true";
        $line[] = "";

        // deploy ftp
        $io->title('FTPデプロイ設定');
        $q = new ChoiceQuestion(
            "リリース先: ",
            ["Triple-E X Server", "Triple-E Heteml Server", "その他", "未設定"]
        );
        $target = $qHelper->ask($input, $output, $q);
        if ($target === "Triple-E X Server") {
            $user = "master@xs121977.xsrv.jp";
            $password = "Tr4g_Hmke3t-Hkk";
            $host = "sv13250.xserver.jp";
            $set = true;
        } elseif($target === "Triple-E Heteml Server") {
            $user = "triple-e";
            $password = "D54kMMeG1Bwj-eNxjLB7swASMrqKcp";
            $host = "ftp-triple-e.heteml.net";
            $set = true;
        } elseif($target === "その他") {
            $q = new Question("User?: ", false);
            $user = $qHelper->ask($input, $output, $q);
            $q = new Question("Password?: ", false);
            $password = $qHelper->ask($input, $output, $q);
            $q = new Question("Host?: ", false);
            $host = $qHelper->ask($input, $output, $q);
            $set = true;
        } else {
            $user = "";
            $password = "";
            $host = "";
            $set = false;
        }
        if($set) {
            $q = new Question("FTP本番ディレクトリ? : ", false);
            $prodDir = $qHelper->ask($input, $output, $q). "/public_html/build";
            $q = new Question("FTPステージングディレクトリ? : ", false);
            $stgDir = $qHelper->ask($input, $output, $q). "/public_html/build";
        } else {
            $prodDir = "";
            $stgDir = "";
        }
        $line[] = "# ftp deploy";
        $line[] = "DEPLOY_PROD_FTP_USER=". $user;
        $line[] = "DEPLOY_PROD_FTP_HOST=". $host;
        $line[] = "DEPLOY_PROD_FTP_PASS=". $password;
        $line[] = "DEPLOY_PROD_ROOT=". $prodDir;
        $line[] = "DEPLOY_STG_ROOT=". $stgDir;
        $line[] = "# local build directory";
        $line[] = "DEPLOY_LOCAL_ROOT=/public_html/build";


        $fs->touch($dest);
        $fs->appendToFile($dest, implode("\r\n", $line));
        $io->success(".env.local を作成しました");

        return Command::SUCCESS;
    }
}
