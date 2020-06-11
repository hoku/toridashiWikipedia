<?php
/**
 * WikipediaのXMLからタイトルとカテゴリを抽出するスクリプト。
 *
 * @license   MIT License
 * @author    hoku
 */

// 1ファイル当たりに出力するデータ件数のデフォルト値
define('DEFAULT_FLUSH_SIZE', 5000);

// 引数チェック
if (count($argv) < 3) {
    echo "ERROR: 引数が足りません。\n";
    echo "ERROR: php GetWordsFromWikipedia.php srcFilePath outDirPath [flushSize = " . DEFAULT_FLUSH_SIZE . "]\n";
    exit;
}
if (!file_exists($argv[1])) {
    echo "ERROR: 指定されたXMLファイルが存在しません。\n";
    echo "ERROR: srcFilePath => " . $argv[1] . "\n";
    exit;
}
if (!is_numeric($argv[3]) || intval($argv[3] < 1)) {
    echo "ERROR: flushSizeは1以上の数値で指定してください。\n";
    echo "ERROR: flushSize => " . $argv[3] . "\n";
    exit;
}

// 抽出実行
parseWikipedia($argv[1], $argv[2], $argv[3] ?? DEFAULT_FLUSH_SIZE);


/**
 * WikipediaのXMLからタイトルとカテゴリを抽出する。
 *
 * @param string  $srcFilePath WikipediaのXMLファイルパス
 * @param string  $outDirPath  抽出結果の出力先ディレクトリパス
 * @param integer $flushSize   1ファイル当たりに出力するデータ件数
 * @return void
 */
function parseWikipedia(string $srcFilePath, string $outDirPath, int $flushSize = DEFAULT_FLUSH_SIZE) : void
{
    ini_set('memory_limit', '2048M');
    set_time_limit(60 * 60 * 2);

    @mkdir($outDirPath, 0777, true);

    $srcFile = fopen($srcFilePath, 'r');
    if (!$srcFile) {
        fclose($srcFile);
        return;
    }

    $outputs = [];
    $outputCount = 1;
    $outputCategories = [];
    $outputCategoriesCount = 1;
    $pageTmp = [];
    while ($line = fgets($srcFile)) {
        $line = trim($line);
        if ($line === '<page>') {
            $pageTmp = [];
            $pageTmp['categories'] = [];
        } elseif ($line === '</page>') {
            if (mb_stripos($pageTmp['title'], 'Wikipedia:') !== 0 &&
                mb_stripos($pageTmp['title'], 'ファイル:') !== 0 &&
                mb_stripos($pageTmp['title'], 'プロジェクト:') !== 0 &&
                mb_stripos($pageTmp['title'], 'Help:') !== 0 &&
                mb_stripos($pageTmp['title'], 'Portal:') !== 0 &&
                mb_stripos($pageTmp['title'], 'MediaWiki:') !== 0 &&
                mb_stripos($pageTmp['title'], 'Template:') !== 0) {

                if (mb_stripos($pageTmp['title'], 'Category:') === 0) {
                    $outputCategories[] = $pageTmp;
                } else {
                    $outputs[] = $pageTmp;
                }
            }

            if (count($outputs) >= $flushSize) {
                dataFlush($outputs, $outDirPath, 'word_' . ($outputCount++));
                $outputs = [];
            }
            if (count($outputCategories) >= $flushSize) {
                dataFlush($outputCategories, $outDirPath, 'cat_' . ($outputCategoriesCount++));
                $outputCategories = [];
            }
        } elseif (mb_strpos($line, '<comment>') === 0) {
            continue;
        } else {
            preg_match_all('/\\<title\\>(.+)\\<\\/title\\>/', $line, $matches);
            if ($matches[1]) {
                $pageTmp['title'] = $matches[1][0];
            }

            preg_match_all('/\\[\\[(Category|カテゴリ|カテゴリー)\\:([^\\[\\]]+)\\]\\]/', $line, $matches);
            if ($matches[1]) {
                foreach ($matches[2] as $category) {
                    $categoryInfo = explode('|', $category);
                    $pageTmp['categories'][] = $categoryInfo[0];
                }
            }
        }
    }
    fclose($srcFile);

    // 出力しきっていないデータを出力する
    dataFlush($outputs, $outDirPath, 'word_' . ($outputCount++));

    // カテゴリ情報も出力する
    dataFlush($outputCategories, $outDirPath, 'cat_' . ($outputCategoriesCount++));
}

/**
 * 抽出データをファイルに書き込む。
 *
 * @param  array  $outputs     出力する抽出データ
 * @param  string $outDirPath  出力先ディレクトリパス
 * @param  string $outFileName 出力ファイル名
 * @return void
 */
function dataFlush(array $outputs, string $outDirPath, string $outFileName) : void
{
    $outFilePath = $outDirPath . '/' . $outFileName . '.tsv';
    @unlink($outFilePath);

    foreach ($outputs as $output) {
        $line = $output['title'] . "\t" . implode("\t", $output['categories'])."\n";
        file_put_contents($outFilePath, $line, FILE_APPEND);
    }
}
