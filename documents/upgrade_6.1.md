# phpbrewでphp8.1をインストーる

https://laboliy.com/phpbrew-install-php-81/

```
phpbrew self-update
phpbrew update
```

インストール可能なバージョン
```
$ phpbrew known
Read local release list (last update: 2022-08-25 12:35:20 UTC).
You can run `phpbrew update` or `phpbrew known --update` to get a newer release list.
8.1: 8.1.9, 8.1.8, 8.1.7, 8.1.6, 8.1.5, 8.1.4, 8.1.3, 8.1.2 ...
8.0: 8.0.22, 8.0.21, 8.0.20, 8.0.19, 8.0.18, 8.0.17, 8.0.16, 8.0.15 ...
7.4: 7.4.30, 7.4.29, 7.4.28, 7.4.27, 7.4.26, 7.4.25, 7.4.24, 7.4.23 ...
```

最新版を入れてみる
```
phpbrew install php-8.1.9 +default +mysql +fpm +openssl=/opt/homebrew/opt/openssl@1.1 +zlib="$(brew --prefix zlib)" -- --with-external-pcre=$(brew --prefix pcre2) 

...

Error: Configure failed:
The last 5 lines in the log file:
installed software in a non-standard prefix.



Alternatively, you may set the environment variables OPENSSL_CFLAGS

and OPENSSL_LIBS to avoid the need to call pkg-config.

See the pkg-config man page for more details.

Please checkout the build log file for more details:
         tail /Users/solexbeer/.phpbrew/build/php-8.1.9/build.log

```
openssl 周りでエラー

パラメータを少なくして実行する

```
$ phpbrew install php-8.1.9 +default +mysql +fpm
```
とりあえず入った。

## CLI PHPバージョン 切り替え

```
phpbrew switch php-8.1.9
```

## intl extension install

intlが入ってないので入れてみる


https://blog.siwa32.com/phpbrew_intl_install/

参考と同じ様に
```
export PKG_CONFIG_PATH="/usr/local/opt/icu4c/lib/pkgconfig"
phpbrew ext install intl

... 大量の Deprecated が出る

===> Installing intl extension...
Log stored at: /Users/solexbeer/.phpbrew/build/php-8.1.9/ext/intl/build.log
Changing directory to /Users/solexbeer/.phpbrew/build/php-8.1.9/ext/intl
===> Phpize...
===> Configuring...
===> Building...
===> Running make all: /usr/bin/make -C '/Users/solexbeer/.phpbrew/build/php-8.1.9/ext/intl' 'all'  >> '/Users/solexbeer/.phpbrew/build/php-8.1.9/ext/intl/build.log' 2>&1
===> Installing...
===> Running make install: /usr/bin/make -C '/Users/solexbeer/.phpbrew/build/php-8.1.9/ext/intl' 'install'  >> '/Users/solexbeer/.phpbrew/build/php-8.1.9/ext/intl/build.log' 2>&1
===> Extension is installed.
===> Creating config file /Users/solexbeer/.phpbrew/php/php-8.1.9/var/db/intl.ini.disabled
===> Enabling extension intl
[*] intl extension is enabled.
Done.

```
symfony cliを再起動。インストールを確認

## iconv インストール

https://public-constructor.com/php-phpbrew-iconv-extension/

エラーに悩まされたので、ちゃんと参考に従ってみる
```
brew install libiconv

phpbrew ext install iconv -- --with-iconv=$(brew --prefix libiconv)
```

# composer update

めんどくさかったのでphp と extra:symfony:require だけ変えてみたけど怒られたのでちゃんとやる

## symfonyパッケージのバージョンを書き換える

安直なことをするもんではない。`require:`と`require-dev:`内の`symfony/*`で`6.0.*`になっていたものを全て`6.1.*`に手動で書き換え
```
composer update "symfony/*"
```
実行

無事上がった様な気がする。バージョンは`6.1.4`になっている。
# GDが入ってないっぽい
https://public-constructor.com/php-phpbrew-gd-extension/

https://teratail.com/questions/51443?sort=1
```
brew install gd
brew install jpeg
phpbrew ext install gd -- --with-jpeg-dir=/usr/local/lib
```
うまくいかない。

brew実行時にエラー出てる気がするので brewを更新する
```
git -C /usr/local/Homebrew/Library/Taps/homebrew/homebrew-core fetch --unshallow
brew update
```

もう一度 インストールする
```
brew install jpeg
```
なんかすげぇいろいろ進んでる
```
phpbrew ext clean gd
phpbrew ext install gd -- --with-jpeg-dir=/usr/local/Cellar/ --with-webp-dir=/usr/local/Cellar/
```
symfony server restartして動作確認。行けた。
