# 多言語化対応の設定

## 設定ファイル

```
config/packages/translation.yaml
```

デフォルト言語を設定

```yaml
framework:
  default_locale: en
  translator:
    default_path: '%kernel.project_dir%/translations'
    fallbacks:
      - en
```

`en`のところを`ja`に書き換え

## URLで言語を指定できるようにする

```
https://example.com/en/news
```

のようにURLに言語を含められるように設定する

```
packages/routes.yaml
```

```yaml
controllers:
  - resource: ../src/Controller/
+   resource: ../src/Controller/Mvc
  type: attribute
  // 使う言語を羅列
+   prefix:
+       ja: ''
+       en: '/en'
+       tw: '/tw'

  // 多言語化しない部分を設定
+admin:
+    resource: ../src/Controller/Admin/
+    type: attribute
```

## メッセージファイルを作成する

各言語のファイルを作成

```
translations/
    messages.ja.yaml
    messages.en.yaml
    messages.tw.yaml
```

### 内容を記載

日本語

```
messages.ja.yaml

top:
  title: トップページ
```

英語

```
messages.en.yaml

top:
  title: Top page
```

中国語

```
messages.tw.yaml

top:
  title: 首页
```

## テンプレートに記述

```
templates/pages/index.html.twig

<h1>{{ 'top.title'|trans }}</h1>
```

## 現在の言語を取得する

### コントローラ内など

`Request`オブジェクトの`getLocale()`で取得できる

```php
$locale = $request->getLocale();
// en
```

### テンプレート内

```
{{ app.request.locale }}

{% if app.request.locale == "en" %}English{% endif %}
```

## 現在の言語をリクエストオブジェクトなしで取得できるようにする

Entityクラス内などでリクエストオブジェクトが使えない場所で、現在の言語を取得したい場所がある。

### Utilクラスを作成

```php
src/Utils/LocaleUtil.php

<?php

namespace App\Utils;

class LocaleUtil
{
    public const EN = "英語";
    public const TW = "中国語";

    static public string $locale;
    static public function setLocale(string $locale): void
    {
        self::$locale = $locale;
    }
}
```

### Symfony Kernelで設定

```php
src/EventListener/KernelEventListener.php

    public static function getSubscribedEvents(): array
    {
        return [
+           KernelEvents::CONTROLLER => ['onKernelController', 9999],
            KernelEvents::REQUEST => ['onKernelRequest', 9999],
            KernelEvents::RESPONSE => ['onKernelResponse', 9999],
            KernelEvents::EXCEPTION => ['onKernelException', 9999]
        ];
    }

+   public function onKernelController(ControllerEvent $event): void
+   {
+       $request = $event->getRequest();
+       LocaleUtil::setLocale($request->getLocale());
+   }
```

### 利用方法

多言語を取得するメソッドのトレイトを準備

```php
src/Entity/Traits/TranslateTrait.php

<?php

namespace App\Entity\Traits;

use App\Utils\LocaleUtil;

trait TranslateTrait
{
    protected function translate(string $propertyName): string
    {
        $local = LocaleUtil::$locale;
        if($local === "ja") {
            return (string) $this->$propertyName;
        }
        $localPropertyName = $propertyName. ucfirst($local);
        if(!isset($this->$localPropertyName)) {
            return (string) $this->$propertyName;
        }
        $value = trim($this->$localPropertyName);
        if(!$value) {
            return (string) $this->$propertyName;
        }
        return (string) $this->$localPropertyName;
    }
}
```

CMSのエンティティに追加

```php
src/Entity/News/Entry.php

+use App\Entity\Traits\TranslateTrait;

class Entry
{
+    use TranslateTrait;


    /**
     * タイトルEnglish
     * @var string|null 
     */
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $titleEn = null;

    /**
     * タイトル中国語
     * @var string|null
     */
    #[ORM\Column(length: 128, nullable: true)]
    private ?string $titleTw = null;
    
    public function getTitleTrans(): string
    {
        return $this->translate("title");
    }
}
```

### 使用方法

```
<h2>
  {{ entry.titleTrans }}
</h2>
```

# 公式ドキュメント

[https://symfony.com/doc/current/translation.html](https://symfony.com/doc/current/translation.html)
