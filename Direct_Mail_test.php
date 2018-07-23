<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('aliyunDM')) {$zbp->ShowError(48);die();}

$ali_Region = $zbp->Config('aliyunDM')->ali_Region;
$ali_AccessKey = $zbp->Config('aliyunDM')->ali_AccessKey;
$ali_AccessKeySecret = $zbp->Config('aliyunDM')->ali_AccessKeySecret;
$ali_SENDEMAIL = $zbp->Config('aliyunDM')->ali_SENDEMAIL;
$ali_TOEMAIL = $zbp->Config('aliyunDM')->ali_TOEMAIL;


$blogname = $zbp->name;
$subject = $content = '这是一封测试邮件';

if(Direct_Email($ali_Region, $ali_AccessKey, $ali_AccessKeySecret, $ali_SENDEMAIL, $ali_TOEMAIL, $subject, $content, $blogname)) {
	echo '<font color="green">发送成功！请到相应邮箱查收！：）</font>';
}