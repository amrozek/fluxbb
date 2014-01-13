<?php
/***********************************************************************

  This software is free software; you can redistribute it and/or modify it
  under the terms of the GNU General Public License as published
  by the Free Software Foundation; either version 2 of the License,
  or (at your option) any later version.

  This software is distributed in the hope that it will be useful, but
  WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place, Suite 330, Boston,
  MA  02111-1307  USA

************************************************************************/
// Make sure no one attempts to run this script "directly"
if (!defined('PUN'))
    exit;

// Tell admin_loader.php that this is indeed a plugin and that it is loaded
define('PUN_PLUGIN_LOADED', 1);
define('PLUGIN_VERSION', '1.0.1');

// Load the admin_users.php language file
require PUN_ROOT.'lang/'.$admin_language.'/admin_users.php';

// Load the profile.php language file
require PUN_ROOT.'lang/'.$pun_user['language'].'/profile.php';

// Load the Honeypot + SFS language file
if (file_exists(PUN_ROOT.'lang/'.$pun_user['language'].'/honeypot_sfs_plugin.php'))
       require PUN_ROOT.'lang/'.$pun_user['language'].'/honeypot_sfs_plugin.php';
else
       require PUN_ROOT.'lang/English/honeypot_sfs_plugin.php';

if (isset($_POST['form_sent']))
{
	// Lazy referer check (in case base_url isn't correct)
	if (!preg_match('#/admin_loader\.php#i', $_SERVER['HTTP_REFERER']))
		message($lang_common['Bad referrer']);

	$form = array_map('trim', $_POST['form']);

	while (list($key, $input) = @each($form))
	{
		// Only update values that have changed
		if ((isset($pun_config['o_'.$key])) || ($pun_config['o_'.$key] == NULL))
		{
			if ($pun_config['o_'.$key] != $input)
			{
				if ($input != '' || is_int($input))
					$value = '\''.$db->escape($input).'\'';
				else
					$value = 'NULL';

				$db->query('UPDATE '.$db->prefix.'config SET conf_value='.$value.' WHERE conf_name=\'o_'.$db->escape($key).'\'') or error('Unable to update board config', __FILE__, __LINE__, $db->error());
			}
		}
	}

	// Regenerate the config cache
	require_once PUN_ROOT.'include/cache.php';
	generate_config_cache();

	redirect('admin_loader.php?plugin=AP_Honeypot_SFS.php', $lang_honeypot_sfs_plugin['Options updated redirect']);
}
else if (isset($_POST['search_users']))
{
	// Display the admin navigation menu
?>
<div class="linkst">
	<div class="inbox">
		<div><a href="javascript:history.go(-1)"><?php echo $lang_admin_common['Go back'] ?></a></div>
	</div>
</div>

<div id="users1" class="blocktable">
	<h2><span><?php echo $lang_admin_common['Users'] ?></span></h2>
	<div class="box">
		<div class="inbox">
			<table cellspacing="0">
			<thead>
				<tr>
					<th class="tcl" scope="col"><?php echo $lang_common['Username'] ?></th>
					<th class="tc2" scope="col"><?php echo $lang_common['Email'] ?></th>
					<th class="tc3" scope="col"><?php echo $lang_common['Posts'] ?></th>
					<th class="tc4" scope="col"><?php echo $lang_profile['Website'] ?></th>
					<th class="tc5" scope="col"><?php echo $lang_profile['Signature'] ?></th>
					<th class="tcr" scope="col"><?php echo $lang_common['Registered'] ?></th>
				</tr>
			</thead>
			<tbody>
<?php
$result = $db->query('SELECT * FROM '.$db->prefix.'users WHERE id > 1 AND num_posts=0 AND signature IS NOT NULL ORDER BY registered DESC LIMIT 50') or error('Unable to fetch users', __FILE__, __LINE__, $db->error());

// If there are users with URLs in their signatures but 0 posts
if ($db->num_rows($result))
{
	while ($cur_user = $db->fetch_assoc($result))
	{
		echo "\t\t\t\t\t\t".'<tr><td class="tcl"><a href="profile.php?id='.$cur_user['id'].'">'.pun_htmlspecialchars($cur_user['username']).'</a></td><td class="tc2">'.$cur_user['email'].'</td><td class="tc3">'.forum_number_format($cur_user['num_posts']).'</td><td class="tc4" style="word-wrap: break-word">'.pun_htmlspecialchars($cur_user['url']).'</td><td class="tc5" style="word-wrap: break-word">'.pun_htmlspecialchars($cur_user['signature']).'</td><td class="tcr">'.format_time($cur_user['registered'], true).'</td></tr>'."\n";
	}
}
	else
		echo "\t\t\t\t".'<tr><td class="tcl" colspan="6">'.$lang_admin_users['No match'].'</td></tr>'."\n";
?>
			</tbody>
			</table>
		</div>
	</div>
</div>

<div class="linksb">
	<div class="inbox">
		<div><a href="javascript:history.go(-1)"><?php echo $lang_admin_common['Go back'] ?></a></div>
	</div>


<?php
}
else
{
	// Collect some statistics from the database
	$stats = array();

	switch ($db_type)
	{
		case 'pgsql':
			$day_selector = 'DATE(to_timestamp(date))';
			break;

		case 'sqlite':
			$day_selector = 'DATE(date, \'unixepoch\', \'localtime\')';
			break;

		default:
			$day_selector = 'DATE(FROM_UNIXTIME(date))';
			break;
	}


	$result = $db->query('SELECT MIN(date) FROM '.$db->prefix.'test_registrations') or error('Error1', __FILE__, __LINE__, $db->error());
	$stats['collecting_since'] = $db->result($result);

	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'test_registrations WHERE spam=\'0\'') or error('Error2', __FILE__, __LINE__, $db->error());
	$stats['num_nospam'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'test_registrations WHERE spam=\'1\'') or error('Error3', __FILE__, __LINE__, $db->error());
	$stats['num_honeypot'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id) FROM '.$db->prefix.'test_registrations WHERE spam=\'2\'') or error('Error4', __FILE__, __LINE__, $db->error());
	$stats['num_blacklist'] = $db->result($result);
	
	$result = $db->query('SELECT COUNT(id)/7 FROM '.$db->prefix.'test_registrations WHERE spam=\'0\' AND date > '.(time() - 7*24*60*60)) or error('Error5', __FILE__, __LINE__, $db->error());
	$stats['avg_nospam'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id)/7 FROM '.$db->prefix.'test_registrations WHERE spam=\'1\' AND date > '.(time() - 7*24*60*60)) or error('Error6', __FILE__, __LINE__, $db->error());
	$stats['avg_honeypot'] = $db->result($result);
	$result = $db->query('SELECT COUNT(id)/7 FROM '.$db->prefix.'test_registrations WHERE spam=\'2\' AND date > '.(time() - 7*24*60*60)) or error('Error7', __FILE__, __LINE__, $db->error());
	$stats['avg_blacklist'] = $db->result($result);

	$result = $db->query('SELECT '.$day_selector.' AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam=\'0\' GROUP BY day ORDER BY num_blocked DESC LIMIT 1') or error('Error8', __FILE__, __LINE__, $db->error());
	list($stats['most_nospam_date'], $stats['most_nospam_num']) = $db->fetch_row($result);
	$result = $db->query('SELECT '.$day_selector.' AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam=\'1\' GROUP BY day ORDER BY num_blocked DESC LIMIT 1') or error('Error9', __FILE__, __LINE__, $db->error());
	list($stats['most_honeypot_date'], $stats['most_honeypot_num']) = $db->fetch_row($result);
	$result = $db->query('SELECT '.$day_selector.' AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam=\'2\' GROUP BY day ORDER BY num_blocked DESC LIMIT 1') or error('Error10', __FILE__, __LINE__, $db->error());
	list($stats['most_blacklist_date'], $stats['most_blacklist_num']) = $db->fetch_row($result);

	$result = $db->query('SELECT '.$day_selector.' AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam=\'1\' AND date > '.(time()-14*24*60*60).' GROUP BY day') or error('Unable to fetch honeypot 14 day log', __FILE__, __LINE__, $db->error());
	while ($cur_date = $db->fetch_assoc($result))
		$stats['last_14days_honeypot'][$cur_date['day']] = $cur_date['num_blocked'];
	
	$result = $db->query('SELECT '.$day_selector.' AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam=\'2\' AND date > '.(time()-14*24*60*60).' GROUP BY day') or error('Unable to fetch sfs 14 day log', __FILE__, __LINE__, $db->error());
	while ($cur_date = $db->fetch_assoc($result))
		$stats['last_14days_sfs'][$cur_date['day']] = $cur_date['num_blocked'];


	// Display the admin navigation menu
	generate_admin_menu($plugin);
?>
	<div class="block">
		<h2><span>Honeypot + StopForumSpam - v<?php echo PLUGIN_VERSION ?></span></h2>
		<div class="box">
			<div class="inbox">
				<p><?php echo $lang_honeypot_sfs_plugin['Description'] ?></p>
			</div>
		</div>
	</div>
	<div class="blockform">
		<h2 class="block2"><span><?php echo $lang_honeypot_sfs_plugin['Options'] ?></span></h2>
		<div class="box">
			<form method="post" action="admin_loader.php?plugin=AP_Honeypot_SFS.php">
				<p class="submittop"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></p>
				<div class="inform">
					<input type="hidden" name="form_sent" value="1" />
					<fieldset>
						<legend><?php echo $lang_honeypot_sfs_plugin['Settings'] ?></legend>
						<div class="infldset">
						<table class="aligntop" cellspacing="0">
							<tr>
								<th scope="row"><?php echo $lang_honeypot_sfs_plugin['StopForumSpam check label'] ?></th>
								<td>
									<input type="radio" name="form[stopforumspam_check]" value="1"<?php if ($pun_config['o_stopforumspam_check'] == '1') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_admin_common['Yes'] ?></strong>&nbsp;&nbsp;&nbsp;<input type="radio" name="form[stopforumspam_check]" value="0"<?php if ($pun_config['o_stopforumspam_check'] == '0') echo ' checked="checked"' ?> />&nbsp;<strong><?php echo $lang_admin_common['No'] ?></strong>
									<span><?php echo $lang_honeypot_sfs_plugin['StopForumSpam check help'] ?></span>
								</td>
							</tr>
							<tr>
								<th scope="row"><?php echo $lang_honeypot_sfs_plugin['StopForumSpam API label'] ?></th>
								<td>
									<input type="text" name="form[stopforumspam_api]" size="20" maxlength="30" value="<?php echo $pun_config['o_stopforumspam_api'] ?>" />
									<span><?php echo $lang_honeypot_sfs_plugin['StopForumSpam API help'] ?></span>
								</td>
							</tr>
						</table>
						</div>
					</fieldset>
				</div>
			<p class="submitend"><input type="submit" name="save" value="<?php echo $lang_admin_common['Save changes'] ?>" /></p>
			</form>
		</div>
	</div>

	<div class="blockform block2">
		<h2><span><?php echo $lang_honeypot_sfs_plugin['Search users head'] ?></span></h2>
		<div class="box">
			<form method="post" action="admin_loader.php?plugin=AP_Honeypot_SFS.php">
				<div class="inbox">
					<p>
						<?php echo $lang_honeypot_sfs_plugin['Search users info'] ?>
					</p>
				</div>
				<p class="submitend">
					<input type="submit" name="search_users" value="<?php echo $lang_common['Go'] ?>" />
				</p>
			</form>
		</div>
	</div>

	<div class="blockform block2">
		<h2><span><?php echo $lang_honeypot_sfs_plugin['Statistics'] ?></span></h2>
		<div id="adstats" class="box">
			<div class="inbox">
				<dl>
					<dt><?php echo $lang_honeypot_sfs_plugin['Collecting stats since label'] ?></dt>
					<dd>
						<?php echo ($stats['collecting_since'] != '') ? date($pun_config['o_date_format'], $stats['collecting_since']).' ('.sprintf($lang_honeypot_sfs_plugin['Num days'], floor((time()-$stats['collecting_since'])/(60*60*24))).')'."\n" : $lang_honeypot_sfs_plugin['Not available']."\n" ?>
					</dd>
					<dt><?php echo $lang_honeypot_sfs_plugin['Total label'] ?></dt>
					<dd>
						<?php echo sprintf($lang_honeypot_sfs_plugin['Not spam info'], $stats['num_nospam']) ?><br />
						<?php echo sprintf($lang_honeypot_sfs_plugin['Blocked by Honeypot info'], $stats['num_honeypot']) ?><br />
						<?php echo sprintf($lang_honeypot_sfs_plugin['Blocked by SFS info'], $stats['num_blacklist'])."\n" ?>
					</dd>
					<dt><?php echo $lang_honeypot_sfs_plugin['Average last 7 days label'] ?></dt>
					<dd>
						<?php echo sprintf($lang_honeypot_sfs_plugin['Not spam info'], round($stats['avg_nospam'], 2)).' '.$lang_honeypot_sfs_plugin['per day'] ?><br />
						<?php echo sprintf($lang_honeypot_sfs_plugin['Blocked by Honeypot info'], round($stats['avg_honeypot'], 2)).' '.$lang_honeypot_sfs_plugin['per day'] ?><br />
						<?php echo sprintf($lang_honeypot_sfs_plugin['Blocked by SFS info'], round($stats['avg_blacklist'], 2)).' '.$lang_honeypot_sfs_plugin['per day']."\n" ?>
					</dd>
					<dt><?php echo $lang_honeypot_sfs_plugin['Maximum day label'] ?></dt>
					<dd>
						<?php echo sprintf($lang_honeypot_sfs_plugin['Not spam info'], ($stats['most_nospam_num'] > 0) ? $stats['most_nospam_num'].' ('.$stats['most_nospam_date'].')' : '0') ?><br />
						<?php echo sprintf($lang_honeypot_sfs_plugin['Blocked by Honeypot info'], ($stats['most_honeypot_num'] > 0) ? $stats['most_honeypot_num'].' ('.$stats['most_honeypot_date'].')' : '0') ?><br />
						<?php echo sprintf($lang_honeypot_sfs_plugin['Blocked by SFS info'], ($stats['most_blacklist_num'] > 0) ? $stats['most_blacklist_num'].' ('.$stats['most_blacklist_date'].')' : '0')."\n" ?>
					</dd>
                    <dt><?php echo $lang_honeypot_sfs_plugin['Blocked last 14 days label'] ?></dt>
					<dd>
<?php
$result = $db->query('SELECT '.$day_selector.' AS day, COUNT(date) AS num_blocked FROM '.$db->prefix.'test_registrations WHERE spam != \'0\' AND date > '.(time()-14*24*60*60).' GROUP BY day ORDER BY day') or error('Unable to fetch 14 day log', __FILE__, __LINE__, $db->error());

// If there are records of blocked registration attempts during the last 14 days
if ($db->num_rows($result))
{
	echo "\t\t\t\t\t\t".'<table>'."\n";
	echo "\t\t\t\t\t\t".'<tr><td style="padding: 0; border: 0; width:25%">'.$lang_honeypot_sfs_plugin['Date'].'</td><td style="padding: 0; border: 0; width:25%">'.$lang_honeypot_sfs_plugin['Total'].'</td><td style="padding: 0; border: 0; width:25%">Honeypot</td><td style="padding: 0; border: 0; width:25%">SFS</td></tr>'."\n";

	while ($cur_date = $db->fetch_assoc($result))
	{
		$day_honeypot = (isset($stats['last_14days_honeypot'][$cur_date['day']])) ? $stats['last_14days_honeypot'][$cur_date['day']] : '0';
		$day_sfs = (isset($stats['last_14days_sfs'][$cur_date['day']])) ? $stats['last_14days_sfs'][$cur_date['day']] : '0';
		echo "\t\t\t\t\t\t".'<tr><td style="padding: 0; border: 0">'.$cur_date['day'].'</td><td style="padding: 0; border: 0">'.$cur_date['num_blocked'].'</td><td style="padding: 0; border: 0">'.$day_honeypot.'</td><td style="padding: 0; border: 0">'.$day_sfs.'</td></tr>'."\n";
	}

	echo "\t\t\t\t\t\t".'</table>'."\n";
}
else
	echo "\t\t\t\t\t\t".$lang_honeypot_sfs_plugin['Not available']."\n";
?>
					</dd>
				</dl>
			</div>
		</div>
	</div>


<?php
}
?>