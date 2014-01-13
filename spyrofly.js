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
	spyro.src = 'style/Spyro/spyro.fly.gif';

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