<?php
#注册插件
RegisterPlugin("aliyunDM","ActivePlugin_aliyunDM");	
require_once dirname(__FILE__).'/aliyun-php-sdk-dm/aliyun-php-sdk-core/Config.php';
use Dm\Request\V20151123 as Dm;	

function ActivePlugin_aliyunDM() {
    Add_Filter_Plugin('Filter_Plugin_PostComment_Succeed','aliyunDM_Main');
}

function aliyunDM_Main(&$cmt){
	global $zbp;
	$ali_Region = $zbp->Config('aliyunDM')->ali_Region;
	$ali_AccessKey = $zbp->Config('aliyunDM')->ali_AccessKey;
	$ali_AccessKeySecret = $zbp->Config('aliyunDM')->ali_AccessKeySecret;
	$ali_SENDEMAIL = $zbp->Config('aliyunDM')->ali_SENDEMAIL;
	$ali_TOEMAIL = $zbp->Config('aliyunDM')->ali_TOEMAIL;
	$IS_SEND_MAIL = $zbp->Config('aliyunDM')->IS_SEND_MAIL;
	$IS_REPLY_MAIL = $zbp->Config('aliyunDM')->IS_REPLY_MAIL;
	$CmtID = $cmt->ID;
	$CmtAuthorID = $cmt->AuthorID;
	$CmtName = $cmt->Name;
  	if($CmtName == "admin") {$CmtName = "博主";}
  	//由于使用评论编辑器插件发送后不显示表情，所以重新转为HTML，请确认是否有过滤<script> 标签
	$CmtContent = html_entity_decode($cmt->Content);
	$CmtEmail = $cmt->Email;
	$CmtHomePage = $cmt->HomePage;
	$CmtLogID = $cmt->LogID;
	$CmtRootID = $cmt->RootID;
	$CmtParentID = $cmt->ParentID;
	$blogname = $zbp->name;
	$log_title = $zbp->GetPostByID($CmtLogID)->Title;
	$subject = "日志《{$log_title}》收到了新的评论";
	if(strpos($ali_TOEMAIL, '@139.com') === false) {
		$content = "评论内容：{$CmtContent}<br /><br />发件人:".$CmtName."<br />";
		if(!empty($CmtEmail)) $content .= "Email：{$CmtEmail}<br />";
		if(!empty($CmtHomePage)) $content .= "主页：{$CmtHomePage}<br />";
		$content .= "<br /><strong>=> 现在就前往<a href=\"{$_SERVER['HTTP_REFERER']}#cmt{$CmtID}\" target=\"_blank\">日志页面</a>进行查看</strong><br />";
	}
	else {
		$content = $CmtContent;
	}
	if($IS_SEND_MAIL = 'Y' or $IS_SEND_MAIL = true) {
		if($CmtAuthorID == '0') Direct_Email($ali_Region, $ali_AccessKey, $ali_AccessKeySecret, $ali_SENDEMAIL, $ali_TOEMAIL, $subject, $content, $blogname);
	}
	if($IS_REPLY_MAIL = 'Y' or $IS_REPLY_MAIL = true) {
		if($CmtParentID > 0) {
			$pinfo = $zbp->GetCommentByID($CmtParentID);
			if(!empty($pinfo->Email)) {
				$subject = "您在【{$blogname}】发表的评论收到了回复";
				//可在此自定义邮件回复样式
              	$content = "<table style=\"font-family: 微软雅黑,verdana, arial; margin: 0 auto; width: 100%;\" cellspacing=\"0\" cellpadding=\"0\">					<tbody>					<tr>						<td style=\"background: #08c; color: #fff; font-family: 微软雅黑,verdana, arial; font-size:15px;line-height: 50px;\"><strong>&nbsp; 您在【{$blogname}】的留言有了新的回复：</strong></td>					</tr>					<tr>						<td style=\"border: solid 1px #ccc;font-size: 13px;line-height: 180%;padding: 20px;\"><span style=\"color: rgb(186, 76, 50); font-family:微软雅黑, verdana, arial; line-height: 23.3999996185303px;\">{$pinfo->Name}</span>, 您好!						<p>您曾在<span style=\"color:#ba4c32;\">《{$log_title}》</span>的留言:</p>						<blockquote style=\"width: 94%;color: #8b8b8b;margin: 0 auto;padding: 10px;clear: both;border: 1px solid #ebebeb;\">							{$pinfo->Content}						</blockquote>						<p><span style=\"color:#ba4c32;\">{$CmtName}</span> 给你的回复:</p>						<blockquote style=\"width: 94%;color: #8b8b8b;margin: 0 auto;padding: 10px;clear: both;border: 1px solid #ebebeb;\">							{$CmtContent}						</blockquote>						<p style=\"padding: 5px;\">您可以点此 <a href=\"{$_SERVER['HTTP_REFERER']}#cmt{$CmtID}\" target=\"_blank\">查看完整回复內容</a>，欢迎您再度光临 <a href=\"{$zbp->host}\" target=\"_blank\">{$blogname}</a>！</p>						<hr>						<p><strong>温馨提示：此邮件由{$blogname}自动发送，请勿直接回复。</strong></p>						</td>					</tr>				</tbody>			</table>";
				Direct_Email($ali_Region, $ali_AccessKey, $ali_AccessKeySecret, $ali_SENDEMAIL, $pinfo->Email, $subject, $content, $blogname);
			}
		}
	}
}

function Direct_Email($region, $accesskey, $accesskeysecret, $mailuser, $mailto, $subject,  $content, $fromname) {
  

				/*阿里云邮件推送SDK开始*/									
				//需要设置对应的region名称，如华东1（杭州）设为cn-hangzhou，新加坡Region设为ap-southeast-1，澳洲Region设为ap-southeast-2。
				$iClientProfile = DefaultProfile::getProfile($region, $accesskey, $accesskeysecret);								
				//新加坡或澳洲region需要设置服务器地址，华东1（杭州）不需要设置。
  				if ($region == "ap-southeast-1") {
				$iClientProfile::addEndpoint("ap-southeast-1","ap-southeast-1","Dm","dm.ap-southeast-1.aliyuncs.com");
                }
  				elseif ($region == "ap-southeast-2") {
				$iClientProfile::addEndpoint("ap-southeast-2","ap-southeast-2","Dm","dm.ap-southeast-2.aliyuncs.com");
                }
				$client = new DefaultAcsClient($iClientProfile);				
				$request = new Dm\SingleSendMailRequest();   			 
				//新加坡或澳洲region需要设置SDK的版本，华东1（杭州）不需要设置。
				$region !== "cn-hangzhou"?$request->setVersion("2017-06-22"):"";
				$request->setAccountName($mailuser);
				$request->setFromAlias($fromname);
				$request->setAddressType(1);
				$request->setTagName("");
				$request->setReplyToAddress("true");
				$request->setToAddress($mailto);								
				$request->setSubject($subject);
				$request->setHtmlBody($content);								
				try {
								$response = $client->getAcsResponse($request);
								//return($response);
								return true;
				}
				catch (ClientException  $e) {
								//print_r($e->getErrorCode());   
								//print_r($e->getErrorMessage());   
								return false;
				}
				catch (ServerException  $e) {								
								//print_r($e->getErrorCode());   
								//print_r($e->getErrorMessage());
								return false;
				}
                /*阿里云邮件推送SDK结束*/
}


function InstallPlugin_aliyunDM() {
    global $zbp;
	if(!$zbp->Config('aliyunDM')->HasKey('Version')) {
		$zbp->Config('aliyunDM')->Version = '1.0';
		$zbp->Config('aliyunDM')->ali_Region = 'cn-hangzhou';
		$zbp->Config('aliyunDM')->ali_AccessKey = 'YourAccessKeyID';
		$zbp->Config('aliyunDM')->ali_AccessKeySecret = 'YourAccessKeySecret';
		$zbp->Config('aliyunDM')->ali_SENDEMAIL = 'YourEmail';
		$zbp->Config('aliyunDM')->ali_TOEMAIL = '666666@qq.com';
		$zbp->Config('aliyunDM')->IS_SEND_MAIL = 'Y';
		$zbp->Config('aliyunDM')->IS_REPLY_MAIL = 'Y';
		$zbp->SaveConfig('aliyunDM');
	}
}
function UninstallPlugin_aliyunDM() {
    global $zbp;
	$zbp->DelConfig('aliyunDM');
}