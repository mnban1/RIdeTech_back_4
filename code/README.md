# 予約管理マネージャー — Laravel 実装

設計ドキュメント（`../README.md` 参照）をもとに、Laravel 11 の MVC モデルで構築した予約管理システムです。

## ディレクトリ構成

```
code/
├── app/
│   ├── Http/Controllers/   コントローラ
│   └── Models/             Eloquent モデル (13テーブル)
├── database/
│   └── migrations/         マイグレーション (13ファイル)
├── resources/
│   └── views/              Blade テンプレート
└── routes/
    └── web.php             ルート定義
```

## セットアップ

このフォルダの中身は **既存の Laravel プロジェクトに統合する形** で利用します。

### 1. Laravel プロジェクトの新規作成

```bash
composer create-project laravel/laravel reservation-manager
cd reservation-manager
```

### 2. このフォルダの中身を統合

```bash
# code/ の中身を Laravel プロジェクトのルートにコピー
cp -r /path/to/code/. ./
```

### 3. 環境設定

`.env` を作成して MySQL の接続情報を設定:

```bash
cp .env.example .env
php artisan key:generate
```

`.env` の DB 設定:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=reservation_manager
DB_USERNAME=root
DB_PASSWORD=
```

### 4. マイグレーション実行

```bash
php artisan migrate
```

13テーブルが作成されます（依存順に実行されるよう日時を調整済み）:

```
users → categories → shops → staffs → courses
→ reservations → memos → conversations → messages
→ reviews → products → orders → order_items
```

### 5. 開発サーバ起動

```bash
php artisan serve
```

http://localhost:8000 でアクセスできます。

## 主な実装ポイント

### 認証

`users` テーブルに `role` カラム（`user` / `shop_admin`）を持ち、ロールベースで権限分岐します。Laravel Breeze や Fortify で認証画面を追加可能。

### ダブルブッキング防止

`reservations` テーブルに `(staff_id, reserved_at)` の UNIQUE 制約があるため、DBレベルで二重予約を防ぎます。

### 論理削除（SoftDelete）

全テーブルに `deleted_at` を持たせ、Eloquent の `SoftDeletes` トレイトで論理削除に対応。誤削除しても `restore()` で復元可能。

### CHECK 制約

価格 `>= 0`、評価 `1〜5` 等は DB レベルでも担保。

## 関連ドキュメント

- `../RideTech_4-1.pdf` — 題材決定資料
- `../自主制作.pdf` — 5W2H + 機能要件表（F-001〜F-015）+ 画面要件表
- `../テーブル定義書.pdf` — DB スキーマ詳細
- `../wireframes.html` — 13画面のワイヤーフレーム
- Figma — UI / 画面遷移図 / ER図 / 命名規則
