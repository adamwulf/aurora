/* xMenu1 Object Prototype

  Parameters:
    triggerId   - id string of element; when clicked shows menu.
    menuId      - id string of menu.
    mouseMargin - integer margin around menu;
                  when mouse is outside this margin the menu is hid.
*/

function xMenu1(triggerId, menuId, mouseMargin)
{
  var trg = xGetElementById(triggerId);
  var mnu = xGetElementById(menuId);
  if (trg && mnu) {
    xAddEventListener(trg, 'click', onClick, false);
  }
  function onClick()
  {
  var x, y, cw = xClientWidth(), ch = xClientHeight(), bw = xWidth(mnu), bh = xHeight(mnu);
  /// find x
  if (xPageX(trg) + bw > cw + xScrollLeft()) {
    // align box right with label right
    x = xPageX(trg) + xWidth(trg) - bw;
  }
  else {
    // align box left with label left
    x = xPageX(trg);
  }
  /// find y
  if (xPageY(trg) + xHeight(trg) + bh > ch + xScrollTop()) {
    // put box above label
    y = xPageY(trg) - bh;
  }
  else {
    // put box under label
    y = xPageY(trg) + xHeight(trg);
  }
  
  // a debug alert
  // alert("before edit to: " + y);
  
  // added by awulf@ev1.net
  // this adjusts for if the trigger is inside of a scrollable div
  par = trg;
  while(par != null){
	  x -= xScrollLeft(par);
	  y -= xScrollTop(par);
	  par = xParent(par, true);
  }
  // now i need to re-add the adjustment for how far the window has scrolled... i hope this works cross browser...
  y += xScrollTop(document.body);
  x += xScrollLeft(document.body);
  // end addition by awulf
  // a debug alert
  // alert("move to: " + y); 
  
  
  xMoveTo(mnu, x, y);

  // xMoveTo(mnu, xPageX(trg), xPageY(trg) + xHeight(trg));
    xShow(mnu);
    xAddEventListener(document, 'mousemove', onMousemove, false);
  }
  function onMousemove(ev)
  {
    var e = new xEvent(ev);
    if (!xHasPoint(mnu, e.pageX, e.pageY, -mouseMargin) &&
        !xHasPoint(trg, e.pageX, e.pageY, -mouseMargin))
    {
      xHide(mnu);
      xMoveTo(mnu, -100, 1);
      xRemoveEventListener(document, 'mousemove', onMousemove, false);
    }
  }
} // end xMenu1
