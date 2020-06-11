toridashiWikipedia
================

**toridashiWikipedia** は、WikipediaのXMLデータからタイトルとカテゴリをサクッと抽出するためのスクリプトです。

自然言語処理を行いたい時なんかに使えます。


使い方
-----

``` shell
# 最新のWikipediaデータをダウンロードする
curl https://dumps.wikimedia.org/jawiki/latest/jawiki-latest-pages-articles.xml.bz2 -o jawiki-latest-pages-articles.xml.bz2

# 解凍する
bunzip2 jawiki-latest-pages-articles.xml.bz2

# toridashiWikipediaのソースをクローンしてくる
git clone https://github.com/hoku/toridashiWikipedia.git

# WikipediaのXMLからタイトルとカテゴリを抽出する
php toridashiWikipedia/GetWordsFromWikipedia.php jawiki-latest-pages-articles.xml out 5000

# 待っていると、outディレクトリに抽出結果が吐き出される
cd out
```


実行パラメータ
-----------

* 第1引数 : WikipediaのXMLファイルパス
* 第2引数 : 抽出結果の出力先ディレクトリパス
* 第3引数 : 出力時の1ファイル当たりのデータ件数 (オプション。デフォルトは5000。)


抽出結果
-------

tsvファイルで出力されます。

* タイトルファイル:
  * 「word_XXXX.tsv」というファイル名で出力されます。
  * 1列目にタイトル、2列目以降にカテゴリが入ります。


* カテゴリファイル:
  * 「cat_XXXX.tsv」というファイル名で出力されます。
  * 1列目にカテゴリ名、2列目以降にカテゴリが入ります。
  * 2列目以降のデータを上手く使うことで、カテゴリの階層構造を取得できます。


ライセンス
-------

MIT License.
