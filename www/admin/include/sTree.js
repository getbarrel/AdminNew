/*
* JavaScript Library for sTree Component
*
*
* @author
*		Dion(Enet corp. Agent-Team). No rights reserved...
*/


/**
* Constant for sTree
*/

var TREE_NODE_ROOT_IMG = "../resources/Common_TreeNode_FolderRoot.gif";
var TREE_FOLDER_OPEN_IMG = "../resources/Common_TreeNode_FolderOpen.gif";
var TREE_FOLDER_CLOSED_IMG = "../resources/Common_TreeNode_FolderClosed.gif";

var TREE_LINE_OPEN_IMG = "../resources/Common_TreeNode_LineNodeOpen.gif";
var TREE_LINE_CLOSED_IMG = "../resources/Common_TreeNode_LineNodeClosed.gif";
var TREE_LINE_LAST_IMG = "../resources/Common_TreeNode_LineLast.gif";
var TREE_LINE_LAST_OPEN_IMG = "../resources/Common_TreeNode_LineLastOpen.gif";
var TREE_LINE_LAST_CLOSED_IMG = "../resources/Common_TreeNode_LineLastClosed.gif";

var TREE_LINE_NODE_IMG = "../resources/Common_TreeNode_LineNode.gif";
var TREE_LINE_VERT_IMG = "../resources/Common_TreeNode_LineVert.gif";
var TREE_LINE_BLANK = "../resources/Common_TreeNode_Blank.gif";

var TREE_NODE_LEAF_IMG = "../resources/Common_TreeNode_Note.gif";

var TREE_NODE_MMATOP_IMG= "../resources/MMAReport_Top_Root.gif";


/**
* Create sTree
*/
function sTree() {
	this.id = compManager.generateID();
	
	this.nodes = new Array();
	
	this.bgImage = null;
	
	this.addNode = streeAddNode;
	this.deleteNode = streeDeleteNode;
	this.toHTML = streeToHTML;
	this.draw = streeDraw;
	this.setTwoDepth = streeSetTwoDepth;
	
	this.bInheritAttribute = true;  
	
	this.selectedNode = null;
	
	compManager.add(this);	
	
	if(compManager.theme.treeBgColor) {
		
	}
	
}

function streeSetTwoDepth()
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
* Add Node stree stree
*/
function streeAddNode(objNode) {
	
	objNode.root = this;
	
	this.nodes[this.nodes.length] = objNode;
	
	objNode.idx = this.nodes.length-1;
}

/**
* Delete node
*/
function streeDeleteNode(objNode) {
    
    __sdeleteNode(this, objNode);
    
}


function __sdeleteNode(objParNode, objNode) {
    
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
        __sdeleteNode(objParNode.nodes[i], objNode);
    }
    
}    

/**
* Convert stree to HTML
*/
function streeToHTML() {
	
	var szHtml = "";
	
	for(var i=0; i<this.nodes.length; i++) {
		
		szHtml += this.nodes[i].toHTML();
	}
	
		
	return szHtml;
	

	
}


/**
* Draw stree
*/
function streeDraw() {
	
	/** Change sTree Color */
	if(self.location.href.indexOf("Tree.jsp") > 0) {
		self.document.body.style.backgroundColor = compManager.theme.treeBgColor;
		this.workAreaBgColor = compManager.theme.treeBgColor;
	}
	
	
	var szHtml = this.toHTML();
	document.write(szHtml);
	
	
}

/**
* Create sTree Node
*/
function sTreeNode(text, fimage, oimage) {
	
	this.id = compManager.generateID();
	this.cid = null;
	this.is_layout_apply = null;
	this.cdepth = null;
	this.text = text;
	this.oimage = oimage;
	this.fimage = fimage;
	this.action = null;
	this.nodes = new Array();
	this.parent = null;
	
	this.tooltip = text;
	
	this.virtualNode = false; 
	
	this.expanded = false;
	
	this.toHTML = streeNodeToHTML;
	this.addNode = streeNodeAddNode;
	this.depth = streeNodeDepth;	
	this.hasChilds = streeNodeHasChilds;
	this.getSignImage = streeNodeGetSignImage;
	this.getFolderImage = streeNodeGetFolderImage;
	this.getRootNode = streeNodeGetRootNode;
	this.getRootTree = streeNodeGetRootTree;
	this.isLast = streeNodeIsLast;
	this.select = streeNodeSelect;
	this.deselect = streeNodeDeselect;
	this.expand = streeNodeExpand;
	this.shrink = streeNodeShrink;
	this.clone = streeNodeClone;
	
	compManager.add(this);
}


/**
* Add sub node to a node
*/
function streeNodeAddNode(objNode) {
	
	objNode.parent = this;
	
	this.nodes[this.nodes.length] = objNode;
	
	objNode.idx = this.nodes.length-1;
	
}

/** 
* Get root stree
*/
function streeNodeGetRootTree() {
	
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
function streeNodeGetRootNode() {
	
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
function streeNodeIsLast() {
	
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
function streeNodeSelect() {
	
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
function streeNodeDeselect() {
	
	/*document.all[this.id + "_text"].style.color = this.color;
	document.all[this.id + "_text"].style.backgroundColor = this.workAreaBgColor;
	
	document.all[this.id + "_folderimage"].src = this.fimage;*/

	document.getElementById(this.id + "_text").style.color = this.color;
	document.getElementById(this.id + "_text").style.backgroundColor = this.workAreaBgColor;
	
	document.getElementById(this.id + "_folderimage").src = this.fimage;
}	


/**
* Expand this node
*/
function streeNodeExpand() {
	
	this.expanded = true;
	
	if(this.hasChilds()) {
		//document.all[this.id + "_signimage"].src = this.getSignImage();
		document.getElementById(this.id + "_signimage").src = this.getSignImage();
	}
	
	try {
		
		/*
		for(var i=0; i<this.nodes.length; i++) {
			//document.all[this.nodes[i].id].style.display = "block";
			__treeNodeShowChild(this.nodes[i]);
		}
		*/
		//document.all[this.id + "_child"].style.display = "block";
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
function streeNodeShrink() {
	
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
function streeNodeGetSignImage() {
	
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
function streeNodeGetFolderImage() {
	
	if(this.selected) return this.oimage;
	else return this.fimage;
}
	


/**
* Check if has child...
*/
function streeNodeHasChilds() {
	
	if(this.nodes.length == 0) return false;
	else return true;
}


	

/**
* calculate depth
*/
function streeNodeDepth() {
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
function streeNodeClone() {
	
	var newNode = new sTreeNode(this.text, this.fimage, this.oimage);
	
	return newNode;
}


/**
* convert sTreeNode to HTML
*/
function streeNodeToHTML() {
	
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
				";border-width:0";
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
					" onMouseOver=\"streeNodeOnMouseOver('" + this.id + "',event);return false;\"" +
					" onMouseOut=\"streeNodeOnMouseOut('" + this.id + "',event);return false;\"" +
					" onClick=\"streeNodeOnClick('" + this.id + "',event);return false;\""+
					"></td>";
		
	}
	
	
	szHtml += "<td nowrap width=18><!--div style='position:absolute;width:5px;'--><div style='position:relative;z-index:2;top:0px;'><input type=checkbox name='pcode[]' id='__cid_" + this.id + "' onclick='checkBoxOnClick(this)' value='" + this.cid + "' cdepth='" + this.cdepth + "' is_layout_apply='" + this.is_layout_apply + "' style='padding:0px;margin:0px;border:0px;' ></div><!--/div--></td>";
					
	szHtml += "<td nowrap><img width=19 height=16  id=" + this.id + "_folderimage src=" + this.getFolderImage() + " border=0" +
					" onMouseOver=\"streeNodeOnMouseOver('" + this.id + "',event);return false;\"" +
					" onMouseOut=\"streeNodeOnMouseOut('" + this.id + "',event);return false;\""+
					" onClick=\"streeNodeOnClick('" + this.id + "',event);return false;\"" +
					"></td>";
					
	
	szHtml += "<td nowrap><div id='" + this.id + "_text' title='" + this.tooltip + "'" +  
					" onMouseOver=\"streeNodeOnMouseOver('" + this.id + "',event);return false;\"" +
					" onMouseOut=\"streeNodeOnMouseOut('" + this.id + "',event);return false;\"" +
					" " + //onClick=\"streeNodeOnClick('" + this.id + "',event);return false;\"
					" onDblClick=\"streeNodeOnDblClick('" + this.id + "',event);return false;\"" +
					" onContextMenu=\"streeNodeOnContextMenu('" + this.id + "',event);return false;\"" +
					" style=''><label for='__cid_" + this.id + "'>" + this.text +  
					"</label></div></td>";
	
	szHtml += "</tr></table>";
	
	
	
	if(this.expanded) szHtml += "<div id=" + this.id + "_child style='margin:0;padding:0'>";
	else szHtml += "<div id=" + this.id + "_child style='margin:0;padding:0;display:none;'>";
	
	for(var i=0; i<this.nodes.length; i++){
		szHtml += this.nodes[i].toHTML();
	}
	
	szHtml += "</div>";
	return szHtml;
	
}

function streeNodeOnMouseOver(id,evt) {
	
	var nodeComp = compManager.get(id);
	//var node = document.all[id];
	var node = document.getElementById(id);
	
	
	if(nodeComp != nodeComp.getRootTree().selectedNode) {
		//alert(id + "_text ::::: "+document.all[id + "_text"].length);
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


function streeNodeOnMouseOut(id,evt) {
	
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


function streeNodeOnClick(id,evt) {
	
	var nodeComp = compManager.get(id);
	//var node = document.all[id];
	var node = document.getElementById(id);
	var tg=evt.target?evt.target:evt.srcElement;
	
	//if(event.srcElement.id == id+"_text") { 
	if(tg.id == id+"_text") { 
		
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
		
	//} else if(event.srcElement.id == id + "_signimage" ||	event.srcElement.id == id + "_folderimage") { 
	} else if(tg.id == id + "_signimage" ||	tg.id == id + "_folderimage") { 
		
		
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
	
	
	//event.cancelBubble = nodeComp.cancelBubble;
	evt.cancelBubble = nodeComp.cancelBubble;
	
	return false;
	
}


function streeNodeOnDblClick(id,evt) {
	
	var nodeComp = compManager.get(id);
	//var node = document.all[id];
	var node = document.getElementById(id);
	var tg=evt.target?evt.target:evt.srcElement;
	if(nodeComp.root) return; 
	
	//if(event.srcElement.id == id+"_text") { 
	if(tg.id == id+"_text") { 
		
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
	
	
	//event.cancelBubble = nodeComp.cancelBubble;
	evt.cancelBubble = nodeComp.cancelBubble;
	
	
	return false;
}




function streeNodeOnContextMenu(id,evt) {
	
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
	
	
	//event.cancelBubble = true;
	evt.cancelBubble = true;
	
	return false;
}

function checkBoxOnClick(obj){

	//alert(obj.value.substring(0,(obj.cdepth+1)*3)+":::"+obj.cdepth);
	//var _depth = obj.cdepth;
	var _depth = obj.getAttribute("cdepth");
	//var cobj = document.all.__cid;
	var cobj=document.getElementsByName('pcode[]');
	
	for(i=0; i < cobj.length;i++){
		//alert(obj.value.substring(0,(obj.cdepth+1)*3)+":::"+cobj[i].value.substring(0,(obj.cdepth+1)*3));
		
		
		if(obj.value == cobj[i].value){
			//alert(obj.value+"=="+cobj[i].value);
			if(obj.checked){
				cobj[i].checked = true;
			}else{
				cobj[i].checked = false;
			}
		}else{
			if(obj.value.substring(0,(_depth+1)*3) == cobj[i].value.substring(0,(_depth+1)*3)){
				//if(obj.checked && cobj[i].is_layout_apply == "Y"){
				if(obj.checked && cobj[i].getAttribute("is_layout_apply") == "Y"){
					cobj[i].checked = true;
				}else{
					cobj[i].checked = false;
				}
			}
			
		}
			
	}
	
}