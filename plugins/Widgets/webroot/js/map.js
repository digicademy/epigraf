/*
 * Map widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from '/js/utils.js';

/**
 * Displays article locations on a leaflet map. This can happen in two ways:
 *
 * 1. In Articles index view, all locations are displayed on map and are reloaded with AJAX
 *    after every map move event (zoom, drag).
 * 2. In single Article view, location (or more locations) of this article are displayed in
 *    the map section. Locations can be added, edited and removed.
 */
export class MapWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);
        this.mapElement = element;

        this.rowTable = element.dataset.rowTable || 'items';
        this.rowTypes = element.dataset.rowTypes ? element.dataset.rowTypes.split(',') : [];
        this.editTypes = (element.dataset.editTypes || element.dataset.rowTypes || '').split(',');
        this.fieldName = element.dataset.fieldName || 'value';
        this.searchText = element.dataset.searchText || '';
        this.segments = Utils.splitString(element.dataset.segments);

        this.apiUrl = null;
        this.requestMode = null;
        this.maxZoom = 19;
        this.oldZoomLevel = undefined;

        // Zoom to loaded markers if the map is opened the first time
        this.positionInitialized = false;

        this.isUpdating = false;
        this.moveTimeout = null;

        let mode = element.dataset.mode;
        if (this.mapElement.closest('.widget-document-edit')) {
            mode = 'edit';
        }

        // Change the URL in search mode
        this.pushHistory = mode === 'search';

        // Cluster markers in search mode
        this.clusterMarkers = mode === 'search';
        this.markerLayer = undefined;

        // Load markers by AJAX in search mode
        this.ajaxMarkers = mode === 'search';

        // Allow dragging in edit mode
        this.editMarkers = mode === 'edit';
        this.showNumber = this.editMarkers && ((element.dataset.showNumber || '1') === '1');

        // Make marker radius toggleable
        this.displayMarkerRadius = false;

        // Content of markers depending on quality
        this.customStyles = {
            quality: {
                0: {content: '?'},
                1: {content: '?'},
                2: {content: ''},
                3: {content: ''},
                4: {content: ''},
            }
        };

        this.facets = {};

        // Data for user location / GPS
        this.userMarker = undefined;
        this.userPosition = {
            lat: 49.98419,
            lng: 8.2791,
            zoom: 12,
            initialized: false,
            geolocation: false
        };

        this.initMap();
        this.bindEvents();
        this.updateMarkers();
    }

    /**
     * Create the map and its layers.
     */
    initMap() {
        this.map = L.map(this.mapElement, {
            gestureHandling: true
        });

        // Map layers
        L.tileLayer(
            'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            {
                maxZoom: this.maxZoom,
                attribution: '© OpenStreetMap'
            }).addTo(this.map);

        this.loaderLayer = L.layerGroup().addTo(this.map);

        // Marker layers
        this.subGroups = {
            0 :  L.layerGroup(),
            1 :  L.layerGroup(),
            2 :  L.layerGroup()
        };

        if (this.clusterMarkers) {
            this.clusterLayer = L.markerClusterGroup.layerSupport({
                iconCreateFunction: (cluster) => this.renderCluster(cluster),
                spiderfyOnMaxZoom: false,
                showCoverageOnHover: false,
                zoomToBoundsOnClick: true,
                maxClusterRadius: 40
                // singleMarkerMode: true
            });

            this.map.addLayer(this.clusterLayer);
            this.markerLayer = this.clusterLayer;

            this.clusterLayer.checkIn(this.subGroups[0]);
            this.clusterLayer.checkIn(this.subGroups[1]);
            this.clusterLayer.checkIn(this.subGroups[2]);
        }

        else {
            this.markerLayer = this.map;
        }

        this.map.addLayer(this.subGroups[0]);
        this.map.addLayer(this.subGroups[1]);
        this.map.addLayer(this.subGroups[2]);

        // Controls
        this.initControls();

        // Listen to legend and filter clicks
        this.mapElement.addEventListener('click',(event) => this.onLegendClicked(event));

        // Listen to map events
        this.listenResize(this.mapElement, () => this.map.invalidateSize());
    }

    /**
     * Create map controls
     */
    initControls() {
        // Legend control
        this.legend = L.control({position: 'bottomleft'});

        this.legend.onAdd = (map) => {
            const div = L.DomUtil.create('div', 'legend');
            div.innerHTML += '<span class="data-quality data-quality-2" data-quality-content="">geprüft</span>';
            div.innerHTML += '<span class="data-quality data-quality-1" data-quality-content="?">vermutet</span>';
            div.innerHTML += '<span class="data-quality data-quality-0" data-quality-content="?">ungeprüft</span>';

            if (this.segments.length > 1) {
                for (let i = 0; i < this.segments.length; i++) {
                    div.innerHTML += `<span class="data-segment data-segment-${i + 1}">${this.segments[i]}</span>`;
                }
            }
            return div;
        };
        this.legend.addTo(this.map);

        // Scale bar
        this.scaleBar = L.control.scale({position: 'bottomright', maxWidth: 150, imperial: false}).addTo(this.map);

        // Own position
        this.geoLocButton = L.easyButton(
            'btn-geolocation',
            () => {
                this.positionInitialized = false;
                this.updateUserPosition();
            },
            'Zoom to user position'
        ).addTo(this.map);
        this.geoLocButton.button.classList.remove('btn-geolocation-active');
        this.geoLocButton.button.classList.add('btn-geolocation-inactive');

        // Radius toggle
        this.toggleRadiusButton = L.easyButton(
            'btn-toggle-radius',
            () => this.toggleMarkerRadius(),
            'Toggle marker radius'
        ).addTo(this.map);
        this.toggleRadiusButton.button.classList.toggle('active', this.displayMarkerRadius);

        // Zoom to marker button
        this.zoomToMarkerButton = L.easyButton(
            'btn-zoom-to-marker', () => {
                this.fitMarkers();
            },
            'Zoom to marker'
        ).addTo(this.map);

        // Search
        this.geocodeLayer = L.layerGroup();
        this.geocodeBox;

        const geocoderService = L.Control.Geocoder.nominatim(
            {serviceUrl: App.baseUrl + 'services/geo/'}
        );
        this.geocoder = L.Control.geocoder({
            query: this.searchText,
            defaultMarkGeocode: false,
            geocoder: geocoderService
        }).on('markgeocode', (e) => this.onGeocode(e))
          .addTo(this.map)

    }

    /**
     * Bind events to map.
     */
    bindEvents() {
        // General map events
        this.map.on('movestart', (event) => {
            this.oldZoomLevel = this.map.getZoom();
        });

        this.map.on('moveend', (event) => {
            this.rerenderMarkers();
            if (this.ajaxMarkers) {
                this.onMapMoved();
            }
        });

        if (this.clusterMarkers) {
            // Click single markers
            this.clusterLayer
                .on('click', (marker) => {
                    App.openDetails(marker.sourceTarget.customData.url, {'external':true});
                });

            // Click clusters
            this.clusterLayer
                .on('clusterclick', (c) => {
                    this.clusterLayer.options.zoomToBoundsOnClick = true;

                    const markers = c.layer.getAllChildMarkers();
                    let popupContent;

                    // If items in cluster are NOT in the same place, exit function
                    if (this.map.getZoom() < this.maxZoom) {
                        const coordsToCompare = markers[0]._latlng;
                        const tolerance = 0.0001;
                        for (let marker of markers) {
                            if ((Math.abs(marker._latlng.lat - coordsToCompare.lat) + Math.abs(marker._latlng.lng - coordsToCompare.lng)) > tolerance) {
                                return;
                            }
                        }
                    }

                    // else, disable default zoom behaviour and create popup
                    this.clusterLayer.options.zoomToBoundsOnClick = false;

                    popupContent = '<ol class="popup-article-list">';
                    for (let marker of markers) {
                        popupContent +=
                            `<li class="data-quality-${marker.customData.quality || 0}" data-quality-content="${this.customStyles.quality[marker.customData.quality || 0].content}">
                                <a href="${marker.customData.url}" class="frame" target="_blank">
                                ${marker.customData.caption || 'No title'}
                                </a>
                             </li>`;
                    }
                    popupContent += '</ol>';

                    L.popup({maxHeight: 250})
                        .setLatLng(c.layer.getLatLng())
                        .setContent(popupContent)
                        .openOn(this.map);
                });
        }

        // Item events
        const section = this.mapElement.closest('.doc-section');
        if (section) {
            section.addEventListener('epi:add:item', event => this.onItemAdded(event));
            section.addEventListener('epi:remove:item', event => this.onItemRemoved(event));
            section.addEventListener('epi:change:item', event => this.onItemChanged(event));

        }

        const entity = this.mapElement.closest('.widget-entity');
        if (entity) {
            entity.addEventListener('epi:change:entity', event => this.onItemChanged(event));
        }

    }

    /**
     * Stop loading.
     */
    resetLoading() {
        this.removeAllLoaders();

        this.apiUrl = null;
        this.mapReady = false;

        // List of tiles in z/y/x format and whether they wer completely loaded
        this.tilesLoaded = {};
        this.tilesQueue = [];
        this.loaders = [];

        // Cache which markers were loaded by their ID
        this.markersLoaded = {};

        if (this.clusterMarkers) {
            this.clusterLayer.clearLayers();
        }
    }

    /**
     * Update the base URL.
     */
    updateUrl() {
        if (!this.apiUrl) {
            return;
        }

        let url = new URL(this.apiUrl, App.baseUrl);
        url.searchParams.delete('page');
        url.searchParams.delete('tile');
        url.searchParams.delete('limit');

        url.searchParams.set('sort', 'distance');
        url.searchParams.set('direction', 'asc');

        const mapCenter = this.getCenter();
        const mapZoom = this.getZoom();
        url.searchParams.set('lat', mapCenter.lat);
        url.searchParams.set('lng', mapCenter.lng);
        url.searchParams.set('zoom', mapZoom);

        url = url.toString();

        // TODO: update the download links (CSV..)
        if (window.history.pushState && this.pushHistory) {
            window.history.pushState(url, "Epigraf - search results", url);
        }

        this.apiUrl = url;
    }

    /**
     * Start loading the markers.
     * Called after parameters have changed and on first loading.
     */
    updateMarkers() {
        this.resetMarkers();
        this.loadMarkers();
    }

    /**
     * Stop loading new markers.
     *
     * @returns {boolean} False if widget is currently updating
     */
    resetMarkers() {
        if (this.isUpdating) {
            return false;
        }

        // Stop loading
        App.ajaxQueue.stop('map');
        this.resetLoading();

        // Reset data element
        const elm = this.getDataElement();
        elm.dataset.consumed = 'false';

        // Reset URL
        this.apiUrl = elm.dataset.url;

        const parsedUrl = new URL(this.apiUrl, App.baseUrl);
        this.requestMode =  parsedUrl.searchParams.get('mode');
        this.mapReady = true;


    }

    /**
     * Fires when map is moved (zoom or drag).
     */
    onMapMoved() {
        clearTimeout(this.moveTimeout);
        this.moveTimeout = setTimeout(() => {
            this.loadMarkers();
            this.userPosition.zoom = this.map.getZoom();
        }, App.settings.timeout);
    }

    /**
     * Return element in which geoJSON data is stored.
     *
     * @returns {HTMLElement} Stores geoJSON data
     */
    getDataElement() {
        return this.mapElement.nextElementSibling;
    }

    /**
     * Get the URL for fetching the next page or tile.
     *
     * @param tile The tile array
     * @return {string|boolean} URL of map tile
     */
    getTileUrl(tile) {
        // No API URL
        if (!this.apiUrl || !tile || !this.rowTypes.length) {
            return false;
        }

        tile.page += 1;
        let url = new URL(this.apiUrl, App.baseUrl);
        url.searchParams.set('tile', tile.id);
        url.searchParams.set('page', tile.page);
        url.searchParams.set('itemtypes', this.rowTypes.join(','));
        // url.searchParams.set('template', 'raw');
        url.searchParams.set('snippets', 'article,properties');
        url.searchParams.delete('sort');
        url.searchParams.delete('direction');
        url.searchParams.delete('lat');
        url.searchParams.delete('lng');
        url.searchParams.delete('zoom');
        url.pathname = url.pathname + '/items.geojson';
        url = url.toString();

        return url;
    }

    /**
     * Create a list of tiles where markers have to be loaded.
     */
    initTiles() {
        this.removeAllLoaders();
        this.tilesQueue = [];

        // Calculate tiles
        // See https://wiki.openstreetmap.org/wiki/Zoom_levels
        const tile_z = Math.max(1, this.map.getZoom() - 1);

        const bounds = this.map.getBounds();
        const tiles_x = [
            Utils.lon2tile(bounds.getWest(), tile_z),
            Utils.lon2tile(bounds.getEast(), tile_z)
        ];

        const tiles_y = [
            Utils.lat2tile(bounds.getNorth(), tile_z),
            Utils.lat2tile(bounds.getSouth(), tile_z)
        ];

        // Check which tiles already were loaded
        for (let x = tiles_x[0]; x <= tiles_x[1]; x++) {
            for (let y = tiles_y[0]; y <= tiles_y[1]; y++) {

                // Check upper zoom levels as well
                let tile_zoomed = '';
                let x_zoomed = x;
                let y_zoomed = y;

                let loaded = false;
                for (let z_zoomed = tile_z; z_zoomed >= 1; z_zoomed--) {
                    tile_zoomed = `${z_zoomed}/${y_zoomed}/${x_zoomed}`;
                    loaded = this.tilesLoaded[tile_zoomed];
                    if (loaded) {
                        break;
                    } else {
                        y_zoomed = Math.round(y_zoomed / 2);
                        x_zoomed = Math.round(x_zoomed / 2);
                    }
                }

                if (!loaded) {
                    const tileId = `${tile_z}/${y}/${x}`;

                    this.tilesQueue.push(
                        {
                            z: tile_z, y: y, x: x,
                            id: tileId,
                            loader: this.addLoader(tile_z, y, x),
                            page: 0
                        }
                    );

                    this.tilesLoaded[tileId] = false;
                }
            }
        }
    }

    /**
     * Return the next tile (of markers) that should be loaded.
     */
    finishTile(tile) {
        this.tilesLoaded[tile.id] = true;
        tile.loader.remove();
    }

    /**
     * Load markers on initialization and after move events.
     */
    loadMarkers() {
        if (!this.mapReady) {
            return;
        }

        if (!this.positionInitialized) {
            this.initMapPosition();
        }

        this.loadMarkersFromDataElement();
        if (!this.positionInitialized) {
            this.fitMarkers();
        }

        if (this.apiUrl) {
            this.initUserPosition(() => this.loadMarkersFromAjax());
        }

    }

    /**
     * Fetch and merge more markers in the current viewport.
     *
     * @param tile The tile to load
     * @return {void | boolean}
     */
    fetchMore(tile) {
        // Finished all
        if (!tile && (this.tilesQueue.length === 0)) {
            return false;
        }

        // Next tile in the queue
        if (!tile) {
            tile = this.tilesQueue.pop();
        }

        // Construct URL (including the page parameter)
        const url = this.getTileUrl(tile);
        if (!url) {
            return false;
        }

        // Request data
        App.ajaxQueue.add('map',
            {
                type: 'GET',
                url: url,
                //dataType: 'html',
                dataType: 'json',
                beforeSend: (xhr) => {
                    App.showLoader();
                },
                success: (data, textStatus, xhr) => {
                    this.isUpdating = true;

                    // Add markers
                    const geoJson = this.extractJsonData(data);
                    this.addMarkers(geoJson);
                    this.isUpdating = false;

                    // Fetch more
                    if (!data.pagination.page_next) {
                        this.finishTile(tile);
                        this.fetchMore();
                    } else {
                        this.fetchMore(tile);
                    }
                },
                error: (xhr, textStatus, errorThrown) => {
                    // If aborted
                    if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                        return;
                    }

                    // No results
                    if (xhr.status === 404) {
                        // Fetch more
                        this.finishTile(tile);
                        this.fetchMore();
                        return;
                    }
                    let msg = Utils.getValue(xhr.responseJSON,'error.message');
                    msg = msg ? (errorThrown + ': ' + msg) : errorThrown;
                    App.showMessage(msg, textStatus);
                },
                complete: (xhr, textStatus) => {
                    App.hideLoader();
                }
            }
        );
    }

    /**
     * Extract JSON from the HTML data element.
     *
     * At the end the data-consumed attribute will be set to "false" to
     * prevent double consumption of the same data.
     *
     * @returns {JSON | Array}
     */
    extractHtmlData(elm) {
        let values = [];

        if (elm && elm.dataset.consumed !== 'true') {
            elm.dataset.consumed = 'true';
            try {
                values = JSON.parse(elm.textContent);
            } catch (error) {
                console.log('Could not parse GeoJSON data from HTML element.');
            }
        }

        return values;
    }

    /**
     * Extract data from JSON responses
     *
     * @param data Data from AJAX
     * @returns {JSON} Formatted geojson data
     */
    extractJsonData(data) {
        return data.features || [];
    }

    /**
     * Creates or updates a single marker
     *
     * @param {*} marker The marker or false for non-existing markers.
     * @param {{
     *     number: number,
     *     id: number,
     *     caption: string,
     *     quality: number,
     *     radius: number,
     *     url: string,
     *     lat: string,
     *     lng: string
     * }} data New marker data (coordinates and metadata) or false the use the existing data
     * @returns {*} The marker
     */
    renderMarker( marker, data = null) {
        // Update marker
        if (marker) {
            if (data == null) {
                data = marker.customData;
            }

            // Move to the quality layer
            if (marker.customData.quality !== data.quality) {
                const oldLayer = this.subGroups[marker.customData.quality] || this.markerLayer;
                const newLayer = this.subGroups[data.quality] || this.markerLayer;
                oldLayer.removeLayer(marker);
                newLayer.addLayer(marker);
            }

            // Update position
            if ((marker.customData.lat !== data.lat) || (marker.customData.lng !== data.lng)) {
                marker.setLatLng([data.lat, data.lng]);
            }
        }
        // Add marker
        else {
            if (data == null) {
                data = {};
            }
            const edit = this.editMarkers && ((data.edit === undefined) || (data.edit));
            marker = L.marker([data.lat, data.lng], {draggable: edit});
            this.makeMarkerDraggable(marker);
        }

        // Set data
        marker.customData = data;

        // Set marker radius (minimum is 10px)
        const hasRadius = (data.radius !== undefined) && Number(data.radius) !== 0;
        const zoomLevel = this.map.getZoom();
        let iconRadius = 10;
        if (hasRadius && this.displayMarkerRadius && zoomLevel > 13) {
            iconRadius = Math.min(Math.max(this.meterToPixel(data.lat, data.radius, zoomLevel), 20), 500);
        }
        const iconDiameter = iconRadius * 2;

        const classNames = [
            'item-marker',
            (hasRadius && this.displayMarkerRadius) ? 'area-marker' : 'point-marker'
        ];

        if (zoomLevel >= 14) {
            classNames.push('zoomed-in');
        }
        if (data.radius >= 500) {
            classNames.push('area-marker-big');
        }

        // Set marker segment
        if (data.segment) {
            classNames.push(`marker-segment-${data.segment}`);
        }

        // Set marker color and content
        const faceted = data.properties && this.facets;
        let markerStyle = '';
        let markerContent;

        if (faceted) {
            for (const [propertyType, propertyIds] of Object.entries(data.properties)) {
                for (let propertyId of propertyIds) {
                    let item = this.facets[propertyId];
                    if (item && item.color) {
                        markerStyle = `background-color:${item.color};border-color:${item.color};`;
                    }
                }
            }

            markerContent = '1';
        } else {
            markerContent = this.showNumber ? data.number : (this.customStyles.quality[data.quality].content || '');
            classNames.push(`data-quality-${data.quality}`);
        }

        const markerIcon = L.divIcon({
            html: `<div class="item-marker-label" data-quality-content="${markerContent}" style="${markerStyle}"></div>`,
            className: classNames.join(' '),
            iconSize: [iconDiameter, iconDiameter]
        });
        marker.setIcon(markerIcon);

        return marker;
    }

    renderCluster(cluster) {
        const childCount = cluster.getChildCount();
        const scale = d3.scaleLinear([10, 500], [30, 80]).clamp(true);
        const size = scale(childCount);

        const markers = cluster.getAllChildMarkers();
        let counts = {};
        let colors = {};
        markers.forEach(marker => {
            if (marker.customData.properties) {
                for (const [propertyType, propertyIds] of Object.entries(marker.customData.properties)) {
                    for (let propertyId of propertyIds) {
                        let item = this.facets[propertyId];
                        if (item && item.color) {
                            colors[propertyId] = colors[propertyId] || item.color;
                            counts[propertyId] = counts[propertyId] || 0;
                            counts[propertyId] += 1;
                        }
                    }
                }
            }
        });

        const childQuality = markers.reduce((carry, marker) => Math.min(carry, marker.customData.quality),2);
        const qualityClass = ' data-quality-' + childQuality;

        let markerHtml;
        let markerClass;
        if (Object.keys(counts).length > 0) {
            markerHtml = PieChartWidget.getSvg(counts, colors, childCount, size);
            markerClass = 'marker-cluster';
        } else {
            markerHtml  = `<div class="item-marker-label"><span style="line-height:${0.85 * size}px;">${childCount}</span></div>`;
            markerClass = 'marker-cluster' + qualityClass;
        }

        return new L.DivIcon({
            html: markerHtml,
            className: markerClass,
            iconSize: new L.Point(size, size)
        });
    }

    /**
     * Rerender markers after move event (zoom or drag).
     *
     * @param {number} [zoom] New zoom level.
     */
    rerenderMarkers(zoom) {
        let update = false;
        if (zoom) {
            this.map.setZoom(zoom);
            update = true;
        } else {
            zoom = this.map.getZoom();
            update = (zoom >= 14 || (this.oldZoomLevel === 14 && zoom === 13));
        }
        const bounds = this.map.getBounds();

        if (update) {
            for (const marker in this.markersLoaded) {
                const inMapBounds = bounds.contains(this.markersLoaded[marker].getLatLng());
                const radius = this.markersLoaded[marker].customData.radius;
                const hasRadius = (radius !== 0) && (radius !== '');
                if (inMapBounds && hasRadius) {
                    this.renderMarker(this.markersLoaded[marker]);
                }
            }
        }
    }

    /**
     * Add markers for multiple geoJSON objects.
     *
     * @param data Array of geoJSON objects
     * @returns integer Number of markers that were added inside the view bounds
     */
    addMarkers(data) {
        data.forEach(geoJson => {
                if ((geoJson.geometry.type === "Point") || this.editMarkers) {
                    // Careful: geojson uses lnglat, leaflet latlng -> reverse()
                    const data = geoJson.data;
                    const coord = Utils.getValue(geoJson, 'geometry.coordinates', [0, 0]);
                    data.lat = coord[1];
                    data.lng = coord[0];

                    if (!this.markersLoaded[data.id]) {
                        const marker = this.renderMarker(null, data);
                        this.markersLoaded[data.id] = marker;

                        let layer = this.subGroups[data.quality] || this.markerLayer;
                        layer.addLayer(marker);
                    }
                }
            }
        );
    }

    /**
     * Returns current user position
     *
     * @param options
     * @returns {Promise<unknown>} Current user position.
     */
    getPosition(options) {
        return new Promise((resolve, reject) =>
            navigator.geolocation.getCurrentPosition(
                position => resolve(position),
                error => reject(error),
                options
            )
        );
    }

    /**
     * Gets and zooms to current user position.
     *
     * @param onReady After AJAX call for loading markers has finished.
     * @returns {Promise<void>}
     */
    async updateUserPosition(onReady) {
        if (!navigator.geolocation) {
            App.showMessage("Your browser doesn't support the geolocation feature.", "notice");
        } else {
            try {
                const position = await this.getPosition({enableHighAccuracy: true});
                this.userPosition.lat = position.coords.latitude;
                this.userPosition.lng = position.coords.longitude;
                this.userPosition.geolocation = true;
            } catch (err) {
                console.log(err);
                App.showMessage("Please enable the location feature of your browser if you want to see your location.", "notice");
            }
        }

        this.showUserPosition();

        if (onReady !== undefined) {
            onReady();
        }
    }

    /**
     * Displays user position on map.
     */
    showUserPosition() {
        if (!this.positionInitialized) {
            this.map.setView([this.userPosition.lat, this.userPosition.lng], this.userPosition.zoom);
        }

        this.userPosition.initialized = true;
        this.positionInitialized = true;

        // Remove old marker
        if (this.userMarker) {
            this.map.removeLayer(this.userMarker);
            this.userMarker = undefined;
        }

        // Add geolocated marker
        if (this.userPosition.geolocation) {

            this.geoLocButton.button.classList.remove('btn-geolocation-inactive');
            this.geoLocButton.button.classList.add('btn-geolocation-active');

            const markerOptions = L.divIcon({
                html: '',
                className: 'user-position-marker',
                iconSize: 15
            });
            this.userMarker = L.marker([this.userPosition.lat, this.userPosition.lng], {
                icon: markerOptions
            }).addTo(this.map);
        } else {
            this.geoLocButton.button.classList.remove('btn-geolocation-active');
            this.geoLocButton.button.classList.add('btn-geolocation-inactive');
        }
    }

    /**
     * Toggle the display of the marker radius and update the map.
     *
     * @param {boolean} [show] Show or hide the marker radius. Leave empty to toggle.
     */
    toggleMarkerRadius(show) {
        this.displayMarkerRadius = show || !this.displayMarkerRadius;
        this.toggleRadiusButton.button.classList.toggle('active', this.displayMarkerRadius);
        this.rerenderMarkers();
    }

    /**
     * Determines user position for the first time.
     *
     * @param onReady After AJAX call for loading markers has finished.
     */
    initUserPosition(onReady) {
        if (!this.userPosition.initialized) {
            this.updateUserPosition(onReady);
            return;
        }

        if (onReady !== undefined) {
            onReady();
        }
    }

    /**
     * Initialize map position.
     */
    initMapPosition() {
        const elm = this.getDataElement();
        const lat = elm.dataset.locationLat || null;
        const lng = elm.dataset.locationLng || null;
        const zoom = elm.dataset.locationZoom || this.userPosition.zoom;

        if (lat && lng) {
            this.userPosition.lat = lat;
            this.userPosition.lng = lng;
            this.userPosition.zoom = zoom;
            this.map.setView([this.userPosition.lat, this.userPosition.lng], this.userPosition.zoom);
            this.userPosition.initialized = true;
            this.positionInitialized = true;
        }
    }

    getCenter() {
        return this.userPosition.initialized ? this.map.getCenter() : {lat: this.userPosition.lat, lng: this.userPosition.lng};
    }

    getZoom() {
        return this.userPosition.initialized ? this.map.getZoom() : this.userPosition.zoom;
    }

    /**
     * Add and fit first bunnch of markers.
     *
     */
    loadMarkersFromDataElement() {
        // Markers
        this.isUpdating = true;
        const geoJson = this.extractHtmlData(this.getDataElement());
        this.addMarkers(geoJson);
        this.isUpdating = false;

        return geoJson.length;
    }

    /**
     * Reload markers that were not included in data element.
     */
    loadMarkersFromAjax() {
        App.ajaxQueue.stop('map');
        this.updateUrl();
        this.initTiles();

        // Add up to four tiles to the loading queue
        this.fetchMore();
        this.fetchMore();
        this.fetchMore();
        this.fetchMore();
    }

    /**
     * Fit map bounds to marker extent.
     */
    fitMarkers() {
        const bounds = L.featureGroup(Object.values(this.markersLoaded)).getBounds();
        if (bounds.isValid()) {
            this.map.fitBounds(bounds.pad(0.1));
            if (this.map.getZoom() > 14) {
                this.rerenderMarkers(14);
            }
            this.positionInitialized = true;
        }
    }

    /**
     * Show a rectangle on the tile that is currently loaded.
     *
     * @param z
     * @param y
     * @param x
     */
    addLoader(z, y, x) {
        const latlngs = [
            [Utils.tile2lat(y, z), Utils.tile2long(x, z)],
            [Utils.tile2lat(y + 1, z), Utils.tile2long(x + 1, z)]
        ];

        const rectOptions = {color: '#01545b', stroke: false, fillOpacity: 0.1};
        const loadingPolygon = L.rectangle(latlngs, rectOptions);

        loadingPolygon.addTo(this.loaderLayer);
        this.loaders.push(loadingPolygon);
        return loadingPolygon;
    }

    /**
     * Remove all loaders of widget.
     */
    removeAllLoaders() {
        if (this.loaders) {
            this.loaders.forEach(loader => loader.remove());
        }
    }

    showMap() {
        const container = this.mapElement.closest('.widget-map-container');
        if (container) {
            container.style.display = "block";
        }
        this.map.invalidateSize();
    }

    hideMap() {
        const container = this.mapElement.closest('.widget-map-container');
        if (container) {
            container.style.display = "none";
        }
    }

    onLegendClicked(event) {
        if (!event.target.matches('.legend span')) {
            return;
        }

        let subGroup;
        try {
            let quality = Array.from(event.target.classList).filter(x => x.startsWith('data-quality-'));
            quality = quality.length > 0 ? quality[0].replace('data-quality-','') : undefined;
            quality = Number(quality);
            subGroup = this.subGroups[quality] || undefined;
        } catch (error) {
            console.log(error);
        }

        if (!subGroup) {
            return;
        }

        const active = !event.target.classList.contains('disabled');

        if (active) {
            this.map.removeLayer(subGroup);
            event.target.classList.add('disabled');
        } else  {
            this.map.addLayer(subGroup);
            event.target.classList.remove('disabled');
        }

    }

    /**
     * Fires when new location for article is created. Add marker to map.
     * // TODO: only for items inside a map section
     * // TODO: Show marker from property
     *
     * @param {Event} event The added item can be found in the event.target property
     */
    onItemAdded(event) {
        if (!this.editTypes.includes(event.target.dataset.rowType)) {
            return;
        }

        this.showMap();

        this.initUserPosition(() => {
            const markerId = event.target.dataset.rowId;
            const mapCenter = this.getCenter();

            let quality = Utils.getInputValue(event.target.querySelector(`[data-row-field='published'] select`), 0);
            quality = quality === '' ? 0 : quality;

            const newMarkerData = [{
                'type': 'Feature',
                'data': {
                    'number': Utils.getInputValue(event.target.querySelector(`[data-row-field='sortno'] input`), 0),
                    'id': markerId,
                    'caption': null,
                    'quality': quality,
                    'radius': Utils.getInputValue(event.target.querySelector(`[data-row-field='${this.fieldName}.radius'] input`), 0),
                    'url': null
                },
                'geometry': {'type': 'Point', 'coordinates': [mapCenter.lng, mapCenter.lat]}
            }];

            this.addMarkers(newMarkerData);
            this.updateRow(markerId, mapCenter);
        });
    }

    /**
     * Fires when location for article is deleted. Remove marker from map.
     *
     * @param event Item Removed
     */
    onItemRemoved(event) {
        if (!this.rowTypes.includes(event.target.dataset.rowType)) {
            return;
        }

        const markerId = event.target.dataset.rowId;
        const currentMarker = this.markersLoaded[markerId];

        if (!currentMarker) {
            return;
        }

        this.map.removeLayer(currentMarker);
        delete this.markersLoaded[markerId];

        if (Object.keys(this.markersLoaded).length === 0) {
            this.hideMap();
        }
    }

    /**
     * Observe location changes and update marker position.
     *
     * // TODO: Show marker from property
     *
     * @param {CustomEvent} event The 'epi:change:item' event
     */
    onItemChanged(event) {
        if (!this.editTypes.includes(event.target.dataset.rowType)) {
            return;
        }

        this.showMap();

        const markerId = event.target.dataset.rowId;
        const currentMarker = this.markersLoaded[markerId];

        if (!currentMarker) {
            return;
        }

        const currentData = Object.assign({}, currentMarker.customData);

        let quality =  Utils.getInputValue(event.target.querySelector(`[data-row-field='published'] select`), currentData.quality);
        quality = quality === '' ? 0 : quality;

        currentData.lat = Utils.getInputValue(event.target.querySelector(`[data-row-field='${this.fieldName}.lat'] input`), currentData.lat);
        currentData.lng = Utils.getInputValue(event.target.querySelector(`[data-row-field='${this.fieldName}.lng'] input`), currentData.lng);
        currentData.radius = Utils.getInputValue(event.target.querySelector(`[data-row-field='${this.fieldName}.radius'] input`), currentData.radius);
        currentData.number = Utils.getInputValue(event.target.querySelector(`[data-row-field='sortno'] input`), currentData.number);
        currentData.quality = quality;

        this.renderMarker(currentMarker, currentData);
    }

    /**
     * Update a row after the marker has been dragged
     *
     * @param rowId Shared id of marker and item.
     * @param coords New coordinates of marker.
     * @param radius The new radius of the marker.
     */
    updateRow(rowId, coords, radius) {
        const row = document.querySelector(`[data-row-table='${this.rowTable}'][data-row-id='${rowId}']`);
        if (row) {
            const inputLat = row.querySelector(`[data-row-field='${this.fieldName}.lat'] input`);
            const inputLng = row.querySelector(`[data-row-field='${this.fieldName}.lng'] input`);
            Utils.setInputValue(inputLat, coords.lat);
            Utils.setInputValue(inputLng, coords.lng);

            if (radius !== undefined) {
                const inputRadius = row.querySelector(`[data-row-field='${this.fieldName}.radius'] input`);
                Utils.setInputValue(inputRadius, radius);
            }
        }
    }

    /**
     * Update marker properties and rerender marker on dragend.
     *
     * @param marker Dragged marker.
     */
    makeMarkerDraggable(marker) {
        //marker.options.autoPan = true;
        //marker.options.autoPanPadding = [20, 20];

        // Prevent weird map pan/jumping when marker is clicked
        marker.on('mousedown', (event) => {
            event.originalEvent.preventDefault();
        });

        marker.on('dragend', () => {
            const rowId = marker.customData.id;
            const coords = marker.getLatLng();
            marker.customData.lat = coords.lat;
            marker.customData.lng = coords.lng;
            this.updateRow(rowId, coords);
            this.map.panTo([coords.lat, coords.lng]);
        });
    }

    /**
     * Convert meter to map pixel. Used for high zoom levels where marker have to cover a certain area.
     *
     * @param latitude
     * @param meters
     * @param zoomLevel
     * @returns {number} New pixel radius of marker.
     */
    meterToPixel(latitude, meters, zoomLevel) {
        const earthCircumference = 40075017;
        const latitudeRadians = latitude * (Math.PI / 180);
        const metersPerPixel = earthCircumference * Math.cos(latitudeRadians) / Math.pow(2, zoomLevel + 8);
        return meters / metersPerPixel;
    }

    /**
     * Show the geocoding search result on the map.
     *
     * @param event
     */
    onGeocode(event) {
        const bbox = event.geocode.bbox;
        const corners = [
            [bbox.getSouthEast().lat, bbox.getSouthEast().lng],
            [bbox.getNorthEast().lat, bbox.getNorthEast().lng],
            [bbox.getNorthWest().lat, bbox.getNorthWest().lng],
            [bbox.getSouthWest().lat, bbox.getSouthWest().lng]
        ];

        // Create polygon if it doesn't exist
        if (!this.geocodeBox) {
            const polygon = L.polygon(corners, {weight: 1}).addTo(this.geocodeLayer)
            this.geocodeBox = {
                box : polygon,
            }

            if (this.editMarkers) {
                polygon.on('click', () => this.onGeocodeApply());
                polygon.on('mouseover', () => {
                    polygon.setStyle({weight: 3});
                });
                polygon.on('mouseout', () => {
                    polygon.setStyle({weight: 1});
                });

            }
        }
        // Or update
        else {
            this.geocodeBox.box.setLatLngs(corners);
        }

        this.map.addLayer(this.geocodeLayer);
        this.map.fitBounds(this.geocodeBox.box.getBounds());
    }

    onGeocodeApply() {
        if (!this.geocodeBox) {
            return;
        }

        // Last marker in this.markersLoaded
        let markerId = Object.keys(this.markersLoaded).pop();
        if (!markerId) {

            const newMarkerData = [{
                'type': 'Feature',
                'data': {
                    'number': 0,
                    'id': -1,
                    'caption': null,
                    'quality': 0,
                    'radius': 0,
                    'url': null
                },
                'geometry': {'type': 'Point', 'coordinates': [0, 0]}
            }];
            this.addMarkers(newMarkerData);
            markerId = -1;
        }

        const currentMarker = this.markersLoaded[markerId];
        if (!currentMarker) {
            return;
        }

        const currentData = Object.assign({}, currentMarker.customData);
        const bounds = this.geocodeBox.box.getBounds();
        const center = bounds.getCenter();
        const southEast = bounds.getSouthEast();
        const distance = Math.round(center.distanceTo(southEast));

        currentData.lat = center.lat;
        currentData.lng = center.lng;
        currentData.radius = distance;

        this.map.removeLayer(this.geocodeLayer);
        this.renderMarker(currentMarker, currentData);
        this.toggleMarkerRadius(true);
        this.updateRow(markerId, center, distance);
    }

    updateColors(data) {
        this.facets = data;

        if (this.facets) {
            this.legend.remove();
        } else {
            this.legend.addTo(this.map);
        }

        const bounds = this.map.getBounds();
        for (const marker in this.markersLoaded) {
            const inMapBounds = bounds.contains(this.markersLoaded[marker].getLatLng());
            if (inMapBounds) {
                this.renderMarker(this.markersLoaded[marker]);
            }
        }

        if (this.clusterMarkers) {
            this.map.eachLayer(layer => {
                if (layer instanceof L.MarkerCluster) {
                    // const inMapBounds = bounds.contains(layer.getLatLng());
                    layer.setIcon(this.renderCluster(layer));
                }
            });
        }

    }

}

/**
 * Generate pie charts
 */
class PieChartWidget {

    static getSvg(values, colors, total, size) {
        const margin = (0.15 * size) / 2;
        size = 0.85 * size;
        values = Array.isArray(values) ? values : Object.values(values);
        colors = Array.isArray(colors) ? colors : Object.values(colors);

        let pie = d3.pie()(values);
        let arc = d3.arc().innerRadius(0).outerRadius(size / 2);

        let svg = d3.create("svg")
            .attr("width", size)
            .attr("height", size)
            .attr("style", `margin-top: ${margin}px;margin-left: ${margin}px;`)
            .attr("viewBox", [-size/2, -size/2, size, size]);

        svg.selectAll("path")
            .data(pie)
            .enter()
            .append("path")
            .attr("d", arc)
            .attr("fill", (d, i) => colors[i]);

        // Add total number in the center of the pie chart
        svg.append("text")
            .attr("text-anchor", "middle")
            //.attr("dominant-baseline", "middle")
            .attr("dy", "0.35em")
            .attr("fill", "white")
            .attr("font-size", '0.8rem')
            .attr("line-height", '0.8rem')
            .text(total);

        return svg.node().outerHTML;
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['map'] = MapWidget;
