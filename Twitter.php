<?php
/**
 * Community Twitter Extension, giving (defined) users the opportunity to twitter using
 * one or more community twitter account.
 * Users have the ability to tweet, retweet and delete tweets.
 *
 * http://hickerspace.org/wiki/Community_Twitter_Extension
 *
 * @author Basti (http://hickerspace.org/wiki/User:Basti)
 *
 *
 * Integration in personal Menu by Yaron Koren ("Admin Links",
 * http://www.mediawiki.org/wiki/Extension:Admin_Links).
 * 
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License
 * as published by the Free Software Foundation; either version 2
 * of the License, or (at your option) any later version.
 * 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

if (!defined("MEDIAWIKI")) die("This is a MediaWiki extension and cannot be run stand-alone.");

// credits
$wgExtensionCredits['specialpage'][] = array(
	'path' => __FILE__,
	'name' => 'Community Twitter',
	'version' => '0.2',
	'author' => 'Basti',
	'url' => 'http://hickerspace.org/wiki/Community_Twitter_Extension',
	'description'  => 'Provides a special page, where special users are able to twitter via a community account without the need of giving away login credentials.',
);

$wgtwitterIP = dirname( __FILE__ ) . '/';
$wgExtensionMessagesFiles['twitter'] = $wgtwitterIP . 'Twitter.i18n.php';
$wgSpecialPages['twitter'] = 'twitter';
// Define aliases.
if(!empty($ctAliases)) {
	foreach ($ctAliases as $alias) {
		$wgSpecialPages[$alias] = 'twitter';
	}
}
$wgHooks['PersonalUrls'][] = 'twitter::addURLToUserLinks';
$wgAvailableRights[] = 'twitter';
// Only members of defined group see the link to the twitter special page
$wgGroupPermissions[$ctAllowedGroup]['twitter'] = true;
$wgAutoloadClasses['twitter'] = $wgtwitterIP . 'Twitter_body.php';
