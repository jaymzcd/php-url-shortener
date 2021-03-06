<?php

require 'config.php';

$url = DEFAULT_URL . '/';

if (isset($_GET['slug'])) {

	$slug = $_GET['slug'];

	if ('@' == $slug) {
		$url = 'http://twitter.com/' . TWITTER_USERNAME;
	} else if (' ' == $slug) { // +
		$url = 'https://plus.google.com/u/0/' . GOOGLE_PLUS_ID . '/posts';
	} else {

		$slug = preg_replace('/[^a-z0-9]/si', '', $slug);

		if (is_numeric($slug) && strlen($slug) > 8) {
			$url = 'http://twitter.com/' . TWITTER_USERNAME . '/status/' . $slug;
		} else {

			$db = new MySQLi(MYSQL_HOST, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DATABASE);
			$db->set_charset('utf8mb4');

			$escapedSlug = $db->real_escape_string($slug);
			$redirectResult = $db->query('SELECT url FROM redirect WHERE slug = "' . $escapedSlug . '"');

			if ($redirectResult && $redirectResult->num_rows > 0) {
				$db->query('UPDATE redirect SET hits = hits + 1 WHERE slug = "' . $escapedSlug . '"');
				$url = $redirectResult->fetch_object()->url;
			} else {
				$url = DEFAULT_URL . $_SERVER['REQUEST_URI'];
			}

			$db->close();

		}
	}
}

header('Location: ' . $url, null, 301);

?>
<meta http-equiv=refresh content="0;URL=<?php echo $url; ?>"><a href="<?php echo $url; ?>">Continue</a><script>location.href='<?php echo $url; ?>'</script>