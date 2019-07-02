/*
	xMenu3_1.js
	Cross-Browser.com
*/

var pg = null;
var xDowngrade = true;

if (document.getElementById || document.all) {
  xDowngrade = false;
  document.write("<link rel='stylesheet' type='text/css' href='xmenu3_2_dhtml.css' />");
  document.writeln("<script type='text/javascript' src='../x_core.js'></script>");
  document.writeln("<script type='text/javascript' src='../x_event.js'></script>");
  document.writeln("<script type='text/javascript' src='xmenu3.js'></script>");
}

window.onload = function() {
  if (!xDowngrade) {
    pg = new xPage();
  }
}

function winOnResize() {
	// paint menus
  if (pg) pg.paint();
}

function winOnScroll() {
	// paint menus
  if (pg) {
    var st = xScrollTop();
    // m1
    if (st > pg.m1Top) {xSlideTo(pg.m1.ele, xScrollLeft() + pg.m1Left, st, 700);}
    else {xSlideTo(pg.m1.ele, xScrollLeft() + pg.m1Left, pg.m1Top, 700);}
  }
}

function xPage() { // object prototype

  /// xPage.paint() Method

  this.paint = function() {
    window.scrollTo(0,0);
    var x, w, cw;
    // Calculate column's width and x coord
    cw = xClientWidth();
    if (cw < this.cMinW) {w = this.cMinW;}
    else if (cw > this.cMaxW) {
      w = this.cMaxW;
      x = (xClientWidth() - w) / 2; // center column in window
    }
    else {w = cw;}
    // set positions
    // header
    xMoveTo(this.h, x, 2);
    // m1
    xMoveTo(this.m1.ele, x, xPageY(this.h) + xHeight(this.h)); 
    // column
    xMoveTo(this.c, x, xPageY(this.m1.ele) + xHeight(this.m1.ele)); 
    // footer
    xMoveTo(this.f, x, xTop(this.c)+xHeight(this.c)); 
    // Show everything
    xShow(this.h);
    xShow(this.c);
    xShow(this.f);


    // refresh menu positions
    this.m1.paint();
    xShow(this.m1.ele);
  }

  /// xPage Properties and Constructor

  // adjustable page parameters
  this.cMinW = 300; // column min width
  this.cMaxW = 600; // column max width
 this.m1H = 36;    // menu 1 height
  this.hH = 24;     // header height
  this.fH = 24;     // footer height

  // element references
  this.h = xGetElementById('xHead');
  this.c = xGetElementById('xColumn');
  this.f = xGetElementById('xFoot');

  // Create menu 1
  this.m1 = new xMenu('xMenu1',         // element id
    'horizontal', 2, -6,                // mnuType, verOfs, hrzOfs
    -12, -16, -20, -4,                  // lbl selection area clipping (top,right,bottom,left)
    -32, null, null, null,              // box selection area clipping
    'myHMBarLbl', 'myHMBarLblHvr',       // barLblOutStyle, barLblOvrStyle
    'myHMBarLblHvrClosed',                // barLblOvrClosedStyle
    'myMLbl', 'myMLblHvr',             // lblOutStyle, lblOvrStyle
    'myHMBar', 'myMBox');            // barStyle, boxStyle

  //
  this.m1.load();
  
  // Paint everything
  this.paint();
  
  // Set menu z's
  xZIndex(this.m1.ele, 30);
  
  // Listen to resize and scroll events
  xAddEventListener(window, 'resize', winOnResize);
  xAddEventListener(window, 'scroll', winOnScroll);
}

