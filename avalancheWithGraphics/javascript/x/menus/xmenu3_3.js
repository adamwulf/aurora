/*
	xMenu3_1.js
	Cross-Browser.com
*/

window.onload = function() {
  // Create menu 1
  this.m1 = new xMenu('xMenu1',         // element id
    'horizontal', 2, -6,                // mnuType, verOfs, hrzOfs
    -12, -16, -20, -4,                  // lbl selection area clipping (top,right,bottom,left)
    -32, null, null, null,              // box selection area clipping
    'myHMBarLbl', 'myHMBarLblHvr',       // barLblOutStyle, barLblOvrStyle
    'myHMBarLblHvrClosed',                // barLblOvrClosedStyle
    'myMLbl', 'myMLblHvr',             // lblOutStyle, lblOvrStyle
    'myHMBar', 'myMBox');            // barStyle, boxStyle

  this.m1.load();
  
  // Set menu z's
  xZIndex(this.m1.ele, 30);
	this.m1.paint();
	xShow(this.m1.ele);
}

function winOnResize() {
	// paint menus
	this.m1.paint();
	xShow(this.m1.ele);
}

  xAddEventListener(window, 'resize', winOnResize);


