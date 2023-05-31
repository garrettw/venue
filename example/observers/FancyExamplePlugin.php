<?php

use Venue\Event;

/**
 * An example Venue Observer
 *
 * This is an example Observer/plugin, which will override
 * previously called listeners. This example Observer enhances
 * a post's message
 *
 * @author      Garrett Whitehorn
 * @package     Venue
 * @subpackage  VenueExample
 * @version     1.0
 */
class FancyExamplePlugin extends Venue\Observer
{
    public function onFormatMessage(Event $event) {
        $message = strip_tags($event->data);
        $message = preg_replace('/\[b\](.+?)\[\/b\]/is', '<span style="font-weight:bold">$1</span>', $message);
        $message = preg_replace('/\[u\](.+?)\[\/u\]/is', '<span style="text-decoration:underline">$1</span>', $message);
        $message = preg_replace('/\[url=([^\[\]]+)\](.+?)\[\/url\]/is', '<a href="$1">$2</a>', $message);
        $message = preg_replace('/\[url\](.+?)\[\/url\]/is', '<a href="$1">$1</a>', $message);
        return nl2br($message);
    }
}
