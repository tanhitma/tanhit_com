function open_win(url)
{
    var video_id = url.split('v=')[1];
    var ampersandPosition = video_id.indexOf('&');
    if(ampersandPosition != -1) {
        video_id = video_id.substring(0, ampersandPosition);
    }
    var embed_url = 'http://www.youtube.com/embed/'+video_id+'?rel=0';
    myWindow=window.open(embed_url, 'wppage', 'width=640,height=390,location=no,left=300,top=200,location=no,scrollbar=no,toolbar=no,statusbar=no');
    myWindow.focus();
    return false;
}
