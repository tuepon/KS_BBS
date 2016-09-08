<?php

/**
 * config.php
 *
 * @author Kosuke Shibuya <kosuke@jlamp.net>
 * @since 2016/09/08
 */
// 画像のアッップロード機能を有効にする true|false
define('BBS_FUNC_IMAGE', true);

// 掲示板のタイトル
define('BBS_TITLE', '掲示板サンプル');

// 掲示板の説明
define('BBS_DESCRIPTION', 'ここに掲示板の説明を記述します。');

// 新規スレッド投稿
define('BBS_NEW_THREAD', '新規スレッド投稿');

// 返信投稿
define('BBS_REPLY', '返信投稿');

// 返信投稿
define('BBS_REPLYING', '返信する');

// 返信数
define('BBS_REPLY_COUNT', '返信数');

// 掲示板トップへ
define('BBS_TO_TOP', '掲示板トップへ');

// 投稿する
define('BBS_POST', '投稿する');

// 検索
define('BBS_SEARCH', '検索');

// クリアする
define('BBS_CLEAR', 'クリアする');

// 新しい投稿順
define('BBS_ORDER_NEW', '新しい投稿順');

// 返信の多い順
define('BBS_ORDER_COMMENT', '返信の多い順');

// 次へ
define('NEXT', '次へ');

// 前へ
define('PREV', '前へ');

// フォームラベル - タイトル
define('BBS_POST_TITLE', 'タイトル');

// フォームラベル - お名前
define('BBS_POST_NAME', 'お名前');

// フォームラベル - 内容
define('BBS_POST_CONTENT', '内容（1,000文字以下）');

// フォームラベル - 削除キー
define('BBS_POST_DELKEY', '削除キー');

// フォームラベル - 画像
define('BBS_POST_IMAGE', '画像');

// エラーメッセージ - CSRF
define('BBS_ERR_CSRF', '二重投稿を検知したので、処理を中断しました。');

// メッセージ - 投稿成功時
define('BBS_POST_SUCCESS', '正常に登録しました。');
