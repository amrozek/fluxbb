<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<pun_language>" lang="<pun_language>" dir="<pun_content_direction>">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />


<script type="text/javascript">
var isposted = 'no';

function start_spyro() {
//Gliding spyro function copyright The DtTvB/Spyrorocks
//You may not use/reproduce without permission.

	var startx = 0;
	var starty = 200;
	var spyro  = new Image();
	var dttvb  = [document, 'style', 'appendChild', 0];
	var style  = { };
	spyro.onload = imageloaded;
	spyro.src = '/img/spyro.fly.gif';

	function getViewport() {
		var de = document.body;
		var c  = [de.clientWidth, de.clientHeight];
		if (window.innerWidth)  c[0] = Math.min(c[0], window.innerWidth);
		if (window.innerHeight) c[1] = Math.min(c[1], window.innerHeight);
		return c;
	}
	function frame() {
		startx += 7;
		starty += 0.7;
		var vp = getViewport();
		var maxx = vp[0] - spyro.width;
		if (startx >= maxx) {
			clearTimeout (dttvb[3]);
			spyro.parentNode.removeChild (spyro);
		}
		var difx = maxx - startx;
		if (difx < 100) {
			if (typeof spyro.style.filter != 'undefined')
				spyro.style.filter = 'alpha(opacity=' + difx + ')';
			else if (typeof spyro.style.MozOpacity != 'undefined')
				spyro.style.MozOpacity = (difx / 100);
		}
		style.left = startx + 'px';
		style.top  = Math.round(starty) + 'px';
	}
	function imageloaded() {
		spyro.onload = function() { };
		startx = 0 - spyro.width;
		style = spyro[dttvb[1]];
		style.left = startx + 'px';
		style.top  = starty + 'px';
		style.position = 'absolute';
		dttvb[0]['body'][dttvb[2]] (spyro);
		dttvb[3] = setInterval(frame, 20);
	}
}
</script>


<pun_head>
</head>

<body onload="start_spyro();">

<div id="punredirect" class="pun">
<div class="top-box"><div><!-- Top Corners --></div></div>
<div class="punwrap">

<div id="brdmain">
<pun_redir_main>
</div>

<pun_footer>

</div>
<div class="end-box"><div><!-- Bottom Corners --></div></div>
</div>

</body>
</html>
