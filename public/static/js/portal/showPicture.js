function showSlide() {
	var imagebox = document.getElementById('img-box');
	var images = imagebox.getElementsByTagName('div');
	var pointsbox = document.getElementById('pointsbox');
	var spans = pointsbox.getElementsByTagName('span');
	var arrowL = document.getElementById('arrowl');
	var arrowR = document.getElementById('arrowr');

	if(images.length != spans.length) {
		return false;
	}

	function showPictures(index) {
		for(var i = 0; i < images.length; i++) {
			images[i].index = i;
			images[i].style.zIndex = 100 - i; //给图片排个序

			images[i].style.filter = 'alpha(opacity: 0)';

			images[i].style.opacity = 0;
			spans[i].index = i; //小圆点设置
			spans[i].className = '';

			images[i].timer = null; //事先定义
			images[i].alpha = null; //事先定义
		}
		//		images[index].style.opacity = startOP(images[index], 100);

		images[index].style.filter = 'alpha(opacity: 100)';
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
		spans[i].onclick = function() {
			var eve = event || window.event; //获取事件对象,为了支持IE8
			var objEle = eve.target || eve.srcElement; //获取document 对象的引用
			clearInterval(imageInitailMove);
			count = objEle.index;
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
	arrowL.onclick = turnLeft;

	function turnLeft() {
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
	arrowR.onclick = turnRight;

	function turnRight() {
		clearInterval(imageInitailMove);
		imageMove();
		imageInitailMove = setInterval(imageMove, 5000);
	}
	//用了css-transition来控制透明度改变
	
	touchDirection();
	//滑动处理
	function touchDirection() {
		var mybody = imagebox;
		var __sliderWidth = imagebox.clientWidth * .1;
		var startX, startY, moveEndX, moveEndY, X, Y;
		mybody.addEventListener('touchstart', function(e) {
			e.preventDefault();
			startX = e.touches[0].pageX;
			startY = e.touches[0].pageY;
		}, false);
		mybody.addEventListener('touchend', function(e) {
			e.preventDefault();
			moveEndX = e.changedTouches[0].pageX;
			moveEndY = e.changedTouches[0].pageY;
			X = moveEndX - startX;
			Y = moveEndY - startY;
			if (Math.abs(X) > __sliderWidth && X > 0) {
//				console.log("向右");
				return turnRight();
			} else if(Math.abs(X) > __sliderWidth && X < 0) {
//				console.log("向左");
				return turnLeft();
			}
		}, false);
	}
}

addLoadEvent(showSlide);

function addLoadEvent(func) { //通用性
	var oldonload = window.onload;
	if(typeof window.onload != 'function') {
		window.onload = func;
	} else {
		window.onload = function() {
			oldonload();
			func();
		}
	}
}
//showSlide();