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

## Basic usage

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

### Display the map

To display the map, you just have to edit your active theme `index.html` and add this to your blog posts section :
```
{{ map_article }}
```

Don't worry : leaflet scripts and stylesheets are loaded only if the coordinates meta (or the address meta -- see below) is set.

### Access the `meta.coordinates`

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

See it live : [momh.fr/test/leaflet/first](http://momh.fr/test/leaflet/first)

## Geocoding

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

Just like before, to display the map, you just have to edit your active theme `index.html` and add this to your blog posts section :
```
{{ map_article }}
```

### Access the `meta.address`

You can now access the current page `meta.address` ; in your theme`index.hmtl` :
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

See it live : [momh.fr/test/leaflet/second](http://momh.fr/test/leaflet/second)

## Mixing coordinates and addresses

Of course you can mix coordinates and addresses just like in this example : [momh.fr/test/leaflet/third](http://momh.fr/test/leaflet/third) which is made from coordinates and addresses specified in its meta :
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

If this line isn't present or the value is empty, the pico_leaflet plugin uses the `yourdomain.tld/map`.

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

## Providers

By default the map provider is [www.openstreetmap.org](http://www.openstreetmap.org), but you can add several other providers thanks to the Leaflet plugin [leaflet-providers](https://github.com/leaflet-extras/leaflet-providers).

__WARNING__ : some providers (the ones that require registration) are not supported yet in this pico_leaflet plugin.

In order to add other providers, you have to enable it in your `config.php` :
```
$config['leaflet']['providers'] = true;
```
Then, you have to specify the providers you want to load. First, you have to open the `plugins/pico_leaflet/leaflet-providers/leaflet-providers.js` file and check the `Definition of providers` part (begins at line 65). You can preview the full list of layers here : [http://leaflet-extras.github.io/leaflet-providers/preview/index.html](http://leaflet-extras.github.io/leaflet-providers/preview/index.html).

In your `config.php`, you have to specify the providers inside an array, using a name of your choice (without space) as the key and the `provider[.<variant>]`string used in the leaflet-providers script or the "Provider names" that appears in [http://leaflet-extras.github.io/leaflet-providers/preview/index.html](http://leaflet-extras.github.io/leaflet-providers/preview/index.html).

For example :

```
$config['leaflet']['providers_enabled'] = array(
    'thland' => 'Thunderforest.Landscape', // Landscape variant of the Thunderforest provider
    'stato' => 'Stamen.Toner', // Toner variant of Stamen provider
    'stawa' => 'Stamen.Watercolor', Watercolor variant of Stamen provider
    'hydda' => 'Hydda' // Hydda provider
);
```

### Specifying the default provider

You can specify the default provider adding this to your `config.php`, using one of the key/name of the `providers_enabled` array :
```
$config['leaflet']['providers_default'] = 'stato';
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


 


