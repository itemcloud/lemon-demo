<?php //Add-On for youtube.com video links
$yt_video_links_addon['addon-name'] = 'YouTube Video Links';
$yt_video_links_addon['addon-version'] = '1.0';
$yt_video_links_addon['item-display'] = 'youtubeVideoLinks';

//Add to global $addOns variable
$addOns[] = $yt_video_links_addon;

class youtubeVideoLinks {
	function updateOutputHTML ($item) {
		if(strpos($item->file, 'youtube.com') && $this->getYoutubeIdFromUrl($item->file)) { 
			$youtube_ID = $this->getYoutubeIdFromUrl($item->file); 
			$ytFrame = '<iframe width="100%" height="446" src="https://www.youtube.com/embed/' . $youtube_ID . '" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>';
			$item->fileOutput = $ytFrame;
		}
	}
	
	function getYoutubeIdFromUrl($url) {
		$parts = parse_url($url);
		if(isset($parts['query'])){
			parse_str($parts['query'], $qs);
			if(isset($qs['v'])){
				return $qs['v'];
			}else if(isset($qs['vi'])){
				return $qs['vi'];
			}
		}
		if(isset($parts['path'])){
			$path = explode('/', trim($parts['path'], '/'));
			return $path[count($path)-1];
		}
		return false;
	}
}
?>