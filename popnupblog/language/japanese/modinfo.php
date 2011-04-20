<?php
// $Id: modinfo.php,v 1.1.1.1 2005/08/28 02:13:09 yoshis Exp $ 
define('_MI_POPNUPBLOG_APPL_DESC', 'ユーザからのブログ開設申請を受付けるかどうか');
define('_MI_POPNUPBLOG_1_LINE', '最近更新されたブログ');
define('_MI_POPNUPBLOG_CONF_DESC', '説明');
define('_MI_POPNUPBLOG_TRACKBACK', 'トラックバック');
define('_MI_POPNUPBLOG_REWRITE_TITLE', 'Apache rewrite engineを使用する');
define('_MI_POPNUPBLOG_NAME', 'PopnUpブログ');
define('_MI_POPNUPBLOG_DESC', 'POPメールサーバからUPできるブログです');
define('_MI_POPNUPBLOG_UNUSE_UPDATE_PING', '更新Pingを不使用');
define('_MI_POPNUPBLOG_UNUSE_TRACKBACK', 'トラックバック不使用');
define('_MI_POPNUPBLOG_APPL_WAITING_TITLE', 'Popnupブログ承認待ち');
define('_MI_POPNUPBLOG_NAME_BIG_BLOCK', 'Popnupブログ');
define('_MI_POPNUPBLOG_USE_REWRITE', 'Rewrite使用');
define('_MI_POPNUPBLOG_UPDATE_PING', '更新Pingを利用');
define('_MI_POPNUPBLOG_1_LINE_DESC', '最新リストの1行表示ブロック');
define('_MI_POPNUPBLOG_REWRITE_DESC', 'Rewriteを使用すると /modules/popnupblog/view/index.php?uid=1 =&gt; /modules/popnupblog/view/1.html になります。(エキスパート用)');
define('_MI_POPNUPBLOG_APPL_WAITING', '承認待ち');
define('_MI_POPNUPBLOG_UPDATE_PING_DESC', '更新Pingを利用');
define('_MI_POPNUPBLOG_WRITE','ブログを書く');
define('_MI_POPNUPBLOG_PREFERENCE', '設定');
define('_MI_POPNUPBLOG_APPLY', '申請');
define('_MI_POPNUPBLOG_TRACKBACK_DESC', 'トラックバックを使用するか');
define('_MI_POPNUPBLOG_UNUSE_REWRITE', 'Rewrite不使用');
define('_MI_POPNUPBLOG_APPL_DENY', '申請不可能');
define('_MI_POPNUPBLOG_CONFIG_RSS_DEF', 'ユーザが自由に書けるPopnupなブログ');
define('_MI_POPNUPBLOG_USE_TRACKBACK', 'トラックバック使用');
define('_MI_POPNUPBLOG_APPL_ALLOW', '申請可能');
define('_MI_POPNUPBLOG_APPL_OK', 'ユーザ申請あり・なし');
define('_MI_POPNUPBLOG_USE_UPDATE_PING', '更新Pingを使用');
define('_MI_POPNUPBLOG_CONFIG_RSS_DESC', 'RSSで公開されるPopnupブログの説明です');
// Add 2006.02.02 by yoshis
define('_MI_POPNUPBLOG_FILECHRSET', '添付ファイル名称コード');
define('_MI_POPNUPBLOG_FILECHRSET_DESC', 'サーバ保存時のファイル名に使う文字コードを設定します(SJIS,UTF-8,EUC-JP等)');
// Add 2004.10.27 by yoshis
define('_MI_POPNUPBLOG_MAILSERVER', 'メール・サーバ');
define('_MI_POPNUPBLOG_MAILSERVER_DESC', '受信用メールのPOP3サーバを設定します');
define('_MI_POPNUPBLOG_MAILUSER', 'メール・ユーザー');
define('_MI_POPNUPBLOG_MAILUSER_DESC', '受信用メールのユーザー名を設定します');
define('_MI_POPNUPBLOG_MAILPWD', 'メール・パスワード');
define('_MI_POPNUPBLOG_MAILPWD_DESC', '受信用メールのパスワードを設定します');
define('_MI_POPNUPBLOG_MAILADDR', 'メール・アドレス');
define('_MI_POPNUPBLOG_MAILADDR_DESC', '受信用メールのアドレスを設定します');
// Add 2005.01.22 by yoshis
define('_MI_POPNUPBLOG_GUESTBLOGID','Anonymousメールの投稿先ブログＩＤ');
define('_MI_POPNUPBLOG_ACTVTYPE','新規申請ブログの有効化の方法');
define('_MI_POPNUPBLOG_AUTOACTV','自動的にアカウントを有効にする');
define('_MI_POPNUPBLOG_ADMINACTV','管理者が確認してアカウントを有効にする');
define('_MI_POPNUPBLOG_NEWUNOTIFY','ブログ申請や承認を必要とする場合にメールにて知らせを受け取る');
define('_MI_POPNUPBLOG_SHOWNAME','ユーザ名の変わりに本名を表示する');
// Add 2006.03.21 by yoshis
define('_MI_POPNUPBLOG_GROUPSETBYUSER','ユーザによるグループ設定許可（投稿・閲覧・コメント・投票）');

// For notify
define ('_MI_POPNUPBLOG_BLOG_NOTIFY', '表示中のブログ'); 
define ('_MI_POPNUPBLOG_BLOG_NOTIFYDSC', '表示中のブログに対する通知オプション');

define ('_MI_POPNUPBLOG_GLOBAL_NOTIFY', 'モジュール全体');
define ('_MI_POPNUPBLOG_GLOBAL_NOTIFYDSC', 'ブログモジュール全体における通知オプション');

define ('_MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFY', '新規投稿');
define ('_MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYCAP', 'このブログにおいて新規投稿があった場合に通知する');
define ('_MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYDSC', 'このブログにおいて新規投稿があった場合に通知する');
define ('_MI_POPNUPBLOG_BLOG_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: ブログにて新規投稿がありました');

define ('_MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFY', '新規投稿（投稿文含む）');
define ('_MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYCAP', 'このブログにおいて新規投稿があった場合に通知する（投稿文含む）');
define ('_MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYDSC', 'このブログにおいて新規投稿があった場合に通知する（投稿文含む）');
define ('_MI_POPNUPBLOG_BLOG_NEWFULLPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: ブログにて新規投稿がありました（投稿文含む）');

define ('_MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFY', '新規コメント（コメント文含む）');
define ('_MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYCAP', 'このブログにおいて新規コメントがあった場合に通知する（コメント文含む）');
define ('_MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYDSC', 'このブログにおいて新規コメントがあった場合に通知する（コメント文含む）');
define ('_MI_POPNUPBLOG_BLOG_NEWCOMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: ブログにて新規コメントがありました（コメント文含む）');

define ('_MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFY', '新規ブログ');
define ('_MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYCAP', '新規ブログが作成された場合に通知する');
define ('_MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYDSC', '新規ブログが作成された場合に通知する');
define ('_MI_POPNUPBLOG_GLOBAL_NEWBLOG_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規ブログが作成されました');

define ('_MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFY', '新規投稿');
define ('_MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYCAP', '新規投稿があった場合に通知する');
define ('_MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYDSC', '新規投稿があった場合に通知する');
define ('_MI_POPNUPBLOG_GLOBAL_NEWPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規投稿がありました');

define ('_MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFY', '新規投稿（投稿文含む）');
define ('_MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYCAP', '新規投稿があった場合に通知する（投稿文付き）');
define ('_MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYDSC', '新規投稿があった場合に通知する（投稿文付き）');
define ('_MI_POPNUPBLOG_GLOBAL_NEWFULLPOST_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規投稿（投稿文付き）');

define ('_MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFY', '新規コメント（コメント文含む）');
define ('_MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYCAP', '新規コメントがあった場合に通知する（コメント文付き）');
define ('_MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYDSC', '新規コメントがあった場合に通知する（コメント文付き）');
define ('_MI_POPNUPBLOG_GLOBAL_NEWCOMMENT_NOTIFYSBJ', '[{X_SITENAME}] {X_MODULE}: 新規コメント（コメント文付き）');

?>
