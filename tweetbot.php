<?php  

/*

* Change the value of 'num' in the request url to change the number of fetched results.

* 'index'(second argument in find function) is set to 'null' by default, so it is required to be set to an integer


*/

require_once('dom_parser.php');

function fetch_tweets(){

if(isset($_REQUEST['go'])){

$name = strtolower(str_replace(" ","+",$_REQUEST['search_val']));

$google = file_get_html("https://www.google.co.in/search?q={$name}+twitter+profile&num=10");

$handle = $google->find('div.g div.s div.kv cite',0)->plaintext;

//echo $handle;

// Create DOM from URL or file
$html = file_get_html("{$handle}");

$profile_pic = $html->find('img.ProfileAvatar-image',0)->src;

$user_name = $html->find('a.ProfileHeaderCard-nameLink',0)->plaintext;

$twitter_handle = "@" . $html->find('span.u-linkComplex-target',0)->plaintext;

$cover_pic = $html->find('div.ProfileCanopy-headerBg img',0)->src;

$total_tweets = $html->find('span.ProfileNav-value',0)->plaintext;

$following = $html->find('span.ProfileNav-value',1)->plaintext;

$followers = $html->find('span.ProfileNav-value',2)->plaintext;

$likes = $html->find('span.ProfileNav-value',3)->plaintext;

//echo $profile_pic. " " . $user_name . " " . $twitter_handle . " " . $cover_pic . " " . $total_tweets . " ". $following . " " . $followers . " " . $likes;

$tweets = array();

$count = 1;

foreach($html->find('li.js-stream-item') as $tweet){

	if($count > 10){
		break;
	}

	$item = array();

	// Fetching as an HTML Object

	$item["text"] = $tweet->find('div.tweet div.content div.js-tweet-text-container p.tweet-text',0);

	$item["image"] = $tweet->find('div.AdaptiveMedia-photoContainer img',0)->src;

	$item["time"] = $tweet->find('span.u-hiddenVisually',0)->plaintext;

	$item["retweet"] = $tweet->find('div.QuoteTweet-authorAndText',0);

	$tweets[] = $item;

	$count++;

}

//echo $tweets[1]["retweet"];

$data =<<<DELIMITER
<div class="cover_wrapper">
	<img id="cover_pic" class="cover_pic" src="{$cover_pic}" />
	<img class="profile_pic" src="{$profile_pic}" />
	<div class="info">
		<b>{$user_name}</b><br>
		{$twitter_handle}
	</div>
</div>
<div class="stats">
	<table>
		<tr>
			<td><b>TWEETS</b></td>
			<td><b>FOLLOWING</b></td>
			<td><b>FOLLOWERS</b></td>
			<td><b>LIKES</b></td>
		</tr>
		<tr>
			<td>{$total_tweets}</td>
			<td>{$following}</td>
			<td>{$followers}</td>
			<td>{$likes}</td>
		</tr>
	</table>
</div>
<div class="tweets_wrapper">
DELIMITER;

foreach ($tweets as $chirp) {
	
	if(isset($chirp["image"])){

	$temp =<<<DELIMITER
		<div class="tweet">
			<div>{$chirp["text"]}</div>
			<div class="retweet">{$chirp["retweet"]}</div>
			<img class="tweet_image" src='{$chirp["image"]}' alt="" /><br>
			[{$chirp["time"]}]
		</div>
DELIMITER;

	}
	else{

$temp =<<<DELIMITER
		<div class="tweet">
			<div>{$chirp["text"]}</div>
			<div class="retweet">{$chirp["retweet"]}</div><br>
			[{$chirp["time"]}]
		</div>
DELIMITER;

	}

	$data .= $temp;
	
}

$data .= "</div></div>";

echo $data;

//print_r($tweets);

//echo $html;

}
}

?> 

<html>
	<head>
		<title>Tweet Bot | Twitter Crawler</title>
		<meta charset="utf-8"/>
		<style>
			body{
				margin: 0px;
				padding: 0px;
				font-family: sans-serif;
			}
			.search_wrapper{
				height: 430px;
				width: 500px;
				//background-color: #777;
				margin: 0 auto;
			}
			.logo{
				height: 400px;
				width: 300px;
				margin: 0 auto;
				display: block;
			}
			.intro_text{
				width: 350px;
				text-align: center;
				margin: 20px auto 0px;
			}
			.search_field{
				height: 40px;
				width: 400px;
				border-radius: 5px;
				margin-top: 20px;
				font-size: 20px;
			}
			.go_btn{
				height: 40px;
				width: 96px;
				cursor: pointer;
			}
			.results_wrapper{
				height: auto;
				width: 1200px;
				background-color: #000;
				margin: 140px auto 0px;
			}
			a{
				text-decoration: none;
				color: #000;
			}
			.cover_wrapper{
				height: 400px;
				width: 1200px;
				position: relative;
				float: left;
				background-color: #333D80;
			}
			.cover_pic{
				height: 400px;
				width: 1200px;
			}
			.profile_pic{
				position: absolute;
				height: 200px;
				width: 200px;
				border-radius: 5px;
				top: 280px;
				left: 50px;
			}
			.info{
				height: 70px;
				width: 200px;
				text-align: center;
				position: absolute;
				top: 485px;
				left: 50px;
			}
			.stats{
				height: 140px;
				width: 1200px;
				background-color: #fafafa;
				float: left;
			}
			.stats table{
				text-align: center;
				margin-top: 80px;
				width: 650px;
				float: right;
				margin-right: 150px;
			}
			.tweets_wrapper{
				height: auto;
				width: 1200px;
				float: left;
				//background-color: #777;
				padding-top: 50px;
			}
			.tweet{
				width: 800px;
				height: auto;
				padding: 20px;
				border-radius: 10px;
				border: 1px solid #000;
				margin: 0 auto;
				display: block;
				margin-bottom: 50px;
				font-size: 18px;
				word-wrap: break-word;
			}
			.tweet img{
				height: 20px;
			}
			.tweet_image{
				height: 400px !important;
				margin: 0 auto;
				max-width: 700px;
				display: block;
				border-radius: 10px;
			}
			.retweet{
				width: 700px;
				margin-left: 50px;
				border-radius: 5px;
				border: 1px solid #000;
				padding: 5px;
			}
			.retweet:empty{
				display: none;
			}
		</style>
	</head>
	<body>
		<div class="search_wrapper">
			<img class="logo" src="./bot.png"/>
			<p class="intro_text"><b>Tweet Bot</b> can fetch latest tweets of any person.<br>Enter Name or Twitter Handle of that person!</p>
			<form method="post" action="">
				<input class="search_field" name="search_val" type="text">
				<input class="go_btn" type="submit" value="Go" name="go">
			</form>
		</div>
		<div class="results_wrapper">
			<?php fetch_tweets(); ?>
			<div style="clear: both"></div>
		</div>
		<script>
			var cover = document.getElementById("cover_pic");

			if(cover.getAttribute("src") == ""){
				cover.style.display = "none";
			}
		</script
	</body>
</html>