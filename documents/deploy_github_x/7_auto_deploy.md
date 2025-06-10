# githubActionsを使用して自動デプロイを設定する

- github workflowsを設定
- シークレットを登録
- テストする

# github workflowを設定

必要があればローカルのソースコードの `.github` > `workflows` > `deploy_prod.yml`を編集、その他デプロイ時のコマンドを記述する

`deploy_stg.yml`も合わせて編集する

# シークレットを設定

GithubからXサーバーへSSHでログインするための情報を登録する

githubのリポジトリの画面から、上メニュー `Settings`

その後左メニューの `Secrets and variables` > `Actions` へ進む

`New repository secret`を押下

![secret](./images/7_2.png)

## HOST

Name: `SSH_HOST`

Secret :
[サーバー情報を控える](./3_setup_x_control.md)で控えたホスト名

## USERNAME

Name: `SSH_USERNAME`

Secret:  [サーバー情報を控える](./3_setup_x_control.md)で控えたホームディレクトリの `/home/`以降の`xs00000`の部分

## Key

Name: `SSH_PRIVATE_KEY`

Secret: 以下手順でコピペする

ローカルのターミナルで [SSHキーを発行](./3_setup_x_control.md)で作成したSSHキーファイルの内容をコピーする

```
$ cd ~/.ssh

$ cat xs205163.key

-----BEGIN RSA PRIVATE KEY-----
Proc-Type: 4,ENCRYPTED
DEK-Info: AES-128-CBC,045BD9E607CB499063E52AC737552D12

sCfHwv+XM1ELNtxqgcG/DJ5eCmWW8DHxqV/z3mvmVGnbhp7RJeJ9jU6mjS/dzd0z
..............................................................
UN2wh5pd064IV1YoQ+CHjS2Fhud4fh7Vu5VvXv8H2qh8l/uzNib64T74IF0MGEHV
-----END RSA PRIVATE KEY-----
```

`BEGIN RSA` 〜 `END RSA`の行まで全てをコピー

githubのSecretにペーストする

## PassPhrase

Name: `SSH_PASSPHRASE`

Secret: [SSHキーを発行](./3_setup_x_control.md)で作成したSSHキーファイルのパスフレーズを入力

## Port

Name: `SSH_PORT`

Secret: `10022`固定

## ドメイン

Name: `SITE_DOMAIN`

Secret: ドメイン名をそのまま。`www`や`stg`は付けない

# テスト

`main`ブランチで適当に編集してコミット、プッシュする。

githubの `Actions`タブで自動デプロイが実行されているはずなので終わったら結果を確認。

失敗した場合、Secretの登録にミスがあると思われる

`stg`ブランチでもチェックする
