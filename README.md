# SyncDiscordXbox
DiscordアカウントとXboxアカウントを紐づけるやつ

## ウェブサイトのデプロイ(展開)手順
### リポジトリのクローン
```bash
git clone git@github.com:NeiroNetwork/SyncDiscordXbox.git
```
### `.env` ファイルを作成 (コピー)
```bash
cp sample.env .env
chmod o-r .env
```
### 依存関係をインストール
本番環境用にフラグを追加しています
```bash
composer install --no-dev --prefer-dist --classmap-authoritative
```
### データベースのマイグレーション
<a href="#データベースの設定について">データベースの設定について</a> も参照
```bash
php scripts/database.php
```
### 公開
下に記載されているアプリケーションやボットのセットアップが済んだら、nginxなどのソフトウェアで `public/` 以下を公開します。

## OAuth2アプリケーションのセットアップ方法
### Xbox Live OAuth2
1. https://go.microsoft.com/fwlink/?linkid=2083908 にアクセス
2. "新規登録" をクリック
3. アプリケーションの登録を行う
   - 名前を入力する
   - サポートされているアカウントの種類は "個人用 Microsoft アカウントのみ" を選択
   - プラットフォームは "Web" を選択し、リダイレクト URI を入力する
4. "証明書とシークレット" タブをクリック
5. "新しいクライアント シークレット" をクリックしてクライアントシークレットを作成する
   - 説明は入力しなくても良い、期間は自由に設定する
   - 生成された値をコピーしておく
6. 以下の値を `.env` ファイルに記入する
   - アプリケーション(クライアント)ID: `XBL_CLIENT_ID`
   - クライアントシークレット: `XBL_CLIENT_SECRET`
   - リダイレクトURI: `XBL_REDIRECT_URI`

### Discord OAuth2
1. https://discord.com/developers/applications にアクセス
2. "New Application" をクリック、名前を入力してアプリケーションを作成する
3. OAuth2 タブ → General に移動する
   - "Reset Secret" をクリックしてクライアントシークレットを再生成
   - クライアントシークレットはコピーしておく
   - "Add Redirect" をクリックしてリダイレクトURIを追加
4. 以下の値を `.env` ファイルに記入する
   - CLIENT ID (APPLICATION ID): `DISCORD_CLIENT_ID`
   - CLIENT SECRET: `DISCORD_CLIENT_SECRET`
   - (Redirects) URI: `DISCORD_REDIRECT_URI`

### Discord Bot
サーバーでロールを付与するためのボットを作成する
1. Bot タブ → "Add Bot" をクリックしてボットを作成
2. PUBLIC BOT (誰でもボットをサーバーに追加できる) はOFFにしておく
3. サーバーのメンバー更新通知に "SERVER MEMBERS INTENT" が必要なのでONにしておく
4. "Reset Token" をクリックしてトークンを再生成する
   - 生成されたトークンはコピーしておく
5. https://discord.com/api/oauth2/authorize?client_id=0000000000000000000&permissions=402653184&scope=bot でボットをサーバーに参加させる
#### 注意すべきこと
- ボットのロールがメンバーのロールより高くなっている必要がある
- オーナー又は上位のロールが付与されたユーザーのニックネームは編集できない (https://stackoverflow.com/q/45251598)
  - ロールの管理者権限は関係なく、あくまでもロールの順位に依存する

## データベースの設定について
データベースにはMySQLまたはSQLite3を使用します。
### MySQL を使用する場合
`.env` のそれぞれの値を編集します。
- `DB_DRIVER=mysql`: ドライバーにMySQLを使用します
- `DB_HOST`: データベースのホストアドレス
- `DB_DATABASE`: データベースの名前
- `DB_USERNAME`: データベースにアクセスするためのユーザー名
- `DB_PASSWORD`: ユーザーのパスワード
### SQLite3 を使用する場合
`.env` のそれぞれの値を編集します。
- `DB_DRIVER=sqlite`: ドライバーにSQLite3を使用します
- `DB_DATABASE`: データベースのファイルパス (バグを防ぐためフルパスを指定することをお勧めします。)
データベースファイルが存在しない場合は、ファイルを作成します(コマンドは例です)。
```bash
touch database.sqlite
```
### テーブルの作成(マイグレーション)、削除
```bash
# データベースの作成
php scripts/database.php up

# データベースの削除
php scripts/database.php down
```

## FingerprintJS Pro
ウェブフックURL: `https://example.com/webhook/identification.php?key=WEBHOOK_RANDOMの値` と設定する

## ローカルでのテスト
ポートは`8080`から好きなポートに変更可能です。
1. 各種アプリケーションのリダイレクトURIに `http://localhost:8080` を追加
2. `.env` のリダイレクトURIも `http://localhost:8080` に設定
3. テストサーバーを起動する
```bash
php -S localhost:8080 -t public
```

## ボットの実行
`.env` ファイルを書き込み、コマンドを実行します。
```bash
php bot/RoleGiver.php
```