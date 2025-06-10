# init

## .htaccess 作成
```
app:init:htaccess
```
ドキュメントルートに`.htaccess`ファイルを作成

既存の場合は実行しない

## .env.local作成
```
app:init:env
```
.env.localファイルを作成

既存の場合は実行しない

## APP_SECRETの生成
```
app:init:regenerate_secret
```
APP_SECRETの値を生成する。
これは手動で `.env` を書き換える必要がある

