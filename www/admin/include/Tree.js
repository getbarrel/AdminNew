/*
* JavaScript Library for Tree Component
*
*
* @author
*		Dion(Enet corp. Agent-Team). No rights reserved...
*/


/**
* Constant for Tree
*/

var TREE_NODE_ROOT_IMG = "/admin/resources/Common_TreeNode_FolderRoot.gif";
var TREE_FOLDER_OPEN_IMG = "/admin/resources/Common_TreeNode_FolderOpen.gif";
var TREE_FOLDER_CLOSED_IMG = "/admin/resources/Common_TreeNode_FolderClosed.gif";

var TREE_LINE_OPEN_IMG = "/admin/resources/Common_TreeNode_LineNodeOpen.gif";
var TREE_LINE_CLOSED_IMG = "/admin/resources/Common_TreeNode_LineNodeClosed.gif";
var TREE_LINE_LAST_IMG = "/admin/resources/Common_TreeNode_LineLast.gif";
var TREE_LINE_LAST_OPEN_IMG = "/admin/resources/Common_TreeNode_LineLastOpen.gif";
var TREE_LINE_LAST_CLOSED_IMG = "/admin/resources/Common_TreeNode_LineLastClosed.gif";

var TREE_LINE_NODE_IMG = "/admin/resources/Common_TreeNode_LineNode.gif";
var TREE_LINE_VERT_IMG = "/admin/resources/Common_TreeNode_LineVert.gif";
var TREE_LINE_BLANK = "/admin/resources/Common_TreeNode_Blank.gif";

var TREE_NODE_LEAF_IMG = "/admin/resources/Common_TreeNode_Note.gif";

var TREE_NODE_MMATOP_IMG= "/admin/resources/MMAReport_Top_Root.gif";


/**
* Create Tree
*/
function Tree() {
	this.id = compManager.generateID();
	
	this.nodes = new Array();
	
	this.bgImage = null;
	
	this.addNode = treeAddNode;
	this.deleteNode = treeDeleteNode;
	this.toHTML = treeToHTML;
	this.draw = treeDraw;
	this.setTwoDepth = treeSetTwoDepth;
	
	this.bInheritAttribute = true;  
	
	this.selectedNode = null;
	
	compManager.add(this);	
	
	if(compManager.theme.treeBgColor) {
		
	}
	
}

function treeSetTwoDepth()
{
	TREE_NODE_ROOT_IMG = "../.../resources/Common_TreeNode_FolderRoot.gif";
	TREE_FOLDER_OPEN_IMG = "../.../resources/Common_TreeNode_FolderOpen.gif";
	TREE_FOLDER_CLOSED_IMG = ".../resources/Common_TreeNode_FolderClosed.gif";

	TREE_LINE_OPEN_IMG = "../.../resources/Common_TreeNode_LineNodeOpen.gif";
	TREE_LINE_CLOSED_IMG = "../.../resources/Common_TreeNode_LineNodeClosed.gif";
	TREE_LINE_LAST_IMG = "../.../resources/Common_TreeNode_LineLast.gif";
	TREE_LINE_LAST_OPEN_IMG = "../.../resources/Common_TreeNode_LineLastOpen.gif";
	TREE_LINE_LAST_CLOSED_IMG = "../.../resources/Common_TreeNode_LineLastClosed.gif";

	TREE_LINE_NODE_IMG = "../.../resources/Common_TreeNode_LineNode.gif";
	TREE_LINE_VERT_IMG = "../.../resources/Common_TreeNode_LineVert.gif";
	TREE_LINE_BLANK = "../.../resources/Common_TreeNode_Blank.gif";

	TREE_NODE_LEAF_IMG = "../.../resources/Common_TreeNode_Note.gif";

	TREE_NODE_MMATOP_IMG= "../.../resources/MMAReport_Top_Root.gif";
}


/**
* Add Node tree tree
*/
function treeAddNode(objNode) {
	
	objNode.root = this;
	
	this.nodes[this.nodes.length] = objNode;
	
	objNode.idx = this.nodes.length-1;
}

/**
* Delete node
*/
function treeDeleteNode(objNode) {
    
    __deleteNode(this, objNode);
    
}


function __deleteNode(objParNode, objNode) {
    
    for(var i=0; i<objParNode.nodes.length; i++) {
        if(objParNode.nodes[i] == objNode) {
       
            var nodes = new Array();
            var ni = 0;
            for(var j=0; j<objParNode.nodes.length; j++) {
                if(i != j) {
                    nodes[ni] = objParNode.nodes[j];
                    nodes[ni].idx = ni;
                    ni++;
                }
            }
            objParNode.nodes = nodes;
            objParNode.getRootTree().selectedNode = null;
            return;
            
        }
    }
    
    for(var i=0; i<objParNode.nodes.length; i++) {
        __deleteNode(objParNode.nodes[i], objNode);
    }
    
}    

/**
* Convert tree to HTML
*/
function treeToHTML() {
	
	var szHtml = "";
	
	for(var i=0; i<this.nodes.length; i++) {
		
		szHtml += this.nodes[i].toHTML();
	}
	
		
	return szHtml;
	

	
}


/**
* Draw tree
*/
function treeDraw() {
	
	/** Change Tree Color */
	if(self.location.href.indexOf("Tree.jsp") > 0) {
		self.document.body.style.backgroundColor = compManager.theme.treeBgColor;
		this.workAreaBgColor = compManager.theme.treeBgColor;
	}
	
	
	var szHtml = this.toHTML();
	
	document.write(szHtml);
	
	
}

/**
* Create Tree Node
*/
function TreeNode(text, fimage, oimage) {
	
	this.id = compManager.generateID();
	
	this.text = text;
	this.oimage = oimage;
	this.fimage = fimage;
	this.action = null;
	this.nodes = new Array();
	this.parent = null;
	
	this.tooltip = text;
	
	this.virtualNode = false; 
	
	this.expanded = false;
	
	this.toHTML = treeNodeToHTML;
	this.addNode = treeNodeAddNode;
	this.depth = treeNodeDepth;	
	this.hasChilds = treeNodeHasChilds;
	this.getSignImage = treeNodeGetSignImage;
	this.getFolderImage = treeNodeGetFolderImage;
	this.getRootNode = treeNodeGetRootNode;
	this.getRootTree = treeNodeGetRootTree;
	this.isLast = treeNodeIsLast;
	this.select = treeNodeSelect;
	this.deselect = treeNodeDeselect;
	this.expand = treeNodeExpand;
	this.shrink = treeNodeShrink;
	this.clone = treeNodeClone;
	
	compManager.add(this);
}


/**
* Add sub node to a node
*/
function treeNodeAddNode(objNode) {
	
	objNode.parent = this;
	
	this.nodes[this.nodes.length] = objNode;
	
	objNode.idx = this.nodes.length-1;
	
}

/** 
* Get root tree
*/
function treeNodeGetRootTree() {
	
	var prev = this;
	var par = this.parent;
	while(par != null) {
		prev = par;
		par = par.parent;
	}
	
	return prev.root;
}


/**
* Get root node
*/
function treeNodeGetRootNode() {
	
	var prev = this;
	var par = this.parent;
	while(par != null) {
		prev = par;
		par = par.parent;
	}
	
	return prev;
}

/**
* Check if last node
*/
function treeNodeIsLast() {
	
	if(this.parent != null) {
		if(this.parent.nodes.length-1 == this.idx) {
			return true;
		} else {
			return false;
		}
	} 
	
	return true;
}


/**
* Select this node
*/
function treeNodeSelect() {
	
	/*document.all[this.id + "_text"].style.color = this.selectedColor;
	document.all[this.id + "_text"].style.backgroundColor = this.selectedBgColor;
		
	document.all[this.id + "_folderimage"].src = this.oimage;*/

	document.getElementById(this.id + "_text").style.color = this.selectedColor;
	document.getElementById(this.id + "_text").style.backgroundColor = this.selectedBgColor;
		
	document.getElementById(this.id + "_folderimage").src = this.oimage;
	
	this.getRootTree().selectedNode = this;
}

/**
* Deselect this node
*/
function treeNodeDeselect() {
	
	document.getElementById(this.id + "_text").style.color = this.color;
	document.getElementById(this.id + "_text").style.backgroundColor = this.workAreaBgColor;
	
	document.getElementById(this.id + "_folderimage").src = this.fimage;
}	


/**
* Expand this node
*/
function treeNodeExpand() {
	
	this.expanded = true;
	
	if(this.hasChilds()) {
		document.getElementById(this.id + "_signimage").src = this.getSignImage();
	}
	
	try {
		
		/*
		for(var i=0; i<this.nodes.length; i++) {
			//document.all[this.nodes[i].id].style.display = "block";
			__treeNodeShowChild(this.nodes[i]);
		}
		*/
		document.getElementById(this.id + "_child").style.display = "block";
	} catch(e) {}
	
	
	if(this.onExpand) {
	
		//window.execScript(this.onExpand);
		if(window.execScript) { // execScript() 함수는 익스(크롬) 전용, firefox에서는 안먹힘 kbk
			window.execScript(this.onExpand); // 익스,크롬 kbk
		} else {
			eval("window."+this.onExpand); // firefox kbk
		}
	}
	
	
	
}


function __treeNodeShowChild(objNode) {
	
	//document.all[objNode.id].style.display = "block";
	document.getElementById(objNode.id).style.display = "block";
	for(var i=0; i<objNode.nodes.length; i++) {
		if(objNode.expanded) {
			__treeNodeShowChild(objNode.nodes[i]);
		}
	}
}
	

/**
* Shrink this node
*/
function treeNodeShrink() {
	
	this.expanded = false;
	
	if(this.hasChilds()) {
		//document.all[this.id + "_signimage"].src = this.getSignImage();
		document.getElementById(this.id + "_signimage").src = this.getSignImage();
	}
	try {
		/*
		for(var i=0; i<this.nodes.length; i++) {
			//document.all[this.nodes[i].id].style.display = "none";
			__treeNodeHideChild(this.nodes[i]);
		}
		*/
		//document.all[this.id + "_child"].style.display = "none";
		document.getElementById(this.id + "_child").style.display = "none";
	} catch(e) {}
}


function __treeNodeHideChild(objNode) {
	
	//document.all[objNode.id].style.display = "none";
	document.getElementById(objNode.id).style.display = "none";
	for(var i=0; i<objNode.nodes.length; i++) {
		
			__treeNodeHideChild(objNode.nodes[i]);
		
	}
}	
	


/**
* get appropriate line image
*/
function treeNodeGetSignImage() {
	
	if(this.hasChilds()) {
		if(this.expanded) {
			if(this.isLast()) { 
				return TREE_LINE_LAST_OPEN_IMG;
			} else {
				return TREE_LINE_OPEN_IMG;
			}
			
		} else {
			if(this.isLast()) { 
				return TREE_LINE_LAST_CLOSED_IMG;
			} else {
				return TREE_LINE_CLOSED_IMG;
			}
		}
			
		
	} else {
		if(this.isLast()){
			return TREE_LINE_LAST_IMG;
		} else {
			return TREE_LINE_NODE_IMG;
		}
	}
	
	
	
}


/**
* get appropriate folder image
*/
function treeNodeGetFolderImage() {
	
	if(this.selected) return this.oimage;
	else return this.fimage;
}
	


/**
* Check if has child...
*/
function treeNodeHasChilds() {
	
	if(this.nodes.length == 0) return false;
	else return true;
}


	

/**
* calculate depth
*/
function treeNodeDepth() {
	var par = this.parent;
	var depth = 0;
	while(par != null) {
		depth++;
		par = par.parent;
	}
	
	return depth;
}


/**
* Clone this node
* only visible property is cloned...
*/
function treeNodeClone() {
	
	var newNode = new TreeNode(this.text, this.fimage, this.oimage);
	
	return newNode;
}


/**
* convert TreeNode to HTML
*/
function treeNodeToHTML() {
	
	var szHtml = "";
	
	
	if(this.virtualNode == true) { return szHtml; } 
	
	var objRoot = this.getRootTree();
	
	if(objRoot.bInheritAttribute) {
		this.color = objRoot.color;
		this.bgColor = objRoot.bgColor;
		this.onColor = objRoot.onColor;
		this.onBgColor = objRoot.onBgColor;
		this.selectedColor = objRoot.selectedColor;
		this.selectedBgColor = objRoot.selectedBgColor;
		this.workAreaBgColor = objRoot.workAreaBgColor;
	}
	
	
	
	szHtml += "<table border=0 cellspacing=0 cellpadding=0 id=" + this.id + " style='background-color:" + this.workAreaBgColor + ";color:" + this.color + 
				";border-width:0;border:0px solid #000000;";
	/*
	if(this.parent != null && this.parent.expanded == false) {
		szHtml += ";display:none";
	}
	*/
	szHtml += "'>";
	
	szHtml += "<tr valign=middle>";
	
	var szLineImg = "";
	
	
	if(this.depth() != 0) { 
		
		
		var iDepth = this.depth();
		var par = this.parent;
		
		for(var i=0; i<iDepth-1; i++) {
		
			if(par.isLast()) {
				szLineImg = "<td nowrap><img border=0 width=19 height=16  src=" + TREE_LINE_BLANK + "></td>" + szLineImg;
			} else {
				szLineImg = "<td nowrap><img width=19 height=16  src=" + TREE_LINE_VERT_IMG + "></td>" + szLineImg;
			}
			
			par = par.parent;
		
		}
		
		
		szHtml += szLineImg;
		
		szHtml += "<td nowrap valign=middle><img width=19 height=16  id=" + this.id + "_signimage src=" + this.getSignImage() +
					" onMouseOver=\"treeNodeOnMouseOver('" + this.id + "',event);return false;\"" +
					" onMouseOut=\"treeNodeOnMouseOut('" + this.id + "',event);return false;\"" +
					" onClick=\"treeNodeOnClick('" + this.id + "',event);return false;\""+
					"></td>";
		
	}
	
	
	szHtml += "<td nowrap><img width=19 height=16  id=" + this.id + "_folderimage src=" + this.getFolderImage() + " border=0" +
					" onMouseOver=\"treeNodeOnMouseOver('" + this.id + "',event);return false;\"" +
					" onMouseOut=\"treeNodeOnMouseOut('" + this.id + "',event);return false;\""+
					" onClick=\"treeNodeOnClick('" + this.id + "',event);return false;\"" +
					"></td>";
					
	
	szHtml += "<td nowrap><div id=" + this.id + "_text title='" + this.tooltip + "'" +  
					" onMouseOver=\"treeNodeOnMouseOver('" + this.id + "',event);return false;\"" +
					" onMouseOut=\"treeNodeOnMouseOut('" + this.id + "',event);return false;\"" +
					" onClick=\"treeNodeOnClick('" + this.id + "',event);return false;\"" +
					" onDblClick=\"treeNodeOnDblClick('" + this.id + "',event);return false;\"" +
					" onContextMenu=\"treeNodeOnContextMenu('" + this.id + "',event);return false;\"" +
					">" + this.text + 
					"</div></td>";
	
	szHtml += "</tr></table>";
	
	
	
	if(this.expanded) szHtml += "<div id=" + this.id + "_child style='margin:0;padding:0'>";
	else szHtml += "<div id=" + this.id + "_child style='margin:0;padding:0;display:none;'>";
	
	for(var i=0; i<this.nodes.length; i++){
		szHtml += this.nodes[i].toHTML();
	}
	
	szHtml += "</div>";
	
	return szHtml;
	
}

function treeNodeOnMouseOver(id,evt) {
	
	var nodeComp = compManager.get(id);
	//var node = document.all[id];
	var node = document.getElementById(id);
	
	
	if(nodeComp != nodeComp.getRootTree().selectedNode) {
		//document.all[id + "_text"].style.color = nodeComp.workAreaOnColor;
		document.getElementById(id + "_text").style.color = nodeComp.workAreaOnColor;
	}
	//document.all[id].style.cursor = "hand";
	document.getElementById(id).style.cursor = "pointer";
	
	
	if(nodeComp.onMouseOver) {
		//window.execScript(nodeComp.onMouseOver);
		if(window.execScript) { // execScript() 함수는 익스(크롬) 전용, firefox에서는 안먹힘 kbk
			window.execScript(nodeComp.onMouseOver); // 익스,크롬 kbk
		} else {
			eval("window."+nodeComp.onMouseOver); // firefox kbk
		}
	}
	
	
	evt.cancelBubble = nodeComp.cancelBubble;
	
}


function treeNodeOnMouseOut(id,evt) {
	
	var nodeComp = compManager.get(id);
	//var node = document.all[id];
	var node = document.getElementById(id);
	
	if(nodeComp != nodeComp.getRootTree().selectedNode) {
		//document.all[id + "_text"].style.color = nodeComp.color;
		document.getElementById(id + "_text").style.color = nodeComp.color;
	}
	//document.all[id].style.cursor = "default";
	document.getElementById(id).style.cursor = "default";
	
	
	if(nodeComp.onMouseOut) {
		//window.execScript(nodeComp.onMouseOut);
		if(window.execScript) { // execScript() 함수는 익스(크롬) 전용, firefox에서는 안먹힘 kbk
			window.execScript(nodeComp.onMouseOut); // 익스,크롬 kbk
		} else {
			eval("window."+nodeComp.onMouseOut); // firefox kbk
		}
	}
	
	
	evt.cancelBubble = nodeComp.cancelBubble;
	
}


function treeNodeOnClick(id,evt) {

	var nodeComp = compManager.get(id);
	//var node = document.all[id];
	var node = document.getElementById(id);
	var target=evt.target?evt.target:evt.srcElement; // 호환성을 위해 event 추가 2011-04-04 kbk
	if(target.id == id+"_text") { 
		
		if(nodeComp.getRootTree().selectedNode != null) {
			nodeComp.getRootTree().selectedNode.deselect();
		}
		
		nodeComp.select();
		if(nodeComp.action != null) {			
			//window.execScript(nodeComp.action);
			if(window.execScript) { // execScript() 함수는 익스(크롬) 전용, firefox에서는 안먹힘 kbk
				window.execScript(nodeComp.action); // 익스,크롬 kbk
			} else {
				eval("window."+nodeComp.action); // firefox kbk
			}
		}
		
	} else if(target.id == id + "_signimage" ||
				target.id == id + "_folderimage") { 
		
		
		if(nodeComp.root) {
	    
	    }
		else {
    		if(nodeComp.expanded) nodeComp.shrink();
    		else nodeComp.expand();
        }
	}
	
	
	if(nodeComp.onClick) {
		//window.execScript(nodeComp.onClick);
		if(window.execScript) { // execScript() 함수는 익스(크롬) 전용, firefox에서는 안먹힘 kbk
			window.execScript(nodeComp.onClick); // 익스,크롬 kbk
		} else {
			eval("window."+nodeComp.onClick); // firefox kbk
		}
	}
	
	
	evt.cancelBubble = nodeComp.cancelBubble;
	
	return false;
	
}


function treeNodeOnDblClick(id,evt) {
	
	var nodeComp = compManager.get(id);
	//var node = document.all[id];
	var node = document.getElementById(id);
	
	if(nodeComp.root) return; 
	var target=evt.target?evt.target:evt.srcElement; // 호환성을 위해 event 추가 2011-04-04 kbk
	if(target.id == id+"_text") { 
		
		if(nodeComp.expanded) nodeComp.shrink();
		else { nodeComp.expand(); }
	}
	
	
	if(nodeComp.onDblClick) {
		//window.execScript(nodeComp.onDblClick);
		if(window.execScript) { // execScript() 함수는 익스(크롬) 전용, firefox에서는 안먹힘 kbk
			window.execScript(nodeComp.onDblClick); // 익스,크롬 kbk
		} else {
			eval("window."+nodeComp.onDblClick); // firefox kbk
		}
	}
	
	
	evt.cancelBubble = nodeComp.cancelBubble;
	
	
	return false;
}




function treeNodeOnContextMenu(id,evt) {
	
	var objNodeComp = compManager.get(id);
	
	if(objNodeComp.context) {
		
		objNodeComp.context.target = objNodeComp;
		objNodeComp.context.show();
		
		if(objNodeComp.getRootTree().selectedNode != null) {
			objNodeComp.getRootTree().selectedNode.deselect();
		}
		
		
		if(objNodeComp.action && objNodeComp != objNodeComp.getRootTree().selectedNode) {
			//window.execScript(objNodeComp.action);
			if(window.execScript) { // execScript() 함수는 익스(크롬) 전용, firefox에서는 안먹힘 kbk
				window.execScript(objNodeComp.action); // 익스,크롬 kbk
			} else {
				eval("window."+objNodeComp.action); // firefox kbk
			}
		}
		
		objNodeComp.select();
		
		
	}
	
	
	if(objNodeComp.onContextMenu) {
		//window.execScript(objNodeComp.onContextMenu);
		if(window.execScript) { // execScript() 함수는 익스(크롬) 전용, firefox에서는 안먹힘 kbk
			window.execScript(objNodeComp.onContextMenu); // 익스,크롬 kbk
		} else {
			eval("window."+objNodeComp.onContextMenu); // firefox kbk
		}
		
	} 
	
	
	evt.cancelBubble = true;
	
	return false;
}

