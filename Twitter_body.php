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

class Twitter extends SpecialPage {

	function Twitter() {
		global $wgUser;
		global $individualAccs;

		$individualAccs = true;

		// In case you want to change the name of the special page, you have to edit Twitter_body.php and Twitter.php
		SpecialPage::SpecialPage("twitter");
		$this->skin = $wgUser->getSkin();
	}

	function execute($query) {
		global $wgOut;
		global $wgUser;
		global $wgScriptPath;
		global $wgRequest;
		global $wgDBserver;
		global $wgDBuser;
		global $wgDBpassword;
		global $wgDBname;
		global $ctTableName;
		global $ctDefaultAccount;
		global $ctDisplayCoordinates;
		global $ctLat;
		global $ctLong;
		global $ctAllowedGroup;
		global $individualAccs;
		global $IP;

		if (!isset($ctTableName) || !isset($ctDefaultAccount) || !isset($ctDisplayCoordinates)) die ("Configure LocalSettings.php first.");

		$dbr =& wfGetDB(DB_SLAVE);

		$this->setHeaders();

		// Grant access to defined group only
		if ($wgUser->getId() != 0 && in_array($ctAllowedGroup, $wgUser->getEffectiveGroups())) {

			require_once("{$IP}/includes/twitteroauth/twitteroauth.php");

			// Query for joint account(s)
			$sql_joint = sprintf("SELECT app_name, consumer_key, consumer_secret, access_token, access_token_secret FROM %s ".
									"WHERE `active` = 1 AND `user_id` = 0;", $ctTableName);
			$res = $dbr->query($sql_joint);

			if ($dbr->numRows($res) == 0) {
				unset($res);
				// Query for individual account(s)
				$sql_individual = sprintf("SELECT app_name, consumer_key, consumer_secret, access_token, access_token_secret FROM %s NATURAL JOIN %s ".
											"JOIN %s ON (user_id=ug_user) WHERE `ug_group` = '%s' AND `active` = 1 AND (`user_id` = %d OR `user_id` = 0);",
											$dbr->tableName("user"), $ctTableName, $dbr->tableName("user_groups"), $ctAllowedGroup, $wgUser->getId());
				$res = $dbr->query($sql_individual);
			} else {
				$individualAccs = false;
			}

			if ($dbr->numRows($res) != 0) {

				$wgOut->addHTML("<script type=\"text/javascript\" src=\"".$wgScriptPath."/extensions/Twitter/js/jquery.js\"></script>\n".
								"<script type=\"text/javascript\" src=\"".$wgScriptPath."/extensions/Twitter/js/twitter.js\"></script>\n");

				// Establish Twitter-connections
				$connections = array();

				while($row = $dbr->fetchObject($res)) {
					$connections[$row->app_name] = @new TwitterOAuth($row->consumer_key, $row->consumer_secret, $row->access_token, $row->access_token_secret);
				}
				if (!twitter::checkConnections($connections)) {
					$wgOut->addHTML("<strong class=\"ct-error\">".wfMsg("twitter_connection_failure")."</strong>");
				} else {
			
					// Get Twitter timeline from default account
					$timeline = $connections[$ctDefaultAccount]->get("statuses/user_timeline", array("include_rts" => "true"));
					$twitterStatuses = array_values(array_filter(array_map(array("Twitter", "getOwnTweets"), $timeline)));

					// Display description template
					$wgOut->addWikiText("{{".wfMsg("twitter_description_template")."}}");

					// Tweet section
					$wgOut->addWikiText("==".wfMsg("twitter_tweet_section_title")."==");
					$wgOut->addHTML(wfMsg("twitter_logged_in_as")." <b>".$wgUser->getName()."</b>.\n<br/>\n".
											"<table class=\"wikitable ct-tweet-section\"><tr><td class=\"ct-tweet-section\">\n");

					// Process update/deletion/retweet

					// New status set by user
					if ($wgRequest->getText("status") != "") {

						// Send status message via OAuth to Twitter
						$responseIds = array();
						foreach($connections as $connection) {
							$tweetData = array("status" => urldecode($wgRequest->getText("status")));
							// Positions get ignored, if latitude and/or longitude are not set
							if ($ctDisplayCoordinates && isset($ctLat) && isset($ctLong)) {
								$tweetData["lat"] = $ctLat;
								$tweetData["long"] = $ctLong;
								$tweetData["display_coordinates"] = "true";
							}
							$responseIds[array_search($connection, $connections, true)] = $connection->post("statuses/update", $tweetData)->id_str;
						}

						// If response seems to be ok, tell the user about success, else, error unknown
						if (twitter::checkResponses($responseIds)) {
							foreach($connections as $name => $connection) {
								$wgOut->addWikiText("<strong class=\"ct-success\">".wfMsg("twitter_update_success")." ([http://twitter.com/".$name."/status/".$responseIds[$name]." ".wfMsg("twitter_check_tweet")."]).</strong>\n");
							}
						
							// Get the new status only to display updated "last tweets" section
							$newStatus = $connections[$ctDefaultAccount]->get("statuses/show", array("id" => $responseIds[$ctDefaultAccount], "include_rts" => "true"));
							array_unshift($twitterStatuses, $newStatus);
						} else {
							$wgOut->addHTML("<strong class=\"ct-error\">".wfMsg("twitter_unknown_error_request")."</strong>\n");
						}

					} else if ($wgRequest->getText("deleteId") != "") {
						// User requests deletion of tweet
						$delIdNotFound = true;
						$delUnknownError = false;

						// Verify that user is author of to be deleted tweet
						for ($i = 0; $i < count($twitterStatuses); $i++) {
							if ($twitterStatuses[$i]->id_str == $wgRequest->getText("deleteId")) {

								$responses = array();

								foreach (array_keys($connections) as $appName) {
									if ($appName == $ctDefaultAccount) continue;
									// Generate hashmap with default account ids as keys and other account ids as values
									$currTimeline = $connections[$appName]->get("statuses/user_timeline", array("include_rts" => "true"));
									$currTimeline = array_values(array_filter(array_map(array("Twitter", "getOwnTweets"), $currTimeline)));
									$hashId = twitter::hashIds($twitterStatuses, $currTimeline);
									if (array_key_exists($wgRequest->getText("deleteId"), $hashId)) {
										$responses[$appName] = $connections[$appName]->post("statuses/destroy", array("id" => $hashId[$wgRequest->getText("deleteId")]));
									}
								}

								// Delete Tweet via OAuth
								$responses[$ctDefaultAccount] = $connections[$ctDefaultAccount]->post("statuses/destroy", array("id" => $wgRequest->getText("deleteId")));

								if (twitter::checkResponses($responses)) {
									$wgOut->addHTML("<strong class=\"ct-success\">".wfMsg("twitter_deletion_success")."</strong>\n");
									// Update timeline array to display updated "your last tweets" section
									unset($twitterStatuses[$i]);
								} else {
									$wgOut->addHTML("<strong class=\"ct-error\">".wfMsg("twitter_unknown_error_request")."</strong>\n");
								}

								$delIdNotFound = false;
							}
						}
						if ($delIdNotFound) $wgOut->addHTML("<strong class=\"ct-error\">".wfMsg("twitter_deletion_tweet_not_found")."</strong>\n");

					} else if ($wgRequest->getText("tweetid") != "" && is_numeric($wgRequest->getText("tweetid")) && in_array($ctAllowedGroup, $wgUser->getEffectiveGroups())) {

						// Retweet status message via OAuth on Twitter
						$responseIds = array();
						foreach($connections as $connection) {
							$responseIds[array_search($connection, $connections, true)] = $connection->post("statuses/retweet/".$wgRequest->getText("tweetid"))->id_str;
						}

						// If response seems to be ok, tell the user about success, else: error unknown
						if (twitter::checkResponses($responseIds)) {
							$wgOut->addWikiText("<strong class=\"ct-success\">".wfMsg("twitter_update_success")."</strong>\n");
						} else {
							$wgOut->addHTML("<strong class=\"ct-error\">".wfMsg("twitter_unknown_error_request")."</strong>\n");
						}

					} else {
						// Tweet textarea with javascript counter (disables the button, if tweet is too long (comfort feature, no security feature)
						$wgOut->addHTML("<form action=\"".$wgScriptPath."/../wiki/Special:Twitter\" method=\"post\" id=\"status_update_form\">\n".
										"<span class=\"ct-tweet\">".wfMsg("twitter_remaining_chars").": <strong id=\"stringlength\"><strong>140</strong></strong><br/>\n".
										"<textarea tabindex=\"1\" autocomplete=\"off\" accesskey=\"u\" name=\"status\" id=\"status\" rows=\"2\" cols=\"40\" class=\"ct-tweet-input\"></textarea>\n".
										"<br/><input type=\"submit\" id=\"tweetbutton\" value=\"  Tweet   \"></span>\n".
										"</form><br /><br /><br /><br /><br /><br />\n".
										"<span class=\"ct-tweet\">".
										"<form action=\"".$wgScriptPath."/../wiki/Special:Twitter\" method=\"post\">Tweet-ID: <input type=\"text\" name=\"tweetid\" />".
										"<br /><a href=\"javascript:alert('".wfMsg("twitter_id_help_text")."')\">".
										wfMsg("twitter_id_help_link")."</a> <input type=\"submit\" value=\"Retweet\"></form></span>");
					}

					$wgOut->addHTML("</td></tr></table><table class=\"wikitable\" class=\"ct-last-tweets\"><tr><td>&nbsp;<b>".wfMsg("twitter_your_last_tweets").":</b><br/><ul>\n");

					// Display users' last tweets to give him the opportunity to delete his own tweets
					if (count($twitterStatuses) > 0) {
						for ($i=0; $i < min(5,count($twitterStatuses)); $i++) {
								if ($twitterStatuses[$i]->text != "") {
								$wgOut->addHTML("<form action=\"".$wgScriptPath."/../wiki/Special:Twitter\" method=\"post\"><input type=\"hidden\" name=\"deleteId\" value=\"".($twitterStatuses[$i]->id_str)."\" /><li>[<input class=\"submit\" type=\"submit\" value=\"".wfMsg("twitter_delete_tweet_link_name")."\" title=\"".wfMsg("twitter_delete_tweet_title_tag")."\" />] ".$twitterStatuses[$i]->text."</li></form>\n");
							}
						}
					} else {
						$wgOut->addHTML("<span class=\"ct-info\">".wfMsg("twitter_no_last_tweets")."</span>");
					}

					$wgOut->addHTML("</ul></td></tr></table>\n");


					// Display Twitter advices template
					$wgOut->addWikiText("{{".wfMsg("twitter_advice_template")."}}");
				}
			} else {
				$wgOut->addHTML("<strong class=\"ct-error\">".wfMsg("twitter_unknown_error_request")."</strong>\n");
			}
		} else {
			// Not logged in or no adequate group status
			$wgOut->addHTML(wfMsg("twitter_access_error")."\n");
		}
	}

	// Add a link to the special 'AdminLinks' page among the user's "personal URLs" at the top 
	// Source: "Admin Links" by Yaron Koren (http://www.mediawiki.org/wiki/Extension:Admin_Links) (customized)
	public static function addURLToUserLinks( &$personal_urls, &$title ) {
		global $wgUser;
		// if user is allowed, add link
		if ( $wgUser->isAllowed( 'twitter' ) ) {
			$al = SpecialPage::getTitleFor( 'twitter' );
			$href = $al->getLocalURL();
			$twitter_vals = array(
				'text' => wfMsg( 'twitter' ),
				'href' => $href,
				'active' => ( $href == $title->getLocalURL() )
			);
			// find the location of the 'my preferences' link, and
			// add the link to 'AdminLinks' right before it.
			// this is a "key-safe" splice - it preserves both the
			// keys and the values of the array, by editing them
			// separately and then rebuilding the array.
			// based on the example at http://us2.php.net/manual/en/function.array-splice.php#31234
			$tab_keys = array_keys( $personal_urls );
			$tab_values = array_values( $personal_urls );
			$prefs_location = array_search( 'preferences', $tab_keys );
			array_splice( $tab_keys, $prefs_location, 0, 'twitter' );
			array_splice( $tab_values, $prefs_location, 0, array( $twitter_vals ) );
			$personal_urls = array();
			for ( $i = 0; $i < count( $tab_keys ); $i++ )
				$personal_urls[$tab_keys[$i]] = $tab_values[$i];

		}
		return true;
	}

	// Return only self-tweeted messages
	function getOwnTweets($timelineItem) {
		global $wgUser;
		global $individualAccs;

		if (stripos($timelineItem->source, "User:".$wgUser->getName()."\"") !== false || !$individualAccs) {
			return $timelineItem;
		} else {
			return null;
		}
	}

	// Checks an array of connections
	function checkConnections($connections) {
		$result = false;
		foreach($connections as $connection) {
			$result = (!property_exists($connection->get('account/verify_credentials'), "error")) ? true : false;
		}
		return $result;
	}

	// Checks an array of responses
	function checkResponses($connections) {
		$result = true;
		foreach($connections as $connection) {
			if (!isset($connection)) $result = false;
		}
		return $result;
	}

	// Links message ids from one account to the same message on another account
	function hashIds($timelineA, $timelineB) {
		$hashList = array();
		foreach ($timelineA as $a) {
			foreach ($timelineB as $b) {
				if ($a->text == $b->text) {
					$hashList[$a->id_str] = $b->id_str;
				}
			}
		}
		return $hashList;
	}

}

?>
