<!DOCTYPE html>
<html>
<head>
	<title>Bulk Sitemap Extractor</title>
<style type="text/css">
body {
  background: #ddd;
  margin: 0;
  padding: 0;
}
#wrap {
  width: 500px;
  background: #fff;
  padding: 20px;
  box-sizing: border-box;
  margin: 20px auto;
}
h1 {
  font-size: 22px;
  margin: 0 0 10px;
}
.l {
  width: 100%;
  box-sizing: border-box;
  border: 1px solid #ddd;
  padding: 10px;
  font-family: arial;
  margin: 0 0 10px;
  height: 300px;
}
.s {
  background: #2980b9;
  color: #fff;
  border: none;
  font-size: 14px;
  padding: 5px 20px;
  cursor: pointer;
}	
.ldg {
    background: #f3e5f5 none repeat scroll 0 0;
    clear: both;
    overflow: hidden;
    margin: 0 0 10px;
}
.ldgi {
    background: #9c27b0 none repeat scroll 0 0;
    display: block;
    height: 10px;
}
p {
  margin: 10px 0;
}
</style>
</head>
<body>
<div id="wrap">
	<h1>Bulk Sitemap Extractor 1.0</h1>
	<?php 
	define("dsn","mysql:host=localhost;dbname=resep");
	define("user","root");
	define("passwd","root");
	$pdo  = new PDO(dsn, user, passwd);
	if(isset($_POST['s'])){
		$l 		= $_POST['l'];
		// echo $l;
		$date   = date('Y-m-d-H-i-s');
		$file 	= $date.'.txt';
		file_put_contents($file, $l);
		$data	= file_get_contents($file);
		$data 	= explode("\n", $data);
		$total 	= count($data);
		echo '<p>Total: '.$total.' sitemaps saved. <a href="index.php?u='.$file.'&s=1">Click here</a> to grab all urls!</p>';
	} elseif(isset($_GET['u'])) {
		$file 	= $_GET['u'];
		$data 	= file_get_contents($file);
		$data 	= explode("\n", $data);
		$total 	= count($data);
		$step 	= $_GET['s'];
		$persen = number_format($step/$total*100,2);
		if(!file_exists('hasil')) mkdir('hasil');
		$saved 	= 'hasil/url.'.$file;
		if(!file_exists($saved)){
			file_put_contents($saved, '');
		}
		$urls 	= file_get_contents($saved);
		if($step>$total){
			echo '<p>Extract urls done! You can download your extracted urls <a href="'.$saved.'">here</a>.</p>';
		} else {
			$url = str_replace(array("\n","\r"," ","\t"),'',$data[$step-1]);
			echo '<p>Grabbing urls... ('.$persen.'%)</p>';
			echo '<div class="ldg"><div class="ldgi" style="width:'.$persen.'%"></div></div>';
			echo '<small>'.$url.'</small>';
			$ua  = 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)';
	    	$xua = explode('|', $ua);
	    	shuffle($xua);
		    $theua = $xua[0];
		    // ini_set('user_agent', $theua);
			$xml 	= simplexml_load_string(file_get_contents($url));
			// print_r($xml);
			$link 	= $urls;
			foreach($xml->url as $links){
				$link .= trim($links->loc)."\n";
				$q1  = 'INSERT INTO url(url) VALUES(?)';
				$s1  = $pdo->prepare($q1);
				$s1->execute([trim($links->loc)]);
			}
			file_put_contents($saved, $link);
			echo '<meta http-equiv="refresh" content="3; url=index.php?u='.$file.'&s='.($step+1).'">';
		}
	} else {
	?>
	<form method="post" action="">
		<textarea name="l" class="l" placeholder="insert list of sitemaps to extract"></textarea>
		<button type="submit" name="s" class="s">Extract</button>
	</form>
<?php } ?>
</div>
</body>
</html>