<?php
require '../../../zb_system/function/c_system_base.php';
require '../../../zb_system/function/c_system_admin.php';
$zbp->Load();
$action='root';
if (!$zbp->CheckRights($action)) {$zbp->ShowError(6);die();}
if (!$zbp->CheckPlugin('aliyunDM')) {$zbp->ShowError(48);die();}

$blogtitle='阿里云邮件推送新评论提醒';
require $blogpath . 'zb_system/admin/admin_header.php';
require $blogpath . 'zb_system/admin/admin_top.php';
?>


<script type="text/javascript">
    jQuery(function($) {
        $('#testsend').click(function() {
            $('#testresult').html('邮件发送中..');
            $.get(bloghost + 'zb_users/plugin/aliyunDM/Direct_Mail_test.php', {
                sid: Math.random()
            },
            function(result) {
                if ($.trim(result) != '') {
                    $('#testresult').html(result);
                } else {
                    $('#testresult').html('发送失败！');
                }
            });
        });
        $(document).ready(function(){  
        $("#ali_Region").val("<?php echo $zbp->Config('aliyunDM')->ali_Region;?>");
    }) 
    });
    setTimeout(hideActived, 2600);
</script>

<div id="divMain">
  <div class="divHeader"><?php echo $blogtitle;?></div>
  <div class="SubMenu">
  </div>
  <div id="divMain2">
<!--代码-->
    <?php
	if(isset($_POST['ali_Region'])){
		$zbp->Config('aliyunDM')->ali_Region = $_POST['ali_Region'];
		$zbp->Config('aliyunDM')->ali_AccessKey = $_POST['ali_AccessKey'];
		$zbp->Config('aliyunDM')->ali_AccessKeySecret = $_POST['ali_AccessKeySecret'];
		$zbp->Config('aliyunDM')->ali_SENDEMAIL = $_POST['ali_SENDEMAIL'];
		$zbp->Config('aliyunDM')->ali_TOEMAIL = $_POST['ali_TOEMAIL'];
		$zbp->Config('aliyunDM')->IS_SEND_MAIL = $_POST['IS_SEND_MAIL'];
		$zbp->Config('aliyunDM')->IS_REPLY_MAIL = $_POST['IS_REPLY_MAIL'];
		$zbp->SaveConfig('aliyunDM');
		$zbp->SetHint('good','新评论邮件提醒插件提醒您，配置已保存，请勿忘记测试发送邮件');
		Redirect('./main.php');
	}
	?>
	<form id="form1" name="form1" method="post"> 
		<table width="100%" style='padding:0;margin:0;' cellspacing='0' cellpadding='0' class="tableBorder">
			<tr>
				<th width="15%"><p align="center">配置名称</p></th>
				<th width="50%"><p align="center">配置内容</p></th>
				<th width="35%"><p align="center">测试发送</p></th>
			</tr>
			<tr>
				<td><div align="center">Region名称</div></td>
				<td>
				    <select id="ali_Region" name="ali_Region" >
				      <option value ="cn-hangzhou">华东1（杭州）</option>
				      <option value ="ap-southeast-1">新加坡</option>
				      <option value="ap-southeast-2">澳洲</option>
				    </select>
				</td>
				<td><span>测试发送:<font color="red">（请先在左边设置好相关信息<b>保存</b>后测试）</font></span></td>
			</tr>
			<tr>
				<td><div align="center">AccessKeyID</div></td>
				<td><input name="ali_AccessKey" type="text" id="ali_AccessKey" value="<?php echo $zbp->Config('aliyunDM')->ali_AccessKey;?>"/></td>
				<td><input id="testsend" type="button" value="发送一封测试邮件" /></td>
			</tr>
			<tr>
				
				<td><div align="center">AccessKeySecret</div></td>
				<td><input type="text" name="ali_AccessKeySecret" id="ali_AccessKeySecret" value="<?php echo $zbp->Config('aliyunDM')->ali_AccessKeySecret;?>"/></td>
				<td><span>测试结果:</span></td>
			</tr>
			<tr>
			    <td><div align="center">发信邮箱</div></td>
				<td><input name="ali_SENDEMAIL" type="text" id="ali_SENDEMAIL" value="<?php echo $zbp->Config('aliyunDM')->ali_SENDEMAIL;?>"/></td>
				<td rowspan="3"><div id="testresult" style="height:64px; padding:10px; border:1px dashed #ccc; overflow:auto;/*background-color:#bbd9e2;*/"></div></td>
			</tr>
			<tr>
				<td><div align="center">收信邮箱</div></td>
				<td><input name="ali_TOEMAIL" type="text" id="ali_TOEMAIL" value="<?php echo $zbp->Config('aliyunDM')->ali_TOEMAIL;?>"/></td>
			</tr>

			<tr>
				<td><div align="center">发送选项</div></td>
				<td>
				<label><input type="checkbox" name="IS_SEND_MAIL" id="IS_SEND_MAIL" value="true" <?php if($zbp->Config('aliyunDM')->IS_SEND_MAIL) echo 'checked="checked"'?> />收到评论时通知自己(√)</label>
				<label><input type="checkbox" name="IS_REPLY_MAIL" id="IS_REPLY_MAIL" value="true" <?php if($zbp->Config('aliyunDM')->IS_REPLY_MAIL) echo 'checked="checked"'?> />回复评论时通知评论者(√)</label>
				</td>

			</tr>
			<tr>
				<td><div align="center">配置保存</div></td>
				<td><input name="" type="Submit" class="button" value="保　存" /></td>
				<td rowspan="2">

				</td>
			</tr>
		</table>
		<table width="100%" style="padding:0;margin:0;" cellspacing="0" cellpadding="0" class="tableBorder table_striped table_hover">
			<tbody><tr height="32"><td>使用帮助：<a href="https://blog.fanlibei.com/post/14.html" target="_Blank" id="code" style="">点击就送，屠龙报道</a></td></tr>
		</tbody></table>
	</form>
<!--代码-->
  </div>
</div>

<?php
require $blogpath . 'zb_system/admin/admin_footer.php';
RunTime();
?>