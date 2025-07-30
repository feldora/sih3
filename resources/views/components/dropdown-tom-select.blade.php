@props([
    'name',
    'label' => null,
    'selected' => null,
    'placeholder' => 'Select option...',
    'error' => null,
    'id' => null,
    'ajaxUrl' => null,
    'options' => [], // untuk static options
    'multiple' => false,
    'required' => false,
    'disabled' => false,
    'searchable' => true,
    'clearable' => true,
    'createOnBlur' => false,
    'maxItems' => null,
    'SearchFlter' => "",
])

@php
    $id = $id ?? 'tomselect-' . uniqid();
    $selectName = $multiple ? $name . '[]' : $name;
@endphp

<div class="mb-4">
    @if($label)
        <label for="{{ $id }}" class="block mb-1 text-sm font-medium text-gray-700">
            {{ $label }}
            @if($required)
                <span class="text-red-500">*</span>
            @endif
        </label>
    @endif
    
    <select
        id="{{ $id }}"
        name="{{ $selectName }}"
        data-placeholder="{{ $placeholder }}"
        data-ajax-url="{{ $ajaxUrl }}"
        data-multiple="{{ $multiple ? 'true' : 'false' }}"
        data-searchable="{{ $searchable ? 'true' : 'false' }}"
        data-clearable="{{ $clearable ? 'true' : 'false' }}"
        data-create-on-blur="{{ $createOnBlur ? 'true' : 'false' }}"
        data-max-items="{{ $maxItems }}"
        class="tomselect-input form-control border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 @error($name) border-red-500 @enderror"
        @if($multiple) multiple @endif
        @if($required) required @endif
        @if($disabled) disabled @endif
    >
        {{-- Static options --}}
        @if(!$ajaxUrl && count($options) > 0)
            @foreach($options as $option)
                @php
                    $value = is_array($option) ? $option['value'] : $option;
                    $label = is_array($option) ? $option['label'] : $option;
                    $isSelected = false;
                    
                    if ($multiple && is_array($selected)) {
                        $isSelected = in_array($value, $selected);
                    } elseif (!$multiple) {
                        $isSelected = (string)$value === (string)$selected;
                    }
                @endphp
                <option value="{{ $value }}" @if($isSelected) selected @endif>
                    {{ $label }}
                </option>
            @endforeach
        @endif

        {{-- Pre-selected option for AJAX --}}
        @if($ajaxUrl && $selected)
            @if($multiple && is_array($selected))
                @foreach($selected as $item)
                    <option value="{{ is_array($item) ? $item['value'] : $item }}" selected>
                        {{ is_array($item) ? $item['label'] : $item }}
                    </option>
                @endforeach
            @elseif(!$multiple)
                <option value="{{ is_array($selected) ? $selected['value'] : $selected }}" selected>
                    {{ is_array($selected) ? $selected['label'] : $selected }}
                </option>
            @endif
        @endif
    </select>
    
    @error($name)
        <div class="text-red-500 text-sm mt-1">{{ $message }}</div>
    @enderror
</div>

@once
    @push('styles')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
        </style>
    @endpush
    
    @push('scripts')
        {{-- <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script> --}}
        <script>
            function initTomSelect() {
                document.querySelectorAll('.tomselect-input').forEach(function(selectEl) {
                    if (selectEl.tomselect) return; // Already initialized
                    
                    const ajaxUrl = selectEl.dataset.ajaxUrl;
                    const isMultiple = selectEl.dataset.multiple === 'true';
                    const isSearchable = selectEl.dataset.searchable === 'true';
                    const isClearable = selectEl.dataset.clearable === 'true';
                    const createOnBlur = selectEl.dataset.createOnBlur === 'true';
                    const maxItems = selectEl.dataset.maxItems ? parseInt(selectEl.dataset.maxItems) : null;
                    const hasError = selectEl.classList.contains('border-red-500');
                    
                    const config = {
                        valueField: 'value',
                        labelField: 'label',
                        searchField: 'label',
                        placeholder: selectEl.dataset.placeholder || 'Select option...',
                        closeAfterSelect: !isMultiple,
                        hideSelected: isMultiple,
                        allowEmptyOption: isClearable,
                        maxItems: maxItems,
                        onInitialize: function() {
                            if (hasError) {
                                this.control.classList.add('has-error');
                            }
                        },
                        onError: function(error) {
                            console.error('TomSelect Error:', error);
                        }
                    };
                    
                    // Configure search
                    if (!isSearchable) {
                        config.controlInput = null;
                        config.searchField = null;
                    }
                    
                    // Configure create option
                    if (createOnBlur) {
                        config.create = true;
                        config.createOnBlur = true;
                    }
                    
                    // Configure AJAX loading
                    if (ajaxUrl) {
                        config.preload = 'focus';
                        config.load = function(query, callback) {
                            // const url = ajaxUrl + (ajaxUrl.includes('?') ? '&' : '?') + 'q=' + encodeURIComponent(query);
                            const searchFilter = "{{ $SearchFlter }}";

                            const url = ajaxUrl + (ajaxUrl.includes('?') ? '&' : '?') + (searchFilter ? searchFilter + '&' : '') + 'q=' + encodeURIComponent(query);

                            fetch(url, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json',
                                }
                            })
                            .then(response => {
                                if (!response.ok) {
                                    throw new Error('Network response was not ok');
                                }
                                return response.json();
                            })
                            .then(data => {
                                // Ensure data is in correct format
                                const formattedData = Array.isArray(data) ? data : (data.data || []);
                                callback(formattedData);
                            })
                            .catch(error => {
                                console.error('AJAX Error:', error);
                                callback();
                            });
                        };
                    }
                    
                    // Initialize TomSelect
                    try {
                        new TomSelect(selectEl, config);
                    } catch (error) {
                        console.error('TomSelect initialization error:', error);
                    }
                });
            }
            
            // Initialize on DOM ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', initTomSelect);
            } else {
                initTomSelect();
            }
            
            // For dynamic content (like Livewire)
            window.initTomSelect = initTomSelect;
        </script>
    @endpush
@endonce