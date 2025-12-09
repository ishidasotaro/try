# スケジュール統合カレンダー

PHP 単一ページで動作する、カレンダー投稿・予定登録・空き時間算出のデモアプリです。

## 使い方
1. `index.php` をブラウザで開きます（Docker/PHP 内でホストするか、PHP のビルトインサーバーで `php -S 0.0.0.0:8000` などを利用）。
2. 「AI認識モックを実行」を押すとサンプル予定が今週の日付に登録されます。
3. 任意の予定を「予定の手動調整」フォームから登録すると集約カレンダーに即時反映され、08:00-20:00 の空き時間が日別に表示されます。
4. アップロード欄からカレンダー画像を追加するとプレビューされ、将来的な OCR/AI 連携の導線として利用できます。

## Docker での起動手順
開発・動作確認用に PHP + Apache ベースの簡易 Dockerfile を同梱しています。

```sh
# ビルド
docker build -t schedule-demo .

# 起動 (ポート 8080 でホストへ公開)
docker run --rm -p 8080:80 schedule-demo
```

ブラウザで `http://localhost:8080` にアクセスするとアプリが表示されます。

### Docker Compose を使う場合の例
MySQL などの周辺サービスと一緒に立ち上げたい場合は、以下をプロジェクトルートに作成して利用できます。

```yaml
services:
  app:
    build: .
    ports:
      - "8080:80"
    volumes:
      - .:/var/www/html:ro
    depends_on:
      - db
  db:
    image: mysql:8.0
    environment:
      MYSQL_ROOT_PASSWORD: example
      MYSQL_DATABASE: schedule
```

実行手順:

```sh
# バックグラウンドで起動
COMPOSE_PROJECT_NAME=schedule-demo docker compose up -d

# 終了
COMPOSE_PROJECT_NAME=schedule-demo docker compose down
```

現状のアプリは DB を利用していませんが、今後のデータ永続化用に MySQL を同時起動する際の参考例として記載しています。

## 主な機能
- カレンダー投稿（画像プレビュー + AI 連携のモック）
- 予定の手動登録・上書き
- 週次（本日含む 7 日間）の予定一覧と空き時間算出

本アプリは OCR/AI 部分を PHP/JS で組み込むための UI 土台として利用できます。
