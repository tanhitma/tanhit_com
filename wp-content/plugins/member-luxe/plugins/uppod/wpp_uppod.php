<?php
/*

*/

// SETTINGS
$wpm_uppod_settings['uppod.swf']= plugins_url().'/member-luxe/plugins/uppod/uppod.swf';
$wpm_uppod_settings['swfobject.js']='http://ajax.googleapis.com/ajax/libs/swfobject/2.2/swfobject.js';
$wpm_uppod_settings['adobe_update']='Необходимо обновить <a href="http://get.adobe.com/flashplayer/" target="_blank">Adobe Flash Player</a>';
$wpm_uppod_settings['wmode']='transparent';

//VIDEO
$wpm_uppod['video']['style']= plugins_url().'/member-luxe/plugins/uppod/video47_black.txt';
$wpm_uppod['video']['width']='640';
$wpm_uppod['video']['height']='360';

$wpm_uppod['video']['style2']='';
$wpm_uppod['video']['width2']='400';
$wpm_uppod['video']['height2']='300';

//AUDIO
$wpm_uppod['audio']['style']='';
$wpm_uppod['audio']['width']='500';
$wpm_uppod['audio']['height']='33';

//PHOTO
$wpm_uppod['photo']['style']='';
$wpm_uppod['photo']['width']='400';
$wpm_uppod['photo']['height']='300';

function wpm_uppod($atts, $content = null){
    global $wpm_uppod;
	global $wpm_uppod_settings;
    $o='';
    $fv='';
    if($atts['video']){
		$m='video';
		$wpm_uppod['video']['width'] = $atts['width'];
		$wpm_uppod['video']['height'] = $atts['height'];
		
	}
	if($atts['audio']){
		$m='audio';
		if($atts['autoplay'] == 'on'){
			if($atts['color'] == 'black') $wpm_uppod['audio']['style']= plugins_url().'/member-luxe/plugins/uppod/audio47_black_autoplay.txt';
			else $wpm_uppod['audio']['style']= plugins_url().'/member-luxe/plugins/uppod/audio47_white_autoplay.txt';
		}else{
			if($atts['color'] == 'black') $wpm_uppod['audio']['style']= plugins_url().'/member-luxe/plugins/uppod/audio47_black.txt';
			else $wpm_uppod['audio']['style']= plugins_url().'/member-luxe/plugins/uppod/audio47_white.txt';
			}

	}
	if($atts['photo']){
		$m='photo';
	}
	$atts['type']?$t=$atts['type']:$t='';
	foreach($atts as $k => $value) {
		$k!=$m?$fv.=',"'.$k.'":"'.$value.'"':'';
	}
	$num=rand(0,1000);
    if($atts['autoplay'] == 'on'){
		$wpm_uppod['video']['style']= plugins_url().'/member-luxe/plugins/uppod/video47_black_autoplay.txt';
		}else{
			$wpm_uppod['video']['style']= plugins_url().'/member-luxe/plugins/uppod/video47_black.txt';
			}
	
	if(isset($m)){
        strpos($atts[$m], ',') === false
            ? (strpos($atts[$m], '.txt') == strlen($atts[$m]) - 4
                ? $fv .= ',"pl":"' . $atts[$m] . '"'
                : $f = $atts[$m])/*$fv .= ',"file":"' . $atts[$m] . '"')*/
            : $fv .= ',"pl":"' . wpm_uppod_pl($atts[$m]) . '"';
    	if($wpm_uppod_settings['uppod.swf']=='http://'|$wpm_uppod_settings['uppod.swf']==''){
    		$o='Ошибка: в настройках плагина Uppod не указана ссылка на плеер (<a href="http://uppod.ru/player/faq/wordpress/">Видео урок</a>)';
    	}else{
			$o='<div id="'.$m.'player'.$num.'">'.$wpm_uppod_settings['adobe_update'].'</div><script type="text/javascript">var flashvars = {'.($wpm_uppod[$m]['style'.$t]!=''?'"st":"'.$wpm_uppod[$m]['style'.$t].'"':'"m":"'.$m.'"').$fv.'};'.(isset($f)?('flashvars[(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[!+[]+!+[]+!+[]]]=window[([][(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]+([][(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]]("'.$f.'");'):'').'var params = {allowFullScreen:"true", allowScriptAccess:"always",id:"'.$m.'player'.$num.'"'.($wpm_uppod_settings['wmode']!=''?',"wmode":"'.$wpm_uppod_settings['wmode'].'"':'').'}; new swfobject.embedSWF("'.$wpm_uppod_settings['uppod.swf'].'", "'.$m.'player'.$num.'", "'.$wpm_uppod[$m]['width'.$t].'", "'.$wpm_uppod[$m]['height'.$t].'", "10.0.0.0", false, flashvars, params);</script>';
		}
	}
    return $o;
}
function wpm_uppod_swfobject() {
	global $wpm_uppod_settings;
	echo '<script src="'.$wpm_uppod_settings['swfobject.js'].'" type="text/javascript"></script>';
}
function wpm_uppod_pl($str) {
	$pl="{'playlist':[";
	$obj=split(',',$str);
	for($i=0;$i<count($obj);$i++){
		$pl.="{'file':'".$obj[$i]."'},";
	}
	return chop($pl,',')."]}";
}
add_action('wpm_head', 'wpm_uppod_swfobject');
add_shortcode('wpm_uppod', 'wpm_uppod');
