/* xMenu1 Object Prototype

  Parameters:
    triggerId   - id string of element; when clicked shows menu.
    menuId      - id string of menu.
*/

var yTooltipCurrentTarget = false;
var hoveringOverTarget = false;
var px, py;
function yTooltipGroup(triggerId, menuId)
{
  var trg = xGetElementById(triggerId);
  if(trg){
	  trg.toolTip = this;
	  var mnu = xGetElementById(menuId);
	  xZIndex(menuId,2);
	  if (trg && mnu) {
	    xAddEventListener(trg, 'mousemove', beginHover, true);
	    xAddEventListener(trg, 'mouseout',  endHover, true);
	    xAddEventListener(trg, 'click',  onMousemove, true);
	  }
   }


  this.openTip = function (){

	if(hoveringOverTarget && yTooltipCurrentTarget == trg){
	    var x, y, cw = xClientWidth(), ch = xClientHeight(), bw = xWidth(mnu), bh = xHeight(mnu);
	    var deltaY = 14;
	    var deltaX = 14;
	    /// find x
	    if (px + bw + deltaX > cw + xScrollLeft()) {
	      // align box right with label right
	      x = px - bw - deltaX/2;
	    }
	    else {
	      // align box left with label left
	      x = px + deltaX*1.5;
	    }
	    /// find y
	    if (py + bh + deltaY > ch + xScrollTop()) {
	      // put box above label
	      y = py - bh - deltaY;
	    }
	    else {
	      // put box under label
	      y = py + deltaY;
	    }

	    // a debug alert
	    // alert("before edit to: " + y);

	    // added by awulf@ev1.net
	    // this adjusts for if the trigger is inside of a scrollable div
	    // par = trg;
	    // while(par != null){
	//	  x -= xScrollLeft(par);
	//	  y -= xScrollTop(par);
	//	  par = xParent(par, true);
	    // }
	    // now i need to re-add the adjustment for how far the window has scrolled... i hope this works cross browser...
	    // y += xScrollTop(document.body);
	    // x += xScrollLeft(document.body);
	    // end addition by awulf
	    // a debug alert
	    // alert("move to: " + y); 


	    xMoveTo(mnu, x, y);

	    // xMoveTo(mnu, xPageX(trg), xPageY(trg) + xHeight(trg));
	    xShow(mnu);
	 }
	 xAddEventListener(document, 'mousemove', onMousemove, false);
  }
  function onMousemove(oEvent){
	hoveringOverTarget = false;
	//yTooltipCurrentTarget = false;
	xHide(mnu);
	xMoveTo(mnu, -1000, -1000);
	xRemoveEventListener(document, 'mousemove', onMousemove, false);
  }


  function beginHover(oEvent)
  {
    var e = new xEvent(oEvent);
    px = e.pageX;
    py = e.pageY;
    if (!hoveringOverTarget) {
      yTooltipCurrentTarget = trg;
      hoveringOverTarget = true;
      xTimer.set('timeout', trg.toolTip, 'openTip', 1000, false)
    }
  };

  function endHover()
  {
    if (hoveringOverTarget) {
      hoveringOverTarget = false;
      yTooltipCurrentTarget = false;
    }
  };
} // end xMenu1
