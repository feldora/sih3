@props([
    'name',
    'label',
    'value' => '',
    'items' => [],
    'itemLabel' => 'label', // field untuk label item, misal 'name' atau 'label'
    'placeholder' => 'Pilih...'
])

<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <div class="relative">
        <input 
            type="text" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="{{ $value }}" 
            class="form-input mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
            placeholder="{{ $placeholder }}"
            readonly
            onclick="openPopupModal('{{ $name }}')"
        >
        @error($name)
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
</div>

<!-- Modal Popup -->
<div id="popup-modal-{{ $name }}" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden z-50">
    <div class="bg-white p-6 rounded-md shadow-lg w-96 max-h-[70vh] overflow-auto">
        <h2 class="text-lg font-medium mb-4">Pilih {{ $label }}</h2>
        <input 
            type="text" 
            id="popup-search-{{ $name }}" 
            class="form-input w-full mb-4" 
            placeholder="Cari {{ $label }}..." 
        >
        <ul id="popup-list-{{ $name }}" class="max-h-60 overflow-auto">
            <!-- Daftar item akan ditambahkan disini melalui JavaScript -->
        </ul>
        <button type="button" onclick="closePopupModal('{{ $name }}')" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded-md">Tutup</button>
    </div>
</div>

<script>
    window.popupItems = window.popupItems || {};
    window.popupItems['{{ $name }}'] = @json($items);

    function getItemLabel(item, labelField) {
        if (typeof item === 'string') return item;
        return item[labelField] || '';
    }

    function showPopupItems(name, searchTerm = '') {
        const items = window.popupItems[name] || [];
        const labelField = @json($itemLabel);
        const list = document.getElementById('popup-list-' + name);
        const input = document.getElementById(name);

        list.innerHTML = '';
        const filtered = items.filter(item => getItemLabel(item, labelField).toLowerCase().includes(searchTerm.toLowerCase()));

        if (filtered.length > 0) {
            filtered.forEach(item => {
                const label = getItemLabel(item, labelField);
                const li = document.createElement('li');
                li.className = 'p-2 cursor-pointer hover:bg-gray-200';
                li.innerHTML = label;
                li.onclick = () => {
                    input.value = label;
                    closePopupModal(name);
                };
                list.appendChild(li);
            });
        }
    }

    function openPopupModal(name) {
        document.getElementById('popup-modal-' + name).classList.remove('hidden');
        showPopupItems(name);
        document.getElementById('popup-search-' + name).value = '';
    }

    function closePopupModal(name) {
        document.getElementById('popup-modal-' + name).classList.add('hidden');
    }

    document.addEventListener('DOMContentLoaded', function() {
        @php
            // Untuk mendukung banyak komponen di satu halaman
        @endphp
        @foreach([$name] as $n)
        document.getElementById('popup-search-{{ $n }}').addEventListener('input', function() {
            showPopupItems('{{ $n }}', this.value);
        });
        showPopupItems('{{ $n }}');
        @endforeach
    });
</script>
