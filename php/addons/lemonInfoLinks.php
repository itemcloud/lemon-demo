<?php //Add-On for youtube.com video links
$info_links_addon['addon-name'] = 'Info Links';
$info_links_addon['addon-version'] = '1.0';
$info_links_addon['item-display'] = 'infoLinks';

//Add to global $addOns variable
$addOns[] = $info_links_addon;

class infoLinks {
	function updateOutputHTML ($item) {
		//only update raw info to be safe
		//include new infoLinks().updateOutputHTML($item) to use with another add-on
		$raw_input = ($item->infoOutput == $item->infoDisplayHTML()) ? $item->info : NULL;
		
		if($raw_input) { $item->infoOutput = nl2br($this->replaceUrls($item->info)); }
	}

	function replaceUrls($inputText) {
		// make the urls hyper links (rough development version) yikes!
		$reg_exUrl = '@(http(s)?)?(://)?(([a-zA-Z])([-\w]+\.)+([^\s\.]+[^\s]*)+[^,.\s])@';
		
		$inputText =  preg_replace($reg_exUrl, '<a href="http$2://$4" target="_blank" title="$0">$0</a>', $inputText);
		return $inputText;
    }
}
?>
