<?php

/**
 * @copyright (C) 2012 FluxBB (http://fluxbb.org)
 * @license GPL - GNU General Public License (http://www.gnu.org/licenses/gpl.html)
 * @package FluxBB
 */

// Define the version and database revision that this code was written for
define('FORUM_VERSION', '1.4.8');

define('FORUM_DB_REVISION', 15);
define('FORUM_SI_REVISION', 2);
define('FORUM_PARSER_REVISION', 2);

class PhpBB_3_0 extends Forum
{
	// Will the passwords be converted?
	var $converts_password = false;

	var $steps = array(
		'bans'					=> array('phpbb_banlist', 'ban_id'),
		'categories'			=> array('phpbb_forums', 'forum_id'/*, 'forum_type = 0'*/),
		'censoring'				=> array('phpbb_words', 'word_id'),
		'forums'				=> array('phpbb_forums', 'forum_id'/*, 'forum_type <> 0'*/),
		/*'groups'				=> array('phpbb_groups', 'group_id', 'group_id > 7'),*/
		'posts'					=> array('phpbb_posts', 'post_id'),
		'topic_subscriptions'	=> array('phpbb_topics_watch', 'topic_id'),
		'topics'				=> array('phpbb_topics', 'topic_id'),
		'users'					=> array('phpbb_users', 'user_id', 'group_id <> 6 AND user_id <> 1'),
	);

	function initialize()
	{
		$this->db->set_names('utf8');
	}

	/**
	 * Check whether specified database has valid current forum software structure
	 */
	function validate()
	{
		if (!$this->db->table_exists('phpbb_banlist'))
			conv_error('Selected database does not contain valid phpBB installation');
	}

	
	
	
	function convert_bans()
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'b.ban_id AS id, u.username, b.ban_ip AS ip, b.ban_email AS email, b.ban_until AS expire',
			'JOINS'        => array(
				array(
					'LEFT JOIN'	=> 'phpbb_users AS u',
					'ON'		=> 'u.user_id=b.ban_userid'
				),
			),
			'FROM'		=> 'phpbb_banlist AS b',
		)) or conv_error('Unable to fetch bans', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('bans', $this->db->num_rows($result));
		while ($cur_ban = $this->db->fetch_assoc($result))
		{
			$this->fluxbb->add_row('bans', $cur_ban);
		}
	}

	function convert_categories()
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'cat_id AS id, cat_title AS cat_name',
			'FROM'		=> 'phpbb_categories',
			'ORDER BY'	=> 'cat_order ASC'
		)) or conv_error('Unable to fetch categories', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('categories', $this->db->num_rows($result));
		$i = 1;
		while ($cur_cat = $this->db->fetch_assoc($result))
		{
			$cur_cat['disp_position'] = $i++;
			$this->fluxbb->add_row('categories', $cur_cat);
		}
	}

	function convert_censoring()
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'word_id AS id, word AS search_for, replacement AS replace_with',
			'FROM'		=> 'phpbb_words',
		)) or conv_error('Unable to fetch words', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('censoring', $this->db->num_rows($result));
		while ($cur_censor = $this->db->fetch_assoc($result))
		{
			$this->fluxbb->add_row('censoring', $cur_censor);
		}
	}

	function convert_forums()
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'phpbb_forums.forum_id AS id, cat_id, forum_name, forum_desc, forum_topics AS num_topics, forum_posts AS num_posts, forum_order AS disp_position, phpbb_posts.post_username AS last_poster, forum_last_post_id AS last_post_id, phpbb_posts.post_time AS last_post',
			'JOINS'        => array(
				array(
					'INNER JOIN'	=> 'phpbb_posts',
					'ON'		=> 'phpbb_forums.forum_last_post_id = phpbb_posts.post_id'
				),
			),
			'FROM'		=> 'phpbb_forums',
			'ORDER BY'	=> 'forum_order ASC'
		)) or conv_error('Unable to fetch forums', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('forums', $this->db->num_rows($result));
		while ($cur_forum = $this->db->fetch_assoc($result))
		{
			$cur_forum['forum_desc'] = $this->convert_message($cur_forum['forum_desc']);

			if ($cur_forum['num_topics'] == 0)
				$cur_forum['last_post'] = $cur_forum['last_post_id'] = $cur_forum['last_poster'] = NULL;

			$this->fluxbb->add_row('forums', $cur_forum);
		}
	}

	/*function convert_groups()
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'rank_id AS g_id, rank_title AS g_title, rank_title AS g_user_title',
			'FROM'		=> 'phpbb_ranks',
			'WHERE'		=> 'rank_id = 1 OR rank_id = 5 OR rank_id = 6'
		)) or conv_error('Unable to fetch groups', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('groups', $this->db->num_rows($result));
		while ($cur_group = $this->db->fetch_assoc($result))
		{
			$cur_group['g_id'] = $this->grp2grp($cur_group['g_id']);

			$this->fluxbb->add_row('groups', $cur_group);
		}
	}*/

	function convert_posts($start_at)
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'p.post_id AS id, IF(p.post_username=\'\', u.username, p.post_username) AS poster, p.poster_id, p.post_time AS posted, p.poster_ip, pt.post_text AS message, p.topic_id',
			'JOINS'        => array(
				array(
					'LEFT JOIN'	=> 'phpbb_users AS u',
					'ON'		=> 'u.user_id=p.poster_id'
				),
				array(
					'INNER JOIN' => 'phpbb_posts_text AS pt',
					'ON'		=> 'p.post_id=pt.post_id'
				),
			),
			'FROM'		=> 'phpbb_posts AS p',
			'WHERE'		=> 'p.post_id > '.$start_at,
			'ORDER BY'	=> 'p.post_id ASC',
			'LIMIT'		=> PER_PAGE,
		)) or conv_error('Unable to fetch posts', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('posts', $this->db->num_rows($result), $start_at);

		if (!$this->db->num_rows($result))
			return false;

		while ($cur_post = $this->db->fetch_assoc($result))
		{
			$start_at = $cur_post['id'];
			$cur_post['message'] = $this->convert_message($cur_post['message']);

			$this->fluxbb->add_row('posts', $cur_post);
		}

		return $this->redirect('phpbb_posts', 'post_id', $start_at);
	}

	function convert_topic_subscriptions()
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'DISTINCT user_id, topic_id',
			'FROM'		=> 'phpbb_topics_watch',
		)) or conv_error('Unable to fetch topic subscriptions', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('topic subscriptions', $this->db->num_rows($result));
		while ($cur_sub = $this->db->fetch_assoc($result))
		{
			$this->fluxbb->add_row('topic_subscriptions', $cur_sub);
		}
	}

	function convert_topics($start_at)
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'phpbb_topics.topic_id AS id, phpbb_users.username AS poster, topic_title AS subject, topic_time AS posted, topic_first_post_id AS first_post_id, phpbb_posts.post_time AS last_post, topic_last_post_id AS last_post_id, phpbb_posts.post_username AS last_poster, topic_views AS num_views, topic_replies AS num_replies, IF(topic_status=1, 1, 0) AS closed, IF(topic_type=1 OR topic_type=2, 1, 0) AS sticky, IF(topic_moved_id=0, NULL, topic_moved_id) AS moved_to, phpbb_topics.forum_id',
			'JOINS'        => array(
				array(
					'LEFT JOIN'	=> 'phpbb_posts',
					'ON'		=> 'phpbb_topics.topic_last_post_id = phpbb_posts.post_id'
				),
				array(
					'LEFT JOIN'	=> 'phpbb_users',
					'ON'		=> 'phpbb_topics.topic_poster = phpbb_users.user_id'
				),
			),
			'FROM'		=> 'phpbb_topics',
			'WHERE'		=> 'phpbb_topics.topic_id > '.$start_at,
			'ORDER BY'	=> 'phpbb_topics.topic_id ASC',
			'LIMIT'		=> PER_PAGE,
		)) or conv_error('Unable to fetch topics', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('topics', $this->db->num_rows($result), $start_at);

		if (!$this->db->num_rows($result))
			return false;

		while ($cur_topic = $this->db->fetch_assoc($result))
		{
			if($cur_topic['last_post'] != NULL){
				$start_at = $cur_topic['id'];
				$cur_topic['subject'] = html_entity_decode($cur_topic['subject'], ENT_QUOTES, 'UTF-8');
			
				$this->fluxbb->add_row('topics', $cur_topic);
			}
		}

		return $this->redirect('phpbb_topics', 'topic_id', $start_at);
	}

	function convert_users($start_at)
	{
		$result = $this->db->query_build(array(
			'SELECT'	=> 'user_id AS id, username, user_password AS password, user_website AS url, user_icq AS icq, user_msnm AS msn, user_aim AS aim, user_yim AS yahoo, user_posts AS num_posts, user_from AS location, user_viewemail AS email_setting, user_timezone AS timezone, user_regip AS registration_ip, user_regdate AS registered, user_lastvisit AS last_visit, user_sig AS signature, user_email AS email, user_avatar, user_gender AS gender', 
			'FROM'		=> 'phpbb_users',
			'WHERE'		=> 'user_id <> 1 AND user_id > 1 AND user_email != \'\' AND user_id > '.$start_at,
			'ORDER BY'	=> 'user_id ASC',
			'LIMIT'		=> PER_PAGE,
		)) or conv_error('Unable to fetch users', __FILE__, __LINE__, $this->db->error());

		conv_processing_message('users', $this->db->num_rows($result), $start_at);

		if (!$this->db->num_rows($result))
			return false;

		while ($cur_user = $this->db->fetch_assoc($result))
		{
			$start_at = $cur_user['id'];
			$cur_user['username'] = html_entity_decode($cur_user['username']);
			if($cur_user['id'] == 10 || $cur_user['id'] == 2){ // Set myself and SR as administrator
				$cur_user['group_id'] = PUN_ADMIN;
			}
			else {
				$cur_user['group_id'] = PUN_MEMBER; // $this->grp2grp($cur_user['group_id']); // I don't think this function works, but moderators can be added manually later
			}
			$cur_user['email_setting'] = !$cur_user['email_setting'];
			$cur_user['signature'] = $this->convert_message($cur_user['signature']);

			$this->convert_avatar($cur_user);
			unset($cur_user['user_avatar']);

			$this->fluxbb->add_row('users', $cur_user, array($this->fluxbb, 'error_users'));
		}

		return $this->redirect('phpbb_users', 'user_id', $start_at);
	}
	

	/**
	 * Convert group id to the FluxBB style (use FluxBB constants, see index.php:83)
	 */
	function grp2grp($id)
	{
		if($id == 1){ //Admins
			$group_id = PUN_ADMIN;
		}
		else if($row['user_rank'] == 6) { //Global mods
			$group_id = 5; 
		}
		else { // regular members
			$group_id = PUN_MEMBER;
		}
		
		return $group_id;
	}

	/**
	 * Convert BBcode
	 */
	function convert_message($message)
	{
		static $patterns, $replacements;

		$message = html_entity_decode($message, ENT_QUOTES, 'UTF-8');

		if (!isset($patterns))
		{
			$patterns = array(
				'%\[(\/?)(b|i|u|list|\*|img|url|code|quote|spoiler|size):[a-z0-9]{5,10}(\=[^\]]*)?(:[a-z])?\]%i'	=>	'[$1$2$3]', // Strip text after colon in tag name
				
				'%\[(\/?)(youtube):[a-z0-9]{5,10}(:[a-z])?\](https?:\/\/www.youtube.com\/watch\?v\=)?%i'	=>	'[$1$2$3]', // fix youtube BBCode to match FluxBB's format
				
				'%\\[(\/?)(color|code)(\=[^\]]*)?(:[a-z0-9])?:[a-z0-9]{5,10}\]%i'	=>	'[$1$2$3]', // Strip text after colon in tag name (color and code tags)

				'%\[/?(flash|font|size)(?:\=[^\]]*)?\]%i'													=> '',	// Strip tags not supported by FluxBB

				// Smileys
				'#<!-- s.*? --><img src=".*?" alt="(.*?)" title=".*?" \/><!-- s.*? -->#i'			=>	'$1',

				'#<!-- [mw] --><a class="postlink" href="(.*?)">(.*?)</a><!-- [mw] -->#i'			=>	'[url=$1]$2[/url]',
				'#<!-- e --><a href="mailto:(.*?)">(.*?)</a><!-- e -->#i'							=>	'[email=$1]$2[/email]',
			);
		}

		$message = preg_replace(array_keys($patterns), array_values($patterns), $message);

		if (!isset($replacements))
		{
			$replacements = array(
				'=-O'		=> ':o',
				'8-)'			=> ':cool:',
				':-/'		=> ':/',
				':-D'		=> ':D',
				':-)'		=> ':)',
				':-('		=> ':(',
				':-!'		=> ':|',
				'>:-o'		=> ':mad:',
				'>:-|'		=> ':|',
				':rolls eyes:'		=> ':rolleyes:',
			);
		}

		return $this->fluxbb->preparse_bbcode(str_replace(array_keys($replacements), array_values($replacements), $message), $errors);
	}

	/**
	 * Copy avatar file to the FluxBB avatars dir
	 */
	function convert_avatar($cur_user)
	{
		static $config;

		if (empty($cur_user['user_avatar'])){
			
			return false;
		}
		
		// Fetch avatar from remote url
		if (strpos($cur_user['user_avatar'], '://') !== false)
			return $this->fluxbb->save_avatar($cur_user['user_avatar'], $cur_user['id']);

		else if (isset($this->path))
		{
			if (!isset($config))
			{
				$config = array();

				$result = $this->db->query_build(array(
					'SELECT'	=> 'config_name, config_value',
					'FROM'		=> 'phpbb_config',
					'WHERE'		=> 'config_name IN (\'avatar_path\', \'avatar_salt\', \'avatar_gallery_path\')'
				)) or conv_error('Unable to fetch config', __FILE__, __LINE__, $this->db->error());

				while ($cur_config = $this->db->fetch_assoc($result))
					$config[$cur_config['config_name']] = $cur_config['config_value'];
			}

			// Look for user avatar from gallery
			$cur_avatar_file = $this->path.rtrim($config['avatar_gallery_path'], '/').'/'.$cur_user['user_avatar'];
			if (file_exists($cur_avatar_file))
				return $this->fluxbb->save_avatar($cur_avatar_file, $cur_user['id']);

			// Fetch avatar from local file
			$old_avatars_dir = $this->path.rtrim($config['avatar_path'], '/').'/';

			$cur_avatar_file = $old_avatars_dir.$cur_user['user_avatar'];
			if (file_exists($cur_avatar_file))
				return $this->fluxbb->save_avatar($cur_avatar_file, $cur_user['id']);
			
		}

		return false;
	}
}
