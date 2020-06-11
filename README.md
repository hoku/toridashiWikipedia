toridashiWikipedia
================

**toridashiWikipedia** は、WikipediaのXMLデータからタイトルとカテゴリをサクッと抽出するためのスクリプトです。
PHP製です。


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
ls -al
```


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
