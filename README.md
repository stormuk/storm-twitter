storm-twitter
===========================

A Twitter API 1.1 compliant class that provides an array of a users timeline from Twitter for use in a nice cached-based way.

Uses Abraham Williams's Twitter OAuth class


Version 2.1
============================

The class has now been updated to support multiple username/screennames, as well as full support for any parameters you want to pass through to the statuses/user_timeline API. (https://dev.twitter.com/docs/api/1.1/get/statuses/user_timeline).


Basic Usage
============================

Basic usage of the class would be similar to

    require('StormTwitter.class.php');
    
    $config = array(
      'directory' => '', //The path used to store the .tweetcache cache file.
      'key' => '<App Key from Twitter>',
      'secret' => '<App Secret from Twitter>',
      'token' => '<Token from Twitter>',
      'token_secret' => '<Token Secret from Twitter>',
      'screenname' => '<Twitter Screename>', //This is now deprecated and you shouldn't define this - but it's here for backwards compatibility
      'cache_expire' => 3600 //The duration of the cache  
    );
    
    $twitter = new StormTwitter($config);
    
    // getTweets is the only public method. For legacy reasons, it takes between 0 and 3 parameters.
    // getTweets(twitter_screenname, number_of_tweets, custom_parameters_to_go_twitter);
    
    $tweets = $twitter->getTweets('lgladdy', 1);
    
    echo "<pre>";
    var_dump($tweets);
    echo "</pre>";
    
    $tweets = $twitter->getTweets('stormuk', 1, array('include_rts' => true, 'exclude_replies' => true));
    
    echo "<pre>";
    var_dump($tweets);
    echo "</pre>";
    
    

Change Log
============================

Version 2.1

Support for the much more sensical username before count in getTweets. (Pull request from stevelacey)
Check if the OAuthRequest class is already defined in our context. If it is, don't bother loading OAuth.php. This will stop compatibility errors with other OAuth plugins, but could mean you get other random errors relating to OAuth if you using anything other than the standard PHP OAuth library.


Version 2.0.4

Improve error handling if we don't get a standard error response from twitter, but something still went wrong.
