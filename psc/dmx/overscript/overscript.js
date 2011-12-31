/**
 * ------------------------------------------------------------------------
 *  
 *   Overscript v1.1
 *   ----------
 *
 *  Made by Jacques Bodin-Hullin
 *     Jardin <jacques@bodin-hullin.net>
 *
 *   <http://www.bodin-hullin.net/?file=script#overscript>
 *
 *  Started on July 19, 2007
 *  Last update September 16, 2007
 *
 * ------------------------------------------------------------------------
 *
 *   This script is for showing an information when the mouse is at the
 *  top of an element.
 *
 *  Copyright (C) 2007  Jacques 'Jardin' Bodin-Hullin
 *
 *  This file is part of Overscript.
 *
 *   Overscript is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *   Overscript is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *   You should have received a copy of the GNU General Public License
 *  along with Overscript.  If not, see <http://www.gnu.org/licenses/>.
 *
 * ------------------------------------------------------------------------
 */

/*
 * Vars for over
 */
var over_height = 12;
var over_width = 15;
var over_id = 'bubble';


/**
 * OverScript
 *
 * @param text string The information
 * @param evt JScript 'event' (only this)
 */
function over(text, evt)
{
   //remove the bubble
   /*var bodies = document.getElementsByTagName('body');*/
   var lastBubble = document.getElementById(over_id);
   var newContent = document.createTextNode(text);

   if (lastBubble != null) {
      var oldContent = lastBubble.firstChild;
      lastBubble.removeChild(oldContent);
      lastBubble.appendChild(newContent);
   }
   else {
      var bubble = document.createElement('div');
      bubble.setAttribute('id', over_id);
      bubble.appendChild(newContent);
      document.body.appendChild(bubble);
   }

   /*bodies[0].appendChild(bubble);*/

   var newBubble = document.getElementById(over_id);
   
   newBubble.style.visibility = 'visible';

      /*position*/
   if (document.all) {
      var ypos = event.y+document.body.scrollTop - 11 + over_height + 'px';
      var xpos = event.x+document.body.scrollLeft - 4 + over_width + 'px';
   }
   else {
      var ypos = evt.pageY  + over_height + 'px';
      var xpos = evt.pageX + over_width + 'px';
   }

   newBubble.style.top = ypos;
   newBubble.style.left = xpos;
}

function overstop() {
   var bubble = document.getElementById(over_id);
   bubble.style.visibility = "hidden";
}
