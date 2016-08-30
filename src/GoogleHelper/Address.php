<?php

namespace GoogleHelper;
/**
* Class to handle Google API address formats
*
* @category  Class
* @version   0.0.1
* @since     2016-08-04
* @author    Wesley Dekkers <wesley@wd-media.nl>
*/
class Address
{
  /**
  * Load address info based on basic address data
  *
  * @example
  * <code>
  * $result = \GoogleHelper\Address::geo_code($API_KEY, $params);
  * </code>
  *
  * @param String - Google API Key
  * @param Array  - of Parameters such as Address, city, zip
  *
  * @uses For error codes: https://developers.google.com/maps/documentation/geocoding/intro#StatusCodes
  * @uses For API Key: https://developers.google.com/maps/documentation/geocoding/get-api-key#get-an-api-key
  *
  * @example
  * Return body
  * <code>
  * {
  *   "results" : [
  *      {
  *         "address_components" : [
  *            {
  *               "long_name" : "1600",
  *               "short_name" : "1600",
  *               "types" : [ "street_number" ]
  *            },
  *            {
  *               "long_name" : "Amphitheatre Pkwy",
  *               "short_name" : "Amphitheatre Pkwy",
  *               "types" : [ "route" ]
  *            },
  *            {
  *               "long_name" : "Mountain View",
  *               "short_name" : "Mountain View",
  *               "types" : [ "locality", "political" ]
  *            },
  *            {
  *               "long_name" : "Santa Clara County",
  *               "short_name" : "Santa Clara County",
  *               "types" : [ "administrative_area_level_2", "political" ]
  *            },
  *            {
  *               "long_name" : "California",
  *               "short_name" : "CA",
  *               "types" : [ "administrative_area_level_1", "political" ]
  *            },
  *            {
  *               "long_name" : "United States",
  *               "short_name" : "US",
  *               "types" : [ "country", "political" ]
  *            },
  *            {
  *               "long_name" : "94043",
  *               "short_name" : "94043",
  *               "types" : [ "postal_code" ]
  *            }
  *         ],
  *         "formatted_address" : "1600 Amphitheatre Parkway, Mountain View, CA 94043, USA",
  *         "geometry" : {
  *            "location" : {
  *               "lat" : 37.4224764,
  *               "lng" : -122.0842499
  *            },
  *            "location_type" : "ROOFTOP",
  *            "viewport" : {
  *               "northeast" : {
  *                  "lat" : 37.42382538,
  *                  "lng" : -122.08290
  *               },
  *               "southwest" : {
  *                  "lat" : 37.4211274197085,
  *                  "lng" : -122.0855988802915
  *               }
  *            }
  *         },
  *         "place_id" : "ChIJ2eUgeAK6j4ARbn5u_wAGqWA",
  *         "types" : [ "street_address" ]
  *      }
  *   ],
  *   "status" : "OK"
  * }
  * </code>
  *
  * @return **Object** with specific address data
  *
  * @since   2016-08-04
  * @author  Wesley Dekkers <wesley@wd-media.nl> 
  **/
  public static function geo_code($key=NULL, $params=NULL, $exception = TRUE){
    // Make a valid query string so Google will accept this
    $query_string = self::prepare_query_string($params);

    // Check if key is set
    if(!$key){
      throw new Exception("No Google API Key is set");
    }

    // Load the basic url
    $request_url = 'https://maps.googleapis.com/maps/api/geocode/json';

    // Load the paramaters + key
    $request_options = '?address='.$query_string.'&key='.$key;

    $result = json_decode(file_get_contents($request_url."".$request_options));
    // When exception throw!
    if($result->status != "OK" && $exception){
      throw new Exception("Google API Error: ".$result->status.", ".$result->error_message);
    }

    return $result;
  }


  /**
  * Prepare the query string so google will accept it
  *
  * @param Array of Parameters such as Address, city, zip
  *
  * @example
  * <code>
  * $query_string = \GoogleHelper\Address::prepare_query_string($params);
  * </code>
  *
  * @return query string
  *
  * @since   2016-08-04
  * @author  Wesley Dekkers <wesley@wd-media.nl> 
  **/
  public static function prepare_query_string($params){
    // Check if parameters are set
    if(!$params){throw new Exception("Error no valid parameters set");}

    $query_string = '';
    foreach ($params as $param) {
      $query_string .= ($query_string)? ",+".$param : $param;
    }
    return str_replace(" ","+",$query_string);
  }

  
  /**
  * Save a map image for an address with marker
  *
  * @param String - $api_key Google API key for 
  * @param String - $address - address you want to load
  * @param String - $width - width of the image
  * @param String - $height - height of the image
  * @param String - $destination - Where to save the image
  * @param String - $color - red/... 
  * @param String - $zoom - How far zoomed
  * @param String - $format - jpg/png/gif
  * @param String - $map_type road map / satelite
  *
  * @example
  * <code>
  * $query_string = \GoogleHelper\Address::save_image_address($api_key, $address, $width, $height, $destination, $color, $zoom, $format, $map_type);
  * </code>
  *
  * @return String / Bool
  *
  * @since   2016-08-05
  * @author  Wesley Dekkers <wesley@wd-media.nl> 
  **/
  public function save_image_address($api_key, $address, $width, $height, $destination, $color='red', $zoom=13, $format="jpg", $map_type="roadmap"){
    if(file_exists($destination)){
      unlink($destination);
    }

    $src = "https://maps.googleapis.com/maps/api/staticmap?zoom=".$zoom."&size=".$width."x".$height."&maptype=".$roadmap."&format=".$format."&markers=color:".$color."%7C".$address."&key=".$api_key;

    if(!copy($src, $destination)){
      $destination = false;
    }

    return $destination;
  }
}
?>