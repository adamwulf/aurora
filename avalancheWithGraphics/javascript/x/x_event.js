// x_event.js
// X v3.15, Cross-Browser DHTML Library from Cross-Browser.com
// Copyright (c) 2002,2003,2004 Michael Foster (mike@cross-browser.com)
// This library is distributed under the terms of the LGPL (gnu.org)

function xAddEventListener(e,eventType,eventListener,useCapture) {
  if(!(e=xGetElementById(e))) return;
  eventType=eventType.toLowerCase();
  if((!xIE4Up && !xOp7) && e==window) {
    if(eventType=='resize') { window.xPCW=xClientWidth(); window.xPCH=xClientHeight(); window.xREL=eventListener; xResizeEvent(); return; }
    if(eventType=='scroll') { window.xPSL=xScrollLeft(); window.xPST=xScrollTop(); window.xSEL=eventListener; xScrollEvent(); return; }
  }
  var eh='e.on'+eventType+'=eventListener';
  if(e.addEventListener) e.addEventListener(eventType,eventListener,useCapture);
  else if(e.attachEvent) e.attachEvent('on'+eventType,eventListener);
  else if(e.captureEvents) {
    if(useCapture||(eventType.indexOf('mousemove')!=-1)) { e.captureEvents(eval('Event.'+eventType.toUpperCase())); }
    eval(eh);
  }
  else eval(eh);
}
function xRemoveEventListener(e,eventType,eventListener,useCapture) {
  if(!(e=xGetElementById(e))) return;
  eventType=eventType.toLowerCase();
  if((!xIE4Up && !xOp7) && e==window) {
    if(eventType=='resize') { window.xREL=null; return; }
    if(eventType=='scroll') { window.xSEL=null; return; }
  }
  var eh='e.on'+eventType+'=null';
  if(e.removeEventListener) e.removeEventListener(eventType,eventListener,useCapture);
  else if(e.detachEvent) e.detachEvent('on'+eventType,eventListener);
  else if(e.releaseEvents) {
    if(useCapture||(eventType.indexOf('mousemove')!=-1)) { e.releaseEvents(eval('Event.'+eventType.toUpperCase())); }
    eval(eh);
  }
  else eval(eh);
}
function xEvent(evt) { // cross-browser event object prototype
  this.type = '';
  this.target = null;
  this.pageX = 0;
  this.pageY = 0;
  this.offsetX = 0;
  this.offsetY = 0;
  this.keyCode = 0;
  var e = evt ? evt : window.event;
  if(!e) return;
  if(e.type) this.type = e.type;
  if(e.target) this.target = e.target;
  else if(e.srcElement) this.target = e.srcElement;
  if(xOp5or6) { this.pageX = e.clientX; this.pageY = e.clientY; }
  else if(xDef(e.clientX,e.clientY)) { this.pageX = e.clientX + xScrollLeft(); this.pageY = e.clientY + xScrollTop(); }
  if(xDef(e.offsetX,e.offsetY)) { this.offsetX = e.offsetX; this.offsetY = e.offsetY; }
  else { this.offsetX = this.pageX - xPageX(this.target); this.offsetY = this.pageY - xPageY(this.target); }
  if (e.keyCode) { this.keyCode = e.keyCode; } // for moz/fb, if keyCode==0 use which
  else if (xDef(e.which)) { this.keyCode = e.which; }
}
function xResizeEvent() { // window resize event simulation
  if (window.xREL) setTimeout('xResizeEvent()', 250);
  var cw = xClientWidth(), ch = xClientHeight();
  if (window.xPCW != cw || window.xPCH != ch) { window.xPCW = cw; window.xPCH = ch; if (window.xREL) window.xREL(); }
}
function xScrollEvent() { // window scroll event simulation
  if (window.xSEL) setTimeout('xScrollEvent()', 250);
  var sl = xScrollLeft(), st = xScrollTop();
  if (window.xPSL != sl || window.xPST != st) { window.xPSL = sl; window.xPST = st; if (window.xSEL) window.xSEL(); }
}
// end x_event.js