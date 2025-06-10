# Xサーバーに nodeJsをインストールしてwebpackのビルドをリモートで行える様にする実験

## 手順
- 事前準備・X-Server契約・SSH通す・PHPの準備・FTPの準備
- X-ServerにnodeJsをインストールする
- yarnコマンドをインストールする
- サンプルアプリをbacklogのリポジトリ経由でリリースする
- webpackのビルドをCLIから試してみる
- リポジトリからのwebhookで実行するデプロイプログラムにビルドを追加してみる

## 事前準備

### X-Serverを契約する
サーバーがないと始まらないのでX-Serverを契約する。
今回は10日間無料を利用する。
とりあえずスタンダードプランを使ってみる

#### 契約内容

|項目| 選択       |
|:-----|:---------|
|サーバーID| xs726933 |
|プラン| スタンダード   |
|Wordpressクイックスタート| 無し       |

#### 登録情報

適当に個人情報を入力
特に変わったことはない。
今回は自分個人の情報で登録

#### 確認コード入力

上記のメールに確認コードが届くので、入力して進む

#### SMS・電話認証

携帯に認証コードが届くので入力して進む

#### 契約完了

コンパネに遷移する。
一旦契約は完了した

### Xサーバーの各種設定

コンパネからサーバー管理へ移動、ドメイン設定に初期ドメインがある
#### ドメイン
```
xs729999.xsrv.jp
```
#### SSH
SSH設定から

今回は鍵の共有はしないので、自分のmacの`id_rsa.pub`登録する

公開鍵登録・更新の入力に
```
$ cat ~/.ssh/id_rsa.pub
```
で取得した公開鍵をペーストする

SSH設定で ONにするのを忘れない様に

#### SSHでログインしてみる
```
$ vim ~/.ssh/config
```
でSSH設定へ、今回は以下の様に入力した
```
# n3s X Server
Host n3s_x
  HostName sv14999.xserver.jp
  Port 10022
  User xs729999
  IdentityFile ~/.ssh/id_rsa
```
入力内容はサーバー情報から取得できる
```
HostName sv14999.xserver.jp
```
これはホスト名のところになる
```
User xs729999
```
これはサーバーのユーザーなので、ホームディレクトリの項目`/home/xs729999`から取れる

ポートは`10022`固定

```
$ ssh n3s_x
```
でログインしてみる。

最初はサーバーのフィンガープリントを受け入れるか聞かれるので`yes`

`id_rsa`のパスフレーズを入力するとログインできる

#### PHPのパスを通す

初期ではphpは5.4なので、8.0にパスを変える
```
$ vi .bash_profile
```
最後に以下を追記
```
alias php='/usr/bin/php8.0'
```
反映
```
$ source .bash_profile
$ php -v
PHP 8.0.16 (cli) (built: Mar 11 2022 17:25:42) ( NTS )
Copyright (c) The PHP Group
Zend Engine v4.0.16, Copyright (c) Zend Technologies
```
バージョンが変われば成功
#### composerをインストールする
https://getcomposer.org/download/

最新版を入れる
```
php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
php -r "if (hash_file('sha384', 'composer-setup.php') === '55ce33d7678c5a611085589f1f3ddf8b3c52d662cd01d4ba75c0ee0459970c2200a51f492d557530c71c15d8dba01eae') { echo 'Installer verified'; } else { echo 'Installer corrupt'; unlink('composer-setup.php'); } echo PHP_EOL;"
php composer-setup.php
php -r "unlink('composer-setup.php');"
```
実行すると`composer.phar`がインストールできる
#### composerのパスを通す
さっき同様にcomposerコマンドを作る
```
$ vi .bash_profile
```
以下追記
```
alias composer='/usr/bin/php8.0 ~/composer.phar'
```
PHPの実行パスを指定して、ユーザールートにある`composer.phar`を実行するコマンドにする

反映
```
$ source .bash_profile
$ composer -v

   ______
  / ____/___  ____ ___  ____  ____  ________  _____
 / /   / __ \/ __ `__ \/ __ \/ __ \/ ___/ _ \/ ___/
/ /___/ /_/ / / / / / / /_/ / /_/ (__  )  __/ /
\____/\____/_/ /_/ /_/ .___/\____/____/\___/_/
                    /_/
Composer version 2.3.10 2022-07-13 15:48:23
```
完了
### FTP
説明では

| Key |Value|
|:----|:----|
|Host|sv14165.xserver.jp|
|User|xs726933|
|Pass|FTPパスワード(サーバーパスワードと同じ値)|
となっていたが入れん。
一旦スルーする。

# XサーバーにnodeJsをインストールする
ここから本題
### 参考文献
https://qiita.com/yuu_1st/items/95865adfbe5ddb3eeeb0

https://github.com/hokaccha/nodebrew


## nodebrewをインストール
```
$ curl -L git.io/nodebrew | perl - setup
```
ホームに`.nodebrew`が入った。
パスを通してみる
```
$ echo 'export PATH=$HOME/.nodebrew/current/bin:$PATH' >> ~/.bashrc
```
参考文献の通りに改行が入っていないのでコメントアウト行の最後に追記されてしまっている

改行を入れて
```
# User specific aliases and functionsexport

PATH=$HOME/.nodebrew/current/bin:$PATH
```
となる様に修正して反映
```
$ source .bashrc 
$ nodebrew -v
nodebrew 1.2.0
```

## nodejsインストール

```
$ nodebrew install stable
$ nodebrew list
$ nodebrew use v18.7.0
```
`v18.7.0`だとなにやらライブラリが足らないらしい
https://it.ama2pro.net/2022/05/31/node%E3%81%AEv18%E3%82%92%E4%BD%BF%E3%81%A3%E3%81%9F%E3%82%89%E3%82%A8%E3%83%A9%E3%83%BC%E3%81%AB%E3%81%AA%E3%81%A3%E3%81%9F%EF%BC%88centos7%EF%BC%89/
### v16系を使う
ローカルが`v16.9.0`なのでこれを入れてみる
```
$ nodebrew install v16.9.0
$ nodebrew list
v16.9.0
v18.7.0
$ nodebrew use v16.9.0
use v16.9.0
$ node -v
v16.9.0
$ npm -v
7.21.1
```
nodejsはいけたっぽい!

# yarnコマンドのインストール
```
$ npm install yarn

added 1 package, and audited 2 packages in 510ms

found 0 vulnerabilities
npm notice 
npm notice New major version of npm available! 7.21.1 -> 8.17.0
npm notice Changelog: https://github.com/npm/cli/releases/tag/v8.17.0
npm notice Run npm install -g npm@8.17.0 to update!
npm notice 
```
## パスを通してみる
https://qiita.com/kokushin/items/06d2eeb1e95d65cc549b

ローカルインストールしたので、以下に作られているはず
```
node_modules/yarn
```
`.bash_profile`のパスに追加してみる
```
$ vi ~/.bash_profile

...
PATH=$PATH:$HOME/bin:$HOME/node_modules/yarn/bin
```
`$HOME/node_modules/yarn/bin`を追加

反映して確認
```
$ source .bash_profile
$ yarn -v
1.22.19
```
yarnも入ったと思う

## globalインストールでも大丈夫
```
$ npm install -g yarn
```
Pathは
```
export PATH="$PATH:`yarn global bin`"
```
で通る

# サンプルアプリをbacklogのリポジトリ経由でリリースする

ローカルにテスト用プロジェクトを生成
```
(ローカルにて)
$ git clone git@github.com:triple-E-Japan-inc/Symfony6_cms_skelton.git cms-test
```
backlogにリモートリポジトリを作成

テスト用プロジェクトのgit設定を変更し、リモートをbacklogに設定、この辺りはsource treeの力を借りたので割愛

`master`ブランチをbacklogのリポジトリにプッシュしたのでこれをXサーバーへリリースする

```
$ git clone --mirror https://{user}:{password}@triple-e.backlog.jp/git/TELIB/cms-test.git .git
$ git --git-dir=./.git fetch
$ git --git-dir=./.git --work-tree=./ checkout master -f
Already on 'master'
```
リリースできた

### とりあえず必要な設定を入れる
```
$ composer install
$ php bin/console app:init
$ php bin/console app:seed --refresh
```
いつも通りここまでは普通に行った。

# webpackのビルドをCLIから試してみる
まず`node_modeules`が入るか！？
```
$ yarn install
```
めっちゃ`warning`出たけど進んでみる
```
$ yarn build

yarn run v1.22.19
warning ../package.json: No license field
$ encore production --progress
Running webpack ...

95% emitting emit ImageminWebpWebpackPlugin(node:114748) [DEP_WEBPACK_COMPILATION_ASSETS] DeprecationWarning: Compilation.assets will be frozen in future, all modifications are deprecated.
BREAKING CHANGE: No more changes should happen to Compilation.assets after sealing the Compilation.
	Do changes to assets earlier, e. g. in Compilation.hooks.processAssets.
	Make sure to select an appropriate stage from Compilation.PROCESS_ASSETS_STAGE_*.
(Use `node --trace-deprecation ...` to show where the warning was created)
99% done plugins FriendlyErrorsWebpackPlugin DONE  Compiled successfully in 7796ms                                                                                                19:01:52

12 files written to public_html/build
Entrypoint app 98.8 KiB = runtime.5332280c.js 1.55 KiB 755.5a8586e9.js 87.9 KiB app.e68a5b4c.css 2.8 KiB app.f9bf0d94.js 6.55 KiB
Entrypoint admin [big] 351 KiB = runtime.5332280c.js 1.55 KiB 755.5a8586e9.js 87.9 KiB 168.61e85889.js 94.9 KiB admin.78d7e7f0.css 162 KiB admin.2e1b826f.js 5.12 KiB
Entrypoint _tmp_copy 1.55 KiB (35.6 KiB) = runtime.5332280c.js 2 auxiliary assets
webpack compiled successfully
Done in 8.93s.

```
ん？できた？

ブラウズしてみる。順番が悪かったのか`.htaccess`が設定できていなかったので手動で登録。
初期画面が無事出たよ！！Year!!Yahoo!!

# リポジトリからのwebhookで実行するデプロイプログラムにビルドを追加してみる

backlogのリポジトリのwebhookからリリースできる設定をアップする
## deploy.php
受け取るPHPプログラムはいつも通り
https://github.com/triple-E-Japan-inc/backlog-git-deploy

backlog側のwebhookも忘れずに

### デプロイテスト
まず普通にPHPの更新を試してみる

単純にHTMLを変更する。これは無事に反映

## webpackを自動ビルドさせてみる

これが本来の目的

### deploy.php に追加
yarnのフルパス
```
/home/xs729999/node_modules/yarn/bin/yarn
```
deploy.phpに追加
```
// yarn
define('YARN', '/home/xs729999/node_modules/yarn/bin/yarn');

...

exec(YARN. ' build');
```

ローカルでscssに手を加えてプッシュ、ビルドを確認するも反映無し。OTZ

#### --cwdパラメータ
実行ディレクトリが違うのでは？とのことで、`--cwd`パラメータで作業ディレクトリを指定してみる
```
$comm = YARN. ' --cwd '.PROD_ROOT .' encore prod';
shell_exec($comm);
```
https://classic.yarnpkg.com/en/docs/cli#toc-cwd

`master`へプッシュして確認、HTML反映はあるがscssのビルドはされていない OTZ

### 結果01

どうやらCLI越しのPHPからはyarnコマンドは実行できるが、http経由のPHPでは実行できないらしい。

php-cgi から `shell_exec` などで他に設置したyarnを叩いてもダメ、`sh`で書いたものを叩いてもダメでした。

# 対策1

CLIからなら行けるので、cronから叩いてみる

## build.php
```
<?php
$comm = "/home/xs7s9999/.nodebrew/current/bin/yarn --cwd /home/xs729999/xs729999.xsrv.jp build";
$res = shell_exec($comm);
echo $res;
```
上記をたたくcron
```
24 * * * * /usr/bin/php8.0 /home/xs729999/xs729999.xsrv.jp/build.php
```

## 結果
cronでもダメぽ。

このPHPをCLIから実行すればビルドされるのだが……

シェルスクリプトでもだめ

# なんかできた
いろいろやった結果なんかできた。
## プロジェクトルートにシェルスクリプトを設置
`build.sh`って名前で作成
```
#!/usr/bin/sh

~/.nodebrew/current/bin/node ~/.nodebrew/current/bin/yarn install
~/.nodebrew/current/bin/node ~/.nodebrew/current/bin/yarn build
```
nodeへのパス + yarnへのパスで設置

yarnをローカルインストールした場合
```
#!/usr/bin/sh

~/.nodebrew/current/bin/node ~/node_modules/yarn/bin/yarn.js install
~/.nodebrew/current/bin/node ~/node_modules/yarn/bin/yarn.js build
```

`yarn install`と`yarn build`が実行される

これをwebhookでうけるスクリプトから叩く
```
public_html/checkout/deploy.php

... master brunchをチェックアウトした後に ....
$comm = "/usr/bin/sh ../../build.sh";
echo shell_exec($comm);
```
これでバックログにmasterをプッシュしたらmasterをチェックアウトしてビルドがかかる。

# ステージング
productionでフックを受けてstgの階層でビルドさせる様にしたがこれはどうにもうまく行かない。
productionの方のビルドが走ってしまう。

stg環境にも`deploy.php`と`build.sh`を用意して、productionのフックが`stg`だった場合stgの`deploy.php`へ丸投げする形で解決した
