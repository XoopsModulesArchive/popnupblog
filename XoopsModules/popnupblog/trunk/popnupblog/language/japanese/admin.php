<?php
// $Id: admin.php,v 1.1.1.1 2005/08/28 02:13:09 yoshis Exp $ 
// For Menu
define('_AM_POPNUPBLOG_PERMITIONERR', '書き込み権限がありません。');
define('_AM_PREFERENCES','一般設定');
define('_AM_POPNUPBLOG_GOMOD','モジュール画面へ');
define('_AM_POPNUPBLOG_SUPPORTSITE','サポート・サイト');
// For Main
define('_AM_POPNUPBLOG_CREATE', '新規作成');
define('_AM_POPNUPBLOG_EDIT', '編集');
define('_AM_POPNUPBLOG_OPERATION', '操作');
define('_AM_POPNUPBLOG_APPLICATED_USER', 'ブログ申請者');
define('_AM_POPNUPBLOG_BLOG_TITLE', 'ブログ・タイトル');
define('_AM_POPNUPBLOG_REJECT_APPLICATION', '却下');
define('_AM_POPNUPBLOG_USER_LIST', 'ユーザ一覧');
define('_AM_POPNUPBLOG_PERMISSION', 'グループ別権限設定');
define('_AM_POPNUPBLOG_LASTUPDATE', '最終更新日');
define('_AM_POPNUPBLOG_BLOG_EMAIL', '投稿用メールアドレス');	//add Yoshi.S
define('_AM_POPNUPBLOG_SEND_EMAIL', '配信用メールアドレス');	//add Yoshi.S
define('_AM_POPNUPBLOG_MLFUNCTION', 'ＭＬ機能');
define('_AM_POPNUPBLOG_ACTIVATE', '有効にする');
define('_AM_POPNUPBLOG_POPSERVER', 'POPサーバ');
define('_AM_POPNUPBLOG_POPUSER', 'POPユーザ');
define('_AM_POPNUPBLOG_POPPASSWORD', 'POPパスワード');
define('_AM_POPNUPBLOG_POPADDRESS', 'POPメールアドレス');
define('_AM_POPNUPBLOG_ALLOW_APPLICATION', '承認');
define('_AM_POPNUPBLOG_NEW_VERSION', '新しいバージョンがリリースされています！');
define('_AM_POPNUPBLOG_NAME', '名前');
define('_AM_VOTE', '投票');
define('_AM_COMMENT', 'コメント');
define('_AM_READ', '閲覧');
define('_AM_POPNUPBLOG_UID', 'uid');
define('_AM_POPNUPBLOG_UNAME', 'ユーザ名');
define('_AM_POPNUPBLOG_POST', '投稿');
define('_AM_POPNUPBLOG_GROUPID', 'ブログ・グループ');
define('_AM_POPNUPBLOG_PLUGIN', 'プラグイン：');
define('_AM_POPNUPBLOG_CANTADDEMAIL', 'そのメールアドレスは既に登録されています！');
//%%%%%%	Ported from newbb admin %%%%%
define('_AM_ADMIN','管理者');
define('_AM_PRIVATE','プライベート(管理者)');
define('_AM_POPNUPBLOG_FORM_ADMIN_USAGE','下のリストに追加したユーザがプライベート利用できます。');
define('_AM_MODERATOR','モデレータ');
define('_AM_BLOGCONF','ブログ設定');
define('_AM_BLOGDESCRIPTION','ブログの説明');
define('_AM_ADDACAT','カテゴリの追加');
define('_AM_LINK2ADDCAT','ブログのカテゴリを新規に追加します。');
define('_AM_EDITCATTTL','カテゴリの編集');
define('_AM_LINK2EDITCAT','ブログのカテゴリ名を編集します。');
define('_AM_RMVACAT','カテゴリの削除');
define('_AM_LINK2RMVCAT','ブログのカテゴリを削除します。');
define('_AM_REORDERCAT','カテゴリの配置変更');
define('_AM_LINK2ORDERCAT','ブログカテゴリの表示順序を変更します。');
define('_AM_CONFIRM','確認');
define('_AM_ADD','追加');
define('_AM_REMOVE','削除');
define('_AM_NONE','なし');
define('_AM_CATEGORY','カテゴリ');
define('_AM_EDIT','編集');
define('_AM_CATEGORYUPDATED','カテゴリーを更新しました。');
define('_AM_EDITCATEGORY','編集するカテゴリ：');
define('_AM_CATEGORYTITLE','カテゴリ名：');
define('_AM_SELECTACATEGORYEDIT','編集するカテゴリを選択');
define('_AM_CATEGORYCREATED','新規カテゴリを作成しました。');
define('_AM_NTWNRTFUTCYMDTVTEFS','注意: カテゴリ下のブログは削除されません。ブログの削除は個別に行って下さい。');
define('_AM_REMOVECATEGORY','カテゴリを削除');
define('_AM_CREATENEWCATEGORY','新規カテゴリの作成');
define('_AM_CATEGORYMOVEUP','カテゴリを移動しました');
define('_AM_TCIATHU','選択されたカテゴリは既に一番上に配置されています');
define('_AM_CATEGORYMOVEDOWN','カテゴリを移動しました');
define('_AM_TCIATLD','選択されたカテゴリーは既に一番下に配置されています');
define('_AM_SETCATEGORYORDER','カテゴリ表示位置の設定');
define('_AM_TODHITOTCWDOTIP','<br />トップページにおけるカテゴリの表示位置の設定を行います。カテゴリの位置を上に移動する場合は「上に移動」ボタンをクリック、下げる場合は「下に移動」ボタンをクリックして下さい。');
define('_AM_ECWMTCPUODITO','１回クリックする度に位置が1つ移動します。');
define('_AM_CATEGORY1','カテゴリ');
define('_AM_MOVEUP','上に移動');
define('_AM_MOVEDOWN','下に移動');
define('_AM_CLEAR','クリア');
define('_AM_SAVECHANGES','変更を保存');
define('_AM_CATEGORYDELETED','カテゴリを削除しました。');
// After V3.0
define('_AM_POPNUPBLOG_MAILPRIFIX_DESC', '投稿の際は、\'b%s,\'をメール・タイトルの先頭に追加して下さい。');
define('_AM_POPNUPBLOG_ML_DESC', '(ML用アドレスへの投稿はプリフィックス不要です。)');
define('_AM_AUTOAPPROVE','管理者の介在しない新規投稿の自動承認');
define('_AM_WAITING','承認待ちの投稿');
define('_AM_LINK2WAITING','承認待ちの投稿を編集します。');
define('_AM_NEWSUB','新規投稿記事');
define('_AM_NEWCOMMENT','新規コメント');
define('_AM_TITLE','タイトル');
define('_AM_POSTED','投稿日時');
define('_AM_POSTER','投稿者');
define('_AM_ACTION','管理');
define('_AM_DELETECOMMENT', '削除実行');
define('_AM_TRACKBACKS','トラックバック');
define('_AM_LINK2TRACKBACKS','トラックバックを管理します。');
?>
