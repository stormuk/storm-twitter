<?php
/*
* The base class for the storm twitter feed for developers.
* This class provides all the things needed for the wordpress plugin, but in theory means you don't need to use it with wordpress.
* What could go wrong?
*/

require_once('oauth/twitteroauth.php');

class StormTwitter {

  private $defaults = array(
    'directory' => '',
    'key' => '',
    'secret' => '',
    'token' => '',
    'token_secret' => '',
    'screenname' => ''      
  );
  
  public $st_last_cached = false;
  public $st_last_error = false;
  
  function __construct($args = array()) {
    $this->defaults = array_merge($this->defaults, $args);
  }
  
  function __toString() {
    return print_r($this->defaults, true);
  }
  
  //I'd prefer to put username before count, but for backwards compatibility it's not really viable. :(
  function getTweets($count = 20,$screenname = false) {  
    if ($count > 20) $count = 20;
    if ($count < 1) $count = 1;
    
    if ($screenname === false) $screenname = $this->defaults['screenname'];
  
    $result = $this->checkValidCache($screenname);
    
    if ($result !== false) {
      return $this->cropTweets($result,$count);
    }
    
    //If we're here, we need to load.
    $result = $this->oauthGetTweets($screenname);
    
    if (isset($result['errors'])) {
      return array('error'=>'Twitter said: '.$result['errors'][0]['message']);
    } else {
      return $this->cropTweets($result,$count);
    }
    
  }
  
  private function cropTweets($result,$count) {
    return array_slice($result, 0, $count);
  }
  
  private function getCacheLocation() {
    return $this->defaults['directory'].'.tweetcache';
  }
  
  private function checkValidCache($screenname) {
    $file = $this->getCacheLocation();
    if (is_file($file)) {
      $cache = file_get_contents($file);
      $cache = @json_decode($cache,true);
      if (count($cache) != 2) {
        unlink($file);
        return false;
      }
      if (!isset($cache)) {
        unlink($file);
        return false;
      }
      
      // Delete the old cache from the first version, before we added support for multiple usernames
      if (isset($cache['time'])) {
        unlink($file);
        return false;
      }
      
      //Check if we have a cache for the user.
      if (!isset($cache[$screenname])) return false;
      
      if (!isset($cache[$screenname]['time']) || !isset($cache[$screenname]['tweets'])) {
        unset($cache[$screenname]);
        file_put_contents($file,json_encode($cache));
        return false;
      }
      
      if ($cache[$screenname]['time'] < (time() - 3600)) {
        $result = $this->oauthGetTweets($screenname);
        if (!isset($result['errors'])) {
          return $result;
        }
      }
      return $cache[$screenname]['tweets'];
    } else {
      return false;
    }
  }
  
  private function oauthGetTweets($screenname) {
    $key = $this->defaults['key'];
    $secret = $this->defaults['secret'];
    $token = $this->defaults['token'];
    $token_secret = $this->defaults['token_secret'];
    
    if (empty($key)) return array('error'=>'Missing Consumer Key - Check Settings');
    if (empty($secret)) return array('error'=>'Missing Consumer Secret - Check Settings');
    if (empty($token)) return array('error'=>'Missing Access Token - Check Settings');
    if (empty($token_secret)) return array('error'=>'Missing Access Token Secret - Check Settings');
    if (empty($screenname)) return array('error'=>'Missing Twitter Feed Screen Name - Check Settings');
    
    $connection = new TwitterOAuth($key, $secret, $token, $token_secret);
    $result = $connection->get('statuses/user_timeline', array('screen_name' => $screenname, 'count' => 20, 'trim_user' => true));
    
    if (!isset($result['errors'])) {
      $cache[$screenname]['time'] = time();
      $cache[$screenname]['tweets'] = $result;
      $file = $this->getCacheLocation();
      file_put_contents($file,json_encode($cache));
      $this->st_last_cached = $cache[$screenname]['time'];
    } else {
      $last_error = '['.date('r').'] Twitter error: '.$result['errors'][0]['message'];
      $this->st_last_error = $last_error;
    }
    
    return $result;
  
  }
}