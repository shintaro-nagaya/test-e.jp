# Install

## Requirement

PHP : >= 8.2
ext : GD , exif ...

```
git clone git@github.com:triple-E-Japan-inc/Symfony6_cms_skelton.git your-project
```
php8.0 / symfony 6.0 の環境をクローンする場合は
```
git clone -b sf6.0 git@github.com:triple-E-Japan-inc/Symfony6_cms_skelton.git your-project
```
[symfony6_cms_skelton::sf6.0](https://github.com/triple-E-Japan-inc/Symfony6_cms_skelton/tree/sf6.0)

## PHP環境作成

クローンできたらPHPコンテナ内で

``` 
$ composer install
```

初期設定

```
$ php bin/console app:init
```

DB接続設定とSMTP設定が聞かれるので入力

FTPアカウントも聞かれるが、現在はサーバー上でビルドするため不要・`3:未設定`を選ぶ

## docker起動

ローカルに`docker-compose.yml`がない場合はinit時にファイルが作成されます。

symfonyCliで実行する場合はこの設定を推奨します。dockerを起動してください。

```
$ docker-compose up -d
```

別途docker環境を利用する場合は適宜差し替えてください。

### APP_SECRET変更 プロジェクト開始時のみ!

!!!!! プロジェクト初回のみ`.env`ファイルの`APP_SECRET`を変更する !!!!!

```
$ php bin/console app:init:regenerate_secret
```

出てきた文字列を`.env`の`APP_SECRET=`の値と差し替える

## DBシーディング

```
$ php bin/console doctrine:database:create
$ php bin/console doctrine:migrations:migrate
$ php bin/console app:seed
```

途中で管理画面アカウントの情報を聞かれるので入力。ローカルではなんでも良い

## NodeModulesとビルド

フロントのターミナルにて
```
$ yarn install
$ yarn build
```
node_moduleのインストールとビルドが実行される

# Gitリポジトリ削除 プロジェクト開始時のみ!

ベース開発リポジトリと切り離すために`.git`ディレクトリを削除する

sourceTreeなどで新しくローカルリポジトリを作成する

リポジトリ一覧の`新規` > `ローカルリポジトリを作成`

- 保存先のパスをプロジェクトルートディレクトリに指定
- 名前は任意のものを

`作成`

## Backlogのリポジトリをリモートに登録 プロジェクト開始時のみ!

sourceTreeのリポジトリを開いて左上`設定` > リモートタブ > `追加`

リモートの名前は任意ですが、`origin`や`backlog`

URLはbacklogの`Git`ページからSSHのURLをコピーしてペーストして `OK` > `OK`

`master`ブランチをプッシュしてみる

# 開発サーバーの立ち上げ

フロントのターミナルにて

```
$ yarn serve [Apacheで設定したドメイン]
```

assetsを変更した際に監視ビルドとブラウザの自動更新が働くサーバー(webpack dev-serer)が立ち上がる

ただし、このサーバーはassetsの変更しか検知してくれないので

```
$ yarn serve [Apacheで設定したドメイン]
```

または

```
$ yarn watch
```

を別のターミナルで立ち上げつつ

```
$ yarn sync [Apacheで設定したドメイン]
```

を実行して http://localhost:3000 にアクセスすると、template/の変更も監視しながら開発できる。

# DBのリフレッシュ
バックエンド構築が進すみ他に取り込む際や、作業中にDBで混乱したら以下コマンドで再構築・サンプルデータのシードを行う
``` 
$ php bin/console app:seed --refresh
```
DBを削除して再度作成・マイグレーション・シーディングまで一括で行える

# yarn deploy
ヘテムルなどのサーバーサイドでビルドできない環境の場合、コマンドでローカルビルドしたファイルをアップロードする
```
$ yarn deploy
```
ステージングか本番のどちらにアップロードするか聞かれるので選択
### yarn deployのFTP設定
`.env.local`にある以下部分
```
###> Node/ftp deploy ###
# deploy ftp account - production
DEPLOY_PROD_FTP_USER=
DEPLOY_PROD_FTP_HOST=
DEPLOY_PROD_FTP_PASS=
# deploy ftp account - staging
DEPLOY_STG_FTP_USER=
DEPLOY_STG_FTP_HOST=
DEPLOY_STG_FTP_PASS=
# deploy remote root dir (アップロード先パス)
DEPLOY_PROD_ROOT=
DEPLOY_STG_ROOT=
# deploy local dir (ローカルパス)
DEPLOY_LOCAL_ROOT=/public/build
###< Node/ftp deploy ###
```

stagingFTP設定がない場合PRODと同じ設定が利用される

# ページ作成手順

URLで `/company` 会社概要ページを作るとする

## コントローラクラスの作成

クラス名・ファイル名はパスカルケース(アッパーキャメルケース)で命名する

```
src/Controller/Mvc/CompanyController.php

<?php
namespace App\Controller\Mvc;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: "/company")]
class CompanyController extends AbstractController
{
    #[Route(path: '/', name: 'company_index')]
    public function index(): Response
    {
        return $this->render('pages/company/index.html.twig', []);
    }
}
```

まず、このコントローラ内でのURLを

```
#[Route(path: "/company")]
```

で指定する。コントローラ内に複数ページをルーティングした場合、全てのルーティングが `/company` 配下になる

```
    #[Route(path: '/', name: 'company_index')]
    public function index(): Response
```

この部分で個別ページの指定となる。

`path: '/'` 部分でこのアクションのURLを決定する。この場合URLは `/company/` となる

`name: 'company_index'` 部分でルーティング名を指定。twigの `path()` 関数などでこのルーティング名を使う

```
        return $this->render('pages/company/index.html.twig', []);
```

ここで実行するviewテンプレートファイルを指定する。第二引数はアサインする変数を指定するが、今回は指定なしなので ` [] `としている

## View Templateファイルの作成

コントローラで指定したviewテンプレートファイルを作成、ファイル名はスネークケースで命名

```
templates/pages/company/index.html.twig

{% extends "pages/base.html.twig" %}
{% block title %}会社概要{% endblock %}
{% block body %}
  {# ページコンテンツをここに #}
{% endblock %}
```

## 下層ページ追加

会社概要の下層ページとして沿革ページを追加していく

```diff
src/Controller/Mvc/CompanyController.php

<?php
namespace App\Controller\Mvc;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(path: "/company")]
class CompanyController extends AbstractController
{
    #[Route(path: '/', name: 'company_index')]
    public function index(): Response
    {
        return $this->render('pages/company/index.html.twig', []);
    }
    
+   #[Route(path: '/outline', name: 'company_outline')]
+   public function outline(): Response
+   {
+       return $this->render('pages/company/outline.html.twig', []);
+   }
}
```

アクションメソッドを追加する。`path: '/outline' `としているのでURLは `/company/outline` となる

`name メソッド名 viewファイル指定部分` も `outline` にする

## 沿革ページview作成

```
templates/pages/company/outline.html.twig

{% extends "pages/base.html.twig" %}
{% block title %}沿革{% endblock %}
{% block body %}
  {# ページコンテンツをここに #}
{% endblock %}
```

## リンク追加

会社概要から沿革へのリンクを作成

```diff
templates/pages/company/index.html.twig

{% extends "pages/base.html.twig" %}
{% block title %}会社概要{% endblock %}
{% block body %}
+ <a href="{{ path('company_outline') }}">沿革</a>
{# ページコンテンツをここに #}
{% endblock %}
```

ハイパーリンクタグで `path()`関数を使用、ルーティング名を指定する

沿革から会社概要へのリンクを作成

```diff
templates/pages/company/outline.html.twig

{% extends "pages/base.html.twig" %}
{% block title %}沿革{% endblock %}
{% block body %}
+ <a href="{{ path('company_index') }}">会社概要</a>
  {# ページコンテンツをここに #}
{% endblock %}
```

### 未作成のルートを先にpath()で作成する場合

ルート名が未作成の場合、レンダリングでエラーとなってしまうので、コメントアウトしておき、作成後にコメントアウトを外すと良い

```
<a href="{# TODO route未作成 {{ path('company_point') }} #}">営業所</a>
```

## 画像の配置

以下ディレクトリに画像ファイルを配置する

```
assets/images/company/image.png
```

HTML側での利用には `asset()` 関数を利用する

```
templates/pages/company/index.html.twig

{% block body %}
  <a href="{{ path('company_outline') }}">沿革</a>

+ <img src="{{ asset('build/images/company/image.png') }}" alt="---">
{% endblock %}
```

本番環境では jpg,pngファイルから自動できに webp を生成して、`.htaccess` の設定で webpが利用される様になっているので、ページ構築時に画像ファイルの圧縮は基本的に不要となっている

### scss内でbackground-imageを利用する場合

```
h1 {
  background-image: url(~/images/company/image.png);
}
```

# 404ページ

https://127.0.0.1:8000/_error/404
でプレビューする

```
templates/bundles/TwigBundle/Exception/error404.html.tiwg
```

を編集する

# Symfony Server

https://qiita.com/ippey_s/items/8919f618d13b3b6242e9

https://symfony.com/doc/current/setup/symfony_server.html

## symfony Cliをインストール

https://symfony.com/download

`hosts`ファイル
```
127.0.0.1 localhost
```
が有効になっているか確認

## CA install
``` 
symfony server:ca:install
```

## DockerでMysqlを実行
``` 
docker-compose build
docker-compose up -d
```

## Server start
``` 
symfony server:start -d
```

これでHTTPSでローカルにアクセス
``` 
https://127.0.0.1:8000
```
ポート変更
``` 
symfony server:start -d --port=8081
```
サーバー停止
``` 
symfony server:stop
```

## symfonyコマンドとか毎回打つのがめんどくさい
のでエイリアスを設定
``` 
vim ~/.bash_profile

alias sf='symfony'
alias sfp='symfony php'
alias sfc='symfony php bin/console'
```
`source ~/.bash_profile`で有効化

### サーバー起動
``` 
sf server:start -d
```
### Symfony コマンド実行
``` 
sfc cache:clear
```
### symfony経由でPHP実行
``` 
sfp -v
```

## 開発に必要なコマンド
``` 
docker-compose up -d
yarn encore dev --watch
symfony server:start -d
```
