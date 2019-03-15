var mybody = document.getElementsByTagName('body')[0];
//滑动处理
var startX, startY, moveEndX, moveEndY, X, Y;
mybody.addEventListener('touchstart', function(e) {
	e.preventDefault();
	startX = e.touches[0].pageX;
	startY = e.touches[0].pageY;
}, false);
mybody.addEventListener('touchmove', function(e) {
	e.preventDefault();
	moveEndX = e.changedTouches[0].pageX;
	moveEndY = e.changedTouches[0].pageY;
	X = moveEndX - startX;
	Y = moveEndY - startY;
	if(Math.abs(X) > Math.abs(Y) && X > 0) {
		alert("向右");
	} else if(Math.abs(X) > Math.abs(Y) && X < 0) {
		alert("向左");
	} else if(Math.abs(Y) > Math.abs(X) && Y > 0) {
		alert("向下");
	} else if(Math.abs(Y) > Math.abs(X) && Y < 0) {
		alert("向上");
	} else {
		alert("没滑动");
	}
});