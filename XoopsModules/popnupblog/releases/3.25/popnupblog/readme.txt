Pop'n'Up Blog Document
-------------------------------------------------------------------------------
Module Name  : PopnupBlog
Code Name    : Denali
Auther       : Yoshi Sakai ( webmaster@bluemooninc.biz )
Company      : Bluemoon inc. ( http://www.bluemooninc.biz/ )
Start date   : 08,Mar,2004
License under: GPL 2.00 ( Donation Welcome! Contact auther. )
-------------------------------------------------------------------------------
Based By : SimpleBLOG V0.2.1RC2. http://sourceforge.jp/projects/xoops-modules/
-------------------------------------------------------------------------------
*****************
Special Thanks to
*****************
SimpleBlog by Kousuke
mailalias,world time and more by Hoshiyan
download by Nat Sakimura,funran7
trackback and ping by Kazy
Portuguesebr language by Douglas
Nederlands language by Ozzy
Polish language by Maru
Russian language by Oleg
French language by Outch
Italian language by meta99.info
V3.00 sponsored by Blue-dotters.jp / Notocord inc.

************
Introduction
************
The PopnupBlog is XOOPS 2.0.x Blog/MailingList module. You can upload easily and attach automatically! (Thumbnail too.)
And also you can post via email with attached files!! You can post several times a day.

If it combines with BM-Survey, it is applicable to all investigation reports with a any files.

*******************
Denali V3 Highlight
*******************
Supported mailing list. It can set a ML posting address as each blog. And it does not need adding a mail subject prefix as ML posting.
Bugfixed, trackback and RSS feed.

*******************
Release Information
*******************
V3.00 2007/01/10 Launch out V3.
V3.01 2007/02/03 Bugfix for URL return after update blog and delete referer at edit.php
V3.02 2007/02/04 Bugfix for xoops [code] tag at include/sanitize.php
V3.03 2007/02/09 Bugfix for clear the ML address at class/popnupblog.php(setBlogInfo)
                 Bugfix for get the list of pop information at class/bloginfo.php(get_PopAccessInfo)
V3.04 2007/02/14 Supported for mb_encode_mimeheader bug. Added CR to mail subject under the PHP 4.4.0/4.4.1. Other versions use the mb_encode_mimeheader at class/sendmail.php(enc_mimeheader)
V3.05 2007/03/08 Supported for blog owner can delete a post.
V3.06 2007/04/06 Add auto string for blank title at setBlogInfo(). Add $BlogCNF['autoForMail'] for mb_convert_encoding as auto expanding.
V3.07 2007/04/15 Security update for "postid" parameter as SQL injection. Reported by Secunia Advisory:SA24761.
V3.08 2007/05/01 Supporting hide group permissions at application posting.
V3.09 2007/06/23 Modify: Comment ML subject is more simply as "Re: POST TITLE". That was loop back long strings.
Modify: head_n.cgi changed to header.cgi. It just put into single log file. ($head_prifix changed to $headerlog)
Support: Use real name for comment return when set real name mode at module admin.
V3.10 2007/06/30 Added new class as download.class.php. Then ext2mime.php move into download.class.php.
Bugfix: ML sending with multi-binary name files.
V3.11 2007/07/08 Bugfix: Posted to the irregular blog number when received two or more mails at one single task.(Not at ML receiving)
V3.12 2007/07/19 Admin can delete extinct member's blog.
V3.13 2007/08/30 Fix for Remote File Inclusion Vulnerability on class/sendmail.php.
V3.14 2007/09/26 Bugfix: serch.inc.php. It doesn't work without comment.
V3.15 2007/10/04 Fix: At emailalias.php get_uid_bymail(). Return uid as match as email address on xoops user table.
V3.16 2007/10/17 Bugfix: group permission setting by user. ( It was all cleared. )
V3.17 2007/11/09 Bugfix: Lost the uid when posting by email to the guest can post blog. Fix: _MD_POPNUPBLOG_TITLE_PREFIX appear at blog title when it has no title. So that move from php script to html smarty, and change name to _MB_POPNUPBLOG_TITLE_PREFIX. Fix: comment class is very conflict name, so it changed to pb_comment.
V3.18 2007/12/20 Fix: Blog owner can change the preference when "Allow to application for user" off at admin preference.Blog owner can delete any posted messages. User can click the poster name and jump to userinfo at blog.
V3.19 2008/02/01 Bugfix: The fatal error occurred when display of deleted user's comment.
V3.19a 2008/02/08 Update: Langage files of Nederlands(Thx, G.Arjan) and English.
v3.20 2008/04/25 Security Update for XSS Vulnerability at download.php. Add uid parameter at index.php for someones blog.
v3.21 2008/08/28 Added more sanitization to index.php and class/PopnupBlogUtils.php
v3.22 2009/02/18 Added new template popnupblog_submit.html. MarkItUp and Lightbox jQuery plugin was supported.
V3.23 2009/03/02 Bugifx: At download.php, it couldn't send html header. Template fix for IE6.
V3.24 2009/10/26 Renew: Simplify last update block and other templates.
V3.25 2009/12/02 Supported: User can share writing as a group post permission.

*******************
Update v2.5 to v3.0
*******************
Login as admin and run XOOPS_URL/modules/popnupblog/admin/sqlupdate.php by browser.

***************
Rapidly Install
***************
1.Extract these module files. (XOOPS_ROOT./module)
2.If you don't installed before, Install from module admin,
If you installed already. Add email row in popnupblog_info table.
3.Create a thumbnail folder. (Usually XOOPS_ROOT./uploads/thumbs) Set the permission as 777 or like that.
4.Customize the pop.ini.php, mail server, pop id and password.
5.Create blog user from Popnupblog admin. Set the moblog mail address.
6.Precisely set the time zone.
  'Server timezone','Default timezone' at System Admin.
  'Time Zone' at Edit Account.

DONE! HAVE FUN!

*************
Set up Manual
*************

The installation method 

1. Extract the Module

A ZIP file extract to (XOOPS_ROOT./module). 

2. Installation 

2-1. Install 
Install from module management of an administrator menu.

2-2. Regist Blog user 
Create new blog user from PopnupBlog admin.

3. Create a Thumbnail Folder. 
Usually, it recommends creating in a upload folder. (XOOPS_ROOT./uploads/thumbs). GD library needs to be supported for creation of thumbnail.

3-1.Set the time zone.
Precisely set the time zone.
  'Server timezone','Default timezone' at System Admin.
  'Time Zone' at Edit Account.

4. Setup of a pop.ini.php 
for pop'n'up operation.

4-0.Preferences of PopnupBlog in admin menu

Character-code for attach file: specifies the character conversion code when keeping a multi-byte file name. 
Mail Server: registers the mail server which receives the arrival-of-the-mail mail from a BOLG contributor. 
Mail User: sets up mail user name as receiving BLOG. 
Mail Password: sets up mail password as the receiving BLOG. 
Mail Address: sets up a receiving mail address. 

4-1. Setup of Operation 
As for pop.ini.php, a setup has separated for every section.

4-1-1.POP server section 
A contributor transmits mail to the appointed mail address. A program receives mail from the mail address. POP server section is the section which performs a setup for carrying out mail reception.

$limit_min will perform reception processing again, if it has passed above from receiving time last time. Whenever a user displays a main menu, mail reception is performed based on this lapsed time. It is the completion message of reception at the time of performing. 
$JUST_POPED mail reception program'pop.php' directly.

4-1-2.Mail Ctrl section 
Reception of mail generates a log. Moreover, a setup of mail to refuse etc. is possible.

$log_dir is a setup of the folder which records a log. 
$log sets up the successful log file of mail. 
$denylog sets up the log file of the refused mail. 
$denylog_save is a setup of whether to save the log of refusal mail. 
$head_save is a setup of whether to save the header information on mail that it succeeded. 
$head_prefix sets up header strings of the file name in the case of saving the header information on mail that it succeeded. 
$maxline sets up the number of the maximum lines of a log file. registered in $guestpost_uid guest's post -- it saves to BLOG of UID (It forbids by NULL) header strings of the attached file of 
$guestpost_uname guest post is set up. 
$nosubject is the title appended automatically, when mail without a title is received.

4-1-3.Attached file section 
Set up about attached files.

$BlogCNF['uploads'] specifies the folder for attached file preservation. 
$BlogCNF['thumb_dir'] specifies the folder for thumbnail. 
$BlogCNF['img_dir'] specifies the folder for image. 
$BlogCNF['subtype'] registers the MIME type to receive. 
$BlogCNF['imgtype'] registers MIME of the object which performs URL automatic appending as an image file. 
$BlogCNF['viri'] sets up the file extension of the ban on appending. 
$BlogCNF['w']specifies the breadth of the picture file which creates automatic thumbnail. 
$BlogCNF['h'] specifies the height of the picture file which creates automatic thumbnail. 
$BlogCNF['thumb_ext'] specifies the extension of the picture file which creates automatic thumbnail. (Jpg, Png) 
$BlogCNF['gd_ver'] performs version specification of GD library of PHP. 
$BlogCNF['maxbyte'] specifies the maximum capacity of an attached file.

4-1-4.Deny section 
$imgonly is not saved when you have no attached file. ( 1=Does'nt save / 0=Save anyway ) 
$BlogCNF['post_limit'] sets up the maximum character sequence which can be posted. 
$BlogCNF['text_limit'] Max charactor for recently blog, 0 = Subject only
$deny registers the mail address of the ban on post. It is mainly an object for the defense from virus mail. 
$deny_mailer registers the mailer of the ban on post. It is mainly an object for the defense from automatic mail distribution software. 
$deny_title registers the mail title of the ban on post. It is mainly an object for the defense from SPAM mail. 
$deny_lang registers the character code of the ban on post. It is used to mainly extract a post language. 
$del_ereg registers the specification cut when the same character continues. The under-bar which mainly continues is removed. 
$word[] registers the remove phrase from the body of email.

5. Write Blog 
Oh Yeah! Let's begin the blog!

5-1.Write Blog on the Browser 
When you done the setup, Let's try 1st blog writing. Login as blog user, Click Menu->SimleBLOG->WriteBlog?. Write a blog and click Send. You can see it on the list.

5-2.Post Blog from mailer 
Now, I suppose so you want to post from mail. Write mail and send to $mail(POP server section on pop.ini.php) from blog user address(Post mail address on PopnupBlog admin).

5-3.Update for moblog 
The update process has 2 way. There're auto and self. The auto is wait a someone click popnupblog/index.php. When someone click the PopnupBlog on the menu, the pop precess will work with interval parameter($limit_min). On the other hands, The Self is Hit the URL 'pop.php' you can see a result message.

5-4.Doesn't work? 
If you see the error message. It probably return from deny process. So, let's check the ./log/deny.cgi. It has your denied mail information. pop.php has many way of protects. Please check the deny section's parameter.
--------------------------------------------------------------------------------
