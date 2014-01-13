##
##
##        Mod title:  Simple Poll
##
##      Mod version:  1.0.3
##  Works on FluxBB:  1.4, 1.4.1, 1.4.2
##     Release date:  2010-08-10
##      Review date:  YYYY-MM-DD (Leave unedited)
##           Author:  kg (kg@as-planned.com)
##
##      Description:  This mod adds the ability to add a very simple poll 
##                    to the first post in a topic.
##
##   Repository URL:  http://fluxbb.org/resources/mods/xxx (Leave unedited)
##
##   Affected files:  edit.php
##                    include/functions.php
##                    post.php
##                    viewtopic.php
##
##       Affects DB:  Yes
##
##            Notes:  A patch file is included to perform the changes
##                    described below. The patch was made against the
##                    stock 1.4.1 version of FluxBB.
##                    This mod was optimized for ease of integration, not
##                    performance. An extra database query is executed on
##                    each topic view.
##                    Please note that changes 34-38 are optional.
##                    The max number of options can be configured in
##                    by changing the constant definition of 
##                    AP_POLL_MAX_CHOICES in ap_poll.php. When the other 
##                    constant, AP_POLL_ENABLE_PROMOTED, is changed to true,
##                    an extra checkbox will be shown for admins to promote 
##                    a poll. We use this to show the poll on our homepage,
##                    but this will always require custom code.
##                    I recommend you use the patch file, since there are a 
##                    lot of small changes. Note that the patch includes
##                    all the new files that were added. However it does not 
##                    include the optional CSS and Javascript code 
##                    (changes 34 - 38 at the bottom of the file). 
##
##       DISCLAIMER:  Please note that "mods" are not officially supported by
##                    FluxBB. Installation of this modification is done at 
##                    your own risk. Backup your forum database and any and
##                    all applicable files before proceeding.
##
##


#
#---------[ 1. UPLOAD ]-------------------------------------------------------
#

install_mod.php to /
files/ap_poll.php to /includes/ap_poll.php
files/lang/English/ap_poll.php to /lang/English/ap_poll.php


#
#---------[ 2. RUN ]----------------------------------------------------------
#

install_mod.php


#
#---------[ 3. DELETE ]-------------------------------------------------------
#

install_mod.php

#
#---------[ 4. OPEN ]---------------------------------------------------------
#

edit.php

#
#---------[ 5. FIND (line: 10) ]---------------------------------------------
#

require PUN_ROOT.'include/common.php';

#
#---------[ 6. AFTER, ADD ]-------------------------------------------------
#

require PUN_ROOT.'include/ap_poll.php';

#
#---------[ 7. FIND (line: 69) ]---------------------------------------------------------
#

		else if ($pun_config['p_subject_all_caps'] == '0' && is_all_uppercase($subject) && !$pun_user['is_admmod'])
			$errors[] = $lang_post['All caps subject'];

#
#---------[ 8. AFTER, ADD ]---------------------------------------------
#
	
	
		// AP Poll
		ap_poll_form_validate($errors);
		// /AP Poll

#
#---------[ 9. FIND (line: 107) ]---------------------------------------------------
#

			// Update the topic and any redirect topics
			$db->query('UPDATE '.$db->prefix.'topics SET subject=\''.$db->escape($subject).'\' WHERE id='.$cur_post['tid'].' OR moved_to='.$cur_post['tid']) or error('Unable to update topic', __FILE__, __LINE__, $db->error());

#
#---------[ 10. AFTER, ADD ]--------------------------------------------
#


			// AP Poll
			ap_poll_save($cur_post['tid']);
			// /AP Poll

#
#---------[ 11. FIND (line:257) ]-------------------------------------------------
#

?>
	</div>


#
#---------[ 12. AFTER, ADD ]--------------------------------------------
#


			<!-- AP Poll -->
			<?php if ($can_edit_subject) ap_poll_form_edit($cur_post['tid']); ?>
			<!-- /AP Poll -->


#
#---------[ 13. OPEN ]---------------------------------------------------------
#

include/functions.php

#
#---------[ 14. FIND (line: 669) ]---------------------------------------------
#

	// Delete any subscriptions for this topic
	$db->query('DELETE FROM '.$db->prefix.'subscriptions WHERE topic_id='.$topic_id) or error('Unable to delete subscriptions', __FILE__, __LINE__, $db->error());

#
#---------[ 15. AFTER, ADD ]-------------------------------------------------
#


	// AP Poll
	require PUN_ROOT.'include/ap_poll.php';
	ap_poll_delete($topic_id);
	// /AP Poll

#
#---------[ 16. OPEN ]---------------------------------------------------------
#

post.php

#
#---------[ 17. FIND (line: 10) ]---------------------------------------------
#

require PUN_ROOT.'include/common.php';

#
#---------[ 18. AFTER, ADD ]-------------------------------------------------
#

require PUN_ROOT.'include/ap_poll.php';

#
#---------[ 19. FIND (line: 144) ]---------------------------------------------
#

	$now = time();

#
#---------[ 20. AFTER, ADD ]-------------------------------------------------
#


	// AP Poll
	ap_poll_form_validate($errors);
	// /AP Poll

#
#---------[ 21. FIND (line: 297) ]---------------------------------------------
#

			update_forum($fid);

#
#---------[ 22. AFTER, ADD ]-------------------------------------------------
#

		
		// AP Poll
		ap_poll_save($new_tid);
		// /AP Poll

#
#---------[ 23. FIND (line: 563) ]---------------------------------------------
#

 ?>
 			</div>

#
#---------[ 24. AFTER, ADD ]-------------------------------------------------
#
		
		
			<!-- AP Poll -->
			<?php ap_poll_form_post($tid); ?>
			<!-- /AP Poll -->


#
#---------[ 25. OPEN ]---------------------------------------------------------
#

viewtopic.php

#
#---------[ 26. FIND (line: 10) ]---------------------------------------------
#

require PUN_ROOT.'include/common.php';

#
#---------[ 27. AFTER, ADD ]-------------------------------------------------
#

require PUN_ROOT.'include/ap_poll.php';

#
#---------[ 28. FIND (line: 85) ]---------------------------------------------
#
		exit;
	}
}

#
#---------[ 29. AFTER, ADD ]-------------------------------------------------
#

// AP Poll
else if ($action == 'ap_vote') 
{
	ap_poll_vote($id, $pun_user['id']);
}
// /AP Poll

#
#---------[ 30. FIND (line: 213) ]---------------------------------------------
#

// Retrieve the posts (and their respective poster/online status)

#
#---------[ 31. BEFORE, ADD ]-------------------------------------------------
#

// AP Poll
$ap_current_poll = ap_poll_info($id, $pun_user['id']);
// /AP Poll

#
#---------[ 32. FIND (line: 367) ]---------------------------------------------
#

<?php if ($cur_post['edited'] != '') echo "\t\t\t\t\t\t".'<p class="postedit"><em>'.$lang_topic['Last edit'].' '.pun_htmlspecialchars($cur_post['edited_by']).' ('.format_time($cur_post['edited']).')</em></p>'."\n"; ?>

#
#---------[ 33. AFTER, ADD ]-------------------------------------------------
#

					<?php if ($cur_post['id'] == $cur_topic['first_post_id']) ap_poll_display($id, $ap_current_poll) ?>

#
#---------[ 34. OPEN ]-------------------------------------------------
#

Your stylesheet

#
#---------[ 35. AT END, ADD ]-------------------------------------------------
#

/* Poll */
#ap_poll_input {
}

.ap_poll_hidden { display: none; }

fieldset.ap_poll {
	margin-top: 2em;
	padding: 1em;
	background-color: #F6F9FC;
	border: 1px solid #DFE6EE;
}

.ap_poll legend {
	font-weight: normal;
}

.ap_poll p {
	font-weight: bold;
}

.ap_poll table {
	width: auto;
}

.ap_poll th {
	font-weight: normal;
	padding: .5em 1em .5em 0;
}

.ap_poll td {
	padding: .5em 1em;
}

.ap_poll label {
	
}

.ap_poll .percent {
	text-align: right;
}

.ap_poll .results .bar {
	height: 20px;
	background-color: #44699C;
}

.ap_poll .results .bar .top {
	background-color: #4F78B2;
	width: 100%;
	height: 10px;
}

.ap_poll .total {
	color: #b7b7b7;
	margin-top: .5em;
	font-style: italic;
}

#
#---------[ 36. OPEN ]-------------------------------------------------
#

header.php

#
#---------[ 37. FIND (line: 140) ]-------------------------------------------------
#

// JavaScript tricks for IE6 and older

#
#---------[ 38. BEFORE, ADD ]-------------------------------------------------
#

if (defined('AP_POLL_MAX_CHOICES')) 
{
	?>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.4.2/jquery.min.js"></script>
	<script type="text/javascript">
	jQuery(document).ready(function() {
		jQuery('#ap_poll_enabled').change(function() {
			if (jQuery('#ap_poll_enabled').attr('checked')) {
				jQuery('#ap_poll_input').show();
			} else {
				jQuery('#ap_poll_input').hide();
			}
		});
	});
	</script>
	<?php
}

#
#---------[ 39. SAVE/UPLOAD ]-------------------------------------------------
#