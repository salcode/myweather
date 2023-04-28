<?php
/**
 * My Weather
 *
 * Plugin Name:       My Weather REST API endpoint
 * Plugin URI:        https://github.com/salcode/myweather
 * Description:       Displays weather data at REST API endpoint.
 * Version:           0.1.0
 * Requires at least: 6.1
 * Requires PHP:      7.2
 * Author:            Sal Ferrarello
 * Author URI:        https://salferrarello.com
 * Text Domain:       myweather
 * License:           MIT
 * License URI:       https://mit-license.org/
 * Update URI:        false
 *
 * This plugin uses the api.openweathermap.org service to
 * retrieve weather data as defined by the constants:
 * - SF_MY_WEATHER_API_KEY
 * - SF_MY_WEATHER_LAT
 * - SF_MY_WEATHER_LON
 * and displays the result at the REST API route
 * /wp-json/myweather/v1/weather
 */

namespace salcode\MyWeather;

use RuntimeException;
use Throwable;
use WP_REST_Response;
use WP_REST_Server;

const TRANSIENT_KEY = 'sf_my_weather';

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

add_action( 'rest_api_init', __NAMESPACE__ . '\register_api_endpoints' );
function register_api_endpoints() {
	register_rest_route(
		'myweather/v1',
		'/weather',
		[
			'methods'             => WP_REST_Server::READABLE,
			'callback'            => __NAMESPACE__ . '\get_weather',
			'permission_callback' => '__return_true',
		]
	);
}

function get_weather() {
	try {
		$weather_data = get_transient( TRANSIENT_KEY );
		if ( false === $weather_data ) {
			$weather_data = fetch_weather();
			set_transient(
				TRANSIENT_KEY,
				$weather_data,
				90 // cache for 90 seconds.
			);
		}
		return new WP_REST_Response(
			$weather_data,
			200
		);
	} catch (Throwable $t) {
		delete_transient( TRANSIENT_KEY );
		return new WP_REST_Response(
			[
				'message' => $t->getMessage(),
			],
			500
		);
	}
}

function fetch_weather() {
	if ( ! defined( 'SF_MY_WEATHER_API_KEY' ) ) {
		throw new RuntimeException('Constant SF_MY_WEATHER_API_KEY is not defined');
	}
	if ( ! defined( 'SF_MY_WEATHER_LAT' ) ) {
		throw new RuntimeException('Constant SF_MY_WEATHER_LAT is not defined');
	}
	if ( ! defined( 'SF_MY_WEATHER_LON' ) ) {
		throw new RuntimeException('Constant SF_MY_WEATHER_LON is not defined');
	}
	$lat = SF_MY_WEATHER_LAT;
	$lon = SF_MY_WEATHER_LON;
	$api_key = SF_MY_WEATHER_API_KEY;
	$units = 'imperial';
	$url = sprintf(
		'https://api.openweathermap.org/data/3.0/onecall?lat=%1$s&lon=%2$s&appid=%3$s&units=%4$s',
		$lat,
		$lon,
		$api_key,
		$units
	);
	$response = wp_remote_get( $url );
	if ( is_wp_error( $response ) ) {
		throw new RuntimeException( 'Failed to load data' );
	}
	$body     = wp_remote_retrieve_body( $response );
	return json_decode( $body );
}
