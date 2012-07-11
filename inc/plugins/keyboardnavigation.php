<?php
/**
 * Keyboard Navigation 1.0
 * Copyright 2012 Fábio Maia, All Rights Reserved
 */
 
// Disallow direct access to this file for security reasons
if(!defined("IN_MYBB"))
{
	die("Direct initialization of this file is not allowed.<br /><br />Please make sure IN_MYBB is defined.");
}

$plugins->add_hook("showthread_end", "keyboardnavigation_showthread");
$plugins->add_hook("forumdisplay_end", "keyboardnavigation_forumdisplay");

function keyboardnavigation_info()
{
	return array(
		"name"			=> "Keyboard Navigation",
		"description"	=> "A plugin which enables keyboard navigation throughout the forum.",
		"website"		=> "http://mybb.com",
		"author"		=> "F&#225;bio Maia",
		"authorsite"	=> "http://community.mybb.com/user-16693.html",
		"version"		=> "1.0",
		"guid" 			=> "0ba023d1cd47865867778b73940c0bf7",
		"compatibility" => "14*,16*"
	);
}

function keyboardnavigation_showthread()
{
	global $mybb, $pages, $result, $perpage, $db, $headerinclude;

	/**
	* postcount? thread?
	*/

	// If there's no page parameter or page=first, we're on page 1
	if(!$mybb->input['page'] || $mybb->input['page'] == "first")
	{
		$mybb->input['page'] = "1";
	}

	// If page=last, we're on the last page
	if($mybb->input['page'] == "last")
	{
		$mybb->input['page'] = $pages;
	}

	// If there's a pid but no page parameter, find the page we're on
	if($mybb->input['pid'])
	{
		if(($result % $perpage) == 0)
		{
			$mybb->input['page'] = $result / $perpage;
		}
		else
		{
			$mybb->input['page'] = intval($result / $perpage) + 1;
		}
	}
	
	// Define page related variables to be used in the JavaScript file
	$thread_url_page     = htmlspecialchars_decode(get_thread_link((int)$mybb->input['tid'], (int)$mybb->input['page']));
	$thread_url_nextpage = htmlspecialchars_decode(get_thread_link((int)$mybb->input['tid'], (int)$mybb->input['page']+1));
	$thread_url_prevpage = htmlspecialchars_decode(get_thread_link((int)$mybb->input['tid'], (int)$mybb->input['page']-1));
	
	// If the page we're on is the last, then the next page is itself
	if($mybb->input['page'] == $pages)
	{
		$thread_url_nextpage = $thread_url_page;
	}

	// If the page we're on is the first, then the previous page is itself
	if($mybb->input['page'] == "1")
	{
		$thread_url_prevpage = $thread_url_page;
	}

	// Get array of tids to pick out at random later for the random key
	$query = $db->simple_select("threads", "tid");
	while($threads = $db->fetch_array($query))
	{
		$tids[] = $threads['tid'];
	}

	// Define tid related variables to be used in the JavaScript file
	$thread_url_thread     = get_thread_link($mybb->input['tid']);
	$thread_url_nextthread = get_thread_link($mybb->input['tid']+1);
	$thread_url_prevthread = get_thread_link($mybb->input['tid']-1);
	$thread_url_random     = get_thread_link(array_rand(array_flip($tids)));

	// If tid=1, then the previous thread is itself
	if($mybb->input['tid'] == "1")
	{
		$thread_url_prevthread = $thread_url_thread;
	}

	// If the random tid is equal to the current tid, generate a new tid
	if($thread_url_random == $thread_url_thread)
	{
		$thread_url_random = get_thread_link(array_rand(array_flip($tids)));
	}

	// Append scripts to the bottom of headerinclude
	$headerinclude .= "\n<script type=\"text/javascript\">\n<!--\n\tvar page = \"{$thread_url_page}\";\n\tvar nextpage = \"{$thread_url_nextpage}\";\n\tvar prevpage = \"{$thread_url_prevpage}\";\n\tvar url = \"{$thread_url_thread}\";\n\tvar nexturl = \"{$thread_url_nextthread}\";\n\tvar prevurl = \"{$thread_url_prevthread}\";\n\tvar random = \"{$thread_url_random}\";\n// -->\n</script>";
	$headerinclude .= "\n<script type=\"text/javascript\" src=\"{$mybb->settings['bburl']}/jscripts/keyboardnavigation.js\"></script>";
}

function keyboardnavigation_forumdisplay()
{
	global $mybb, $pages, $headerinclude;

	// If there's no page parameter or page=first, we're on page 1
	if(!$mybb->input['page'] || $mybb->input['page'] == "first")
	{
		$mybb->input['page'] = 1;
	}

	// If page=last, we're on the last page
	if($mybb->input['page'] == "last")
	{
		$mybb->input['page'] = $pages;
	}

	// Define page related variables to be used in the JavaScript file
	$forum_url_page     = htmlspecialchars_decode(get_forum_link((int)$mybb->input['fid'], (int)$mybb->input['page']));
	$forum_url_nextpage = htmlspecialchars_decode(get_forum_link((int)$mybb->input['fid'], (int)$mybb->input['page']+1));
	$forum_url_prevpage = htmlspecialchars_decode(get_forum_link((int)$mybb->input['fid'], (int)$mybb->input['page']-1));

	// If the page we're on is the last, then the next page is itself
	if($mybb->input['page'] == $pages)
	{
		$forum_url_nextpage = $forum_url_page;
	}

	// If the page we're on is the first, then the previous page is itself
	if($mybb->input['page'] == "1")
	{
		$forum_url_prevpage = $forum_url_page;
	}

	// If page=2, remove the page=1 parameter from the previous page
	if($mybb->input['page'] == "2")
	{
		$forum_url_prevpage = get_forum_link($mybb->input['fid']);
	}

	// Define fid related variables to be used in the JavaScript file
	$forum_url_forum     = get_forum_link($mybb->input['fid']);
	$forum_url_nextforum = get_forum_link($mybb->input['fid']+1);
	$forum_url_prevforum = get_forum_link($mybb->input['fid']-1);

	// If fid=1, then the previous forum is itself
	if($mybb->input['fid'] == "1")
	{
		$forum_url_prevforum = $forum_url_forum;
	}

	// Append scripts to the bottom of headerinclude
	$headerinclude .= "\n<script type=\"text/javascript\">\n<!--\n\tvar page = \"{$forum_url_page}\";\n\tvar nextpage = \"{$forum_url_nextpage}\";\n\tvar prevpage = \"{$forum_url_prevpage}\";\n\tvar url = \"{$forum_url_forum}\";\n\tvar nexturl = \"{$forum_url_nextforum}\";\n\tvar prevurl = \"{$forum_url_prevforum}\";//\n-->\n</script>";
	$headerinclude .= "\n<script type=\"text/javascript\" src=\"{$mybb->settings['bburl']}/jscripts/keyboardnavigation.js\"></script>";
}

?>