/*
 * Plot widget - EpiWidJs framework
 *
 * @author     Epigraf Team
 * @contact    jakob.juenger@adwmainz.de
 * @license    https://www.gnu.org/licenses/old-licenses/gpl-2.0.html GPL 2.0
 *
 */

import {BaseWidget} from '/js/base.js';
import Utils from '/js/utils.js';

/**
 * Plot widget
 */
export class PlotWidget extends BaseWidget {
    constructor(element, name, parent) {
        super(element, name, parent);

        /**
         * The plot object (for example a NetworkPlot instance)
         */
        this.plot = null;

        /**
         * The item types managed by this widget (for example "heraldry")
         * @type {string}
         */
        this.itemtype = element.dataset.itemtype;
        this.isUpdating = false;
        this.apiUrl = element.dataset.apiUrl;
        if (this.apiUrl) {
            this.apiUrl = new URL(this.apiUrl, App.baseUrl);
        }

        // Facet events
        this.listenEvent(document,'epi:load:facets', event => this.onLoadFacets(event));
        this.listenEvent(document,'epi:close:facets', event => this.onCloseFacets(event));
    }

    /**
     * Widget initialization: Override in child classes and put all initialization code here.
     *
     * The method is called once after all widgets were constructed and are ready to be used.
     */
    initWidget() {
        this.loadData();
    }

    /**
     * Widget initialization: Override in child classes
     *
     * The method is called each time, a widget is initialized:
     * when it was created and when it is updated.
     */
    updateWidget() {
        // this.loadData();
    }

    /**
     * Load data from a scripts tag
     *
     */
    loadData() {
        if (this.apiUrl) {
            this.loadApiData();
        } else {
            this.loadScriptData();
        }
    }

    loadScriptData() {
        this.isUpdating = true;
        const dataElement = this.getDataElement();
        const data = this.extractData(dataElement);
        const plotType = this.getPlotType();
        this.showData(data, plotType);
        this.isUpdating = false;
    }


    loadApiData() {

        App.ajaxQueue.add('plot',
            {
                type: 'GET',
                url: this.apiUrl,
                //dataType: 'html',
                dataType: 'json',
                beforeSend: (xhr) => {
                    App.showLoader();
                },
                success: (data, textStatus, xhr) => {
                    this.isUpdating = true;
                    const plotType = this.getPlotType();
                    this.showData(data.groups || [], plotType);
                    this.isUpdating = false;
                },
                error: (xhr, textStatus, errorThrown) => {
                    // If aborted
                    if ((xhr.readyState === XMLHttpRequest.UNSENT) && (xhr.status === 0)) {
                        return;
                    }

                    // No results
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
     * Return element in which geoJSON data is stored.
     *
     * @returns {HTMLElement} Stores geoJSON data
     */
    getDataElement() {
        return this.widgetElement.querySelector('script[data-snippet=widget-data]');
    }

    /**
     * Extract JSON from the data element.
     *
     * At the end the data-consumed attribute will be set to "true" to
     * prevent double consumption of the same data.
     *
     * @returns {JSON | Array}
     */
    extractData(elm) {
        let values = [];

        if (elm && elm.dataset.consumed !== 'true') {
            elm.dataset.consumed = 'true';
            try {
                values = JSON.parse(elm.textContent);
            } catch (error) {
                console.log('Could not parse JSON data from HTML element.');
            }
        }

        return values;
    }

    /**
     * Get the plot type from the data attribute
     *
     * @returns {String} One of the following: "map", "timeline", "graph", "auto"
     */
    getPlotType() {
        if (this.widgetElement.dataset.scope) {
            return this.widgetElement.dataset.scope;
        }

        const dataElement = this.getDataElement();
        return dataElement.dataset.scope || 'auto';
    }

    /**
     * Get the canvas element for the plot.
     *
     * Creates a new canvas element if it does not exist.
     *
     * @return {HTMLElement}
     */
    getCanvasElement() {
        let canvas = this.widgetElement.querySelector('div.plot');
        if (!canvas) {
            canvas = document.createElement('div');
            canvas.classList.add('plot');
            canvas.style.width = '100%';
            canvas.style.height = '100%';
            this.widgetElement.appendChild(canvas);
        }
        return canvas;
    }

    /**
     * Plot the data in values.
     *
     * @param {Array} values A list of items. Each item is an object with the following fields: x, totals.
     * @param {String} plotType The type of plot to create. One of the following: "map", "timeline", "graph"
     */
    showData(values, plotType) {

        const canvas = this.getCanvasElement();
        const config = {
            onNodeClick: (params) => this.showDetails(params),
            onGroupClick: (params) => this.showList(params),
            imageBaseUrl: this.widgetElement.dataset.imageBaseUrl
        };

        if (plotType === 'tiles') {
            this.plot = new TilesPlot(canvas, values, config);
        }
        else if (plotType === 'timeline') {
            this.plot = new TracePlot(canvas, values, config);
        }
        else if (plotType === 'graph') {
            this.plot = new NetworkPlot(canvas, values, config);
        }
    }


    /**
     * Open the row in the sidebar
     *
     * @param {Object} params
     */
    showDetails(params) {
        let url = this.widgetElement.dataset.viewUrl;
        if (!url) {
            return;
        }
        url = decodeURI(url).formatUnicorn(params);
        this.emitEvent('epi:open:details', {url: url})
    }

    /**
     * Open all rows of a group in the sidebar
     *
     * @param {Object} params Query parameters to append to the index URL
     */
    showList(params) {
        let url = this.widgetElement.dataset.indexUrl;
        if (!url) {
            return;
        }
        url = decodeURI(url).formatUnicorn(params);
        this.emitEvent('epi:open:details', {url: url})
    }

    /**
     * Use facet colors for the plots
     *
     * @param {CustomEvent} event
     */
    onLoadFacets(event) {
        if (!event.detail.sender) {
            return;
        }

        const facetWidget = event.detail.sender;
        if (!facetWidget.hasFlag('grp')) {
            return;
        }

        //if (facetWidget.widgetElement.classList.contains('widget-filter-item-properties')) {
        const data = facetWidget.getFacets();

        if (this.plot) {
            this.plot.updateColors(data);
        }
        //}
    }

    /**
     * Clear facet colors for the plots
     *
     * @param {CustomEvent} event
     */
    onCloseFacets(event) {
        if (!event.detail.sender) {
            return;
        }
        if (this.plot) {
            this.plot.updateColors();
        }
    }

}

/**
 * Base class for plots
 */
class BasePlot {

    /**
     * Default configuration options
     */
    static defaults = {
        onNodeClick: undefined, // Callback function to show node details
        onGroupClick: undefined // Callback function to show group details
    };

    /**
     *  The plot object (for example a Plotly.js plot)
     */
    plot = null;

    /**
     * Constructor method
     *
     * @param {HTMLElement} canvas The plot container
     * @param {Array} values The data to plot
     * @param {object} config Configuration options to override defaults
     */
    constructor(canvas, values, config = {}) {
        this.canvas = canvas;

        this.config = {
            ...this.constructor.defaults,
            ...config,
            width:  (canvas.clientWidth || this.constructor.defaults.width),
            height: (canvas.clientHeight || this.constructor.defaults.height)
        };

        this.values = values;
    }

    /**
     * Override in child classes and call it in the constructor to create the plot
     */
    initialize() {

    }

    /**
     * Override in child classes to set plot colors
     *
     * @param {Object} data An object with property ids as keys and legend items as values.
     *                      Each legend item must contain a 'color' and 'title' property.
     */
    updateColors(data) {

    }
}

/**
 * Trace plot using Plotly.js
 */
class TracePlot extends BasePlot {
    static defaults = {
        nozero : false,  // Remove 0 values (This is just a hack. Better set them to null in the data.)
        plotType: 'bar'  // 'bar' or 'line'
    };

    constructor(canvas, values, config = {}) {
        super(canvas, values, config);
        this.initialize();
    }

    initialize() {

        let values = this.values.filter(item => (item.x !== undefined) && ((item.totals !== undefined)));

        // Remove 0
        if (this.config.nozero) {
            values = values.filter(item => (item.x !== 0));
        }

        const xValues = values.map(item => item.x);
        const yValues = values.map(item => item.totals);

        const trace = {
            x: xValues,
            y: yValues,
            type: this.config.plotType,
            mode: 'lines+markers',
            marker: { color: 'blue' },
            line: { shape: 'linear' },
        };

        const layout = {
            title: 'Timeline',
            xaxis: {
                title: 'Year',
                type: 'linear'
            },
            yaxis: {
                title: 'Count'
            }
        };

        this.plot = Plotly.newPlot(this.canvas, [trace], layout);
    }

    /**
     * Set plot colors
     *
     * @param {Object} data An object with property ids as keys and legend items as values.
     *                      Each legend item must contain a 'color' and 'title' property.
     */
    updateColors(data) {
        this.facets = data;

        // TODO: Update colors
        console.log(data);
    }
}


/**
 * Tiles plot using Plotly.js
 */
class TilesPlot extends BasePlot {
    static defaults = {
        plotType: 'bar'  // 'bar' or 'line'
    };

    constructor(canvas, values, config = {}) {
        super(canvas, values, config);
        this.initialize();
    }

    initialize() {
        const values = this.values.filter(item => ((item.x !== undefined) && (item.y !== undefined) && (item.totals !== undefined)));

        const xValues = values.map(item => item.x);
        const yValues = values.map(item => item.y);
        const totalValues = values.map(item => item.totals);

        // Extract unique x and y labels
        const uniqueX = [...new Set(xValues)];
        const uniqueY = [...new Set(yValues)];

        // Initialize z matrix with nulls
        const z = uniqueY.map(() => uniqueX.map(() => null));

        // Populate the z matrix with totalValues
        for (let i = 0; i < xValues.length; i++) {
            const xIndex = uniqueX.indexOf(xValues[i]);
            const yIndex = uniqueY.indexOf(yValues[i]);
            z[yIndex][xIndex] = totalValues[i];
        }

        // Create heatmap trace
        const trace = {
            x: uniqueX,
            y: uniqueY,
            z: z,
            type: 'heatmap',
            colorscale: 'Viridis',
            hoverongaps: false
        };

        const layout = {
            title: 'Map',
            xaxis: { title: 'X Axis' },
            yaxis: { title: 'Y Axis' }
        };

        this.plot = Plotly.newPlot(this.canvas, [trace], layout);
    }
}

/**
 * Network plot using D3.js
 *
 */
class NetworkPlot extends BasePlot {
    static defaults = {
        width: 800,
        height: 600,
        scale: 5,
        padding: 40,

        sourceColor: '#6cc24a',
        targetColor: '#4ab7c2',

        sourceRadius: 25,
        targetRadius: 10,

        // Whether to show images if available
        // See the imageBaseUrl setting in PlotWidget.showData().
        sourceImg: true,
        targetImg: true,

        fixTargets : false,
        arcs: false,

        sourceLabelMaxLength: 0,
        targetLabelMaxLength: 20,

        mode: "bimodal",

        // Forces
        fixedLength: 150,
        linkStrength: 0.9,
        centerPullStrength: 0.02,
        targetChargeStrength: -500,
        sourceChargeStrength: -20,
        forceCollide: 100,
        //
        // fixedLength: 0,
        // linkStrength: 0.001,
        // centerPullStrength: 0.01,
        // targetChargeStrength: -100,
        // sourceChargeStrength: -100,
        // forceCollide: 100,

        // Stabilization
        alphaMin: 0.03,
        alphaDecay: 0.09,

        // Auto zoom while stabilizing
        fitInterval: 100
    };

    constructor(canvas, values, config = {}) {

        super(canvas, values, config);

        if (this.config.mode !== "bimodal") {
            this.config.sourceColor  = this.config.targetColor;
            this.config.sourceRadius = this.config.targetRadius;
            this.config.sourceLabelMaxLength = this.config.targetLabelMaxLength;
            this.config.sourceChargeStrength = this.config.targetChargeStrength;

            this.values = this.twoModesToOneMode(values, true);
        }

        this.zoomInterval = null;
        this.initialized = false;
        this.nodesMap = new Map();

        this.initialize();
    }

    initialize() {
        this.initCanvas();
        this.initData();
        this.initialLayout();
        this.createSimulation();
        this.draw();
        this.zoomToFit();
    }

    initCanvas() {
        this.canvas.innerHTML = '';

        this.svg = d3.select(this.canvas)
            .append("svg")
            .attr("width", this.config.width)
            .attr("height", this.config.height);

        this.container = this.svg.append("g")
            .attr("class", "network-group");

        // Save zoom for later use
        this.zoom = d3.zoom().scaleExtent([0.1, 5]);
        this.svg.call(this.zoom.on("zoom", (event) => {
            this.container.attr("transform", event.transform);
        }));
    }

    initData() {

        const config = this.config;

        // Create nodes and links maps
        this.values.forEach(({ x, x_label, x_type, x_id, x_image, y, y_label, y_type, y_id, y_image }) => {
            if (!this.nodesMap.has(x)) this.nodesMap.set(x,
                {
                    id: x,
                    label: x_label,
                    shortLabel: this.shortenLabel(x_label, config.sourceLabelMaxLength),
                    type: x_type,
                    role: 'source',
                    dbid: x_id,
                    image: this.config.sourceImg ? x_image : false,
                    radius: config.sourceRadius,
                    color: config.sourceColor
                }
            );
            if (!this.nodesMap.has(y)) this.nodesMap.set(y,
                {
                    id: y,
                    label: y_label,
                    shortLabel: this.shortenLabel(y_label, config.targetLabelMaxLength),
                    type: y_type,
                    role: 'target',
                    dbid : y_id,
                    image: this.config.targetImg ? y_image : false,
                    radius: config.targetRadius,
                    color: config.targetColor
                }
            );
        });

        this.nodes = Array.from(this.nodesMap.values());
        // this.links = this.values.map(({ x, y, z }) => ({ source: x, target: y, weight: z }));
        this.links = this.values.map(({ x, y, z }) => ({
            source: this.nodesMap.get(x),
            target: this.nodesMap.get(y),
            weight: z
        }));

        this.nodesSorted = this.nodes.slice().sort(
            (a, b) => this.getNodeOrder(a.id) - this.getNodeOrder(b.id)
        );

    }

    initialLayout() {
        const width = this.config.width;
        const height = this.config.height;
        const padding = this.config.padding;

        // Find sources/targets
        const sourceNodes = this.nodes.filter(node => node.role === 'source');
        const targetNodes = this.nodes.filter(node => node.role === 'target');

        const N = targetNodes.length;
        const cx = width / 2;
        const cy = height / 2;

        // Calculate radius for the target ring
        const targetRadius = this.config.targetRadius;
        const factor = 1.25;

        // Minimum radius to prevent overlap for targets
        let ringRadius = (N * targetRadius * factor) / Math.PI;
        // Maximum allowed by canvas size and padding
        const maxRadius = Math.min(cx, cy) - padding - targetRadius;

        // ringRadius = Math.min(ringRadius, maxRadius);

        // Place targets in a ring
        targetNodes.forEach((node, i) => {
            const angle = 2 * Math.PI * (i / N);
            node.x = cx + Math.cos(angle) * ringRadius;
            node.y = cy + Math.sin(angle) * ringRadius;
        });

        // Place sources at center (with small jitter if more than one)
        const jitter = Math.min(12, this.config.sourceRadius);
        sourceNodes.forEach((node, i) => {
            const angle = 2 * Math.PI * (i / (sourceNodes.length || 1));
            node.x = cx + Math.cos(angle) * jitter;
            node.y = cy + Math.sin(angle) * jitter;
        });

        if (this.config.fixTargets) {
            targetNodes.forEach(node => {
                node.fx = node.x;
                node.fy = node.y;
            });
        }
    }

    initialSpiralLayout() {
        const width = this.config.width * this.config.scale;
        const height = this.config.height * this.config.scale;
        const padding = this.config.padding;

        // 1. Compute degree of each node
        const degree = {};
        this.links.forEach(link => {
            degree[link.source.id ? link.source.id : link.source] = (degree[link.source.id ? link.source.id : link.source] || 0) + 1;
            degree[link.target.id ? link.target.id : link.target] = (degree[link.target.id ? link.target.id : link.target] || 0) + 1;
        });

        // Add degree property for convenience
        this.nodes.forEach(n => n.degree = degree[n.id] || 0);

        // 2. Sort nodes by degree descending
        const sortedNodes = this.nodes.slice().sort((a, b) => b.degree - a.degree);

        // 3. Choose k central nodes (suggest: up to 5 or ~sqrt(n))
        const k = Math.min(5, Math.ceil(Math.sqrt(this.nodes.length)));
        const centerNodes = sortedNodes.slice(0, k);
        const outerNodes = sortedNodes.slice(k);

        // 4. Center point
        const cx = width / 2, cy = height / 2;

        // 5. Place central nodes tightly at the center (with jitter to prevent perfect overlap)
        const jitter = 50;
        centerNodes.forEach((node, i) => {
            const angle = (2 * Math.PI * i) / k;
            node.x = cx + Math.cos(angle) * jitter + Math.random()*2 - 1;
            node.y = cy + Math.sin(angle) * jitter + Math.random()*2 - 1;
        });

        // 6. Place outer nodes in a ring (or spiral if many nodes)
        const ringRadius = Math.min(cx, cy) - padding - 30;
        const spiral = outerNodes.length > 24; // use spiral if lots of nodes

        outerNodes.forEach((node, i) => {
            if (!spiral) {
                // Place on a circle
                const angle = (2 * Math.PI * i) / outerNodes.length;
                node.x = cx + Math.cos(angle) * ringRadius;
                node.y = cy + Math.sin(angle) * ringRadius;
            } else {
                // Place in a loose spiral, so nodes don't overlap
                const spiralTurns = 2; // how many circles
                const t = i / outerNodes.length; // 0..1
                const theta = 2*Math.PI * spiralTurns * t;
                const radius = ringRadius * (0.5 + 0.5 * t); // inner to outer
                node.x = cx + Math.cos(theta) * radius;
                node.y = cy + Math.sin(theta) * radius;
            }
        });
    }

    /**
     * Position the nodes randomly in the canvas
     */
    initialRandomLayout() {
        const width = this.config.width;
        const height = this.config.height;
        const padding = this.config.padding;

        this.nodes.forEach(node => {
            node.x = Math.random() * (width - 2 * padding) + padding;
            node.y = Math.random() * (height - 2 * padding) + padding;
        });
    }

    createSimulation() {
        const config = this.config;
        this.simulation = d3.forceSimulation(this.nodes)
            .force("link", d3.forceLink(this.links)
                .id(d => d.id)
                .distance(config.fixedLength)
                // .strength(d => config.linkStrength * d.weight)
                .strength(1)
            )
            .force("charge", d3.forceManyBody()
                .strength(d => d.type === 'properties' ? config.targetChargeStrength : config.sourceChargeStrength)
            )

            .force("collide", d3.forceCollide()
                .radius(d => d.radius)
            )
            .force("x", d3.forceX(config.width / 2).strength(config.centerPullStrength))
            .force("y", d3.forceY(config.height / 2).strength(config.centerPullStrength))
            .alphaMin(config.alphaMin)
            .alphaDecay(config.alphaDecay);

        this.simulation.on("tick", () => this.onTick());

        if (this.zoomInterval) {
            clearInterval(this.zoomInterval);
        }
        this.zoomInterval = setInterval(() => this.zoomToFit(), config.fitInterval);

        this.simulation.on("end", () => {
            if (this.zoomInterval) {
                clearInterval(this.zoomInterval);
                this.zoomInterval = null;
                this.initialized = true;
            }

            if (!this.initialized) {
                this.zoomToFit();
            }
            this.initialized = true;
        });
    }


    getNodeOrder(id) {
        const node = this.nodesMap.get(id);
        if (node && (node.role == "source")) {
            return 0;
        }
        else if (node && (node.role == "source")) {
            return 1;
        }
        return 2;
    }

    shortenLabel(label, maxLength) {
        if ((maxLength === 0) || (label == undefined)) {
            return '';
        }
        return label.length > maxLength ? label.slice(0, maxLength) + '…' : label;
    }

    linkArcPath(link) {
        const sx = link.source.x, sy = link.source.y;
        const tx = link.target.x, ty = link.target.y;

        const dx = tx - sx, dy = ty - sy;
        const dr = Math.sqrt(dx * dx + dy * dy);

        // If the link is "short", use a small radius;
        // if it's long, this will still look good.
        const arc =
            `M${sx},${sy}A${dr},${dr} 0 0,1 ${tx},${ty}`;
        return arc;
    }

    draw() {

        // Edges
        if (this.config.arcs) {
            this.link = this.container.append("g")
                .selectAll("path")
                .data(this.links)
                .join("path")
                .attr("stroke-width", e => 2 * e.weight)
                .attr("fill", "none")
                .attr("stroke", "#aaa")
                .attr("d", d => this.linkArcPath(d));
        } else {
            this.link = this.container.append("g")
                .attr("stroke", "#aaa")
                .selectAll("line")
                .data(this.links)
                .join("line")
                .attr("stroke-width", e => 2 * e.weight)
                .attr("x1", d => d.source.x)
                .attr("y1", d => d.source.y)
                .attr("x2", d => d.target.x)
                .attr("y2", d => d.target.y);
        }

        // Nodes
        // One parent <g> per node
        this.node = this.container.append("g")
            .selectAll("g")
            .data(this.nodesSorted)
            .join("g")
            .attr("transform", d => `translate(${d.x}, ${d.y})`)
            .style("cursor", "pointer")
            .on("click", (event, d) => this.showNode(d))
            .call(this.drag(this.simulation));

        // Draw images for nodes with image URL
        const imageNodes = this.node.filter(d => d.image);

        imageNodes.append("rect")
            .attr("x", d => -d.radius - 2)
            .attr("y", d => -d.radius - 2)
            .attr("width", d => (d.radius * 2) + 4)
            .attr("height", d => (d.radius * 2) + 4)
            .attr("rx", 6) // rounded corners
            .attr("ry", 6) // rounded corners
            .attr("fill", "#E9FFDBFF")
            .attr("stroke", "#fff")
            .attr("stroke-width", 2);

        imageNodes.append("image")
            .attr("href", d => this.config.imageBaseUrl + d.image)
            .attr("width", d => d.radius * 2)
            .attr("height", d => d.radius * 2)
            .attr("x", d => -d.radius)
            .attr("y", d => -d.radius)
            .append("title")
            .text(d => d.label);

        // Draw circles for nodes without image URL
        this.node.filter(d => !d.image)
            .append("circle")
            .attr("stroke", "#fff")
            .attr("stroke-width", 1.5)
            .attr("r", d => d.radius)
            .attr("cx", 0)
            .attr("cy", 0)
            .attr("fill", d => d.color)
            .append("title")
            .text(d => d.label);

        // Node labels
        this.label = this.container.append("g")
            .selectAll("text")
            .data(this.nodesSorted)
            .join("text")
            .text(d => d.shortLabel)
            .attr("font-size", 12)
            .attr("x", d => d.x)
            .attr("y", d => d.y)
            .attr('text-anchor', 'middle')  // Centered horizontally
            .attr("dy", d => d.radius + 16)  // Placed below node
            .style("display", d => (d.shortLabel == "" ? "none" : "block"));
    }

    onTick() {
        const config = this.config;
        this.links.forEach(link => {
            const dx = link.target.x - link.source.x;
            const dy = link.target.y - link.source.y;
            const dist = Math.sqrt(dx * dx + dy * dy);
            if (dist === 0) return;

            let offsetX = 0;
            let offsetY = 0;
            if (config.fixedLength) {
                const diff = (dist - config.fixedLength) / 2;
                offsetX = diff * (dx / dist);
                offsetY = diff * (dy / dist);
            }

            if (!link.source.fx) link.source.x += offsetX;
            if (!link.source.fy) link.source.y += offsetY;
            if (!link.target.fx) link.target.x -= offsetX;
            if (!link.target.fy) link.target.y -= offsetY;
        });

        this.link
            .attr("x1", d => d.source.x)
            .attr("y1", d => d.source.y)
            .attr("x2", d => d.target.x)
            .attr("y2", d => d.target.y);

        if (this.config.arcs) {
            this.link.attr("d", d => this.linkArcPath(d));
        }

        this.node
            .attr("transform", d => `translate(${d.x}, ${d.y})`);
            //.attr("cx", d => d.x)
            //.attr("cy", d => d.y);

        this.label
            .attr("x", d => d.x)
            .attr("y", d => d.y);
    }

    drag(simulation) {
        const threshold = 3; // Minimum pixels moved before restarting simulation
        return d3.drag()
            .on("start", function (event, d) {
                d.__drag_init = { x: event.x, y: event.y, fired: false };
                d.fx = d.x;
                d.fy = d.y;
            })
            .on("drag", function (event, d) {
                if (!d.__drag_init) return;
                const dx = event.x - d.__drag_init.x;
                const dy = event.y - d.__drag_init.y;
                if (!d.__drag_init.fired && (dx * dx + dy * dy > threshold * threshold)) {
                    simulation.alphaTarget(0.3).restart();
                    d.__drag_init.fired = true;
                }
                d.fx = event.x;
                d.fy = event.y;
            })
            .on("end", function (event, d) {
                if (!event.active) simulation.alphaTarget(0);
                d.fx = null;
                d.fy = null;
                delete d.__drag_init;
            });
    }

    // --- ZOOM TO FIT METHOD ---
    zoomToFit(padding = 50) {
        // Compute bounding box of all nodes
        const nodes = this.nodes;
        if (nodes.length === 0) return;
        let minX = d3.min(nodes, d => d.x),
            maxX = d3.max(nodes, d => d.x),
            minY = d3.min(nodes, d => d.y),
            maxY = d3.max(nodes, d => d.y);

        // Prevent zero-size box
        if (minX === maxX) { minX -= 1; maxX += 1; }
        if (minY === maxY) { minY -= 1; maxY += 1; }

        // Add padding
        minX -= padding;
        maxX += padding;
        minY -= padding;
        maxY += padding;

        const width = this.config.width;
        const height = this.config.height;
        const boxWidth = maxX - minX;
        const boxHeight = maxY - minY;
        const scale = Math.min(width / boxWidth, height / boxHeight);

        // Centering translate
        const translate = [
            width/2 - scale * (minX + maxX) / 2,
            height/2 - scale * (minY + maxY) / 2
        ];

        // Perform transition
        this.svg.transition()
            .duration(700)
            .call(
                this.zoom.transform,
                d3.zoomIdentity
                    .translate(translate[0], translate[1])
                        .scale(scale)
                    );
    }

    /**
     * Show property or article in the sidebar
     *
     * @param {Object} node
     */
    showNode(node) {
        if (this.config.onNodeClick) {
            this.config.onNodeClick(node);
        }
    }

    /**
     * Transform bimodal to unimodal network
     *
     * @param {Array} values
     * @param {Boolean} flip Whether to flip source and target
     * @returns {Array}
     */
    twoModesToOneMode(values, flip = false) {

        const nodes = {};  // x => {x_label, x_type}
        const yToXs = {};

        values.forEach(item => {

            // Nodes
            if (!nodes[item.x]) {
                nodes[item.x] = {
                    id: item.x_id, label: item.x_label, type: item.x_type
                };
            }
            if (!nodes[item.y]) {
                nodes[item.y] = {
                    id: item.y_id, label: item.y_label, type: item.y_type
                };
            }

            // Collapse
            if (flip) {
                if (!yToXs[item.x]) {
                    yToXs[item.x] = [];
                }
                yToXs[item.x].push(item.y);
            }
            else {
                if (!yToXs[item.y]) {
                    yToXs[item.y] = [];
                }
                yToXs[item.y].push(item.x);
            }

        });

        // Edges
        const edgeMap = {};
        for (const y in yToXs) {
            const xs = yToXs[y];
            for (let i = 0; i < xs.length; i++) {
                for (let j = i + 1; j < xs.length; j++) {
                    const source = xs[i] < xs[j] ? xs[i] : xs[j];
                    const target = xs[i] < xs[j] ? xs[j] : xs[i];
                    const key = `${source}|${target}`;
                    if (!edgeMap[key]) {
                        edgeMap[key] = {
                            x: source,
                            x_id : nodes[source].id,
                            x_label: nodes[source].label,
                            x_type: nodes[source].type,
                            y: target,
                            y_id: nodes[target].id,
                            y_label: nodes[target].label,
                            y_type: nodes[target].type,
                            z: 0
                        };
                    }
                    edgeMap[key].z += 1;
                }
            }
        }

        return Object.values(edgeMap);
    }
}

window.App.widgetClasses = window.App.widgetClasses || {};
window.App.widgetClasses['plot'] = PlotWidget;
