<?php

require_once("../lib/easypost.php");

$json = "{\"result\":{\"id\":\"trk_71bc712e373a475a9595b3afc92f7348\",\"object\":\"Tracker\",\"mode\":\"production\"," .
        "\"tracking_code\":\"LF100622139US\",\"status\":\"in_transit\",\"created_at\":\"2016-11-15T02:50:20Z\"," .
        "\"updated_at\":\"2016-11-17T14:39:18Z\",\"signed_by\":null,\"weight\":null,\"est_delivery_date\":null," .
        "\"shipment_id\":null,\"carrier\":\"USPS\",\"tracking_details\":[{\"object\":\"TrackingDetail\"," .
        "\"message\":\"October 26 4:25 pm Shipment Accepted in SAN DIEGO, CA\",\"status\":\"in_transit\"," .
        "\"datetime\":\"2016-10-26T16:25:00Z\",\"source\":\"USPS\",\"tracking_location\":{\"object\":\"TrackingLocation\"," .
        "\"city\":\"SAN DIEGO\",\"state\":\"CA\",\"country\":null,\"zip\":\"92177\"}},{\"object\":\"TrackingDetail\"," .
        "\"message\":\"October 26 10:07 pm Accepted at USPS Origin Facility in SAN DIEGO, CA\",\"status\":\"in_transit\"," .
        "\"datetime\":\"2016-10-26T22:07:00Z\",\"source\":\"USPS\",\"tracking_location\":{\"object\":\"TrackingLocation\"," .
        "\"city\":\"SAN DIEGO\",\"state\":\"CA\",\"country\":null,\"zip\":\"92117\"}},{\"object\":\"TrackingDetail\"," .
        "\"message\":\"October 26 11:22 pm Arrived at USPS Facility in SAN DIEGO, CA\",\"status\":\"in_transit\"," .
        "\"datetime\":\"2016-10-26T23:22:00Z\",\"source\":\"USPS\",\"tracking_location\":{\"object\":\"TrackingLocation\"," .
        "\"city\":\"SAN DIEGO\",\"state\":\"CA\",\"country\":null,\"zip\":\"92199\"}},{\"object\":\"TrackingDetail\"," .
        "\"message\":\"October 27 12:47 am Departed USPS Facility in SAN DIEGO, CA\",\"status\":\"in_transit\"," .
        "\"datetime\":\"2016-10-27T00:47:00Z\",\"source\":\"USPS\",\"tracking_location\":{\"object\":\"TrackingLocation\"," .
        "\"city\":\"SAN DIEGO\",\"state\":\"CA\",\"country\":null,\"zip\":\"92199\"}},{\"object\":\"TrackingDetail\"," .
        "\"message\":\"October 27 4:01 am Arrived at USPS Facility in LOS ANGELES, CA\",\"status\":\"in_transit\"," .
        "\"datetime\":\"2016-10-27T04:01:00Z\",\"source\":\"USPS\",\"tracking_location\":{\"object\":\"TrackingLocation\"," .
        "\"city\":\"LOS ANGELES\",\"state\":\"CA\",\"country\":null,\"zip\":\"90009\"}},{\"object\":\"TrackingDetail\"," .
        "\"message\":\"October 28 10:19 am Departed USPS Facility in LOS ANGELES, CA\",\"status\":\"in_transit\"," .
        "\"datetime\":\"2016-10-28T10:19:00Z\",\"source\":\"USPS\",\"tracking_location\":{\"object\":\"TrackingLocation\"," .
        "\"city\":\"LOS ANGELES\",\"state\":\"CA\",\"country\":null,\"zip\":\"90009\"}}],\"carrier_detail\":" .
        "{\"object\":\"CarrierDetail\",\"service\":\"First-Class Package International Service\",\"container_type\":null," .
        "\"est_delivery_date_local\":null,\"est_delivery_time_local\":null,\"origin_location\":\"SAN DIEGO CA, 92117\"," .
        "\"destination_location\":null},\"public_url\":\"https://track.easypost.com/djE6dHJrXzc...\",\"fees\":" .
        "[{\"object\":\"Fee\",\"type\":\"TrackerFee\",\"amount\":\"0.00000\",\"charged\":true,\"refunded\":false}]}," .
        "\"description\":\"tracker.updated\",\"mode\":\"production\",\"previous_attributes\":{\"status\":\"unknown\"}," .
        "\"created_at\":\"2016-11-15T02:51:20Z\",\"pending_urls\":[],\"completed_urls\":[],\"updated_at\":\"2016-11-15T02:51:21Z\"," .
        "\"id\":\"evt_7b063978a5654e21983c68314983378c\",\"user_id\":\"user_10f3d1a86ca74e9a9d454d7cb680f657\"," .
        "\"status\":\"completed\",\"object\":\"Event\"}";

$event = \EasyPost\Event::receive($json);

if ($event->description == "tracker.updated") {
  $tracker = $event->result;

  echo $tracker->tracking_code;
}

