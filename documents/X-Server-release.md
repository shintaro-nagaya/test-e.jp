# Xサーバーへのリリース手順

ドメインは取得済みと仮定する

## DNS設定

ドメインのDNSに情報を登録する

ムームードメインの場合 ムームーDNSの「カスタム設定」へ

IPアドレスはXサーバーのサーバー管理 > サーバー情報 > IPアドレスを参照

ドメイン`example.com`は適宜変更、MXレコードはメールを使用する場合に記述

| サブドメイン | 種別             | 値             | 優先度 |
|--------|----------------|---------------|-----|
|        | A              | 162.43.117.91 | |
| www    | A| 162.43.117.91 |     |
|stg|A|162.43.117.91| |
| |MX|example.com|50|
| |TXT|v=spf1 +a:sv13250.xserver.jp +a:example.com +mx include:spf.sender.xserver.jp ~all| |

## Xサーバーでドメイン追加

Xサーバーのサーバー管理 > ドメイン内 ドメイン設定 > ドメイン設定追加タブ

ドメイン名を入力 `example.com`

```
無料独自SSLを利用する（推奨）
高速化・アクセス数拡張機能「Xアクセラレータ」を有効にする（推奨）
```
はONで良い

`確認画面へ進む` > 登録する

上記DNSの浸透がまだの場合は無料SSLの取得に失敗するがあとで設定しなおせる

### stg サブドメイン

Xサーバーのサーバー管理 > ドメイン内 サブドメイン設定 > ドメイン選択画面から`選択する` > サブドメイン設定追加タブ

サブドメイン名 `stg`

ドキュメントルートを `example.com/public_html/(サブドメイン名)/` の方を選択

`無料独自SSLを利用する`をON

`確認画面へ進む` > 登録する

## Basic認証をかけておく

Xサーバーのサーバー管理 > ホームページ内 アクセス制限 > ドメイン選択画面から`選択する`

対象設定ドメイン `example.com`

現在のフォルダ > `ユーザー設定`

ユーザーIDとパスワードを入力 > `確認画面へ進む` > 登録する

フォルダ一覧へ戻り`現在のフォルダ` > ON / OFF を ONに > `設定する`

## 独自SSL設定

まずドメイン設定が反映しているのを確認する。`example.com`をブラウズしてXサーバーの初期画面がでていれば反映している。お待ちくださいなどのメッセージがでている場合はまだ反映されていない

Xサーバーのサーバー管理 >ドメイン内 SSL設定 > ドメイン選択画面から`選択する` > 独自SSL設定追加タブ

対象ドメイン `www.example.com`

`CSR情報(SSL証明書申請情報)を入力する`はOFF

`確認画面へ進む` > 登録する

続いて `stg.example.com`も同様に設定

## PHP Ver切り替え

Xサーバーのサーバー管理 > PHP内 PHP Ver.切替 > ドメイン選択画面から`選択する`

対象のドメイン `example.com`

変更後のバージョン `PHP8.1.6` (2022年9月現在)

`変更`

## DB作成

Xサーバーのサーバー管理 > データベース内 MySQL設定 > MySQL追加タブ

名称は半角英数10文字以内で

MySQLデータベース名 xs121977_`任意名称`

文字コード `UTF-8`

`確認画面へ進む` > 登録する

MySQLユーザー追加タブへ

ユーザーIDは xs121977_ `半角英数字7文字`・パスワードは半角英数字15文字で

`確認画面へ進む` > 登録する

MySQL一覧タブへ

作成したデータベースの`アクセス権未所有ユーザ`を上記で作成したユーザー選択して`追加`

## メールアカウント作成

クライアントがメールを使用する場合はCPIでサーバーを構築する

ここではお問い合わせフォームなどで使用するSMTPアカウントを作成する

Xサーバーのサーバー管理 > メール内 メールアカウント設定 > ドメイン選択画面から`選択する` > メールアカウント追加タブ

メールアカウント `app`@example.com

パスワード 半角英数15文字

容量 2000 MB

コメント 任意入力

`確認画面へ進む` > 登録する

### info@example.com

クライアントがメールを使用しないが、`info@`で受信をして使用している別メールへ転送したい、場合は上記手順で `info`@example.com も作っておく

Xサーバーのサーバー管理 > メール内 メールアカウント設定 > ドメイン選択画面から`選択する` > メールアカウント一覧タブ

info@example.com の　`転送`へ

メールボックスに残すかどうかの設定 `残さない` にするため `変更` >  `変更する`

転送先アドレスに別メールを入力 > `追加する`

# google reCaptcha
https://www.google.com/recaptcha/admin
で`recaptcha v3`を設定する。

googleアカウントは`triple.e.japan@gmail.com`のもので作成

ドメインは
```
example.com
www.example.com
stg.example.com
localhost
```
を登録。あとで設定するので、サイトキーとサイトシークレットを控えておく


# Backlogにリモートリポジトリ作成

backlogのプロジェクトへ行き、 `Git`へ

`Git`がメニューにない場合 `プロジェクト設定` > 基本設定 > `Gitを使用する`をチェック > `登録`

`Git`のリポジトリ一覧の右上 + をクリック

リポジトリ名は ドメインで (`example.com`)

WebフックURLを
```
https://{BasicAuthUserID}:{BasicAuthPassword}@www.example.com/checkout/deploy.php
```

`リポジトリを作成する`

## ローカルのプロジェクトをプッシュ

作成したリポジトリにローカル`master`ブランチをプッシュ、`master`から`stg`ブランチを分岐してプッシュしておく。

# Xサーバーにデプロイ設定

XサーバーにSSHでログイン。
ドメイン名のディレクトリが作られているはずなのでプロジェクトルートに移動する
```
$ cd example.com
```
backlogのリポジトリをクローン・フェッチ・チェックアウトする。
URLはバックログのプロジェクトのGitリポジトリにあるHTTPSのものをコピーしてくる。
{backlogUserId}:{backlogPassword} はbacklogの自分のログイン情報のものを記述。
ただしIDやパスワードに`: @`が入っているとうまく行かない
```
$ git clone --mirror https://{backlogUserId}:{backlogPassword}@triple-e.backlog.jp/git/EXAMPLE/example.com.git .git
$ git --git-dir=./.git fetch
$ git --git-dir=./.git --work-tree=./ checkout master -f
```
`master`ブランチがチェックアウトされる
## 環境準備

basicAuthの `.htaccess`がすでにあるので、一旦リネームしておく
```
$ mv public_html/.htaccess public_html/.htaccess1

$ composer install

$ php bin/console app:init
```
DBやSMTPやrecaptchaV3を上記で作成した情報を入力、アップロードディレクトリはデフォルトの`var/upload/`に、FTPデプロイ設定は無設定でOK。


`.htaccess`が新たに作られているので、先ほどリネームした`.htaccess1`に記載されている内容を新しい`.htaccess`に転記しておく。
また、SSLリダイレクトのコメントアウトも外しておく
```
$ vi public_html/.htaccess

# BasicAuthの記述をコピーしてくる
SetEnvIf Request_URI ".*" Ngx_Cache_NoCacheMode=off
SetEnvIf Request_URI ".*" Ngx_Cache_AllCacheMode
AuthUserFile "/home/xs121977/example.com/htpasswd/.htpasswd"
AuthName "Member Site"
AuthType BASIC
require valid-user

...

<IfModule mod_rewrite.c>
    RewriteEngine On

    # SSL 以下４行のコメントアウトを外す
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
    RewriteCond %{HTTP_HOST} !^www\. [NC]
    RewriteRule .* https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```
シードしておく
```
$ php bin/console app:seed --refresh
```
ここでは管理ページアカウントは本番のものを設定する

## ビルド
```
$ yarn build
```
完了したらブラウザで確認

## ステージング環境
ステージングを作成する

`~/example.com/`内に`stg`ディレクトリを作成して本番と同様にバックログのリポジトリからクローン。
チェックアウトするブランチを`stg`にする
```
$ mkdir stg
$ cd stg
$ git clone --mirror https://{backlogUserId}:{backlogPassword}@triple-e.backlog.jp/git/EXAMPLE/example.com.git .git
$ git --git-dir=./.git fetch
$ git --git-dir=./.git --work-tree=./ checkout stg -f
$ composer install
$ php bin/console app:init
```
アップロードディレクトリを`../var/upload`にする以外は本番と同じ。シードは行わない。`yarn build`でビルドしておく

`.htaccess`を編集
```
$ vi public_html/.htaccess

<IfModule mod_rewrite.c>
    RewriteEngine On

    # SSL 以下2行のコメントアウトを外す
    RewriteCond %{HTTPS} off
    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
#    RewriteCond %{HTTP_HOST} !^www\. [NC]
#    RewriteRule .* https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```

### ステージング環境をシンボリックリンクにする
Xサーバーのサブドメインは本番の`public_html`配下に固定されているので、`~/example.com/public_html/stg`ディレクトリをシンボリックリンクに変更する

まずステージングのドキュメントルートの絶対パスを調べる
```
$ cd ~/example.com/stg/public_html
$ pwd
/home/xs121977/example.com/stg/public_html
```
`pwd`ででてきたパスをコピーしておく
```
# 本番のドキュメントルートへ移動
$ cd ~/example.com/public_html

# 元からある stgディレクトリを削除 !! この行は失敗が許されないので要注意 !!
$ rm -rf stg

# さっきコピーしたパスにシンボリックリンク作成
$ ln -s /home/xs121977/example.com/stg/public_html stg
```

`https://stg.example.com`をブラウズして確認

## 自動deploy設定

デプロイ設定を編集
```
$ vi ~/example.com/public_html/checkout/env.php

<?php
// stg checkout URI - ステージングのチェックアウトPGのURL　基本的にこれだけ変更する必要がある
// ステージングでBasic認証がかかっている場合URLに認証情報を含めるのを忘れずに
define('STG_CHECKOUT_ENDPOINT', "https://{basicAuthUserID}:{basicAuthPassword}@stg.example.com/checkout/stg.php");
```

設定は以上なのでSSHからログアウト

# リリーステスト

ローカルで`stg`と`master`をプッシュして自動デプロイできるか確認する。
フロントのビルドができているか確認するために、TOPページのscssを何か変更してみると良い

プッシュ後にビルドが反映するまでに３０秒くらいはかかるので、少し待ってから確認する様に。
特に`node_modules`に追加がある場合は結構時間がかかるので、ゆっくり待つ。

`~/example.com/public_html/checkout/log/YYYYMMDD.log`にデプロイした時のログが出力されている

# サーバービルドの設定

プッシュ後に自動でビルドが走るが`webpack.config.js`の`cleanupOutputBeforeBuild()`が有効になっていると、ビルド中のアクセスが500エラーになってしまうため`cleanupOutputBeforeBuild()`をキャンセルする。

その為プッシュするたびにビルドが走り、差分のファイルが作成されるが古いものが残り続けることになる。

サーバーの以下ファイルに`public_html/checkout/asset_clear.php`へのアクセスを追加する。
```
/xs121977.xsrv.jp/public_html/asset_clear.php
```
このファイルはAM4時にcron実行される。
```
<?php
file_get_contents("https://{user}:{password}@stg.example.com/checkout/asset_clear.php");
file_get_contents("https://www.example.com/checkout/asset_clear.php");
```

# X-ServerのSSL設定の注意点

DNSに先にサブドメインのAレコードを設定して1時間上待った上で設定する必要がある

特にリリース済みサイトでサブドメインを追加する場合は一時的にメインドメインの`.htaccess`のSSLリダイレクトを外し、Basic認証がかかっている場合はこれも外して設定する必要がある

```
public_html/.htaccess

SetEnvIf Request_URI ".*" Ngx_Cache_NoCacheMode=off
SetEnvIf Request_URI ".*" Ngx_Cache_AllCacheMode
# Basic認証の以下を一時的にコメントアウト
#AuthUserFile "/home/xs121977/example.com/htpasswd/.htpasswd"
#AuthName "Member Site"
#AuthType BASIC
#require valid-user

# ....

    # SSL 以下を一旦コメントアウト
#    RewriteCond %{HTTPS} off
#    RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [R=301,L]
#    RewriteCond %{HTTP_HOST} !^www\. [NC]
#    RewriteRule .* https://www.%{HTTP_HOST}%{REQUEST_URI} [L,R=301]
```
コンパネでの設定が通れば反映されるのを待たずに上記を戻して問題ない