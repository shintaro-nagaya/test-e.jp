# 問合せフォームの生成

以下コマンドで雛形を生成

```
php bin/console app:generate:inquiry
```

対話で以下を設定
```
名称 (eq. 資料請求) : 資料請求
名前空間 (eq. Document , Campaign\Register ): Inquiry\Document
DBテーブルのプリフィクス (eq. inquiry) : 
```

この時点ではメールアドレスのみの問合せフォームとなっている

## 項目を追加する

以下コマンドでEntityクラスにカラムを追加する
```
php bin/console make:entity
```

実行すると
```
 Class name of the entity to create or update (e.g. OrangePuppy):
 > 
```
とくるので、対象のエンティティクラスの名前空間を指定する
上記の場合は
```
Inquir\Document\Data
```
を入力する
(`App\Entity\`は省略する)

続いて
```
 New property name (press <return> to stop adding fields):
 > 
```
と来るので追加項目を設定する。ここでは`名前`を設定してみる
```
 New property name (press <return> to stop adding fields):
 > name
 
 Field type (enter ? to see all types) [string]:
 > string
 
 Field length [255]:
 > 128
 
 Can this field be null in the database (nullable) (yes/no) [no]:
 > yes

 updated: src/Entity/Inquiry/Document/Data.php

 Add another property? Enter the property name (or press <return> to stop adding fields):
 > 追加の項目がある場合は次の項目の名前、なければ空のままエンター
```
上記では
`name`というカラム名で`string(文字列)`型、文字数は128文字、null許可
で追加した。

型は他にもいろいろあるが
```
integer (整数)
float (浮動少数)
text (複数行テキスト)
date (日付)
datetime (日付+時間)
relation (マスター選択肢などのリレーション型)
```
あたりをよく使う

## Formに項目を追加

```
src/Form/Inquiry/Document/DataType.php
```
ここにフォーム定義を追加する

```
+use Symfony\Component\Form\Extension\Core\Type\TextType;

....

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addRequiredTypes($builder);
        
+       $builder->add('name', TextType::class, [ // TextType で <input type="text">
+           "label" => "氏名", // フォームのラベル
+           "attr" => [
+               "maxlength" => 128 // <input type="text" maxlength="128>
+           ],
+           "constraints" => [
+               new NotBlank([ // サーバーサイドで必須入力チェックをする
+                   "message" => "入力されていません"
+               ])
+           ]
+       ]);
    }

```

`$builder->add()`でフォーム定義していく。ドキュメントは ↓↓↓

https://symfony.com/doc/current/reference/forms/types.html

## マイグレーション

エンティティとフォームに定義できたらマイグレーションを行う

```
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## フォームに追加

```
templates/pages/inquiry/document/index.html.twig
```
コンポーネントの作り方次第だが、

```
{{ form_start(form, {
  action: path('inquiry_document_confirm')
}) }}

  {% include "components/form/_row.html.twig" with {
    form: form.email
  } %}
+ {% include "components/form/_row.html.twig" with {
+   form: form.name
+ } %}

  {% include "components/_google_recaptcha.html.twig" %}
  
  <button type="submit">確認画面へ</button>

{{ form_end(form) }}
```
とする

確認ページにも追加する
```
{{ form_start(confirmForm, {
  action: path('inquiry_document_send')
}) }}

  {% include "components/form/_row.html.twig" with {
    form: form.email,
    value: form.email.vars.value
  } %}
+ {% include "components/form/_row.html.twig" with {
+   form: form.name,
+   value: form.name.vars.value
+ } %}
  
  <a href="{{ path('inquiry_document_index') }}">戻る</a>
  <button type="submit">送信する</button>
  {% include "components/_google_recaptcha.html.twig" %}

{{ form_end(confirmForm) }}
```

また、pardot連携の場合は `send.html.twig`にも追加

```
<form
  {% if app.environment == "prod" %}
  action="{# Pardot endpoint url #}"
  {% else %}
  action="{{ path('inquiry_document_pardot_mock')}}"
  {% endif %}
  method="post"
  class="js_pardot-form"
>
  <input type="hidden" name="email" value="{{ inquiry.email }}">
+ <input type="hidden" name="name" value="{{ inquiry.name }}">
  <p class="c_pardot-form__message js_pardot-form__message">
    ただいま送信中です。送信されない場合<a class="c_pardot-form__message--send js_pardot-form__message--send">こちら</a>を押してください
  </p>
</form>

```

配信メールにも追加
```
templates/mail/inquiry/document/_body.txt.twig

{{ form.email.vars.label }} : {{ form.vars.value.email }}
+{{ form.name.vars.label }} : {{ form.vars.value.name }}
```

管理ページの履歴にも追加
```
templates/admin/inquiry/document/data/index.html.twig

...
{% for entry in data %}
<div class="modal fade" id="modal{{ entry.id }}" tabindex="-1" aria-labelledby="modal{{ entry.id }}" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-body">
        <div class="mb-2">
          <label>{{ dataForm.email.vars.label }}</label>
          <span>{{ entry.email }}</span>
        </div>
+       <div class="mb-2">
+         <label>{{ dataForm.name.vars.label }}</label>
+         <span>{{ entry.name }}</span>
+       </div>
      </div>
    </div>
  </div>
</div>
{% endfor %}
```

