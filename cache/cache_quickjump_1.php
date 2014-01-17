<?php

if (!defined('PUN')) exit;
define('PUN_QJ_LOADED', 1);
$forum_id = isset($forum_id) ? $forum_id : 0;

?>				<form id="qjump" method="get" action="viewforum.php">
					<div><label><span><?php echo $lang_common['Jump to'] ?><br /></span>
					<select name="id" onchange="window.location=('viewforum.php?id='+this.options[this.selectedIndex].value)">
						<optgroup label="SpyroWorld Management">
							<option value="38"<?php echo ($forum_id == 38) ? ' selected="selected"' : '' ?>>SpyroWorld News</option>
							<option value="39"<?php echo ($forum_id == 39) ? ' selected="selected"' : '' ?>>SpyroWorld Pending News</option>
						</optgroup>
						<optgroup label="General">
							<option value="22"<?php echo ($forum_id == 22) ? ' selected="selected"' : '' ?>>Announcements</option>
							<option value="29"<?php echo ($forum_id == 29) ? ' selected="selected"' : '' ?>>Site Help/User Feedback</option>
							<option value="13"<?php echo ($forum_id == 13) ? ' selected="selected"' : '' ?>>General Chat</option>
							<option value="12"<?php echo ($forum_id == 12) ? ' selected="selected"' : '' ?>>Spyro Chat</option>
							<option value="35"<?php echo ($forum_id == 35) ? ' selected="selected"' : '' ?>>Spyro Tips/Secrets/Help</option>
							<option value="34"<?php echo ($forum_id == 34) ? ' selected="selected"' : '' ?>>Spyro Music</option>
							<option value="21"<?php echo ($forum_id == 21) ? ' selected="selected"' : '' ?>>SPAM/Forum Games</option>
						</optgroup>
						<optgroup label="Fun">
							<option value="24"<?php echo ($forum_id == 24) ? ' selected="selected"' : '' ?>>Moneybag&#039;s Shop</option>
							<option value="44"<?php echo ($forum_id == 44) ? ' selected="selected"' : '' ?>>Contests</option>
							<option value="18"<?php echo ($forum_id == 18) ? ' selected="selected"' : '' ?>>Spyro-y Polls</option>
							<option value="19"<?php echo ($forum_id == 19) ? ' selected="selected"' : '' ?>>The Future of Spyro the Dragon</option>
							<option value="43"<?php echo ($forum_id == 43) ? ' selected="selected"' : '' ?>>Fan Clubs</option>
							<option value="20"<?php echo ($forum_id == 20) ? ' selected="selected"' : '' ?>>Spyro Pictures</option>
							<option value="23"<?php echo ($forum_id == 23) ? ' selected="selected"' : '' ?>>Spyro Art Requests</option>
							<option value="25"<?php echo ($forum_id == 25) ? ' selected="selected"' : '' ?>>Spyro Roleplaying</option>
						</optgroup>
						<optgroup label="Upcoming Spyro Games">
							<option value="32"<?php echo ($forum_id == 32) ? ' selected="selected"' : '' ?>>(None)</option>
							<option value="42"<?php echo ($forum_id == 42) ? ' selected="selected"' : '' ?>>Spyro: The Movie</option>
							<option value="33"<?php echo ($forum_id == 33) ? ' selected="selected"' : '' ?>>Spyro: Legacy of Ages</option>
						</optgroup>
						<optgroup label="Special Groups">
							<option value="26"<?php echo ($forum_id == 26) ? ' selected="selected"' : '' ?>>Spyro Fan Fiction Group</option>
							<option value="30"<?php echo ($forum_id == 30) ? ' selected="selected"' : '' ?>>Spyro Fan Game Development</option>
							<option value="31"<?php echo ($forum_id == 31) ? ' selected="selected"' : '' ?>>Spyro Fan Art Group</option>
						</optgroup>
						<optgroup label="Games">
							<option value="1"<?php echo ($forum_id == 1) ? ' selected="selected"' : '' ?>>Spyro The Dragon</option>
							<option value="2"<?php echo ($forum_id == 2) ? ' selected="selected"' : '' ?>>Spyro 2: Ripto&#039;s Rage</option>
							<option value="3"<?php echo ($forum_id == 3) ? ' selected="selected"' : '' ?>>Spyro 3: Year of the Dragon</option>
							<option value="4"<?php echo ($forum_id == 4) ? ' selected="selected"' : '' ?>>Spyro: Season of Ice</option>
							<option value="5"<?php echo ($forum_id == 5) ? ' selected="selected"' : '' ?>>Spyro: Season of Flame</option>
							<option value="6"<?php echo ($forum_id == 6) ? ' selected="selected"' : '' ?>>Spyro: Attack of the Rhynocs</option>
							<option value="7"<?php echo ($forum_id == 7) ? ' selected="selected"' : '' ?>>Spyro: Orange</option>
							<option value="8"<?php echo ($forum_id == 8) ? ' selected="selected"' : '' ?>>Spyro: Enter the Dragonfly</option>
							<option value="9"<?php echo ($forum_id == 9) ? ' selected="selected"' : '' ?>>Spyro: A Hero&#039;s Tail</option>
							<option value="10"<?php echo ($forum_id == 10) ? ' selected="selected"' : '' ?>>Spyro: Shadow Legacy</option>
							<option value="11"<?php echo ($forum_id == 11) ? ' selected="selected"' : '' ?>>The Legend of Spyro: A New Beginning</option>
							<option value="37"<?php echo ($forum_id == 37) ? ' selected="selected"' : '' ?>>The Legend of Spyro: The Eternal Night</option>
							<option value="41"<?php echo ($forum_id == 41) ? ' selected="selected"' : '' ?>>The Legend of Spyro: Dawn of the Dragon</option>
							<option value="45"<?php echo ($forum_id == 45) ? ' selected="selected"' : '' ?>>Skylanders: Spyro&#039;s Adventure</option>
							<option value="15"<?php echo ($forum_id == 15) ? ' selected="selected"' : '' ?>>Spyro (Mobile Phone)</option>
							<option value="16"<?php echo ($forum_id == 16) ? ' selected="selected"' : '' ?>>Spyro the Dragon (Mobile Phone)</option>
							<option value="17"<?php echo ($forum_id == 17) ? ' selected="selected"' : '' ?>>Spyro: Ripto Quest (Mobile Phone)</option>
						</optgroup>
						<optgroup label="Extras">
							<option value="36"<?php echo ($forum_id == 36) ? ' selected="selected"' : '' ?>>Member&#039;s Council</option>
							<option value="27"<?php echo ($forum_id == 27) ? ' selected="selected"' : '' ?>>Mod Talk</option>
							<option value="28"<?php echo ($forum_id == 28) ? ' selected="selected"' : '' ?>>Trash</option>
							<option value="40"<?php echo ($forum_id == 40) ? ' selected="selected"' : '' ?>>Archive</option>
						</optgroup>
					</select>
					<input type="submit" value="<?php echo $lang_common['Go'] ?>" accesskey="g" />
					</label></div>
				</form>
