<?php
/**
 * Pico Leaflet Blog Map
 *
 * Create a specific page with a map -- using the Leaflet library --
 * with all the blog posts with _coordinates_ or _address_ metadatas
 * Create a specific Twig variable to include a map in your template
 *
 * @author Brice Boucard
 * @link https://github.com/bricebou/pico_leaflet
 * @license http://bricebou.mit-license.org/
 */

class Pico_Leaflet {
	public $is_map;

	public function config_loaded(&$settings)
	{
		$this->lmap_styles = $settings['base_url'] .'/'. basename(PLUGINS_DIR) .'/pico_leaflet/pico_leaflet.css';
		if (isset($settings['leaflet']['mapurl']))
		{
			$this->leaflet_mapurl = $settings['leaflet']['mapurl'];
		}
		if (isset($settings['leaflet']['geocoding']))
		{
			$this->leaflet_geocoding = $settings['leaflet']['geocoding'];
		}
		if (isset($settings['leaflet']['providers']))
		{
			$this->leaflet_providers = $settings['leaflet']['providers'];
			if($this->leaflet_providers === true)
			{
				$this->providers_script = $settings['base_url'] .'/'. basename(PLUGINS_DIR) .'/pico_leaflet/leaflet-providers/leaflet-providers.js';
			}
		}
		if (isset($settings['leaflet']['providers_enabled']))
		{
			$this->providers_enabled = $settings['leaflet']['providers_enabled'];
		}
		if (isset($settings['leaflet']['mapbox']))
		{
			$this->providers_mapbox = $settings['leaflet']['mapbox'];
		}
		if (isset($settings['leaflet']['providers_default']))
		{   
			$this->providers_default = $settings['leaflet']['providers_default'];
		}
	}

	public function request_url(&$url)
	{
		// if a url for the map is set in config.php, set is_map to true
		// if the first n characters of the URL match mapurl value
		if (isset($this->leaflet_mapurl) && $this->leaflet_mapurl != '')
		{
			$mapurl = trim($this->leaflet_mapurl);
			$this->is_map = (substr($url, 0, strlen($mapurl)) == $mapurl);
		}
		// if mapurl isn't set, use the default "map" url
		// and set is_map to true if the first three characters are "map"
		else
		{
			$this->is_map = (substr($url, 0, 3) === 'map');
		}
	}

	public function before_read_file_meta(&$headers)
	{
		// Define leaflet variables
		$headers['coordinates'] = 'Coordinates';
		if ($this->leaflet_geocoding === true) {
			$headers['address'] = 'Address';
		}
	}

	public function osm_geocode(&$addresses,&$titleart,&$urlart)
	{
		$nominatim_baseurl = 'http://nominatim.openstreetmap.org/search?format=json&q=';
		foreach ($addresses as $key => $value) {
			$nominatim_query = urlencode($value);
			$data = file_get_contents( "{$nominatim_baseurl}{$nominatim_query}&limit=1" );
			$json = json_decode( $data );
			if (!empty($json)) {
				$this->marker_coordinates[] = $json[0]->lat.','.$json[0]->lon;
				$this->marker_title[] = $titleart;
				$this->marker_url[] = $urlart;
			}
		}
	}

	public function file_meta(&$meta)
	{   
		// Parses meta.coordinates to ensure it is an array
		if (isset($meta['coordinates']) && !is_array($meta['coordinates']) && $meta['coordinates'] !== '')
		{	
			$meta['coordinates'] = explode('|', $meta['coordinates']);
			foreach ($meta['coordinates'] as $key => $value)
			{
				$this->marker_coordinates[] = $value;
				$this->marker_title[] = $meta['title'];
				$this->marker_url[] = $page['url'];
			}
			// Create a variable to initiate actions
			$this->lmap = 'article';
		}
		if (isset($meta['address']) && !is_array($meta['address']) && $meta['address'] != '')
		{
			$meta['address'] = explode('|', $meta['address']);
			$this->osm_geocode($meta['address'],$meta['title'],$page['url']);
			$this->lmap = 'article';
		}
	}

	public function get_pages(&$pages, &$current_page, &$prev_page, &$next_page)
	{
		if ($this->is_map === true)
		{
			foreach ($pages as $page)
			{
				if ($page['coordinates'])
				{   
					$page['coordinates'] = explode('|', $page['coordinates']);
					foreach ($page['coordinates'] as $coordinates)
					{
						$this->marker_coordinates[] = $coordinates;
						$this->marker_title[] = $page['title'];
						$this->marker_url[] = $page['url'];
					}
				}
				if ($page['address'])
				{   
					$page['address'] = explode('|', $page['address']);

					$this->osm_geocode($page['address'],$page['title'],$page['url']);
				}
			}
		}
	}

	public function build_pico_leaflet_head()
	{
		$plhead;
		$plhead = '<link rel="stylesheet" href="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.css" />
			<link rel="stylesheet" href="'.$this->lmap_styles.'" />
			<script src="http://cdn.leafletjs.com/leaflet-0.7.3/leaflet.js"></script>';
		if ($this->leaflet_providers === true) {
			$plhead .= PHP_EOL.'<script src="'.$this->providers_script.'"></script>';
		}
		  return $plhead;
	}

	public function create_markers()
	{
		for ($i=0; $i < count($this->marker_coordinates); $i++)
		{ 
			$markers .= 'var marker = L.marker(['.$this->marker_coordinates[$i].']).addTo(map).bindPopup("<a href=\''.$this->marker_url[$i].'\'>'.$this->marker_title[$i].'</a>");
					coordls.push(L.latLng('.$this->marker_coordinates[$i].'));';
		}
		return $markers;
	}

	public function build_pico_leaflet_map()
	{
		$idmap;
		$providers;
		$basemap;
		$default;
		if ($this->is_map === true)
		{
			$idmap = 'map_global';	
		}
		elseif (!$this->is_map === true && isset($this->lmap) && $this->lmap === 'article')
		{
			$idmap = 'map_article';
		}
		if (isset($this->providers_enabled) && is_array($this->providers_enabled))
		{
			foreach ($this->providers_enabled as $key => $value)
			{
				$providers .= $key.' = L.tileLayer.provider(\''.$value.'\'),'.PHP_EOL;
				$basemap .= '"'.$value.'":'.$key.','.PHP_EOL;
			}
		}
		if (isset($this->providers_mapbox) && is_array($this->providers_mapbox)) {
			foreach ($this->providers_mapbox as $key => $value) {
				$mapid = substr($key, strpos($key, ".")+1);
				$providers .= $mapid.' = L.tileLayer.provider(\'MapBox.'.$key.'\'),'.PHP_EOL;
				$basemap .= '"'.$value.'":'.$mapid.','.PHP_EOL;
			}
		}
		else
		{
			$providers = 'osm = L.tileLayer("http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png", {attribution: "&copy; <a href=\'http://openstreetmap.org\'>OpenStreetMap</a> contributors, <a href=\'http://creativecommons.org/licenses/by-sa/2.0/\'>CC-BY-SA</a>",
				maxZoom: 18});';
			$basemap = '"OpenStreetMap Default":osm';
			$default = 'osm';
		}
		$providers = rtrim($providers, ",".PHP_EOL);
		$basemap = rtrim($basemap, ",".PHP_EOL);

		if (isset($this->providers_default) && $default != 'osm')
		{
			$default = $this->providers_default;
		}

		if (isset($this->lmap) && $this->lmap === 'article') {

		}

		$plbuildmap = '<script>
					var '.$providers.';
					var map = L.map("'.$idmap.'", {
					center: new L.LatLng(45.84028105450088,1.61224365234375),
					zoom: 8,
					layers: ['.$default.']
				});

				var baseMaps = {'.$basemap.'
				};
				L.control.layers(baseMaps).addTo(map);
				var coordls = new Array();
				'.$this->create_markers().'
				var bounds = new L.LatLngBounds(coordls);
				map.fitBounds(bounds);
				</script>';
		return $plbuildmap;
	}


	public function before_render(&$twig_vars, &$twig)
	{
		if ($this->is_map === true) {
			 // Override 404 header
			header($_SERVER['SERVER_PROTOCOL'].' 200 OK');
			// Set page title to Map
			$twig_vars['meta']['title'] = "Map";
			$twig_vars['map_global'] = '<div id="map_global"></div>';
		}
		elseif (!$this->is_map === true && isset($this->lmap) && $this->lmap === 'article')
		{
			$twig_vars['map_article'] = '<div id="map_article"></div>';
		}
	}

	public function after_render(&$output)
	{
		if ($this->is_map === true) {
			// Adding stylesheets and scripts for Leaflet inside <head>
			$output = str_replace('</head>', PHP_EOL.$this->build_pico_leaflet_head().'</head>', $output);
			// Adding the map script before the end of the <body>
			$output = str_replace('</body>', PHP_EOL.$this->build_pico_leaflet_map().'</body>', $output);
		}
		elseif (!$this->is_map === true && isset($this->lmap) && $this->lmap === 'article')
		{
			// Adding stylesheets and scripts for Leaflet inside <head>
			$output = str_replace('</head>', PHP_EOL.$this->build_pico_leaflet_head().'</head>', $output);
			// Adding the map script before the end of the <body>
			$output = str_replace('</body>', PHP_EOL.$this->build_pico_leaflet_map().'</body>', $output);
		}
	}
	
}

?>