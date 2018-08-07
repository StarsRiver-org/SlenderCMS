function showSlide() {
	var imagebox = document.getElementById('img-box');
	var images = imagebox.getElementsByTagName('div');
	var pointsbox = document.getElementById('pointsbox');
	var spans = pointsbox.getElementsByTagName('span');
	var arrowL = document.getElementById('arrowl');
	var arrowR = document.getElementById('arrowr');
	
//	var arrowBox = imagebox.getElementsByClassName('arrow-box');
	
	if(images.length != spans.length) {
		return false;
	}

	function showPictures(index) {
		for(var i = 0; i < images.length; i++) {
			images[i].index = i;
			images[i].style.zIndex = 100 - i; //给图片排个序
			images[i].style.opacity = 0;
			spans[i].index = i; //小圆点设置
			spans[i].className = '';

			images[i].timer = null; //事先定义
			images[i].alpha = null; //事先定义
		}
//		images[index].style.opacity = startOP(images[index], 100);
		images[index].style.opacity = '1';
		spans[index].className = 'now';
	}
	showPictures(0);
	var count = 1; //计数器
	function imageMove() { //定义轮播
		if(count % spans.length == 0) {
			count = 0;
		}
		showPictures(count);
		count++;
	}
	var imageInitailMove = setInterval(imageMove, 5000);
	for(var i = 0; i < spans.length; i++) { //点击小圆点时候的动作
		spans[i].onclick = function(e) {
			clearInterval(imageInitailMove);
			count = e.target.index;
			showPictures(count);
			imageInitailMove = setInterval(imageMove, 5000);
		}
	}
	//hover时的动作
	imagebox.onmouseover = function() {
		clearInterval(imageInitailMove);
//		arrowBox.style.opacity = startOP(arrowBox,40);
	}
	imagebox.onmouseout = function() {
		imageInitailMove = setInterval(imageMove, 5000);
//		arrowBox.style.opacity = startOP(arrowBox,0);
	}

	//左箭头
	arrowL.onclick = function() {
		clearInterval(imageInitailMove);
		if(count == 0) {
			count = spans.length;
		}
		count--;
		var num = count - 1;
		if(count == 0) {
			num = spans.length - 1;
		}
		showPictures(num);
		imageInitailMove = setInterval(imageMove, 5000);
	}
	//右箭头
	arrowR.onclick = function() {
		clearInterval(imageInitailMove);
		imageMove();
		imageInitailMove = setInterval(imageMove, 5000);
	}
	
//不用这个了,用了css-transition来控制透明度改变

	function startOP(obj, utarget) {
		clearInterval(obj.timer); //先关闭定时器
		obj.timer = setInterval(function() {
			var speed = 0;
			if(obj.alpha > utarget) {
				speed = -10;
			} else {
				speed = 10;
			}
			obj.alpha = obj.alpha + speed;
			if(obj.alpha == utarget) {
				clearInterval(obj.timer);
			}
			obj.style.filter = 'alpha(opacity:' + obj.alpha + ')'; //基于IE的
			obj.style.opacity = parseInt(obj.alpha) / 100;
		}, 50);
	}
}
addLoadEvent(showSlide);
function addLoadEvent(func) {//通用性
	var oldonload = window.onload;
	if (typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			oldonload();
			func();
		}
	}
}