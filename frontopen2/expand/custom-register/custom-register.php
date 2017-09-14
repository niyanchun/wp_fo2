<?php
/**
 *
 * Description: 该文件为FO2注册模块。目的为修改默认的后台用户注册表单，用户可以自行输入密码，不必用Email接收密码，跳过Email验证。
 *
 * from ：玩赚乐  http://www.banghui.org
 */

if (!isset($_SESSION)) {
	session_start();
	session_regenerate_id(TRUE);
}

/**
 * Description: 后台注册模块，添加注册表单,修改新用户通知。
 *
 * from ：玩赚乐  http://www.banghui.org
 */
if ( !function_exists('fo_wp_new_user_notification') ) :
function fo_wp_new_user_notification($user_id, $plaintext_pass = '', $flag='') {
	
	if(func_num_args() > 1 && $flag !== 1)
		return;

	$user = new WP_User($user_id);

	$user_login = stripslashes($user->user_login);
	$user_email = stripslashes($user->user_email);
	
	//博客名称
	$blog_name = get_bloginfo('name');
	//博客名称
	$blog_url = site_url();

	// The blogname option is escaped with esc_html on the way into the database in sanitize_option
	// we want to reverse this for the plain text arena of emails.
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

	$message  = sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";
	$message .= sprintf(__('Username: %s'), $user_login) . "\r\n\r\n";
	$message .= sprintf(__('E-mail: %s'), $user_email) . "\r\n";

	@wp_mail(get_option('admin_email'), sprintf(__('[%s] New User Registration'), $blogname), $message);

	if ( empty($plaintext_pass) ){
		return;
	}

	// 你可以在此修改发送给用户的注册通知Email
	$message = '
	<style class="fox_global_style">
	    div.fox_html_content { line-height: 1.5;} /* 一些默认样式 */ blockquote { margin-Top:
	    0px; margin-Bottom: 0px; margin-Left: 0.5em } ol, ul { margin-Top: 0px;
	    margin-Bottom: 0px; list-style-position: inside; } p { margin-Top: 0px;
	    margin-Bottom: 0px }
	</style>
	<blockquote style="margin-top: 0px; margin-bottom: 0px; margin-left: 0.5em;">
	    <div class="FoxDiv20140409214420341055" id="FMOriginalContent">
	        <table cellpadding="0" cellspacing="0" align="center" style="text-align:left;font-family:"微软雅黑","黑体",arial;"
	        width="742">
	            <tbody>
	                <tr>
	                    <td>
	                        <table cellpadding="0" cellspacing="0" style="text-align:left;border:1px solid #50a5e6;color:#fff;font-size:18px;"
	                        width="740">
	                            <tbody>
	                                <tr height="39" style="background-color:#50a5e6;">
	                                    <td style="padding-left:15px;font-family:"微软雅黑","黑体",arial;">
	                                       	<font face="华文行楷" style="font-size: 24px;">您的密码:</font>
	                                    </td>
	                                </tr>
	                            </tbody>
	                        </table>
	                        <table cellpadding="0" cellspacing="0" style="text-align:left;border:1px solid #f0f0f0;border-top:none;color:#585858;background-color:#fafafa;"
	                        width="740">
	                            <tbody>
	                                <tr height="25">
	                                    <td>
	                                    </td>
	                                </tr>
	                                <tr height="40">
	                                    <td style="padding-left:25px;padding-right:25px;font-size:18px;font-family:"微软雅黑","黑体",arial;">
	                                        <font face="华文行楷" style="font-size: 28px;">亲爱的 '.$user_login.' :</font>
	                                    </td>
	                                </tr>
	                                <tr height="15">
	                                    <td>
	                                    </td>
	                                </tr>
	                                <tr height="30">
	                                    <td style="padding-left:55px;padding-right:55px;font-family:"微软雅黑","黑体",arial;font-size:14px;">
	                                        您刚刚在 <font face="华文行楷" style="font-size: 29px;">'.$blog_name.'</font> 使用了账号注册功能。
	                                    </td>
	                                </tr>
	                                <tr height="30">
	                                    <td style="padding-left:55px;padding-right:55px;font-family:"微软雅黑","黑体",arial;font-size:14px;">
	                                        您的账号：'.sprintf(__('%s'), $user_login) .'<br>
						您的邮箱: '.sprintf(__('%s'), $user_email).'<br>
						您的密码: '.sprintf(__('%s'), $plaintext_pass).'<br>
						登录地址: '.sprintf(__('%s'), wp_login_url()).'<br>
	                                    </td>
	                                </tr>
	                                <tr height="20">
	                                    <td style="padding-top:20px;padding-left:55px;padding-right:55px;font-family:"微软雅黑","黑体",arial;font-size:12px;">
						如果您不知道为什么收到了这封邮件，可能是他人不小心输错邮箱意外发给了您，请忽略此邮件。
	                                    </td>
	                                </tr>
	                                <tr height="20">
	                                    <td>
	                                    </td>
	                                </tr>
	                            </tbody>
	                        </table>
	                        <table cellpadding="0" cellspacing="0" style="color:#969696;font-size:12px;vertical-align:middle;text-align:center;" width="740">
	                            <tbody>
	                                <tr height="5">
	                                    <td>
	                                    </td>
	                                </tr>
	                                <tr height="20">
	                                    <td width="680" style="text-align:left;font-family:"微软雅黑","黑体",arial">
	                                        '.date("Y",time()).'
	                                        <span>
	                                            ©
	                                        </span>
	                                        <a href = "'.$blog_url.'" target="_blank" style="text-decoration:none;color:#969696;padding-left:5px;"
	                                           title = "'.$blog_name.'">
	                                            	'.$blog_name.' 版权所有
	                                        </a>
	                                    </td>
	                                </tr>
	                            </tbody>
	                        </table>
	                    </td>
	                </tr>
	            </tbody>
	        </table>
	    </div>
	</blockquote>';
	
    //邮件头，以免乱码,支持HTML
	$message_headers = "Content-Type: text/html; charset=\"utf-8\"\n";
	// sprintf(__('[%s] Your username and password'), $blogname) 为邮件标题
	wp_mail($user_email, sprintf(__('[%s] Your username and password'), $blogname), $message,$message_headers);
}
endif;

/**
 * 重新定义Wordpress找回密码功能邮件内容。修复 WordPress 找回密码提示“抱歉，该key似乎无效”
 * 
 * from： http://www.banghui.org
 */
add_filter('retrieve_password_message', fo_reset_password_message, null, 2);
function fo_reset_password_message( $message, $key ) {
	if ( strpos($_POST['user_login'], '@') ) {
		$user_data = get_user_by('email', trim($_POST['user_login']));
	} else {
		$login = trim($_POST['user_login']);
		$user_data = get_user_by('login', $login);
	}
	
	//用户名
	$user_login = $user_data->user_login;
	//用户邮箱
	$user_email = $user_data->user_email;
	//博客名称
	$blog_name = get_bloginfo('name');
	//博客名称
	$blog_url = site_url();
	//密码找回URL
	$forget_pwd = network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login') ;
	
	$message = '
	<style class="fox_global_style">
	    div.fox_html_content { line-height: 1.5;} /* 一些默认样式 */ blockquote { margin-Top:
	    0px; margin-Bottom: 0px; margin-Left: 0.5em } ol, ul { margin-Top: 0px;
	    margin-Bottom: 0px; list-style-position: inside; } p { margin-Top: 0px;
	    margin-Bottom: 0px }
	</style>
	<blockquote style="margin-top: 0px; margin-bottom: 0px; margin-left: 0.5em;">
	    <div class="FoxDiv20140409214420341055" id="FMOriginalContent">
	        <table cellpadding="0" cellspacing="0" align="center" style="text-align:left;font-family:"微软雅黑","黑体",arial;"
	        width="742">
	            <tbody>
	                <tr>
	                    <td>
	                        <table cellpadding="0" cellspacing="0" style="text-align:left;border:1px solid #50a5e6;color:#fff;font-size:18px;"
	                        width="740">
	                            <tbody>
	                                <tr height="39" style="background-color:#50a5e6;">
	                                    <td style="padding-left:15px;font-family:"微软雅黑","黑体",arial;">
	                                       	<font face="华文行楷" style="font-size: 24px;">找回密码:</font>
	                                    </td>
	                                </tr>
	                            </tbody>
	                        </table>
	                        <table cellpadding="0" cellspacing="0" style="text-align:left;border:1px solid #f0f0f0;border-top:none;color:#585858;background-color:#fafafa;"
	                        width="740">
	                            <tbody>
	                                <tr height="25">
	                                    <td>
	                                    </td>
	                                </tr>
	                                <tr height="40">
	                                    <td style="padding-left:25px;padding-right:25px;font-size:18px;font-family:"微软雅黑","黑体",arial;">
	                                        <font face="华文行楷" style="font-size: 28px;">亲爱的 '.$user_login.' :</font>
	                                    </td>
	                                </tr>
	                                <tr height="15">
	                                    <td>
	                                    </td>
	                                </tr>
	                                <tr height="30">
	                                    <td style="padding-left:55px;padding-right:55px;font-family:"微软雅黑","黑体",arial;font-size:14px;">
	                                        您刚刚在 <font face="华文行楷" style="font-size: 29px;">'.$blog_name.'</font> 使用了找回密码功能。
	                                    </td>
	                                </tr>
	                                <tr height="30">
	                                    <td style="padding-left:55px;padding-right:55px;font-family:"微软雅黑","黑体",arial;font-size:14px;">
	                                        请在<span style="color:rgb(255,0,0)">尽快</span>点击下面链接设置您的新密码：
	                                    </td>
	                                </tr>
	                                <tr height="60">
	                                    <td style="padding-left:55px;padding-right:55px;font-family:"微软雅黑","黑体",arial;font-size:14px;">
	                                        <a href="'.$forget_pwd.'"
	                                        target="_blank" style="color: rgb(255,255,255);text-decoration: none;display: block;min-height: 39px;width: 158px;line-height: 39px;background-color:rgb(80,165,230);font-size:20px;text-align:center;">
	                                                                                重置密码
	                                        </a>
	                                    </td>
	                                </tr>
	                                <tr height="10">
	                                    <td>
	                                    </td>
	                                </tr>
	                                <tr height="20">
	                                    <td style="padding-left:55px;padding-right:55px;font-family:"微软雅黑","黑体",arial;font-size:12px;">
	                                         	如果上面的链接点击无效，请复制以下链接至浏览器的地址栏直接打开。
	                                    </td>
	                                </tr>
	                                <tr height="30">
	                                    <td style="padding-left:55px;padding-right:65px;font-family:"微软雅黑","黑体",arial;">
	                                        <a href="'.$forget_pwd.'" target="_blank" style="color:#0c94de;font-size:12px;">
	                                            '.$forget_pwd.'
	                                        </a>
	                                    </td>
	                                </tr>
	                                <tr height="20">
	                                    <td style="padding-left:55px;padding-right:55px;font-family:"微软雅黑","黑体",arial;font-size:12px;">
	                                        	如果您不知道为什么收到了这封邮件，可能是他人不小心输错邮箱意外发给了您，请忽略此邮件。
	                                    </td>
	                                </tr>
	                                <tr height="20">
	                                    <td>
	                                    </td>
	                                </tr>
	                            </tbody>
	                        </table>
	                        <table cellpadding="0" cellspacing="0" style="color:#969696;font-size:12px;vertical-align:middle;text-align:center;" width="740">
	                            <tbody>
	                                <tr height="5">
	                                    <td>
	                                    </td>
	                                </tr>
	                                <tr height="20">
	                                    <td width="680" style="text-align:left;font-family:"微软雅黑","黑体",arial">
	                                        '.date("Y",time()).'
	                                        <span>
	                                            ©
	                                        </span>
	                                        <a href="'.$blog_url.'" target="_blank" style="text-decoration:none;color:#969696;padding-left:5px;"
	                                        title="'.$blog_name.'">
	                                            	'.$blog_name.' 版权所有
	                                        </a>
	                                    </td>
	                                </tr>
	                            </tbody>
	                        </table>
	                    </td>
	                </tr>
	            </tbody>
	        </table>
	    </div>
	</blockquote>';
	
	$title = "[".$blog_name."]找回密码";
	//邮件头，以免乱码,支持HTML
	$message_headers = "Content-Type: text/html; charset=\"utf-8\"\n";
	//发送邮件
	wp_mail($user_email, $title, $message,$message_headers);
	//通知管理员，有会员正在找回密码，可以协助找回！
	@wp_mail(get_option('admin_email'), '会员'.$user_login.'正在找回密码，请帮忙协助寻回！', '有会员'.$user_login.'正在找回密码，如有可能请帮忙协助寻回！');
	//$msg;
	return null;
}

/**
 * Description: 修改注册表单
 *
 * from ：玩赚乐  http://www.banghui.org
 */
function fo_show_password_field() {
	define('LCR_THEME_URL', get_stylesheet_directory_uri().'/expand/custom-register');
?>
	<p>
		<label for="user_nick">昵称<br /> <input id="user_nick" class="input"
			type="text" tabindex="20" size="25"
			value="<?php echo $_POST['user_nick']; ?>" name="user_nick" />
		</label>
	</p>
	<p>
		<label for="user_pwd1">密码(至少6位)<br /> <input id="user_pwd1"
			class="input" type="password" tabindex="21" size="25"
			value="<?php echo $_POST['user_pass']; ?>" name="user_pass" />
		</label>
	</p>
	<p>
		<label for="user_pwd2">重复密码<br /> <input id="user_pwd2" class="input"
			type="password" tabindex="22" size="25"
			value="<?php echo $_POST['user_pass2']; ?>" name="user_pass2" />
		</label>
	</p>
	<p>
		<label for="CAPTCHA"> <img id="captcha_img" align='left'
			style="width: 135px; height: 40px;"
			src="<?php echo constant("LCR_THEME_URL"); ?>/captcha/captcha.php"
			title="看不清?点击更换" alt="看不清?点击更换"
			onclick="document.getElementById('captcha_img').src='<?php echo constant("LCR_THEME_URL"); ?>/captcha/captcha.php?'+Math.random();document.getElementById('CAPTCHA').focus();return false;" />
			&nbsp; <input id="CAPTCHA" placeholder="验证码"
			style="width: 110px; *float: left; margin-left: 10px;" class="input"
			type="text" tabindex="24" size="10" value="" name="captcha_code" />
		</label>
	</p>
<?php
}

/**
 *
 * Description: 处理表单提交的数据
 *
 * from ：玩赚乐  http://www.banghui.org
 */
function fo_check_fields($login, $email, $errors) {
	//验证码校验
	if(empty($_POST['captcha_code']) || empty($_SESSION['fo_lcr_secretword']) || (trim(strtolower($_POST['captcha_code'])) != $_SESSION['fo_lcr_secretword'])) {
		$errors->add('captcha_spam', "<strong>错误</strong>：验证码不正确");
	}
	
	//移除验证码
	unset($_SESSION['fo_lcr_secretword']);

	if (!isset($_POST['user_nick']) || trim($_POST['user_nick']) == ''){
		$errors->add('user_nick', "<strong>错误</strong>：昵称必须填写");
	}

	if(strlen($_POST['user_pass']) < 6){
		$errors->add('password_length', "<strong>错误</strong>：密码长度至少6位");
	}else if($_POST['user_pass'] != $_POST['user_pass2']){
		$errors->add('password_error', "<strong>错误</strong>：两次输入的密码必须一致");
	}

}

/**
 * Description: 保存表单提交的数据
 *
 * from ：玩赚乐  http://www.banghui.org
 */
function fo_register_extra_fields($user_id, $password="", $meta=array()) {
	$userdata = array();
	$userdata['ID'] = $user_id;
	$userdata['user_pass'] = $_POST['user_pass'];
	$userdata['nickname'] = str_replace(array('<','>','&','"','\'','#','^','*','_','+','$','?','!'), '', $_POST['user_nick']);

	fo_wp_new_user_notification( $user_id, $_POST['user_pass'], 1 );
	wp_update_user($userdata);
}

/**
 * Description: 移除默认密码生成
 *
 * from ：玩赚乐  http://www.banghui.org
 */
function fo_remove_default_password_nag() {
	global $user_ID;
	delete_user_setting('default_password_nag', $user_ID);
	update_user_option($user_ID, 'default_password_nag', false, true);
}

/**
 * Description: 注册提示信息（国际化）
 *
 * from ：玩赚乐  http://www.banghui.org
 */
function fo_register_change_translated_text( $translated_text, $untranslated_text, $domain ) {
  if ( $untranslated_text === 'A password will be e-mailed to you.' )
  	return '';
  else if ($untranslated_text === 'Registration complete. Please check your e-mail.')
  	return '注册成功！';
  else
  	return $translated_text;
}

/**
 * Description: 将自定义方法替换Wordpress自带。注意级别
 *
 * from ：玩赚乐  http://www.banghui.org
 */
add_filter('gettext', 'fo_register_change_translated_text', 20, 3);
add_action('admin_init', 'fo_remove_default_password_nag');
add_action('register_form','fo_show_password_field');
add_action('register_post','fo_check_fields',10,3);
add_action('user_register', 'fo_register_extra_fields');

/**
 * 用户登录成功后，并跳转到至访问前页面，否则至个人中心
 *
 * from ：玩赚乐  http://www.banghui.org
*/
add_filter("login_redirect", "fo_login_redirect", 10, 3);
function fo_login_redirect($redirect_to , $request){

	//跳转至来路页面
	if (isset($_SERVER['HTTP_REFERER'])
	//保证来路不为空
	&& $_SERVER['HTTP_REFERER'] != ''
	//必须是站内
	&& strpos($_SERVER['HTTP_REFERER'],home_url()) == false
	//以防死循环
	&& strpos($_SERVER['HTTP_REFERER'],'login') == false){
		// 登陆前的页面地址
		return $_SERVER['HTTP_REFERER'];
	}

	if(empty($redirect_to)){
		//跳转至管理页
		return home_url();
	}
	// 登陆前的页面地址
	return $redirect_to;
}

/**
 * 用户注册成功后自动登录，并跳转到指定页面
 *
 * from ：玩赚乐  http://www.banghui.org
 */
add_action( 'user_register', 'fo_auto_login_new_user' );
function fo_auto_login_new_user( $user_id ) {

	//设置自动登录
	wp_set_current_user($user_id);
	wp_set_auth_cookie($user_id);

	//跳转至首页
	wp_redirect( home_url() );

	//ok;
	exit;
}

/**
 *
 * Description: 使Wordpress注册支持中文
 *
 * from ：玩赚乐  http://www.banghui.org
 *
 */
add_filter( 'sanitize_user', 'fo_ys_sanitize_user',3,3);
function fo_ys_sanitize_user($username, $raw_username, $strict){
	$username = $raw_username;
	$username = strip_tags($username);
	// Kill octets
	$username = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '', $username);
	$username = preg_replace('/&.+?;/', '', $username); // Kill entities

	// If strict, reduce to ASCII and chinese for max portability.
	if ( $strict )
		$username = preg_replace('|[^a-z0-9 _.\-@\x80-\xFF]|i', '', $username);

	// Consolidate contiguous whitespace
	$username = preg_replace('|\s+|', ' ', $username);

	return $username;
}

?>