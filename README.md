storm-twitter
===========================

A Twitter API 1.1 compliant class that provides an array of a users timeline from Twitter for use in a nice cached-based way.

Uses Abraham Williams's Twitter OAuth class


Version 2.0
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
    // getTweets(number_of_tweets, twitter_screenname, custom_parameters_to_go_twitter);
    
    $tweets = $twitter->getTweets(1,'lgladdy');
    
    echo "<pre>";
    var_dump($tweets);
    echo "</pre>";
    
    $tweets = $twitter->getTweets(1,'stormuk', array('include_rts'=>true,'exclude_replies'=>true));
    
    echo "<pre>";
    var_dump($tweets);
    echo "</pre>";