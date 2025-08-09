{{-- resources/views/components/modal-upload-shapefile.blade.php --}}
@props([
    'modalId' => 'uploadShapefileModal',
    'title' => 'Upload Shapefile',
    'acceptedFiles' => '.shp,.zip'
])

<!-- Modal -->
<dialog id="{{ $modalId }}" class="modal">
    <div class="modal-box w-11/12 max-w-5xl">
        <form method="dialog">
            <button class="btn btn-sm btn-circle btn-ghost absolute right-2 top-2">✕</button>
        </form>
        
        <h3 class="font-bold text-lg mb-4">{{ $title }}</h3>
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Upload Section -->
            <div>
                <form id="uploadShapefileForm" enctype="multipart/form-data">
                    @csrf
                    <div class="form-control w-full mb-4">
                        <label class="label">
                            <span class="label-text">Select Shapefile</span>
                        </label>
                        <input type="file" 
                               name="shp_file" 
                               class="file-input file-input-bordered w-full" 
                               accept="{{ $acceptedFiles }}"
                               required />
                        <label class="label">
                            <span class="label-text-alt text-gray-500">Upload .shp or .zip file</span>
                        </label>
                    </div>
                    
                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary flex-1">
                            <span class="loading loading-spinner loading-sm hidden"></span>
                            Upload & Preview
                        </button>
                        <button type="button" class="btn" onclick="document.getElementById('{{ $modalId }}').close()">Cancel</button>
                    </div>
                </form>
                
                <!-- Alert untuk menampilkan pesan -->
                <div id="uploadAlert" class="alert hidden mt-4">
                    <span id="alertMessage"></span>
                </div>
            </div>

            <!-- Mapping Section -->
            <div id="mappingSection" class="hidden">
                <h4 class="font-semibold mb-3">Field Mapping</h4>
                
                <!-- Info about auto-mapping class -->
                <div class="alert alert-info mb-4">
                    <span class="text-sm">Only inputs with class "auto-mapping" will be mapped</span>
                </div>
                
                <!-- Preview Data -->
                <div class="bg-base-200 p-3 rounded mb-4">
                    <h5 class="font-medium mb-2">Available Fields:</h5>
                    <div id="availableFields" class="text-sm text-gray-600"></div>
                </div>

                <!-- Mapping Form -->
                <form id="mappingForm">
                    <div id="mappingFields" class="space-y-3">
                        <!-- Dynamic mapping fields will be added here -->
                    </div>
                    
                    <div class="flex gap-2 mt-4">
                        <button type="button" id="applyMappingBtn" class="btn btn-success flex-1">
                            Apply Mapping
                        </button>
                        <button type="button" id="autoMapBtn" class="btn btn-outline">
                            Auto Map
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <form method="dialog" class="modal-backdrop">
        <button>close</button>
    </form>
</dialog>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('uploadShapefileForm');
    const modal = document.getElementById('{{ $modalId }}');
    const alert = document.getElementById('uploadAlert');
    const alertMessage = document.getElementById('alertMessage');
    const submitBtn = form.querySelector('button[type="submit"]');
    const spinner = submitBtn.querySelector('.loading');
    const mappingSection = document.getElementById('mappingSection');
    const mappingForm = document.getElementById('mappingForm');
    const availableFields = document.getElementById('availableFields');
    const mappingFields = document.getElementById('mappingFields');
    const applyMappingBtn = document.getElementById('applyMappingBtn');
    const autoMapBtn = document.getElementById('autoMapBtn');
    
    let currentGeoJsonData = null;
    let targetInputs = [];

    // Open modal handler
    const modalBtn = document.getElementById('uploadShapefileModalBtn');
    if (modalBtn) {
        modalBtn.addEventListener('click', function () {
            modal.showModal();
            // Scan for target inputs with auto-mapping class when modal opens
            targetInputs = Array.from(document.querySelectorAll('.auto-mapping'));
            console.log(`Found ${targetInputs.length} input(s) with auto-mapping class`);
        });
    }

    // Upload form handler
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const formData = new FormData(form);
        
        // Show loading state
        submitBtn.disabled = true;
        spinner.classList.remove('hidden');
        hideAlert();
        
        try {
            const response = await fetch('/admin/loadshp', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                }
            });
            
            const data = await response.json();
            
            if (data.success) {
                currentGeoJsonData = data.geojson;
                showAlert('success', 'File uploaded successfully! Configure field mapping below.');
                
                // Show mapping section
                showMappingSection(data.geojson);
                
            } else {
                showAlert('error', data.message || 'Upload failed!');
            }
            
        } catch (error) {
            console.error('Error:', error);
            showAlert('error', 'An error occurred while uploading the file.');
        } finally {
            // Hide loading state
            submitBtn.disabled = false;
            spinner.classList.add('hidden');
        }
    });

    // Show mapping section with available fields
    function showMappingSection(geojson) {
        if (!geojson || !geojson.features || geojson.features.length === 0) {
            showAlert('error', 'No features found in the uploaded file');
            return;
        }

        const firstFeature = geojson.features[0];
        const properties = firstFeature.properties || {};
        const geometry = firstFeature.geometry || {};

        // Show available fields
        const fieldList = Object.keys(properties).map(key => 
            `<span class="badge badge-outline mr-1 mb-1">${key}</span>`
        ).join('');
        availableFields.innerHTML = fieldList || '<span class="text-gray-500">No properties found</span>';

        // Generate mapping fields based on auto-mapping inputs
        generateMappingFields(properties);
        
        mappingSection.classList.remove('hidden');
    }

    // Generate mapping fields based on auto-mapping inputs
    function generateMappingFields(availableProperties) {
        mappingFields.innerHTML = '';
        
        if (targetInputs.length === 0) {
            mappingFields.innerHTML = '<p class="text-warning">No inputs with class "auto-mapping" found on the page.</p>';
            return;
        }

        const propertyKeys = Object.keys(availableProperties);
        
        targetInputs.forEach(input => {
            const inputName = input.name;
            if (!inputName || inputName.startsWith('_')) return; // Skip unnamed or CSRF inputs
            
            const mappingDiv = document.createElement('div');
            mappingDiv.className = 'form-control';
            
            mappingDiv.innerHTML = `
                <label class="label">
                    <span class="label-text font-medium">${inputName}</span>
                </label>
                <select name="mapping_${inputName}" class="select select-bordered select-sm">
                    <option value="">-- Don't Map --</option>
                    ${propertyKeys.map(key => 
                        `<option value="${key}" ${key.toLowerCase() === inputName.toLowerCase() ? 'selected' : ''}>${key}</option>`
                    ).join('')}
                    <option value="__geometry_type">Geometry Type</option>
                    <option value="__coordinates">Coordinates (JSON)</option>
                    <option value="__coordinates_array">Coordinates (Array)</option>
                    <option value="__bbox">Bounding Box (JSON)</option>
                    <option value="__geojson">Full GeoJSON</option>
                </select>
            `;
            
            mappingFields.appendChild(mappingDiv);
        });
    }

    // Auto mapping button handler
    autoMapBtn.addEventListener('click', function() {
        const selects = mappingFields.querySelectorAll('select');
        selects.forEach(select => {
            const inputName = select.name.replace('mapping_', '');
            const options = Array.from(select.options);
            
            // Try to find exact match first
            let matchedOption = options.find(option => 
                option.value.toLowerCase() === inputName.toLowerCase()
            );
            
            // If no exact match, try partial match
            if (!matchedOption) {
                matchedOption = options.find(option => 
                    option.value.toLowerCase().includes(inputName.toLowerCase()) ||
                    inputName.toLowerCase().includes(option.value.toLowerCase())
                );
            }
            
            if (matchedOption) {
                select.value = matchedOption.value;
            }
        });
        
        showAlert('success', 'Auto-mapping applied based on field name similarity');
    });

    // Apply mapping button handler
    applyMappingBtn.addEventListener('click', function() {
        if (!currentGeoJsonData) {
            showAlert('error', 'No data to apply mapping');
            return;
        }

        const mappingConfig = getMappingConfig();
        applyMappingToInputs(currentGeoJsonData, mappingConfig);
        
        showAlert('success', `Mapping applied to ${targetInputs.length} input(s)!`);
        
        // Close modal after 2 seconds
        setTimeout(() => {
            modal.close();
            hideAlert();
            mappingSection.classList.add('hidden');
            form.reset();
        }, 2000);
    });

    // Get mapping configuration from form
    function getMappingConfig() {
        const config = {};
        const selects = mappingFields.querySelectorAll('select');
        
        selects.forEach(select => {
            const inputName = select.name.replace('mapping_', '');
            const sourceField = select.value;
            
            if (sourceField) {
                config[inputName] = sourceField;
            }
        });
        
        return config;
    }

    // Helper function to flatten coordinates recursively
    function flattenCoordinates(coords) {
        if (!Array.isArray(coords)) return [];
        
        let flattened = [];
        for (let item of coords) {
            if (Array.isArray(item) && item.length > 0 && Array.isArray(item[0])) {
                // If item is an array of arrays, recurse
                flattened = flattened.concat(flattenCoordinates(item));
            } else if (Array.isArray(item) && item.length >= 2 && typeof item[0] === 'number') {
                // If item is a coordinate pair [x, y] or [x, y, z]
                flattened.push(item);
            }
        }
        return flattened;
    }

    // Apply mapping to target inputs
    function applyMappingToInputs(geojson, mappingConfig) {
        if (!geojson.features || geojson.features.length === 0) return;
        
        const firstFeature = geojson.features[0];
        const properties = firstFeature.properties || {};
        const geometry = firstFeature.geometry || {};

        targetInputs.forEach(input => {
            const inputName = input.name;
            const sourceField = mappingConfig[inputName];
            
            if (sourceField) {
                let value = '';
                
                // Handle special fields
                switch(sourceField) {
                    case '__geometry_type':
                        value = geometry.type || '';
                        break;
                    case '__coordinates':
                        value = geometry.coordinates ? JSON.stringify(geometry.coordinates) : '';
                        break;
                    case '__coordinates_array':
                        // Flatten coordinates into a simple array
                        if (geometry.coordinates) {
                            const flattened = flattenCoordinates(geometry.coordinates);
                            value = JSON.stringify(flattened);
                        }
                        break;
                    case '__bbox':
                        value = geometry.bbox ? JSON.stringify(geometry.bbox) : '';
                        break;
                    case '__geojson':
                        value = JSON.stringify(geojson);
                        break;
                    default:
                        value = properties[sourceField] || '';
                }
                
                // Set value based on input type
                if (input.type === 'checkbox') {
                    input.checked = Boolean(value);
                } else if (input.type === 'radio') {
                    if (input.value === String(value)) {
                        input.checked = true;
                    }
                } else {
                    input.value = value;
                }
                
                // Trigger change event
                input.dispatchEvent(new Event('change', { bubbles: true }));
            }
        });

        // Dispatch custom event on document for global listeners
        document.dispatchEvent(new CustomEvent('shapefileLoaded', {
            detail: {
                geojson: geojson,
                properties: properties,
                geometry: geometry,
                mappingConfig: mappingConfig,
                targetInputs: targetInputs
            }
        }));
    }

    function showAlert(type, message) {
        alert.className = `alert mt-4`;
        
        if (type === 'success') {
            alert.classList.add('alert-success');
        } else if (type === 'error') {
            alert.classList.add('alert-error');
        } else if (type === 'warning') {
            alert.classList.add('alert-warning');
        }
        
        alertMessage.textContent = message;
        alert.classList.remove('hidden');
    }

    function hideAlert() {
        alert.classList.add('hidden');
        alert.className = 'alert hidden mt-4';
    }
});
</script>