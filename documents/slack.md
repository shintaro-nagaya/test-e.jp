# Slack通知について

https://api.slack.com/apps
から通知用APPを作成

`Create New App` - `From scratch`

`AppName`入力して`Workspace`選択

# Basic Infomation

`Add features and functionality`から
`Bots`を選択

`App Home`へ遷移する

`Review Scopes to Add`

`OAuth & Permissions`へ遷移する

まず`Redirect URLs`を設定
ローカルなら `https://localhost:8000`

次に`scopes`

`Bot Token Scopes`に

```
chat:write
chat:write.customize
chat:write.public
```
を追加

その後ページ上部へスクロール

`OAuth Tokens for Your Workspace`
の
`Install to workspace`を押す

OAuth画面に行くので`許可する`

すると`OAuth Tokens for Your Workspace`に`Bot User OAuth Token`が出るので、これを控える

`.env`に追加
```
SLACK_TOKEN={token}
```

# Slackアプリ側

上記を実行するとワークスペースのApp内に追加されている。

通知を受け取る用のチャンネルを作成する

`.env`への通知先チャンネルを追加
```
# Slackチャンネル 通知送信先
SLACK_CHANNEL=local_notifier
# エラー通知 Slackチャンネル
SLACK_ERROR_CHANNEL=local_notifier
```