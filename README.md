
Community Twitter Extension
===========================

This extension grants (defined) users the ability to tweet using
one or more community twitter accounts.
Users can

- tweet
- retweet
- delete tweets (see below for details)

If you manage a smaller wiki, you have the ability to register an app for each user, so users identify themselves via the app name (see http://twitter.com/Hickernews/status/24840953797).
Applications must link to the user page in the wiki, so each user can only delete their tweets.

Before you start:
-----------------

- Check the MediaWiki article for compatibility issues and more: http://www.mediawiki.org/wiki/Extension:Community_Twitter
- Check our wiki for german instructions: http://hickerspace.org/wiki/Community_Twitter_Extension

How to start:
-------------

1. Register a Twitter account (or more) if you haven't already done.

2. Register an application/applications. Choose what fits your needs:

  - If you maintain a big wiki with lots of users using Community Twitter:
		Register one app. The name of the application gets displayed under the tweet. You might want to call it like the wiki.

  - If you maintain a manageable wiki with just a few users using Community Twitter:
		Register an application for each user who should be able to tweet on dev.twitter.com with read & write access.
		The name of the application gets displayed under the tweet. Call it like the corresponding user and link to the user page 
		(to let the extension link the user to his tweets). This is a cool way to have kind of a signature in order to guard against abuse.
		Users can only delete their own tweets.


3. Append the following CSS code to your /wiki/MediaWiki:Common.css:
```css
	/*
	Start: Community Twitter Extension CSS
	*/

	/* Style submit button like a normal link by Dan Schulz (http://forums.digitalpoint.com/showthread.php?t=403667#post3882723) */
	.submit { background: transparent; border-top: 0; border-right: 0; border-bottom: 1px solid #00F; border-left: 0; color: #00F; display: inline; margin: 0;padding: 0; }
	/* hack needed for IE 7 */
	*:first-child+html .submit { border-bottom: 0; text-decoration: underline; }
	/* hack needed for IE 5/6 */
	* html .submit { border-bottom: 0; text-decoration: underline; }
	.ct-error{ color:#FF0000; }
	.ct-info{ color: #B3B3B3; }
	.ct-success{ color:#006600; }
	span.ct-tweet{ text-align:right;margin: 10px 25px 10px 25px;float:right; }
	table.ct-tweet-section{ width:550px;height:100px; }
	td.ct-tweet-section{ text-align:center; }
	textarea.ct-tweet-input{ width: 490px;height: 50px; }
	table.ct-last-tweets{ width:550px; }

	/*
	End: Community Twitter Extension CSS
	*/
```


4. Create a new table in your SQL-DB by executing the following SQL-Code:
```mysql
	--
	-- Table structure for table `community_twitter`
	--

	CREATE TABLE IF NOT EXISTS `community_twitter` (
	  `user_id` int(11) NOT NULL,
	  `acc_name` text NOT NULL,
	  `consumer_key` text NOT NULL,
	  `consumer_secret` text NOT NULL,
	  `access_token` text NOT NULL,
	  `access_token_secret` text NOT NULL,
	  `active` smallint(6) NOT NULL
	) ENGINE=MyISAM DEFAULT CHARSET=utf8;
```

	Note: If you change the table name, you have to adapt the LocalSettings.php (in step 9).


5. Insert application data in the SQL table (e.g. via phpMyAdmin), depending on what you chose in the second step: 

- as user_id fill in the id of the user, who will be twittering via this app (look up in "user" table). If you chose to register just one app, set this to 0.
	  (Note: joint and individual accounts must not be merged!)
- as acc_name a self-defined name identifying the twitter account (has to be consistent; if you run only one Twitter account, it's everywhere the same)
- consumer_key is the Consumer Key on dev.twitter.com for your app
- consumer_secret is the Consumer Secret on dev.twitter.com for your app
- access_token is the Access Token on dev.twitter.com for your app
- access_token_secret is the Access Token Secret on dev.twitter.com for your app
- set active whether the user should be able to tweet or not (0=false, 1=true)

Note: In case you registered two Twitter accounts, just connect the registered application to both accounts (see e.g. http://jeffmiller.github.com/2010/05/31/twitter-from-the-command-line-in-python-using-oauth).
	  consumer_key and consumer_secret should be the same for each Twitter account in this case, only access_token and access_token_secret vary!

6. Create advice and description wiki pages. Their titles are defined in Twitter.i18n.php ("twitter_advice_template" and "twitter_description_template").
   These pages get displayed above (description) and under (advice) the Tweet! section.

7. Get twitteroauth (https://github.com/abraham/twitteroauth) and extract it, so that it's available at
	<webroot>/w/includes/twitteroauth/twitteroauth.php (OAuth.php should be in the same folder)

8. Move the extracted "Twitter" folder to <webroot>/w/extensions/

9. Append the following configs to your LocalSettings.php and customize them:
```php
	# Community Twitter Extension (settings first, THEN include)
	// DB-Table holding API keys and allowed users
	$ctTableName = "community_twitter";
	// Default Twitter account (has to be similar to the app_name column in the SQL-Table; e.g. used for linking tweets of different accounts)
	$ctDefaultAccount = "Hickernews";
	// Make special page accessible to this group (see http://www.mediawiki.org/wiki/Manual:User_rights#List_of_Groups) (creating a new group might make sense)
	$ctAllowedGroup = "sysop";
	// Set if you want to transmit coordinates with your tweets (has to be enabled in Twitter settings to be displayed)
	$ctDisplayCoordinates = true;
	// Latitude (ignored when $ctDisplayCoordinates is set to false)
	$ctLat = "52.161579";
	// Longitude (ignored when $ctDisplayCoordinates is set to false)
	$ctLong = "9.957183";
	// Aliases for /Special:Twitter
	$ctAliases = array("Hickernews");
	// If this is not the location you installed the extension to, you probably have to edit Twitter_body.php
	require_once("extensions/Twitter/Twitter.php");
```

10. That's it.

