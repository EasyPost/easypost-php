<?php

namespace EasyPost;

class Event extends EasypostResource
{
    /**
     * receive an event
     *
     * @param string $rawInput
     * @return mixed
     * @throws \EasyPost\Error
     */
    public static function receive($rawInput=null){
        if($rawInput == null)
            throw new Error('The raw input must be set');
        $values =  json_decode( $rawInput, TRUE );
        if ($values != null) {
            return self::constructFrom($values, get_class(), null);
        } else {
            throw new Error('There was a problem decoding the webhook');
        }
    }
}
?>
