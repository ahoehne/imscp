<?xml version="1.0" encoding="{THEME_CHARSET}" ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
"http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="ltr" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset={THEME_CHARSET}" />
		<meta http-equiv="X-UA-Compatible" content="IE=8" />
		<title>{TR_CLIENT_ENABLE_AUTORESPOND_PAGE_TITLE}</title>
		<meta name="robots" content="nofollow, noindex" />
		<link href="{THEME_COLOR_PATH}/css/imscp.css" rel="stylesheet" type="text/css" />
		<script type="text/javascript" src="{THEME_COLOR_PATH}/js/imscp.js"></script>
                <script type="text/javascript" src="{THEME_COLOR_PATH}/js/jquery.js"></script>
                <script type="text/javascript" src="{THEME_COLOR_PATH}/js/jquery.imscpTooltips.js"></script>
                <script type="text/javascript" src="{THEME_COLOR_PATH}/js/jquery.ui.core.js"></script>
		<!--[if IE 6]>
		<script type="text/javascript" src="{THEME_COLOR_PATH}/js/DD_belatedPNG_0.0.8a-min.js"></script>
		<script type="text/javascript">
			DD_belatedPNG.fix('*');
		</script>
		<![endif]-->
                <script type="text/javascript">
                        /*<![CDATA[*/
				// later $('#start').timepicker({});
                        /*]]>*/
                </script>
	</head>
	<body>
		<div class="header">
			{MAIN_MENU}

			<div class="logo">
				<img src="{ISP_LOGO}" alt="i-MSCP logo" />
			</div>
		</div>

		<div class="location">
			<div class="location-area">
				<h1 class="email">{TR_MENU_MAIL_ACCOUNTS}</h1>
			</div>
			<ul class="location-menu">
				<!-- <li><a class="help" href="#">Help</a></li> -->
				<!-- BDP: logged_from -->
				<li><a class="backadmin" href="change_user_interface.php?action=go_back">{YOU_ARE_LOGGED_AS}</a></li>
				<!-- EDP: logged_from -->
				<li><a class="logout" href="../index.php?logout">{TR_MENU_LOGOUT}</a></li>
			</ul>
			<ul class="path">
				<li><a href="mail_accounts.php">{TR_MENU_MAIL_ACCOUNTS}</a></li>
				<li><a href="mail_accounts.php">{TR_LMENU_OVERVIEW}</a></li>
				<li><a href="#" onclick="return false;">{TR_ENABLE_MAIL_AUTORESPONDER}</a></li>
			</ul>
		</div>

		<div class="left_menu">
			{MENU}
		</div>

		<div class="body">
			<h2 class="support"><span>{TR_ENABLE_MAIL_AUTORESPONDER}</span></h2>
			<!-- BDP: page_message -->
			<div class="{MESSAGE_CLS}">{MESSAGE}</div>
			<!-- EDP: page_message -->

			<form name="manage_users_common_frm" method="post" action="">
				<fieldset>
					<legend>{TR_ARSP_TIME}</legend>
			                <table cellpadding="5" cellspacing="2" style="width:400px">
                        			<tr>
			                        	<td><b>{TR_ARSP_START} :</b> <br><small> (JJJJ-MM-TT HH:mm)</small></td>
			                        	<td><input id='start' type="text" name="arsp_start" size="13" value="{ARSP_START}"></td>
                        			</tr>
                           			<tr>
			                        	<td><b>{TR_ARSP_STOP} :</b><br><small> (JJJJ-MM-TT HH:mm)</small> </td>
							<td><input type="text" name="arsp_stop" size="13" value="{ARSP_STOP}"></td>
						</tr>
					</table>
				</fieldset>
				<fieldset>
					<legend>{TR_ARSP_MESSAGE}</legend>
					<textarea name="arsp_message" cols="50" rows="15"></textarea>
				</fieldset>

				<div class="buttons">
					<input name="Submit" type="submit" value="{TR_ENABLE}" />
					<input type="button" name="Submit2" value="{TR_CANCEL}" onclick="location = 'mail_accounts.php'" />
				</div>
				<input type="hidden" name="uaction" value="enable_arsp" />
				<input type="hidden" name="id" value="{ARSP_ID}" />
			</form>

		</div>
<!-- INCLUDE "footer.tpl" -->
