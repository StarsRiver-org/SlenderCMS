
function boxShow(btn, box) {
	btn.onclick = function() {
//		display = block针对于mobileNav, height和overflow针对的是Subnav,但是我这样写会同时给他们俩都加上这些属性,不过不影响效果
		
		if(box.style.display !== 'block') {
			box.style.display = 'block';
		} else {
			box.style.display = 'none';
		}
		if(box.style.height !== 'auto' && box.style.overflow !== 'visible') {
			box.style.height = 'auto';
			box.style.overflow = 'visible';
		} else {
			box.style.height = '0';
			box.style.overflow = 'hidden';
		}
	}
}
//boxShow(btn, box);

function boxArrShow(btnArr, boxArr) {
	if (btnArr.length !== boxArr.length) return false;
	
	for (var j = 0; j < btnArr.length; j++) {
		boxShow(btnArr[j], boxArr[j]);
	}
}