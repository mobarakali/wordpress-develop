<?php 
if ( empty($feed) ) {
	$withcomments = 1;
	require('wp-blog-header.php');
}

header('Content-type: text/xml;charset=' . get_settings('blog_charset'), true);

echo '<?xml version="1.0" encoding="'.get_settings('blog_charset').'"?'.'>'; 
?>
<!-- generator="wordpress/<?php echo $wp_version ?>" -->
<rss version="2.0" 
	xmlns:content="http://purl.org/rss/1.0/modules/content/">
<channel>
<?php
$i = 0;
if ($posts) { foreach ($posts as $post) { start_wp();
	if ($i < 1) {
		$i++;
?>
	<title><?php if (is_single()) { echo "Comments on: "; the_title_rss(); } else { bloginfo_rss("name"); echo " Comments"; } ?></title>
	<link><?php (is_single()) ? permalink_single_rss() : bloginfo_rss("url") ?></link>
	<description><?php bloginfo_rss("description") ?></description>
	<pubDate><?php echo gmdate('r'); ?></pubDate>
	<generator>http://wordpress.org/?v=<?php echo $wp_version ?></generator>

<?php 
	  if (is_single()) {
			$comments = $wpdb->get_results("SELECT comment_ID, comment_author, comment_author_email, 
			comment_author_url, comment_date, comment_content, comment_post_ID, 
			$wpdb->posts.ID, $wpdb->posts.post_password FROM $wpdb->comments 
			LEFT JOIN $wpdb->posts ON comment_post_id = id WHERE comment_post_ID = '$id' 
			AND $wpdb->comments.comment_approved = '1' AND ($wpdb->posts.post_status = 'publish' OR $wpdb->posts.post_status = 'page') 
			AND post_date < '".date("Y-m-d H:i:59")."' 
			ORDER BY comment_date LIMIT " . get_settings('posts_per_rss') );
		} else { // if no post id passed in, we'll just ue the last 10 comments.
			$comments = $wpdb->get_results("SELECT comment_ID, comment_author, comment_author_email, 
			comment_author_url, comment_date, comment_content, comment_post_ID, 
			$wpdb->posts.ID, $wpdb->posts.post_password FROM $wpdb->comments 
			LEFT JOIN $wpdb->posts ON comment_post_id = id WHERE $wpdb->posts.post_status = 'publish' 
			AND $wpdb->comments.comment_approved = '1' AND post_date < '".date("Y-m-d H:i:s")."'  
			ORDER BY comment_date DESC LIMIT " . get_settings('posts_per_rss') );
		}
	// this line is WordPress' motor, do not delete it.
		if ($comments) {
			foreach ($comments as $comment) {
?>
	<item>
		<title>by: <?php comment_author_rss() ?></title>
		<link><?php comment_link() ?></link>
		<pubDate><?php comment_time('r'); ?></pubDate>
		<guid><?php comment_link() ?></guid>
			<?php 
			if (!empty($comment->post_password) && $_COOKIE['wp-postpass'] != $comment->post_password) {
			?>
		<description>Protected Comments: Please enter your password to view comments.</description>
		<content:encoded><![CDATA[<?php echo get_the_password_form() ?>]]></content:encoded>
			<?php
			} else {
			?>
		<description><?php comment_text_rss() ?></description>
		<content:encoded><![CDATA[<?php comment_text() ?>]]></content:encoded>
			<?php 
			} // close check for password 
			?>
	</item>
<?php 
			}
		}
	}
} }
?>
</channel>
</rss>
