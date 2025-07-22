/**
 * Universal Map Manager
 * A reusable framework for managing Leaflet maps with dynamic layers
 * 
 * Features:
 * - Dynamic layer management
 * - Configurable data sources
 * - Custom renderers
 * - Event system
 * - Plugin architecture
 * - State management
 * 
 * @author Your Name
 * @version 1.0.0
 */

class UniversalMapManager {
    constructor(config = {}) {
        this.config = this.mergeConfig(config);
        this.map = null;
        this.layers = new Map();
        this.layerConfigs = new Map();
        this.eventHandlers = new Map();
        this.plugins = new Map();
        this.state = {
            initialized: false,
            currentBaseLayer: null,
            activeLayers: new Set(),
            filters: new Map()
        };
        
        this.init();
    }

    // ==================== CONFIGURATION ====================
    mergeConfig(userConfig) {
        const defaultConfig = {
            mapId: 'map',
            center: [0, 0],
            zoom: 10,
            zoomControl: false,
            zoomControlPosition: 'topright',
            
            // Base layers
            baseLayers: {
                street: {
                    url: 'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
                    options: { attribution: '© OpenStreetMap contributors' }
                },
                satellite: {
                    url: 'https://{s}.tile.opentopomap.org/{z}/{x}/{y}.png',
                    options: { attribution: '© OpenTopoMap contributors' }
                },
                terrain: {
                    url: 'https://{s}.tile.openstreetmap.fr/hot/{z}/{x}/{y}.png',
                    options: { attribution: '© OpenStreetMap contributors' }
                }
            },
            
            // Default icons
            icons: {
                default: {
                    iconUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-icon.png',
                    iconSize: [25, 41],
                    iconAnchor: [12, 41],
                    popupAnchor: [1, -34],
                    shadowUrl: 'https://unpkg.com/leaflet@1.9.4/dist/images/marker-shadow.png',
                    shadowSize: [41, 41]
                }
            },
            
            // API endpoints
            endpoints: {},
            
            // UI selectors
            selectors: {
                loader: '#loader',
                sidebar: '#sidebar',
                sidebarToggle: '#sidebarToggle'
            },
            
            // Event callbacks
            callbacks: {
                onInit: null,
                onLayerAdd: null,
                onLayerRemove: null,
                onError: null
            }
        };

        return this.deepMerge(defaultConfig, userConfig);
    }

    deepMerge(target, source) {
        const result = { ...target };
        for (const key in source) {
            if (source[key] && typeof source[key] === 'object' && !Array.isArray(source[key])) {
                result[key] = this.deepMerge(target[key] || {}, source[key]);
            } else {
                result[key] = source[key];
            }
        }
        return result;
    }

    // ==================== INITIALIZATION ====================
    async init() {
        try {
            // Check if map element exists before proceeding
            const mapElement = document.getElementById(this.config.mapId);
            if (!mapElement) {
                console.warn(`Map element with id '${this.config.mapId}' not found. Skipping map initialization.`);
                return;
            }

            await this.initMap();
            this.initBaseLayers();
            this.initUI();
            this.setupEventListeners();
            this.state.initialized = true;
            
            if (this.config.callbacks.onInit) {
                await this.config.callbacks.onInit(this);
            }
        } catch (error) {
            this.handleError('Initialization failed', error);
        }
    }

    async initMap() {
        const mapElement = document.getElementById(this.config.mapId);
        if (!mapElement) {
            return; // Skip initialization if element not found
        }

        // Check if map is already initialized on this container
        if (mapElement._leaflet_id) {
            console.warn('Map container already initialized. Cleaning up...');
            // Remove existing map instance
            if (mapElement._leaflet) {
                mapElement._leaflet.remove();
            }
        }

        try {
            this.map = L.map(this.config.mapId, { 
                zoomControl: this.config.zoomControl 
            }).setView(this.config.center, this.config.zoom);

            if (!this.config.zoomControl) {
                L.control.zoom({ position: this.config.zoomControlPosition }).addTo(this.map);
            }
        } catch (error) {
            console.error('Map initialization error:', error);
            throw error;
        }
    }

    initBaseLayers() {
        const firstLayerKey = Object.keys(this.config.baseLayers)[0];
        const firstLayer = this.config.baseLayers[firstLayerKey];
        
        this.state.currentBaseLayer = L.tileLayer(firstLayer.url, firstLayer.options).addTo(this.map);
    }

    initUI() {
        this.initSidebar();
    }

    initSidebar() {
        const sidebar = document.querySelector(this.config.selectors.sidebar);
        const sidebarToggle = document.querySelector(this.config.selectors.sidebarToggle);
        
        if (sidebar && sidebarToggle) {
            sidebarToggle.addEventListener('click', () => {
                sidebar.classList.toggle('open');
            });

            document.addEventListener('click', (e) => {
                if (window.innerWidth <= 768 && 
                    !sidebar.contains(e.target) && 
                    !sidebarToggle.contains(e.target)) {
                    sidebar.classList.remove('open');
                }
            });
        }
    }

    setupEventListeners() {
        // Map events
        this.map.on('moveend zoomend', () => {
            this.emit('mapUpdate', {
                center: this.map.getCenter(),
                zoom: this.map.getZoom(),
                bounds: this.map.getBounds()
            });
        });
    }

    // ==================== LAYER MANAGEMENT ====================
    
    /**
     * Register a layer configuration
     * @param {string} layerName - Unique layer name
     * @param {Object} config - Layer configuration
     */
    registerLayer(layerName, config) {
        const defaultLayerConfig = {
            type: 'geojson', // 'geojson', 'marker', 'custom'
            endpoint: null,
            renderer: null,
            style: {},
            icon: 'default',
            cluster: false,
            fitBounds: false,
            zIndex: 1,
            visible: false
        };

        this.layerConfigs.set(layerName, { ...defaultLayerConfig, ...config });
        return this;
    }

    /**
     * Toggle layer visibility
     * @param {string} layerName - Layer name
     * @param {boolean} visible - Show/hide layer
     * @param {Object} options - Additional options
     */
    async toggleLayer(layerName, visible, options = {}) {
        try {
            if (visible) {
                await this.showLayer(layerName, options);
            } else {
                this.hideLayer(layerName);
            }
        } catch (error) {
            this.handleError(`Toggle layer ${layerName}`, error);
        }
    }

    /**
     * Show layer
     * @param {string} layerName - Layer name
     * @param {Object} options - Options
     */
    async showLayer(layerName, options = {}) {
        const config = this.layerConfigs.get(layerName);
        if (!config) {
            throw new Error(`Layer '${layerName}' not registered`);
        }

        // Hide layer if already exists
        this.hideLayer(layerName);

        // Load data
        const data = await this.loadLayerData(layerName, options);
        
        // Create layer group
        const layerGroup = L.layerGroup();
        
        // Render data
        await this.renderLayer(layerGroup, data, layerName, { ...config, ...options });
        
        // Add to map
        layerGroup.addTo(this.map);
        
        // Store reference
        this.layers.set(layerName, layerGroup);
        this.state.activeLayers.add(layerName);

        // Fit bounds if requested
        if (config.fitBounds || options.fitBounds) {
            this.fitBoundsToLayer(layerGroup);
        }

        // Trigger callback
        if (this.config.callbacks.onLayerAdd) {
            this.config.callbacks.onLayerAdd(layerName, layerGroup, data);
        }

        this.emit('layerAdded', { layerName, layer: layerGroup, data });
    }

    /**
     * Hide layer
     * @param {string} layerName - Layer name
     */
    hideLayer(layerName) {
        const layer = this.layers.get(layerName);
        if (layer && this.map.hasLayer(layer)) {
            this.map.removeLayer(layer);
            this.layers.delete(layerName);
            this.state.activeLayers.delete(layerName);

            if (this.config.callbacks.onLayerRemove) {
                this.config.callbacks.onLayerRemove(layerName, layer);
            }

            this.emit('layerRemoved', { layerName, layer });
        }
    }

    /**
     * Load layer data
     * @param {string} layerName - Layer name
     * @param {Object} options - Options including filters
     */
    async loadLayerData(layerName, options = {}) {
        const config = this.layerConfigs.get(layerName);
        
        if (options.data) {
            return options.data;
        }

        if (!config.endpoint) {
            throw new Error(`No endpoint defined for layer '${layerName}'`);
        }

        let url = config.endpoint;
        if (options.params) {
            url += `?${new URLSearchParams(options.params).toString()}`;
        }

        const response = await fetch(url);
        if (!response.ok) {
            throw new Error(`Failed to load data for layer '${layerName}': ${response.statusText}`);
        }

        const data = await response.json();
        return data.features || data;
    }

    /**
     * Render layer data
     * @param {L.LayerGroup} layerGroup - Target layer group
     * @param {Array} data - Data to render
     * @param {string} layerName - Layer name
     * @param {Object} config - Layer configuration
     */
    async renderLayer(layerGroup, data, layerName, config) {
        if (config.renderer && typeof config.renderer === 'function') {
            // Custom renderer
            await config.renderer(layerGroup, data, config, this);
        } else {
            // Built-in renderers
            switch (config.type) {
                case 'geojson':
                    this.renderGeoJSON(layerGroup, data, config);
                    break;
                case 'marker':
                    this.renderMarkers(layerGroup, data, config);
                    break;
                default:
                    this.renderGeoJSON(layerGroup, data, config);
            }
        }
    }

    /**
     * Built-in GeoJSON renderer
     */
    renderGeoJSON(layerGroup, data, config) {
        data.forEach(feature => {
            if (!feature || !feature.geometry) return;

            const layer = L.geoJSON(feature, {
                style: config.style,
                pointToLayer: (feature, latlng) => {
                    const icon = this.getIcon(config.icon);
                    return L.marker(latlng, { icon });
                },
                onEachFeature: (feature, layer) => {
                    if (config.popup) {
                        const popupContent = this.generatePopupContent(feature, config);
                        layer.bindPopup(popupContent);
                    }
                    
                    if (config.onClick) {
                        layer.on('click', (e) => config.onClick(feature, layer, e));
                    }
                }
            });

            layer.addTo(layerGroup);
        });
    }

    /**
     * Built-in marker renderer
     */
    renderMarkers(layerGroup, data, config) {
        data.forEach(item => {
            if (!item.latitude || !item.longitude) return;

            const icon = this.getIcon(config.icon);
            const marker = L.marker([item.latitude, item.longitude], { icon });

            if (config.popup) {
                const popupContent = this.generatePopupContent(item, config);
                marker.bindPopup(popupContent);
            }

            if (config.onClick) {
                marker.on('click', (e) => config.onClick(item, marker, e));
            }

            marker.addTo(layerGroup);
        });
    }

    /**
     * Generate popup content
     */
    generatePopupContent(data, config) {
        if (typeof config.popup === 'function') {
            return config.popup(data);
        }

        if (typeof config.popup === 'string') {
            return config.popup;
        }

        // Auto-generate from data
        const properties = data.properties || data;
        const title = properties.name || properties.title || 'Feature';
        let content = `<b>${title}</b>`;

        if (properties.description) {
            content += `<br>${properties.description}`;
        }

        return content;
    }

    /**
     * Get icon by name
     */
    getIcon(iconName) {
        const iconConfig = this.config.icons[iconName] || this.config.icons.default;
        return L.icon(iconConfig);
    }

    /**
     * Fit map to layer bounds
     */
    fitBoundsToLayer(layer) {
        const group = new L.featureGroup([layer]);
        const bounds = group.getBounds();
        if (bounds.isValid()) {
            this.map.fitBounds(bounds);
        }
    }

    // ==================== BASE LAYER MANAGEMENT ====================
    
    /**
     * Switch base layer
     * @param {string} layerName - Base layer name
     */
    switchBaseLayer(layerName) {
        const layerConfig = this.config.baseLayers[layerName];
        if (!layerConfig) {
            throw new Error(`Base layer '${layerName}' not found`);
        }

        if (this.state.currentBaseLayer) {
            this.map.removeLayer(this.state.currentBaseLayer);
        }

        this.state.currentBaseLayer = L.tileLayer(layerConfig.url, layerConfig.options).addTo(this.map);
        this.emit('baseLayerChanged', { layerName, layer: this.state.currentBaseLayer });
    }

    // ==================== FILTER SYSTEM ====================
    
    /**
     * Set filter for layer
     * @param {string} layerName - Layer name
     * @param {Object} filters - Filter parameters
     */
    setLayerFilter(layerName, filters) {
        this.state.filters.set(layerName, filters);
        
        // Reload layer if it's currently visible
        if (this.state.activeLayers.has(layerName)) {
            this.toggleLayer(layerName, true, { params: filters });
        }
    }

    /**
     * Get layer filter
     */
    getLayerFilter(layerName) {
        return this.state.filters.get(layerName) || {};
    }

    // ==================== EVENT SYSTEM ====================
    
    /**
     * Add event listener
     * @param {string} event - Event name
     * @param {Function} handler - Event handler
     */
    on(event, handler) {
        if (!this.eventHandlers.has(event)) {
            this.eventHandlers.set(event, []);
        }
        this.eventHandlers.get(event).push(handler);
        return this;
    }

    /**
     * Remove event listener
     * @param {string} event - Event name
     * @param {Function} handler - Event handler
     */
    off(event, handler) {
        if (this.eventHandlers.has(event)) {
            const handlers = this.eventHandlers.get(event);
            const index = handlers.indexOf(handler);
            if (index > -1) {
                handlers.splice(index, 1);
            }
        }
        return this;
    }

    /**
     * Emit event
     * @param {string} event - Event name
     * @param {*} data - Event data
     */
    emit(event, data) {
        if (this.eventHandlers.has(event)) {
            this.eventHandlers.get(event).forEach(handler => {
                try {
                    handler(data);
                } catch (error) {
                    console.error(`Event handler error for '${event}':`, error);
                }
            });
        }
    }

    // ==================== PLUGIN SYSTEM ====================
    
    /**
     * Register plugin
     * @param {string} name - Plugin name
     * @param {Object} plugin - Plugin object
     */
    use(name, plugin) {
        if (typeof plugin.install === 'function') {
            plugin.install(this);
        }
        this.plugins.set(name, plugin);
        return this;
    }

    /**
     * Get plugin
     * @param {string} name - Plugin name
     */
    getPlugin(name) {
        return this.plugins.get(name);
    }

    // ==================== UTILITY METHODS ====================
    
    /**
     * Get map instance
     */
    getMap() {
        return this.map || null;
    }

    /**
     * Get current state
     */
    getState() {
        return { ...this.state };
    }

    /**
     * Get layer
     * @param {string} layerName - Layer name
     */
    getLayer(layerName) {
        return this.layers.get(layerName);
    }

    /**
     * Check if layer is visible
     * @param {string} layerName - Layer name
     */
    isLayerVisible(layerName) {
        return this.state.activeLayers.has(layerName);
    }

    /**
     * Get all visible layers
     */
    getVisibleLayers() {
        return Array.from(this.state.activeLayers);
    }

    /**
     * Clear all layers
     */
    clearAllLayers() {
        this.state.activeLayers.forEach(layerName => {
            this.hideLayer(layerName);
        });
    }

    /**
     * Handle errors
     */
    handleError(context, error) {
        console.error(`${context}:`, error);
        
        if (this.config.callbacks.onError) {
            this.config.callbacks.onError(context, error);
        }

        this.emit('error', { context, error });
    }

    /**
     * Destroy map manager
     */
    destroy() {
        this.clearAllLayers();
        this.eventHandlers.clear();
        this.plugins.clear();
        this.layerConfigs.clear();
        
        if (this.map) {
            this.map.remove();
            this.map = null;
        }
        
        this.state.initialized = false;
    }
}

// ==================== EXPORT ====================
// For ES6 modules
// export default UniversalMapManager;

// For CommonJS
// module.exports = UniversalMapManager;

// For browser global
if (typeof window !== 'undefined') {
    window.UniversalMapManager = UniversalMapManager;
}