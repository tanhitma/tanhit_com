<?php

class WPMVideoShortCode
{
    private static $videoLink;
    private static $autoPlay;
    private static $width;
    private static $height;
    private static $ratio;
    private static $aspectRatio;
    private static $ratioStyle;
    private static $style;
    private static $videoUrl;
    private static $wrapperClass;

    public static function parse($options)
    {
        self::_parseOptions($options);

        if (self::_isVimeo()) {
            $html = self::_parseVimeo();
        } elseif (self::_isYoutube()) {
            $html = self::_parseYoutube();
        } else {
            $html = self::_parseLocal();
        }

        return $html;
    }

    private static function _parseOptions($options)
    {
        self::$videoLink = $options['video'];
        self::$autoPlay = $options['autoplay'];
        self::$width = $options['width'];
        self::$height = $options['height'];
        self::$ratio = !empty($options['ratio']) ? $options['ratio'] : '16by9';
        self::$style = $options['style'] ? $options['style'] : 'normal';
        self::$ratioStyle = '';

        if (!empty(self::$width) && !empty(self::$height)) {
            self::$ratioStyle = 'padding-bottom: ' . (self::$height / self::$width * 100) . '%;';
            self::$aspectRatio = round(self::$width / self::$height, 2);
        } elseif ($options['ratio'] == '4by3') {
            self::$aspectRatio = 1.33;
        } else {
            self::$aspectRatio = 1.77;
        }

        if (!empty(self::$width)) {
            self::$width = 'max-width: ' . self::$width . 'px';
        } else {
            self::$width = '';
        }

        self::$videoUrl = parse_url(self::$videoLink);
        self::$wrapperClass = 'embed-responsive';

        if (self::$ratioStyle === '') {
            self::$wrapperClass .= ' embed-responsive-' . self::$ratio;
        }
    }

    private static function _isVimeo()
    {
        return in_array(self::$videoUrl['host'], array('www.vimeo.com', 'vimeo.com'));
    }

    private static function _parseVimeo()
    {
        sscanf(parse_url(self::$videoLink, PHP_URL_PATH), '/%d', $vimeo_video_id);

        preg_match('#http://(?:player\.)?vimeo\.com(?:/video)?.*/(\d+)#', self::$videoLink, $vimeo_matches);

        if (isset($vimeo_matches[1])) {
            $vimeo_video_id = $vimeo_matches[1];
        }

        self::$autoPlay = (self::$autoPlay == 'on') ? '?autoplay=true' : '';

        $link = 'http://player.vimeo.com/video/' . $vimeo_video_id . self::$autoPlay;

        $html = '<div class="wpm-video-size-wrap" style="' . self::$width . '"><div class="style-video wpm-video-vimeo style-' . self::$style . '"><div class="' . self::$wrapperClass . '" style="' . self::$ratioStyle . '">';
        $html .= '<iframe class="no_border" style="margin:0 auto; padding:0;" src="' . $link . '" width="' . self::$width . '" height="' . self::$height . '" frameborder="0" webkitAllowFullScreen mozallowfullscreen allowFullScreen></iframe>';
        $html .= '</div></div></div>';

        return $html;
    }

    private static function _isYoutube()
    {
        return strpos(self::$videoUrl['host'], 'youtu') !== false;
    }

    private static function _parseYoutube()
    {
        $pattern = '#(?<=(?:v|i)=)[a-zA-Z0-9-]+(?=&)|(?<=(?:v|i)\/)[^&\n]+|(?<=embed\/)[^"&\n]+|(?<=‌​(?:v|i)=)[^&\n]+|(?<=youtu.be\/)[^&\n]+#';
        preg_match($pattern, self::$videoLink, $matches);
        if (isset($matches[0])) {
            $youtubeId = $matches[0];
        } else {
            parse_str(parse_url(self::$videoLink, PHP_URL_QUERY), $params);
            $youtubeId = isset($params['v']) ? $params['v'] : (isset($params['amp;v']) ? $params['amp;v'] : '0');
        }

        if (self::_protectionIsEnabled()) {
            $html = self::_parseYoutubeProtected($youtubeId);
        } else {
            $html = self::_parseYoutubeIframe($youtubeId);
        }

        return $html;
    }

    private static function _parseLocal()
    {
        $_SESSION["flash"] = $_SERVER["HTTP_HOST"];
        self::$videoLink = wpm_protected_video_link(self::$videoLink);

        if (self::$style != 'normal') {
            $html = '<div class="wpm-video-size-wrap" style="' . self::$width . '"><div class="style-video wpm-video-direct wpjw style-' . self::$style . '" style="' . self::$width . '"><div class="embed-responsive embed-responsive-16by9">';
            $html .= '<video class="embed-responsive-item" width="' . self::$width . '" height="' . self::$height . '" controls preload="metadata" ' . self::$autoPlay . '><source src="' . self::$videoLink['url'] . '" type="video/mp4"></video>';
            $html .= '</div></div></div>';

            return $html;
        } else {
            $html = '<div class="wpm-video-size-wrap" style="' . self::$width . '"><div class="wpm-video-direct no-style video_wrap video_margin_center" style="' . self::$width . '"><div class="' . self::$wrapperClass . '" style="' . self::$ratioStyle . '">';
            $html .= '<video class="embed-responsive-item" width="' . self::$width . '" height="' . self::$height . '" controls preload="metadata" ' . self::$autoPlay . '><source src="' . self::$videoLink['url'] . '" type="video/mp4"></video>';
            $html .= '</div></div></div>';

            return $html;
        }
    }

    private static function _protectionIsEnabled()
    {
        return wpm_yt_protection_is_enabled(get_option('wpm_main_options'));
    }

    private static function _parseYoutubeProtected($youtubeId)
    {
        $link = 'http://www.youtube.com/watch?v=' . $youtubeId;
        $linkCrypted = 'window[([][(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]+([][(![]+[])[+[]]+(![]+[]+[][[]])[+!+[]+[+[]]]+(![]+[])[!+[]+!+[]]+(!![]+[])[+[]]+(!![]+[])[!+[]+!+[]+!+[]]+(!![]+[])[+!+[]]]+[])[!+[]+!+[]+!+[]]]("' . base64_encode($link) . '")';
        $videoId = 'vid_id_' . substr(md5($youtubeId . rand(0, 1000)), 0, 20);
        $script = '<script>wpmVideo.initYT("%s",%s,"%s",%d)</script>';
        if (self::$style != 'normal') {
            $html = '<div class="wpm-video-size-wrap" style="' . self::$width . '">';
            $html .= '<div class="style-video wpm-video-youtube wpjw wpmjw inactive style-' . self::$style . '" style="' . self::$width . '">';
            $html .= '<div class="embed-responsive embed-responsive-16by9">';
            $html .= '<div id="' . $videoId . '"></div>';
            $html .= '</div></div></div>';
            $html .= sprintf($script, $videoId, "'{$link}'", "16:9", intval(self::$autoPlay == 'on'));
        } else {
            $html = '<div class="wpm-video-size-wrap" style="' . self::$width . '">';
            $html .= '<div class="wpm-video-youtube video_wrap video_margin_center wpmjw inactive" style="' . self::$width . '">';
            $html .= '<div class="' . self::$wrapperClass . '" style="' . self::$ratioStyle . '">';
            $html .= '<div id="' . $videoId . '"></div>';
            $html .= '</div></div></div>';
		$html .= sprintf($script, $videoId, "'{$link}'", self::$aspectRatio . ':1', intval(self::$autoPlay == 'on'));
        }

        return $html;
    }

    private static function _parseYoutubeIframe($youtubeId)
    {
        self::$autoPlay = (self::$autoPlay == 'on') ? '?autoplay=1&modestbranding=1&rel=0&showinfo=0' : '?modestbranding=1&rel=0&showinfo=0';
        $iframe = '<iframe width="' . self::$width . '" height="' . self::$height . '" src="http://www.youtube.com/embed/' . $youtubeId . self::$autoPlay . '" frameborder="0" allowfullscreen></iframe>';

        if (self::$style != 'normal') {
            $html = '<div class="wpm-video-size-wrap" style="' . self::$width . '">';
            $html .= '<div class="style-video wpm-video-youtube style-' . self::$style . '"  style="' . self::$width . '">';
            $html .= '<div class="embed-responsive embed-responsive-16by9">';
            $html .= $iframe;
            $html .= '</div></div></div>';

            return $html;
        } else {
            $html = '<div class="wpm-video-size-wrap" style="' . self::$width . '">';
            $html .= '<div class="wpm-video-youtube no-style video_wrap video_margin_center" style="' . self::$width . '">';
            $html .= '<div class="' . self::$wrapperClass . '" style="' . self::$ratioStyle . '">';
            $html .= $iframe;
            $html .= '</div></div></div>';

            return $html;
        }
    }

}