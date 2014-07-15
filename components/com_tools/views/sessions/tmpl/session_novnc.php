<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$juser = JFactory::getUser();

if (!$this->app->sess) {
	echo '<p class="error"><strong>'.JText::_('ERROR').'</strong><br /> '.implode('<br />', $this->output).'</p>';
} else {
	\Hubzero\Document\Assets::addComponentScript('com_tools', 'assets/novnc/util');
	\Hubzero\Document\Assets::addComponentScript('com_tools', 'assets/novnc/ui-custom');
	\Hubzero\Document\Assets::addComponentStylesheet('com_tools', 'assets/novnc/base');
	$base = rtrim(JURI::base(true), '/');
?>

<div id="noVNC-control-bar">
	<!--noVNC Mobile Device only Buttons-->
	<div class="noVNC-buttons-left">
		<input type="image" src="<?php echo $base; ?>/components/com_tools/assets/novnc/images/drag.png"
			   id="noVNC_view_drag_button" class="noVNC_status_button"
			   title="Move/Drag Viewport"
			   onclick="UI.setViewDrag();">
		<div id="noVNC_mobile_buttons">
				<input type="image" src="<?php echo $base; ?>/components/com_tools/assets/novnc/images/mouse_none.png"
					id="noVNC_mouse_button0" class="noVNC_status_button"
					onclick="UI.setMouseButton(1);">
				<input type="image" src="<?php echo $base; ?>/components/com_tools/assets/novnc/images/mouse_left.png"
					id="noVNC_mouse_button1" class="noVNC_status_button"
					onclick="UI.setMouseButton(2);">
				<input type="image" src="<?php echo $base; ?>/components/com_tools/assets/novnc/images/mouse_middle.png"
					id="noVNC_mouse_button2" class="noVNC_status_button"
					onclick="UI.setMouseButton(4);">
				<input type="image" src="<?php echo $base; ?>/components/com_tools/assets/novnc/images/mouse_right.png"
					id="noVNC_mouse_button4" class="noVNC_status_button"
					onclick="UI.setMouseButton(0);">
				<input type="image" src="<?php echo $base; ?>/components/com_tools/assets/novnc/images/keyboard.png"
					id="showKeyboard" class="noVNC_status_button"
					value="Keyboard" title="Show Keyboard"
					onclick="UI.showKeyboard()"/>
				<input type="email"
					autocapitalize="off" autocorrect="off"
					id="keyboardinput" class="noVNC_status_button"
					onKeyDown="onKeyDown(event);" onblur="UI.keyInputBlur();"/>
			</div>
		</div>

		<!--noVNC Buttons-->
		<div class="noVNC-buttons-right">
			<input type="image" src="<?php echo $base; ?>/components/com_tools/assets/novnc/images/ctrlaltdel.png"
				 id="sendCtrlAltDelButton" class="noVNC_status_button"
				title="Send Ctrl-Alt-Del"
				onclick="UI.sendCtrlAltDel();" />
			<input type="image" src="<?php echo $base; ?>/components/com_tools/assets/novnc/images/clipboard.png"
				id="clipboardButton" class="noVNC_status_button"
				title="Clipboard"
				onclick="UI.toggleClipboardPanel();" />
		</div>

		<!-- Clipboard Panel -->
		<div id="noVNC_clipboard" class="triangle-right top">
			<textarea id="noVNC_clipboard_text" rows=5
				onfocus="UI.displayBlur();" onblur="UI.displayFocus();"
				onchange="UI.clipSend();">
			</textarea>
			<br />
			<input id="noVNC_clipboard_clear_button" type="button"
				value="Clear" onclick="UI.clipClear();">
		</div>

	</div> <!-- End of noVNC-control-bar -->

	<div id="noVNC_screen">
        <div id="noVNC_screen_pad"></div>

		<div id="noVNC_status_bar" class="noVNC_status_bar">
				<div id="noVNC_status">Loading</div>
		</div>

		<h1 id="noVNC_logo" style="display:none;"><span>HUB</span><br/>zero</h1>

		<!-- HTML5 Canvas  -->
        <div id="noVNC_container" style="min-height:600px;">
            <canvas id="noVNC_canvas" width="640px" height="20px">
						Canvas not supported.
			</canvas>
		</div>
    </div>

	<script>
		//JS globals for noVNC
		var host, port, password, token, encrypt, connectPath, decryptPath;
		host = '<?php echo $this->output->wsproxy_host; ?>';
		port = '<?php echo $this->output->wsproxy_port; ?>';
		password = '<?php echo $this->output->password; ?>';
		token = '<?php echo $this->output->token; ?>';
		encrypt = ('<?php echo $this->output->wsproxy_encrypt; ?>' == 'Yes') ? true : false;
		connectPath = 'websockify?token=' + token;
	
		//Wire up the resizable element for this page
		var resizeTimeout;
		var resizeAttached = false;
		var hPadding = 5;
		UI.normalStateAchieved = function(){
		if(!resizeAttached){
			resizeAttached = true;
			//Setup a handler to track window resizes
			$(window).resize(function(e){
				if(resizeTimeout){
					clearTimeout(resizeTimeout);
				}
				//Do the resize in a timeout incase we are dragging slowly to avoid bombarding the server
				resizeTimeout = setTimeout(function(){
						var w = $(window);
						var b = $('#noVNC-control-bar');
						var s = $('#noVNC_status');
					var c = $('#app-content');
						var sb = getScrollBarDimensions();
						doResize(b.width() + sb.horizontal, w.height()
																- b.height()
																- s.height()
																- hPadding
																+ sb.vertical);
				}, 1000);
			});
			
			//Setup handler for tracking window focus events (delayed resize if not focused)
			$(window).focus(function(){
					console.log('window focus fired');
				$(window).resize();
			});
			
			//When the page first loads, fire a resize event to get the current screen size
			$(window).resize();
		}
	};
	
	function doResize(w, h) {
		if(!document.hasFocus)
			return;
		UI.requestResize(w, h); //Invoke resize on the server
	}
	
		function getScrollBarDimensions(){
			var elm = document.documentElement.offsetHeight ? document.documentElement : document.body,

			curX = elm.clientWidth,
			curY = elm.clientHeight,

			hasScrollX = elm.scrollWidth > curX,
			hasScrollY = elm.scrollHeight > curY,

			prev = elm.style.overflow,

			r = {
				vertical: 0,
				horizontal: 0
			};

			if(!hasScrollY && !hasScrollX) {
				return r;
			}
			
			elm.style.overflow = "hidden";
			if (hasScrollY) {
				r.vertical = elm.clientWidth - curX;
			}
			if (hasScrollX) {
				r.horizontal = elm.clientHeight - curY;
			}
			elm.style.overflow = prev;
			return r;
		}
		
	//Final attachment for onload
	window.onload = UI.load;
</script>

<?php
	} //end else
?>
