$(document).ready(function() {

if ((screen.width<=800) && (screen.height<=600)) {
alert('Screen size: 1024x768 or larger');
$("link[rel=stylesheet]:not(:first)").attr({href : "detect1024.css"});
}
else  {
alert('Screen size: less than 1024x768, 800x600 maybe?');
$("link[rel=stylesheet]:not(:first)").attr({href : "detect800.css"});
}
});

