/* xMenu1 Object Prototype

  Parameters:
    triggerId   - id string of element; when clicked shows menu.
    menuId      - id string of menu.
    mouseMargin - integer margin around menu;
                  when mouse is outside this margin the menu is hid.
*/

function yFilter(textId, maxToShow)
{
  // the id of the text input which we're going to filter on
  var trg = xGetElementById(textId);
  // an int, the maximum number of cells to show, or 0 for unlimited
  var max = 0;
  
  this.addItem = addItemFunc;
  
  // the items that we're going to look at
  var items = new Array();
  if(xNum(maxToShow)){
	  max = maxToShow;
  }
  
  if (trg) {
    xAddEventListener(trg, 'keyUp', onKeyPress, false);
  }
  
  // strToMatch is the string to match against
  // showThisOnMatch is the id of the element to show on match, fail will be hidden
  // showThisOnFail is the id of the element to show on NOT match, match will be hidden
  function addItemFunc(strToMatch, showThisOnMatch, showThisOnFail)
  {
	  var index = items.length;
	  var newItem = new Array();
	  newItem["string"] = strToMatch;
	  newItem["match"] = showThisOnMatch;
	  newItem["fail"] = showThisOnFail;
	  items[index] = newItem;
  }
  
  function onKeyPress()
  {
	str1=xGetElementById(trg).value.toLowerCase();
	count = 0;
	for(i = 0; i<items.length;i++){
		if((count < max || max == 0) && xNum(items[i]["string"].toLowerCase().indexOf(str1)) && (items[i]["string"].toLowerCase().indexOf(str1) >= 0)){
			if(xDef(xGetElementById(items[i]["match"])) != null)
				xDisplayBlock(xGetElementById(items[i]["match"]));
			if(xDef(xGetElementById(items[i]["fail"])) != null)
				xDisplayNone(xGetElementById(items[i]["fail"]));
			count++;
		}else{
			if(xDef(xGetElementById(items[i]["match"])) != null)
				xDisplayNone(xGetElementById(items[i]["match"]));
			if(xDef(xGetElementById(items[i]["fail"])) != null)
				xDisplayBlock(xGetElementById(items[i]["fail"]));
		}
	}	    
  }
} // end xFilter
