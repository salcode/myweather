# WordPress My Weather REST API Plugin

This plugin uses the [OpenWeather One Call API 3.0](https://openweathermap.org/api/one-call-3) to retrieve the weather data for a specific latitute/longitude and expose that information at the WordPress REST API endpoint `/wp-json/myweather/v1/weather`.

The weather data is cached using a WordPress transient.

## Setup

In addition to installing and activating this plugin, you'll need to set the following PHP constants (this can be done in `wp-config.php` at the root of the WordPress site).

- `SF_MY_WEATHER_API_KEY` you personal API key from openweathermap.org
- `SF_MY_WEATHER_LAT` the latitude of the forecast location
- `SF_MY_WEATHER_LON` the longitude of the forecast location

e.g.

```
define('SF_MY_WEATHER_API_KEY', 'abc123abc123abc123');
define('SF_MY_WEATHER_LAT', '38.991581');
define('SF_MY_WEATHER_LON', '-74.814407');
```
