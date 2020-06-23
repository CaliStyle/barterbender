<?php
require_once('simplepie.inc');
require_once('idn/idna_convert.class.php');

class YnRSSReader
{
  public function parseRSSFeeds($rss_url)
  {
    set_time_limit(0);
    // Initialize feed for use.
    $feed = new SimplePie();
    $feed->set_feed_url($rss_url);

    $feed->set_favicon_handler('./handler_image.php');
    
    $feed->set_image_handler('./handler_image.php');
    
    // Initialize the feed.
    $feed->init();

    //  print_r(count($feed->get_items()));die;
    // Make sure the page is being served with the UTF-8 headers.
    $feed->handle_content_type();
    return $feed;
  }
}
?>
