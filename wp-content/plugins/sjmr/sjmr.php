<?php
/*
 * Plugin Name: sjmr
 * Description:       Handle the basics with this plugin.
 * Version:           0.0.2
 * Author:            David Jensen
 */


function activation()
{
	// exit;
	error_log(__FILE__ . " " . __LINE__ . " " . date(DATE_RFC822));
	// file_put_contents("/srv/www/wordpress/sjmr.txt", "Hello World. Testing!" . time());
	getparameters();
	updatepermalinks();
	error_log(__FILE__ . " " . __LINE__ . " " . print_r(get_defined_constants(), true));
}
register_activation_hook(
	__FILE__,
	'activation'
);
function hook_css()
{
?>
	<style>
		#exampleModalLong {
			position: fixed;
			top: 100px;
		}
	</style>
	<style>
		#site-title {
			background-color: #f1f1f1;
		}
	</style>
	<style>
		.mapouter {
			position: relative;
			text-align: right;
			height: 500px;
			width: 600px;
		}
	</style>
	<style>
		.gmap_canvas {
			overflow: hidden;
			background: none !important;
			height: 500px;
			width: 600px;
		}
	</style>
<?php
}
add_action('wp_head', 'hook_css');
function admin_head()
{
?>
	<link href="/wp-content/plugins/sjmr/bootstrap.css" rel="stylesheet">
	<script src='/wp-content/plugins/sjmr/post.js'></script>
<?php
}
add_action('admin_head', 'admin_head');
add_filter('the_content', 'filter_the_content_in_the_main_loop', 1);
function updatepermalinks()
{
	$table = 'wp_jsdb_sjmr';
	$sql = 'SELECT POST, PERMALINK FROM ' . $table . ' WHERE POST IN  (SELECT ID from wp_jsdb_posts where post_type="post" and post_status="publish")';
	error_log(__FILE__ . " " . __LINE__ . " sql:" . $sql);
	$result = runsql($sql);
	foreach ($result as $row) {
		$post = $row["POST"];
		$permalink = $row["PERMALINK"];
		$PERMALINK = get_the_permalink($post);
		error_log(__FILE__ . " " . __LINE__ . ' $post:' . $post . '; $permalink: ' . $permalink . '; $PERMALINK: ' . $PERMALINK);
		if ($permalink != $PERMALINK) {
			$sql = 'UPDATE ' . $table . ' SET PERMALINK="' . $PERMALINK . '" WHERE POST="' . $post . '"';
			runsql($sql);
			error_log(__FILE__ . " " . __LINE__ . " sql:" . $sql);
		}
	}
}
function getparameters()
{
	$servername = DB_HOST;
	$username = DB_USER;
	$password = DB_PASSWORD;
	$database = DB_NAME;
	$table = 'wp_jsdb_sjmr';
	error_log(__FILE__ . " " . __LINE__ . " " . date(DATE_RFC822));
	error_log(__FILE__ . " " . __LINE__ . " servername:" . $servername);
	// Create connection
	$conn = new mysqli($servername, $username, $password, $database);
	// Check connection
	if ($conn->connect_error) {
		error_log("Connection failed: " . $conn->connect_error);
		return;
	}
	$TABLE = 'wp_jsdb_sjmr';
	// Create database
	$sql = "CREATE TABLE IF NOT EXISTS " . $table . " (POST VARCHAR(1000), TYPE CHAR(20), LINK VARCHAR(1000), PERMALINK VARCHAR(1000))";
	error_log(__FILE__ . " " . __LINE__ . " " . $sql);
	if ($conn->query($sql) === TRUE) {
		error_log(__FILE__ . ":" . __LINE__ . ": Table " . $TABLE . " created");
	} else {
		error_log(__FILE__ . ":" . __LINE__ . ": Error creating table " . $conn->error);
	}
	$conn->close();
}
function runsql($sql)
{
	error_log(__FILE__ . " " . __LINE__ . " " . date(DATE_RFC822));
	$servername = DB_HOST;
	$username = DB_USER;
	$password = DB_PASSWORD;
	$database = DB_NAME;
	// Create connection
	$conn = new mysqli($servername, $username, $password, $database);
	// Check connection
	if ($conn->connect_error) {
		error_log("Connection failed: " . $conn->connect_error);
	}
	error_log(__FILE__ . " " . __LINE__ . " " . $sql);
	$result = $conn->query($sql);
	if (!is_bool($result)) {
		$result = $result->fetch_all(MYSQLI_ASSOC);
	}
	$conn->close();
	return $result;
}
function getlink($post, $type)
{
	return runsql('SELECT * from wp_jsdb_sjmr where POST="' . $post . '" and TYPE="' . $type . '"');
}
function setlink($post, $type, $link, $permalink)
{
	runsql('INSERT INTO wp_jsdb_sjmr VALUES("' . $post . '", "' . $type . '", "' . $link . '","' . $permalink . '")');
}
function filter_the_content_in_the_main_loop($content)
{
	error_log("WP_SITEURL: " . WP_SITEURL);
	$host = parse_url(WP_SITEURL)["host"];
	$doc = new DOMDocument();
	error_log($content);
	$init = false;

	// 	$html=$doc->loadHTML($content);
	// 	error_log($html); 	
	error_log("is_singular: " . (is_singular() ? 'true' : 'false') .
		";in_the_loop: " . (in_the_loop() ? 'true' : 'false') .
		";is_main_query: " . (is_main_query() ? 'true' : 'false'));

	if (in_the_loop() && is_main_query()) {
		$post = get_the_ID();
		$permalink = get_permalink();
		error_log(__FILE__ . " " . __LINE__ . " id: " . $post . "; permalink: " . $permalink);

		$type = 'facebook';
		$facebook = NULL;
		$result = getlink($post, $type);
		error_log("result: " . count($result));
		if (count($result) == 0) {
			if ($init) {
				setlink(
					$post,
					$type,
					"https://www.facebook.com/sanjuanmountainrunners/posts/pfbid023HBeEx8PmwmhvSNY9rwsWuXZu6NqgkzbM7qmrwUVUsqxa6wAsTrxWJzKb2JkTR3l",
					$permalink
				);
				$result = getlink($post, $type);
				$facebook = $result[0]["LINK"];
			}
		} else {
			foreach ($result as $row) {
				error_log("POST: " . $row["POST"] . "; TYPE: " . $row["TYPE"] . "; LINK: " . $row["LINK"]);
			}
			$facebook = $result[0]["LINK"];
		}
		error_log("facebook: " . $facebook);
		$type = 'instagram';
		$instagram = NULL;
		$result = getlink($post, $type);
		error_log("result: " . count($result));
		if (count($result) == 0) {
			if ($init) {
				setlink(
					$post,
					$type,
					"https://www.instagram.com/p/Co3WRTYukVE/?utm_source=ig_web_copy_link",
					$permalink
				);
				$result = getlink($post, $type);
				$instagram = $result[0]["LINK"];
			}
		} else {
			foreach ($result as $row) {
				error_log("POST: " . $row["POST"] . "; TYPE: " . $row["TYPE"] . "; LINK: " . $row["LINK"]);
			}
			$instagram = $result[0]["LINK"];
		}
		error_log("instagram: " . $instagram);
		$type = 'map';
		$map = NULL;
		$result = getlink($post, $type);
		error_log("result: " . count($result));
		if (count($result) == 0) {
			if ($init) {
				setlink(
					$post,
					$type,
					"ringtail trail and dave wood",
					$permalink
				);
				$result = getlink($post, $type);
				$map = $result[0]["LINK"];
			}
		} else {
			foreach ($result as $row) {
				error_log("POST: " . $row["POST"] . "; TYPE: " . $row["TYPE"] . "; LINK: " . $row["LINK"]);
			}
			$map = $result[0]["LINK"];
		}
		error_log("map: " . $map);
		/*
		<div class="mapouter"><div class="gmap_canvas"><iframe width="600" height="500" id="gmap_canvas" src="https://maps.google.com/maps?q=ringtail%20trail%20and%20dave%20wood&t=&z=15&ie=UTF8&iwloc=&output=embed" frameborder="0" scrolling="no" marginheight="0" marginwidth="0"></iframe><a href="https://123movies-to.org"></a><br><style>.mapouter{position:relative;text-align:right;height:500px;width:600px;}</style><a href="https://www.embedgooglemap.net">how to embed google map</a><style>.gmap_canvas {overflow:hidden;background:none!important;height:500px;width:600px;}</style></div></div>
		*/
		$append = '<div class="linkcontainer"><div style="display:flex">';
		if (!is_null($facebook)) {
			$append .= <<<HTML
		<a href="$facebook"><img decoding="async" loading="lazy" class="alignnone wp-image-1633" style="border: none;" src="http://$host/wp-content/uploads/2023/02/index.png" alt="" width="41" height="41"></a>
		HTML;
		}
		if (!is_null($instagram)) {
			$append .= <<<HTML
<a href="$instagram"><img decoding="async" loading="lazy" class="alignnone wp-image-2237" style="border: none;" src="http://$host/wp-content/uploads/2023/02/instagram-Logo-PNG-Transparent-Background-download-300x300.png" alt="" width="41" height="41"></a>
HTML;
		}
		if (!is_null($map)) {
			if (str_starts_with($map, '<iframe')) {
				$append .= <<<HTML
				<span style="color: red; display: inline-block;"><img decoding="async" loading="lazy" class="alignnone wp-image-2322" style="border: none;" src="http://$host/wp-content/uploads/2023/02/map-marker-icon.png" alt="" width="41" height="41" onclick="sjmrcontext.drawmap(this)"></span>
						</div>
				<!-- https://google-map-generator.com/ used to generate map iframe--></p>
					<div class="mapouter" style="display: none;">
				<div class="gmap_canvas">
					$map 
				<p><a href="https://www.embedgooglemap.net">google map api for website</a></p>
					</div>
					</div>
				HTML;				
			} else {
				$append .= <<<HTML
<span style="color: red; display: inline-block;"><img decoding="async" loading="lazy" class="alignnone wp-image-2322" style="border: none;" src="http://$host/wp-content/uploads/2023/02/map-marker-icon.png" alt="" width="41" height="41" onclick="sjmrcontext.drawmap(this)"></span>
		</div>
<!-- https://google-map-generator.com/ used to generate map iframe--></p>
	<div class="mapouter" style="display: none;">
<div class="gmap_canvas"><iframe loading="lazy" id="gmap_canvas" src="https://maps.google.com/maps?q=$map&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="600" height="500" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe> 
<p><a href="https://www.embedgooglemap.net">google map api for website</a></p>
	</div>
	</div>
HTML;
			}
		} else {
			$append .= '</div>';
		}
		$append .= "</div>";
		return $content . $append;
	}
	/*<p><a href="https://www.facebook.com/sanjuanmountainrunners/posts/pfbid023HBeEx8PmwmhvSNY9rwsWuXZu6NqgkzbM7qmrwUVUsqxa6wAsTrxWJzKb2JkTR3l"><img decoding="async" loading="lazy" class="alignnone wp-image-1633" style="border: none;" src="http://$host/wp-content/uploads/2023/02/index.png" alt="" width="41" height="41"></a><a href="https://www.instagram.com/p/Co3WRTYukVE/?utm_source=ig_web_copy_link"><img decoding="async" loading="lazy" class="alignnone wp-image-2237" style="border: none;" src="http://$host/wp-content/uploads/2023/02/instagram-Logo-PNG-Transparent-Background-download-300x300.png" alt="" width="41" height="41" srcset="http://$host/wp-content/uploads/2023/02/instagram-Logo-PNG-Transparent-Background-download-300x300.png 300w, http://$host/wp-content/uploads/2023/02/instagram-Logo-PNG-Transparent-Background-download-150x150.png 150w, http://$host/wp-content/uploads/2023/02/instagram-Logo-PNG-Transparent-Background-download-768x768.png 768w, http://$host/wp-content/uploads/2023/02/instagram-Logo-PNG-Transparent-Background-download-500x500.png 500w, http://$host/wp-content/uploads/2023/02/instagram-Logo-PNG-Transparent-Background-download-400x400.png 400w, http://$host/wp-content/uploads/2023/02/instagram-Logo-PNG-Transparent-Background-download.png 1000w" sizes="(max-width: 41px) 100vw, 41px"></a><span style="color: red; display: inline-block;"><img decoding="async" loading="lazy" class="alignnone wp-image-2322" style="border: none;" src="http://$host/wp-content/uploads/2023/02/map-marker-icon.png" alt="" width="41" height="41" onclick="sjmrcontext.drawmap()"></span><br>
<!-- https://google-map-generator.com/ used to generate map iframe--></p>
<div class="mapouter" style="display: block;">
<div class="gmap_canvas"><iframe loading="lazy" id="gmap_canvas" src="https://maps.google.com/maps?q=looney%20bean,%20montrose,%20co&amp;t=&amp;z=13&amp;ie=UTF8&amp;iwloc=&amp;output=embed" width="600" height="500" frameborder="0" marginwidth="0" marginheight="0" scrolling="no"></iframe> <p></p>
<style>.mapouter{position:relative;text-align:right;height:500px;width:600px;}</style>
<p><a href="https://www.embedgooglemap.net">google map api for website</a></p>
<style>.gmap_canvas {overflow:hidden;background:none!important;height:500px;width:600px;}</style>
</div>
</div>
*/
	// Check if we're inside the main loop in a single Post.
	// if ( is_singular() && in_the_loop() && is_main_query() ) {
	return $content;
}
function writelog($file, $line, $message)
{
	error_log($file . ":" . $line . " " . $message);
}
function addlinks($ID, $POST, $UPDATE)
{
	$servername = DB_HOST;
	$username = DB_USER;
	$password = DB_PASSWORD;
	$database = DB_NAME;
	$table = 'wp_jsdb_sjmr';
	writelog(__FILE__, __LINE__, "Save post " . $ID . " " . get_permalink($ID));
	$sql = 'SELECT count(*) as C from ' . $table . " where POST='" . $ID . "'";
	$result = runsql($sql);
	$c = $result[0]["C"];
	writelog(__FILE__, __LINE__, "Save post result" . $ID . " " . $result . " " . $c);
	if ($c == 0) {
		foreach (array('facebook', 'instagram', 'map') as $type) {
			$sql = 'INSERT into ' . $table . " VALUES('" . $ID . "','" . $type . "', NULL, '" . get_permalink($ID) . "')";
			// writelog(__FILE__, __LINE__, "Save post sql " . $sql);
			runsql($sql);
		}
	}
}
$priority = 10;
$nargs = 3;
add_action('save_post', 'addlinks', $priority, $nargs);
function add_my_media_button()
{
	print('<a href="#" id="insert-my-media" class="button" on onclick="sjmrcontext.post(\'' . get_the_ID() . '\')">Add facebook, instagram, map links</a>');
}
add_action('media_buttons', 'add_my_media_button', 100);
function add_function()
{
	$append = <<< HTML
	<!-- Modal -->
<div class="modal fade" id="exampleModalLong" tabindex="-1" role="dialog" aria-labelledby="exampleModalLongTitle"
  aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-danger" data-dismiss="modal">Clear</button>
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
        <button type="button" class="btn btn-primary">Save changes</button>
      </div>
    </div>
  </div>
</div>
<script src='/js/sjmr.js'></script>
HTML;
	print($append);
}
add_action('wp_footer', 'add_function', 100);
?>