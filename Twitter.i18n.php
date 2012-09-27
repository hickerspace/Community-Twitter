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

/**
 * Internationalization file for Twitter
 *
 * @file
 * @ingroup Language
 * @ingroup I18n
 */

$messages = array();

// English
$messages["en"] = array(
	"twitter" => "Twitter",
	"twitter_no_db_connection" => "No DB connection possible.",
	"twitter_no_db" => "DB does not exist.",
	"twitter_unknown_error" => "Either you were blocked/your account not set up or an unknown error occurred.<br /><br />In case you think you should have access, please contact <a href=\"./Benutzer:Basti\">Basti</a>.",
	"twitter_connection_failure" => "Connection to Twitter could not be established.",
	"twitter_tweet_section_title" => "Tweet!",
	"twitter_logged_in_as" => "Logged in as user",
	"twitter_update_success" => "Status successfully updated",
	"twitter_check_tweet" => "see",
	"twitter_unknown_error_request" => "An error occurred during the communication with Twitter. You can contact <a href=\"./Benutzer:Basti\">Basti</a> if this happens repeatedly.",
	"twitter_tweet_too_long" => "Tweets are limited to 140 characters.",
	"twitter_deletion_success" => "Tweet successfully deleted.",
	"twitter_deletion_tweet_not_found" => "A Tweet with the given ID does not exist.",
	"twitter_remaining_chars" => "Characters left",
	"twitter_your_last_tweets" => "Your last tweets",
	"twitter_delete_tweet_title_tag" => "Delete tweet",
	"twitter_delete_tweet_link_name" => "delete",
	"twitter_no_last_tweets" => "No tweets available",
	"twitter_description_template" => "Hickernews-Beschreibung",
	"twitter_advice_template" => "Hickernews-Hinweise",
	"twitter_access_error" => "You have to be <span class=\"plainlinks\"><a href=\"/wiki/Spezial:UserLogin\" class=\"external text\" title=\"log in\" rel=\"nofollow\">logged in</a></span> and \n".
														"<span class=\"plainlinks\"><a href=\"/wiki/Spezial:Gruppenrechte\" class=\"external text\" title=\"See group rights\" \n".
														"rel=\"nofollow\">allowed</a></span> to access this page."
);
// German (Deutsch)
$messages["de"] = array(
	"twitter" => "Twitter",
	"twitter_no_db_connection" => "Keine Verbindung zur DB m&ouml;glich.",
	"twitter_no_db" => "Die Datenbank existiert nicht.",
	"twitter_unknown_error" => "Entweder wurde dein Zugang zu den Hickernews deaktiviert bzw. noch nicht eingerichtet oder ein unbekannter Fehler ist aufgetreten.<br /><br />Wenn du glaubst, dass du Zugang zum Hickerspace Twitter-Account haben solltest, wende dich an <a href=\"./Benutzer:Basti\">Basti</a>.",
	"twitter_connection_failure" => "Verbindungsaufbau mit Zugangsdaten zu Twitter fehlgeschlagen.",
	"twitter_tweet_section_title" => "Tweet!",
	"twitter_logged_in_as" => "Eingeloggt als Benutzer",
	"twitter_update_success" => "Status erfolgreich aktualisiert",
	"twitter_check_tweet" => "einsehen",
	"twitter_unknown_error_request" => "Ein Fehler bei der Kommunikation mit Twitter ist aufgetreten. Du kannst dich an <a href=\"./Benutzer:Basti\">Basti</a> wenden, wenn dieser Fehler wiederholt auftritt.",
	"twitter_tweet_too_long" => "Die Statusmeldung darf nur maximal 140 Zeichen lang sein.",
	"twitter_deletion_success" => "Tweet erfolgreich gel&ouml;scht.",
	"twitter_deletion_tweet_not_found" => "Ein Tweet von dir mit dieser ID existiert nicht.",
	"twitter_remaining_chars" => "Verbleibende Zeichen",
	"twitter_your_last_tweets" => "Deine letzten Tweets",
	"twitter_delete_tweet_title_tag" => "Tweet l&ouml;schen",
	"twitter_delete_tweet_link_name" => "l&ouml;schen",
	"twitter_no_last_tweets" => "keine Tweets verf&uuml;gbar",
	"twitter_description_template" => "Hickernews-Beschreibung",
	"twitter_advice_template" => "Hickernews-Hinweise",
	"twitter_access_error" => "Du musst <span class=\"plainlinks\"><a href=\"/wiki/Spezial:UserLogin\" class=\"external text\" title=\"Anmelden\" rel=\"nofollow\">angemeldet</a></span> sein und \n".
														"&uuml;ber die entsprechenden <span class=\"plainlinks\"><a href=\"/wiki/Spezial:Gruppenrechte\" class=\"external text\" title=\"Benutzergruppen-Rechte einsehen\" \n".
														"rel=\"nofollow\">Rechte</a></span> verf&uuml;gen, um diese Seite sehen zu k&ouml;nnen.",
	"twitter_id_help_link" => "Wo finde ich die ID?",
	"twitter_id_help_text" => "Die Tweet-ID findet sich im Twitter-Link der zu retweetenden Nachricht. Beispiel: http://twitter.com/Hickernews/status/15036238117478400 (Tweet-Id: 15036238117478400)."
);
