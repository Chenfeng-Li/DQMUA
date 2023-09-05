window.onscroll=keepheader;

function keepheader(){
	var header=document.getElementById('header');
	var sticky = header.offsetTop;
	if(window.pageYOffset>sticky+10){
		header.classList.add("sticky");
		document.getElementById('h1').style.display='none';
	}else{
		header.classList.remove("sticky");
		document.getElementById('h1').style.display='block';
	}
}


var countNavclick=0
function clicknav(){
	countNavclick++;
	if(countNavclick%2==1){
		document.getElementById('navList').style.display="block";
		document.getElementById('mobileNavBar').style.cssText = "display:block;background:#02c4b1;box-shadow: 5px 5px 5px #aaaaaa;";
	}else{
		document.getElementById('navList').style.display="none";
		document.getElementById('mobileNavBar').style.cssText = "display:none;background:none;box-shadow: none";
	}
}

function consistentSize(){
	if (window.innerWidth>=700){
		document.getElementById('navList').style.display="block";
		document.getElementById('mobileNavBar').style.cssText = "background:none;box-shadow: none";
	}
	else{
		document.getElementById('navList').style.display="none";
		document.getElementById('mobileNavBar').style.cssText = "background:none;box-shadow: none";
	}
}

window.onresize = consistentSize;

function isano(){
	if(document.getElementById('isan').checked){
		document.getElementById('name').value='匿名希望';
	}else{
		document.getElementById('name').value='';
	}
}

function entername(){
	if(document.getElementById('name').value!='匿名希望'){
		document.getElementById('isan').checked=false;
	}
}

function supporting(){
	if(document.getElementById('taffy').checked){
		document.getElementById('supportimage').src="image/icon/taffyDQMUA.png";
		document.getElementById('supportimage').alt="DQMUA";
	}else if(document.getElementById('nyaru').checked){
		document.getElementById('supportimage').src="image/icon/nyaruMUA.png";
		document.getElementById('supportimage').alt="MUA";
	}else{
		document.getElementById('supportimage').src="image/icon/sereiNeutral.png";
		document.getElementById('supportimage').alt="Neutral";
	}
}

function wordcount(commentId,element){
	commentId+='';
	document.getElementById(commentId).innerHTML=element.value.length;
}


function revise(commentId,element){
	if(document.getElementById('revise'+commentId).style.display=='none'){
		document.getElementById('revise'+commentId).style.display='block';
		element.innerHTML="取消修改";
	}else{
		document.getElementById('revise'+commentId).style.display='none';
		element.innerHTML="修改";
	}

}

function Delete(commentId,element){
	if(document.getElementById('delete'+commentId).style.display=='none'){
		document.getElementById('delete'+commentId).style.display='block';
		element.innerHTML="取消删除";
	}else{
		document.getElementById('delete'+commentId).style.display='none';
		element.innerHTML="删除";
	}

}
