function VES() {
    var VESContainer = "VEScontainer";
    var VESSourse = "";
    var width = 0;
    var height = 0;

    if (arguments.length > 0) {
        VESContainer = arguments[0];
    }

    if (arguments.length > 1) {
        VESSourse = arguments[1];
    }

    if (arguments.length > 2) {
        width = arguments[2];
    }

    if (arguments.length > 3) {
        height = arguments[3];
    }

    var html2 = [];
    // "defer" attr is needed here otherwise IE executes this too early
    html2.push('<script type="text/javascript" defer="defer">\n');
    html2.push('    Seadragon.Config.debugMode = true;\n');
    html2.push('</script>\n');

    html2.push('<script type="text/javascript">\n');
    html2.push("var viewer = new Seadragon.Viewer('" + VESContainer + "');");
    html2.push("var x = { 'url': " + "'" + VESSourse + "', 'width': " + width + ", 'height': " + height + ", 'tileSize': 256, 'tileOverlap': 1, 'tileFormat': 'jpg' };\n");
    html2.push('var dziJson = eval(x);\n');
    html2.push('viewer.openDzi(dziJson);\n');
    html2.push('</script>\n');
    document.write(html2.join(''));


};


function VES_PRODUCT() {
    var VESContainer = "VEScontainer";
    var VESSourse = "";
    var width = 0;
    var height = 0;

    if (arguments.length > 0) {
        VESContainer = arguments[0];
    }

    if (arguments.length > 1) {
        VESSourse = arguments[1];
    }

    if (arguments.length > 2) {
        width = arguments[2];
    }

    if (arguments.length > 3) {
        height = arguments[3];
    }

    var html2 = [];
    // "defer" attr is needed here otherwise IE executes this too early
    html2.push('<script type="text/javascript" defer="defer">\n');
    html2.push('    Seadragon.Config.debugMode = true;\n');
    html2.push('</script>\n');

    html2.push('<script type="text/javascript">\n');
    html2.push("var viewer = new Seadragon.Viewer('" + VESContainer + "');");
    html2.push("var x = { 'url': " + "'" + VESSourse + "', 'width': " + width + ", 'height': " + height + ", 'tileSize': 256, 'tileOverlap': 1, 'tileFormat': 'jpg' };\n");
    html2.push('var dziJson = eval(x);\n');
    html2.push('viewer.openDzi(dziJson);\n');
    html2.push('</script>\n');
    document.write(html2.join(''));


};

