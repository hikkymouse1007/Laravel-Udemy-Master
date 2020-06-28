## 基本構成は以下の記事を参考
https://qiita.com/A-Kira/items/9a03d7b230741ed7b1de

## セットアップ手順

1. ホームディレクトリに移動してdocker-composeを起動

```
docker-compose up -d
```
2. webコンテナに入る

```
docker-compose exec web bash
```
3. laravelのインストール

```
laravel new
```
4. appディレクトリにlaravelが作成されるのを確認したらMac上のに/etc/hostsにドメインを追加
```
sudo vim /etc/hosts
#/etc/hosts 
127.0.0.1 web.localhost.com web2.localhost.com
```
5. ブラウザから http://web1.localhost.com:8000/ http://web2.localhost.com:8000/
にアクセスしてLaravelのトップ画面を確認する。

6. ホームディレクトリでdockerを止める
```
docker-compose down
```

## 参考記事
nginx-proxy/nginx-proxy
https://github.com/nginx-proxy/nginx-proxy

Dockerサービスの簡単リバースプロキシ
https://qiita.com/South_/items/7bdb1f373410cb1c907b
