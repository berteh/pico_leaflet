Pico Leaflet
============

Adds Leaflet maps to blog posts with Address or Coordinates meta and creates a map with all these blog posts.

A plugin based on [Leaflet](http://leafletjs.com/), "an Open-Source JavaScript Library for Mobile-Friendly Interactive Maps" and that uses the Leaflet plugin [leaflet-providers](https://github.com/leaflet-extras/leaflet-providers).

Thanks to [Derick Rethans](http://talks.php.net/show/osm-lonestar12/11), [Dan Reeves](https://github.com/DanReeves) (author of the picotags plugin which gave me the base to this plugin)...

It gives you access to:
* The current page `meta.coordinates` and `meta.address` arrays if set;
* A `/map` URL with a map with each blog post with coordinates and/or address meta.

##Installation

Place the `pico_leaflet` folder into the `plugins` directory.

### Using HTTPS protocol

If you are using the HTTPS protocol for your website, you can't load the Leaflet library (both javascript and css) from the non secure CDN used by Leaflet.    
In order to get this plugin to work, you'll have to add this to your `config.php`:
```
$config['leaflet']['local'] = true;
```

Now, the javasript and the styles of Leaflet are loaded from your server.

## Basic usage

### `Coordinates` attribute into the page meta

Add a 'Coordinates' attribute into the page meta:

- always start with the latitude;
- latitudes and longitudes must follow this pattern : `xx,xxxx` using the comma to separate integer from decimal;
- you can add multiple coordinates pairs, using the __pipe : `|`__ as separator, just like in the example below :

```
/*
Title: Pico Leaflet : first example
Description: Ajouter une carte à des pages articles dans Pico CMS
Author: Brice Boucard
Date: 2014/08/15
License: Creative Commons Attribution-ShareAlike 4.0 International License
Template: index
Coordinates: 1.45,0.45|1.56,0.47
*/
```

### `Address` attribute into the page meta

Instead of using coordinates, you can use addresses to geolocate your blog posts.

First, you have to enable the geocoding function inside your `config.php` :
```
$config['leaflet']['geocoding'] = true;
```

The geocoding functionnality is based on the service provided by the OpenStreetMaps community : [www.openstreetmap.org](http://www.openstreetmap.org). 

Then, in your .md content files you can use the meta "Address":

- I advise to check the address on the OpenStreetMap website : if it isn't found or the first result, it won't appear or appear with wrong coordinates on your website.
- You can specify multiple addresses using the __pipe : `|`__ as separator, just like in the example below :
```
/*
Title: Pico Leaflet : second example
Description: Ajouter une carte à des pages articles dans Pico CMS
Author: Brice Boucard
Date: 2014/08/15
License: Creative Commons Attribution-ShareAlike 4.0 International License
Template: index
Address: 19, boulevard de la Corderie 87000 Limoges|boulevard des Américains Nantes
*/
```

### Display the map

To display the map, you just have to edit your active theme `index.html` and add this to your blog posts section :
```
{{ map_article }}
```

Don't worry : leaflet scripts and stylesheets are loaded only if the coordinates meta (or the address meta -- see below) is set.

### Access the `meta.coordinates` and `meta.address`

You can now access the current page `meta.coordinates` ; in your theme`index.hmtl` :
```
{% if meta.coordinates %}
    <p>Coordinates :</p>
    <ul>
    {% for coordinate in meta.coordinates %}
        <li>{{ coordinate }}</li>
    {% endfor %}
    </ul>
{% endif %}
```

See it live : [momh.fr/tutos/Pico/pico_leaflet_coord](http://momh.fr/tutos/Pico/pico_leaflet_coord)

To access the current page `meta.address`, it's just like for the `meta.coordinates`: in your theme`index.hmtl` :
```
{% if meta.address %}
    <p>Adresses :</p>
    <ul>
    {% for address in meta.address %}
        <li>{{ address }}</li>
    {% endfor %}
    </ul>
{% endif %}
```

See it live : [momh.fr/tutos/Pico/pico_leaflet_add](http://momh.fr/tutos/Pico/pico_leaflet_add)

### Adding a thumbnail inside Leaflet popups

You just have to add a `Thumbnail` attribute to your page meta, using the `relative path` to your image as the value (meaning using `IMG/logos/conkylogo.png` for the image located here: [http://momh.fr/IMG/logos/conkylogo.png](http://momh.fr/IMG/logos/conkylogo.png)).

```
Title: Pico Leaflet : fourth example
Description: Ajouter une carte à des pages articles dans Pico CMS
Author: Brice Boucard
Date: 2014/08/28
License: Creative Commons Attribution-ShareAlike 4.0 International License
Template: index
Coordinates: 22.610771801269206, 12.4086906909942627|32.33859359694389, 22.473631381988525
Thumbnail: IMG/logos/awn.png
```

See it live : [momh.fr/tutos/Pico/pico_leaflet_thumb](http://momh.fr/tutos/Pico/pico_leaflet_thumb)

## Mixing coordinates and addresses

Of course you can mix coordinates and addresses just like in this example : [momh.fr/tutos/Pico/pico_leaflet_mix](http://momh.fr/tutos/Pico/pico_leaflet_mix) which is made from coordinates and addresses specified in its meta :
```
/*
Title: Pico Leaflet : third example
Description: Personnaliser l'accueil de son terminal avec les programmes cowsay et fortune.
Author: Brice Boucard
Date: 2014/08/15
License: Creative Commons Attribution-ShareAlike 4.0 International License
Template: index
Coordinates: 45.196135,4.836044|45.420207,4.288101
Address: rue du Puy Las Rodas 87000 Limoges|boulevard des frères de Goncourt Nantes
*/
```

## Display a global map that presents every blog posts with coordinates or address meta

First, you can choose the URL where this map will be accessible. In your `config.php`:
```
$config['leaflet']['mapurl'] = 'globalmap';
```

If this line isn't present or the value is empty, the pico_leaflet plugin uses the `yourdomain.tld/map` URL.

Then, to display a map that shows each points announced through coordinates or address meta, you just have to modify your theme `index.html`:
```
{% elseif meta.title == "Map" %}
    <article>
        <h1>Map</h1>

        {{ map_global }}

    </article>
{% else %}
```

See it live : [momh.fr/globalmap](http://momh.fr/globalmap).

__WARNING__ : if you are using Dan Reeves' `picotags` plugin, you have to put this piece of code after the code for the tags pages.

For example, here is a piece of my `index.html` (I've slightly modify the elseif statement to exclude the page with "Map" as a title) :
```
{% elseif pages and meta.title != 'Error 404' and meta.title != 'Map' %}
<!-- tags page -->
    <p>Posts tagged <a href="{{ page.url }}">#{{ current_tag }}</a>:</p>
    {% for page in pages %}
            <article>
                <h2><a href="{{ page.url }}">{{ page.title }}</a></h2>
                <p class="meta">
                    <span class="tags"><br />Tags :
                        {% for tag in page.tags %}
                            <a href="{{ base_url }}/tag/{{ tag }}">#{{ tag }}</a>
                        {% endfor %}
                    </span>
                </p>
                {{ page.excerpt }}
            </article>
    {% endfor %}
{% elseif meta.title == "Map" %}
    <article>
        <h1>Map</h1>

        {{ map_global }}

    </article>
```
or you can use this alternate template structure
```
{% if is_front_page %}
<!-- front page -->
    {{ content }}
<!-- front page -->

{% elseif current_page is not empty %}
    {% if meta.tags is not empty %}
    <article>
        <h1>{{ meta.title }}</h1>
        {{ content }}

        {% if meta.coordinates %}
            <p>Coordonnées :</p>
            <ul>
            {% for coordinate in meta.coordinates %}
                <li>{{ coordinate }}</li>
            {% endfor %}
            </ul>
        {% endif %}
        {% if meta.address %}
            <p>Adresses :</p>
            <ul>
            {% for address in meta.address %}
                <li>{{ address }}</li>
            {% endfor %}
            </ul>
        {% endif %}

        {{ map_article }}
        <p class="meta">
            Tags : 
            {% for tag in meta.tags %}
                <a href="{{ base_url }}/tag/{{ tag }}">#{{ tag }}</a>
            {% endfor %}
        </p>                   
    </article>
    {% else %}
    <article>
        <h1>{{ meta.title }}</h1>
        {{ content }}
    </article>
    {% endif %}
{% elseif current_page is empty %}             
    {% if meta.title != 'Error 404' and meta.title != 'Map' %}
    <!-- tags page -->
    <p>Posts tagged <a href="{{ page.url }}">#{{ current_tag }}</a>:</p>
    {% for page in pages %}
        
            <article>
                <h2><a href="{{ page.url }}">{{ page.title }}</a></h2>
                <p class="meta">
                    <span class="tags"><br />Tags :
                        {% for tag in page.tags %}
                            <a href="{{ base_url }}/tag/{{ tag }}">#{{ tag }}</a>
                        {% endfor %}
                    </span>
                </p>
                {{ page.excerpt }}
            </article>
    {% endfor %}
    <p>
        All tags :
    </p>
    <ul>
        {% for tag in tag_list %}
            <li><a href="/tag/{{ tag }}">#{{ tag }}</a></li>
        {% endfor %}
    </ul>
            
    <!-- tags page -->
    {% elseif meta.title == 'Map' %}
        <article>
            <h1>{{meta.title}}</h1>

            {{ map_global }}

        </article>

    {% endif %}
{% endif %}
```

## Global map settings

By default, the "global map" page uses the index.html from your theme (as we've seen above) and has "Map" as title.  
You can change both of these behaviours using two settings in your `config.php`:
```
$config['leaflet']['maptemplate'] = 'map';
$config['leaflet']['maptitle'] = 'My blog Leaflet map';
```

__WARNING__: if you modify the map title, you have to edit the conditional structure provided above and change the value of `meta.title != 'Map'` and `meta.title == 'Map'`.

If you use a particular template, it can be very light :
```
<!DOCTYPE html>
<html lang="en" class="no-js">
<head>
    <meta charset="utf-8" />
    
    <title>{% if meta.title %}{{ meta.title|escape }} | {% endif %}{{ site_title }}</title>

    <!-- HEAD STUFF FROM YOUR THEME -->
</head>
<body>

    <header id="header">

    </header>

    <section id="content">       

            <article>
                <h1>{{meta.title}}</h1>
                {{ map_global }}
            </article>

    </section>
    <footer id="footer">

    </footer>  
</body>
</html>
```

## Providers

By default the map provider is [www.openstreetmap.org](http://www.openstreetmap.org), but you can add several other providers thanks to the Leaflet plugin [leaflet-providers](https://github.com/leaflet-extras/leaflet-providers).

__WARNING__ : some providers (the ones that require registration except Mapbox) are not supported yet in this pico_leaflet plugin.

In order to add other providers, you have to enable it in your `config.php` :
```
$config['leaflet']['providers'] = true;
```
Then, you have to specify the providers you want to load. First, you have to open the `plugins/pico_leaflet/leaflet-providers/leaflet-providers.js` file and check the `Definition of providers` part (begins at line 65). You can preview the full list of layers here : [http://leaflet-extras.github.io/leaflet-providers/preview/index.html](http://leaflet-extras.github.io/leaflet-providers/preview/index.html).

In your `config.php`, you have to specify the providers inside an array, using a name of your choice (without space) as the key and the `provider[.<variant>]`string used in the leaflet-providers script or the "Provider names" that appears in [http://leaflet-extras.github.io/leaflet-providers/preview/index.html](http://leaflet-extras.github.io/leaflet-providers/preview/index.html).

For example :

```
$config['leaflet']['providers_enabled'] = array(
    'thland' => 'Thunderforest.Landscape',  // Landscape variant of the Thunderforest provider
    'stato' => 'Stamen.Toner',              // Toner variant of Stamen provider
    'stawa' => 'Stamen.Watercolor',         // Watercolor variant of Stamen provider
    'hydda' => 'Hydda'                      // Hydda provider
);
```

### Specifying the default provider

You can specify the default provider adding this to your `config.php`, using one of the key/name of the `providers_enabled` array :
```
$config['leaflet']['providers_default'] = 'stato';
```

### Using Mapbox

To add Mapbox maps, you have first to get their id from your "Projects" page or from the Project tab => Info => Map ID when you are in the map editing page.
Your Map ID must be composed like this : `username.mapmbid`.

In your Pico `config.php`, add this, using  :
```
$config['leaflet']['mapbox'] = array(
    'username.mapmbid' => 'Mapbox',
    'username.mapmbid2' => 'Mapbox 2'
);
```

If you want to use a Mapbox map as the default provider you have to fill the `providers_default` setting with __ONLY__ the `mapmbid` from your MAP ID.
Let's say you have loaded a map which Map ID is "truc.h32k09az" ; for the default provider setting, you will use:
```
$config['leaflet']['providers_default'] = 'h32k09az';
```

## Styling the map

By default, the maps generated with pico_leaflet are style using the `/home/bbrice/WIN_DATA/webdev/pico/plugins/pico_leaflet/pico_leaflet.css` file. It's very basic :
```
#map_article {
    width: 100%;
    height: 200px;
}

#map_global {
    width: 100%;
    height: 400px;
}
```
Of course, you can modify the appearance of the maps but I advise you to do that in your theme css, even if you have to use the !important argument.

## About the devel_geophp branch : using the geocoder-php library

Instead of using the basic geocode function that use the PHP `file_get_contents()` function, you can use "The almost missing Geocoder PHP library!" : [geocoder-php](http://geocoder-php.org/Geocoder/).

First, you have to install and load this library.

Edit the composer.json and add to the `require` section this line&nbsp;:
```
"willdurand/geocoder": "3.0.*@dev"
```

If curl isn't available on your server, you can add this line&nbsp;:
```
"curl/curl": "dev-master"
```

Then you have to update the packages&nbsp;:
```
$ composer update
```

You shoud now have the `vendor/willdurand/geocoder` folder.

Finally you have to replace the `osm_geocode` function, line 106-128&nbsp;:
```
public function osm_geocode(&$addresses,&$titleart,&$urlart,$thumb)
{
    // Using an alternative service based on OSM rather than
    // http://nominatim.openstreetmap.org/search?format=json&q=
    // because of usage policy causing issues on shared web hosting
    $nominatim_baseurl = 'http://open.mapquestapi.com/nominatim/v1/search.php?format=json&q=';
    foreach ($addresses as $key => $value) {
        $nominatim_query = urlencode($value);
        $data = file_get_contents( "{$nominatim_baseurl}{$nominatim_query}&limit=1" );
        $json = json_decode( $data );
        if (!empty($json)) {
            $this->marker_coordinates[] = $json[0]->lat.','.$json[0]->lon;
            $this->marker_title[] = $titleart;
            $this->marker_url[] = $urlart;
            if (isset($this->leaflet_thumb) && $this->leaflet_thumb === true && $thumb != '') {
                $this->marker_thumb[] = '<br /><img src=\'/'.$thumb.'\' />';
            }
            else {
                $this->marker_thumb[] = '';
            }
        }
    }
}
```

by this version&nbsp;:
```
public function osm_geocode(&$addresses,&$titleart,&$urlart,$thumb)
{
    $rootUrl = 'http://open.mapquestapi.com/nominatim/v1/search.php?format=json&q=';
    $geocoder = new \Geocoder\Geocoder();
    $adapter = new \Geocoder\HttpAdapter\CurlHttpAdapter();
    $chain = new \Geocoder\Provider\ChainProvider(array(
        // new \Geocoder\Provider\GoogleMapsProvider($adapter),
        new \Geocoder\Provider\NominatimProvider($adapter,$rootUrl),
    ));
    $geocoder->registerProvider($chain);
    foreach ($addresses as $key => $value) {
        try {
            $geocode = $geocoder->geocode($value)->getCoordinates();
            if (!empty($geocode)) {
                $this->marker_coordinates[] = $geocode[0].','.$geocode[1];
                $this->marker_title[] = $titleart;
                $this->marker_url[] = $urlart;
                if (isset($this->leaflet_thumb) && $this->leaflet_thumb === true && $thumb != '') {
                    $this->marker_thumb[] = '<br /><img src=\'/'.$thumb.'\' />';
                }
                else {
                    $this->marker_thumb[] = '';
                }
            }
        } catch (Exception $e) {
        echo $e->getMessage();
        }
    }
}
```

You can simply download the `pico_leaflet.php` from the `devel_geophp` branch&nbsp;: [https://github.com/bricebou/pico_leaflet/tree/devel_geophp](https://github.com/bricebou/pico_leaflet/tree/devel_geophp).

__WARNING__: if your use a shared web hosting, there is a chance that you obtain errors, time outs, or that you don't get any response when trying to geocode addresses with some APIs like Google Maps, OpenStreetMap...      
It is due to some restrictions of the geocoding services, but using the `$rootUrl` in the code above solves this issue.