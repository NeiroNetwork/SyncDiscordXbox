# SyncDiscordXbox
DiscordアカウントとXboxアカウントを紐づけるやつ

## アプリケーションのセットアップ
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
3. "Reset Token" をクリックしてトークンを再生成する
   - 生成されたトークンはコピーしておく
4. https://discord.com/api/oauth2/authorize?permissions=402653184&scope=bot&client_id=クライアントID でボットをサーバーに参加させる
#### 注意すべきこと
- ボットのロールがメンバーのロールより高くなっている必要がある
- オーナー又は上位のロールが付与されたユーザーの編集はできない (https://stackoverflow.com/q/45251598)
  - 管理者権限が付いていても下位ロールなら無視される