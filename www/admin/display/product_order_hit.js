function deleteProduct(act,id){
	alert(1);
	if(confirm('정말로 삭제하시겠습니까?')){
		document.frames['act'].location.href='./product_input.act.php?act='+act+'&id='+id;
	}
}

function setCategory(cname,cid,depth,pid){
	document.frames['act'].location.href='./product_order_hit.php?cid='+cid+'&depth='+depth+'&view=innerview';
}

function UpdateOrder(pid,thisorder,changeorder){
	//alert(pid+'::'+changeorder+'::"'+thisorder)
	document.vieworderform.vieworder.value = changeorder;
	document.vieworderform._vieworder.value = thisorder;
	document.vieworderform.pid.value = pid;
	
	document.vieworderform.submit();
}



var iciRow, preRow;

function spoit(obj)
{
	iciRow = obj;
	iciHighlight();
}

function iciHighlight()
{
	if (preRow) preRow.style.backgroundColor = "";
	iciRow.style.backgroundColor = "#efefef"; // FFF4E6
	preRow = iciRow;
}

function moveTree(idx)
{
	var objTop = iciRow.parentNode.parentNode;
	var nextPos = iciRow.rowIndex+idx;
	if (nextPos==objTop.rows.length) nextPos = 0;
	objTop.moveRow(iciRow.rowIndex,nextPos);
}

function keydnTree()
{
	if (iciRow==null) return;
	switch (event.keyCode){
		case 38: moveTree(-1); break;
		case 40: moveTree(1); break;
	}
	return false;
}

document.onkeydown = keydnTree;
