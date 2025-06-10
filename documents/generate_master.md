# マスター生成

マスターとして利用するエンティティとCRUD機能を生成する

```
php bin/console app:generate:master
```

設備マスターを生成する例で解説していく

## 対話

- 名称
- 名前空間
- クラス名
- DBテーブルのプレフィクス

を設定する。

```
名称 (eq. 設備マスター) : 設備マスタ
名前空間 (eq. Master\Estate) : Master
クラス名 (eq. Equipment) : Equipment
DBテーブルのプリフィクス (eq. master) : mst
```

## エンティティとリポジトリ

ここでは
- src/Entity/Master/Equipment.php
- src/Repository/Master/EquipmentRepository.php

が生成される

### Entity Class
生成されたクラスは
```
App\Entity\Interfaces\MasterEntityInterface
```
インターフェイスを実装していて
```
App\Entity\Traits\MasterEntityTrait
```
を実装することでそのままDBカラムとして定義される。
 
- name 名称
- enable 有効フラグ
- sort 順番

### Repository Class
エンティティと対になるリポジトリクラスは
```
App\Repository\Interfaces\MasterEntityRepositoryInterface
App\Repository\Interfaces\AdminIndexInterface
```
を実装し、マスターとしてのメソッドを定義している。
管理ページでのCRUDが必要ない場合`AdminIndexInterface`は外しても良い

メソッドの実装は
```
App\Repository\Traits\MasterEntityRepositoryTrait
```
にて行われる

## 管理ページCRUD

以下ファイルでCRUDページを実装

- src/Service/Entity/Master/EquipmentService.php CUDの処理などの実装を行う
- src/Form/Admin/Master/Equipment/EquipmentType.php 登録画面でのフォーム定義
- src/Form/Admin/Master/Equipment/SearchType.php 管理画面一覧ページでの検索フォーム定義
- src/Form/Admin/Master/Equipment/SortType.php 管理画面一覧ページでの順番を登録するフォーム定義
- src/Controller/Admin/Master/EquipmentController.php 管理画面CRUDのコントローラ
- templates/admin/master/equipment/index.html.twig 一覧のView
- templates/admin/master/equipment/form.html.twig 入力フォームのView

(以下執筆中...)