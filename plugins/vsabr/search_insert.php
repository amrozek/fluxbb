<?php

/* ***** mod Very Simple AntiBot Registration - VSABR ***** */

/* Files to be modified by inserting strings into */
/* Option: The question will also be asked for Guests anonymous posting, if authorized
   If you do not want to install this option modifiy next line with :
   $vsabr_post_guest = false;    */
   $vsabr_post_guest = true;
   
/* If mod RMO (Reverse Message Order) is installed we modify include/quickpost.php
   instead of second string of viewtopic.php
   Note : RMO (Reverse Message Order) must be installed before VSABR   */
   
/* -------------------------------------------------------- */
/* String to be searched and string to be inserted in files */
/* ****** Each insert value must be terminated by \n ****** */

if(!$vsabr_post_guest) {
	$files_to_insert = array('register.php');
	$files_to_add = array('register.php');
}
else {
	if(file_exists(PUN_ROOT.'/include/quickpost.php')) {
	  $files_to_insert = array('register.php', 'post.php', 'viewtopic.php', 'include/quickpost.php');
	  $files_to_add = array('register.php','post.php');
		$search_file['viewtopic'] = array(
		  "// Load the viewtopic.php language file",
		);
		$insert_file['viewtopic'] = array(
		  "// [modif oto] - mod VSABR Very Simple AntiBot Registration - Add language file\nif(file_exists(PUN_ROOT.'lang/'.\$pun_user['language'].'/mod_very_simple_antibot.php'))\n  require PUN_ROOT.'lang/'.\$pun_user['language'].'/mod_very_simple_antibot.php';\nelse\n  require PUN_ROOT.'lang/English/mod_very_simple_antibot.php';\n\$mod_vsabr_index = rand(0,count(\$mod_vsabr_questions)-1);\n// [modif oto] - End mod VSABR\n",
		);
	
		$search_file['include/quickpost'] = array(
		  '<p class="buttons"><input type="submit" name="submit"',
		);
		$insert_file['include/quickpost'] = array(
		  "<?php //[modif oto] - mod VSABR Very Simple AntiBot Registration\nif(\$pun_user['is_guest']) : ?>\n<div class=\"inform\">\n\t<fieldset>\n\t\t<legend><?php\techo \$lang_mod_vsabr['Robot title']\t?></legend>\n\t\t<div class=\"infldset\">\n\t\t\t<p><?php echo\t\$lang_mod_vsabr['Robot info']\t?></p>\n\t\t\t<label class=\"required\"><strong><?php\n\t\t\t\t \$question = array_keys(\$mod_vsabr_questions);\n\t\t\t\t \$qencoded = md5(\$question[\$mod_vsabr_index]);\n\t\t\t\t echo\tsprintf(\$lang_mod_vsabr['Robot question'],\$question[\$mod_vsabr_index]);?>\n\t\t\t\t <span><?php echo\t\$lang_common['Required'] ?></span></strong>\n\t\t\t\t <input\tname=\"captcha\" id=\"captcha\"\ttype=\"text\"\tsize=\"10\"\tmaxlength=\"30\" /><input name=\"captcha_q\"\tvalue=\"<?php echo\t\$qencoded\t?>\"\ttype=\"hidden\"\t/><br\t/>\n\t\t\t</label>\n\t\t</div>\n\t</fieldset>\n</div>\n<?php endif; //[modif oto] - End mod VSABR ?>\n",
		);
	}
	else {
	  $files_to_insert = array('register.php', 'post.php', 'viewtopic.php');
 	  $files_to_add = array('register.php','post.php');
		$search_file['viewtopic'] = array(
		  "// Load the viewtopic.php language file",
		  '<p class="buttons"><input type="submit" name="submit"',
		);
		
		$insert_file['viewtopic'] = array(
		  "// [modif oto] - mod VSABR Very Simple AntiBot Registration - Add language file\nif(file_exists(PUN_ROOT.'lang/'.\$pun_user['language'].'/mod_very_simple_antibot.php'))\n  require PUN_ROOT.'lang/'.\$pun_user['language'].'/mod_very_simple_antibot.php';\nelse\n  require PUN_ROOT.'lang/English/mod_very_simple_antibot.php';\n\$mod_vsabr_index = rand(0,count(\$mod_vsabr_questions)-1);\n// [modif oto] - End mod VSABR\n",
		  "<?php //[modif oto] - mod VSABR Very Simple AntiBot Registration\nif(\$pun_user['is_guest']) : ?>\n<div class=\"inform\">\n\t<fieldset>\n\t\t<legend><?php\techo \$lang_mod_vsabr['Robot title']\t?></legend>\n\t\t<div class=\"infldset\">\n\t\t\t<p><?php echo\t\$lang_mod_vsabr['Robot info']\t?></p>\n\t\t\t<label class=\"required\"><strong><?php\n\t\t\t\t \$question = array_keys(\$mod_vsabr_questions);\n\t\t\t\t \$qencoded = md5(\$question[\$mod_vsabr_index]);\n\t\t\t\t echo\tsprintf(\$lang_mod_vsabr['Robot question'],\$question[\$mod_vsabr_index]);?>\n\t\t\t\t <span><?php echo\t\$lang_common['Required'] ?></span></strong>\n\t\t\t\t <input\tname=\"captcha\" id=\"captcha\"\ttype=\"text\"\tsize=\"10\"\tmaxlength=\"30\" /><input name=\"captcha_q\"\tvalue=\"<?php echo\t\$qencoded\t?>\"\ttype=\"hidden\"\t/><br\t/>\n\t\t\t</label>\n\t\t</div>\n\t</fieldset>\n</div>\n<?php endif; //[modif oto] - End mod VSABR ?>\n",
		);
	}
	
	//-- For file post.php
	$search_file['post'] = array(
	  '<p class="buttons"><input type="submit" name="submit"',
	  "\t// Flood protection",
	  '// Load the post.php language file'
	);
	$insert_file['post'] =  array(
	  "<?php //[modif oto] - mod VSABR Very Simple AntiBot Registration\nif(\$pun_user['is_guest']) : ?>\n<div class=\"inform\">\n\t<fieldset>\n\t\t<legend><?php\techo \$lang_mod_vsabr['Robot title']\t?></legend>\n\t\t<div class=\"infldset\">\n\t\t\t<p><?php echo\t\$lang_mod_vsabr['Robot info']\t?></p>\n\t\t\t<label class=\"required\"><strong><?php\n\t\t\t\t \$question = array_keys(\$mod_vsabr_questions);\n\t\t\t\t \$qencoded = md5(\$question[\$mod_vsabr_index]);\n\t\t\t\t echo\tsprintf(\$lang_mod_vsabr['Robot question'],\$question[\$mod_vsabr_index]);?>\n\t\t\t\t <span><?php echo\t\$lang_common['Required'] ?></span></strong>\n\t\t\t\t <input\tname=\"captcha\" id=\"captcha\"\ttype=\"text\"\tsize=\"10\"\tmaxlength=\"30\" /><input name=\"captcha_q\"\tvalue=\"<?php echo\t\$qencoded\t?>\"\ttype=\"hidden\"\t/><br\t/>\n\t\t\t</label>\n\t\t</div>\n\t</fieldset>\n</div>\n<?php endif; //[modif oto] - End mod VSABR ?>\n",
	  "//[modif oto] - mod VSABR Very Simple AntiBot Registration - Validate  answer to the question\nif(\$pun_user['is_guest']) {\n\t\$mod_vsabr_p_question = isset(\$_POST['captcha_q']) ? trim(\$_POST['captcha_q']) : '';\n\t\$mod_vsabr_p_answer = isset(\$_POST['captcha']) ? trim(\$_POST['captcha']) : '';\n\t\$mod_vsabr_questions_array = array();\n\tforeach (\$mod_vsabr_questions as \$k => \$v)\n  \t\$mod_vsabr_questions_array[md5(\$k)] = \$v;\n\tif (empty(\$mod_vsabr_questions_array[\$mod_vsabr_p_question]) || \$mod_vsabr_questions_array[\$mod_vsabr_p_question] != \$mod_vsabr_p_answer)\n  \t\$errors[] = \$lang_mod_vsabr['Robot test fail'];\n}\n//[modif oto] - End mod VSABR\n",
	  "// [modif oto] - mod VSABR Very Simple AntiBot Registration - Add language file\nif(file_exists(PUN_ROOT.'lang/'.\$pun_user['language'].'/mod_very_simple_antibot.php'))\n  require PUN_ROOT.'lang/'.\$pun_user['language'].'/mod_very_simple_antibot.php';\nelse\n  require PUN_ROOT.'lang/English/mod_very_simple_antibot.php';\n\$mod_vsabr_index = rand(0,count(\$mod_vsabr_questions)-1);\n// [modif oto] - End mod VSABR\n",
	);
}

//-- For file register.php
$search_file['register'] = array(
  '<p class="buttons"><input type="submit" name="register"',
  '	// Validate email',
  '// Load the register.php language file'
);
$insert_file['register'] =  array(
  "<!-- [modif oto] - mod VSABR Very Simple AntiBot Registration -->\n<div class=\"inform\">\n\t<fieldset>\n\t\t<legend><?php\techo \$lang_mod_vsabr['Robot title']\t?></legend>\n\t\t<div class=\"infldset\">\n\t\t\t<p><?php echo\t\$lang_mod_vsabr['Robot info']\t?></p>\n\t\t\t<label class=\"required\"><strong><?php\n\t\t\t\t \$question = array_keys(\$mod_vsabr_questions);\n\t\t\t\t \$qencoded = md5(\$question[\$mod_vsabr_index]);\n\t\t\t\t echo\tsprintf(\$lang_mod_vsabr['Robot question'],\$question[\$mod_vsabr_index]);?>\n\t\t\t\t <span><?php echo\t\$lang_common['Required'] ?></span></strong>\n\t\t\t\t <input\tname=\"captcha\" id=\"captcha\"\ttype=\"text\"\tsize=\"10\"\tmaxlength=\"30\" /><input name=\"captcha_q\"\tvalue=\"<?php echo\t\$qencoded\t?>\"\ttype=\"hidden\"\t/><input type=\"hidden\" name=\"username\" value=\"\" /><br />\n\t\t\t</label>\n\t\t</div>\n\t</fieldset>\n</div>\n<!-- [modif oto] - End mod VSABR -->\n",
  "//[modif oto] - mod VSABR Very Simple AntiBot Registration - Validate  answer to the question\n\$mod_vsabr_p_question = isset(\$_POST['captcha_q']) ? trim(\$_POST['captcha_q']) : '';\n\$mod_vsabr_p_answer = isset(\$_POST['captcha']) ? trim(\$_POST['captcha']) : '';\n\$mod_vsabr_questions_array = array();\nforeach (\$mod_vsabr_questions as \$k => \$v)\n  \$mod_vsabr_questions_array[md5(\$k)] = \$v;\nif (empty(\$mod_vsabr_questions_array[\$mod_vsabr_p_question]) || \$mod_vsabr_questions_array[\$mod_vsabr_p_question] != \$mod_vsabr_p_answer)\n  \$errors[] = \$lang_mod_vsabr['Robot test fail'];\n//[modif oto] - End mod VSABR\n",
  "// [modif oto] - mod VSABR Very Simple AntiBot Registration - Add language file\nif(file_exists(PUN_ROOT.'lang/'.\$pun_user['language'].'/mod_very_simple_antibot.php'))\n  require PUN_ROOT.'lang/'.\$pun_user['language'].'/mod_very_simple_antibot.php';\nelse\n  require PUN_ROOT.'lang/English/mod_very_simple_antibot.php';\n\$mod_vsabr_index = rand(0,count(\$mod_vsabr_questions)-1);\n// [modif oto] - End mod VSABR\n",
);

$search_add_file['register'] = array(
	"\tmessage(\$lang_register['No new regs']);\n",
	"\$required_fields = array('req_user' => \$lang_common['Username'], 'req_password1' => \$lang_common['Password'], 'req_password2' => \$lang_prof_reg['Confirm pass'], 'req_email1' => \$lang_common['Email'], 'req_email2' => \$lang_common['Email'].' 2');\n",
);
$insert_add_file['register'] = array(
	"//[modif oto] - VSABR Very Simple Anti Bot Registration\n//If the hidden field username contains something is that it was completed by a BOT.\nif(!empty(\$_REQUEST['username']))\n  message(\$lang_register['No new regs']);\n",
	"//[modif oto] - mod VSABR Very Simple AntiBot Registration - Line added\n\$required_fields['captcha'] = \$lang_mod_vsabr['Robot title'];\n",
);

//-- For file post.php if wanted
$search_add_file['post'] = array(
	"\t\$required_fields['req_username'] = \$lang_post['Guest name'];\n",
);
$insert_add_file['post'] = array(
	"\t//[modif oto] - mod VSABR Very Simple AntiBot Registration - Line added\n\t\$required_fields['captcha'] = \$lang_mod_vsabr['Robot title'];\n",
);
?>
