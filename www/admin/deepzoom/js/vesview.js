(function () {
    var PATH = "/admin/deepzoom/js/";      // the path to the scripts, relative to HTML page
    var SCRIPTS = [         // the script filenames, in dependency order
            "seadragon-dev.js",
            "VESBase.js"
        ];
	
    var html = [];
	document.write('<div id="VEScontainer" style="width:100%;height:100%"></div>');
    for (var i = 0; i < SCRIPTS.length; i++) {
        html.push('<script type="text/javascript" src="');
        html.push(PATH);
        html.push(SCRIPTS[i]);
        html.push('"></script>\n');
    }
	
    document.write(html.join(''));
})();
