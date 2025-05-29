@props(['name', 'label', 'value' => ''])

{{-- Komponen input untuk memilih ikon dengan modal popup --}}



<div class="mb-4">
    <label for="{{ $name }}" class="block text-sm font-medium text-gray-700">{{ $label }}</label>
    <div class="relative">
        <input 
            type="text" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            value="{{ $value }}" 
            class="form-input mt-1 block w-full px-4 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" 
            placeholder="Cari Ikon..."
            readonly
            onclick="openIconModal()"
        >
        @error($name)
            <span class="text-red-500 text-sm">{{ $message }}</span>
        @enderror
    </div>
</div>

<!-- Modal Popup untuk memilih ikon -->
<div id="icon-modal" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-center hidden">
    <div class="bg-white p-6 rounded-md shadow-lg w-96 max-h-[70vh] overflow-auto">
        <h2 class="text-lg font-medium mb-4">Pilih Ikon</h2>
        <input 
            type="text" 
            id="icon-search" 
            class="form-input w-full mb-4" 
            placeholder="Cari Ikon..." 
        >
        <ul id="icon-list" class="max-h-60 overflow-auto">
            <!-- Daftar ikon akan ditambahkan disini melalui JavaScript -->
        </ul>
        <button type="button" onclick="closeIconModal()" class="mt-4 bg-gray-500 text-white px-4 py-2 rounded-md">Tutup</button>
    </div>
</div>

<script>
    // Daftar semua ikon Font Awesome
    const allIcons = [
  "fa-bars", "fa-font-awesome", "fa-caret-down", "fa-flag", "fa-handshake", "fa-camera-retro", "fa-universal-access", "fa-hand-spock", "fa-ship", "fa-venus", "fa-file-image", "fa-spinner", "fa-check-square", "fa-credit-card", "fa-pie-chart", "fa-won", "fa-file-text", "fa-arrow-right", "fa-play-circle", "fa-facebook-official", "fa-medkit", "fa-caret-down", "fa-universal-access", "fa-flag", "fa-search", "fa-address-book", "fa-address-card", "fa-bandcamp", "fa-bath", "fa-bathtub", "fa-drivers-license", "fa-eercast", "fa-envelope-open", "fa-etsy", "fa-free-code-camp", "fa-grav", "fa-handshake", "fa-id-badge", "fa-id-card", "fa-imdb", "fa-linode", "fa-meetup", "fa-microchip", "fa-podcast", "fa-quora", "fa-ravelry", "fa-s15", "fa-shower", "fa-snowflake", "fa-superpowers", "fa-telegram", "fa-thermometer", "fa-thermometer-0", "fa-thermometer-1", "fa-thermometer-2", "fa-thermometer-3", "fa-thermometer-4", "fa-thermometer-empty", "fa-thermometer-full", "fa-thermometer-half", "fa-thermometer-quarter", "fa-thermometer-three-quarters", "fa-times-rectangle", "fa-user-circle", "fa-user", "fa-vcard", "fa-window-close", "fa-window-maximize", "fa-window-minimize", "fa-window-restore", "fa-wpexplorer", "fa-address-book", "fa-address-card", "fa-adjust", "fa-american-sign-language-interpreting", "fa-anchor", "fa-archive", "fa-area-chart", "fa-arrows", "fa-arrows-h", "fa-arrows-v", "fa-asl-interpreting", "fa-assistive-listening-systems", "fa-asterisk", "fa-at", "fa-audio-description", "fa-automobile", "fa-balance-scale", "fa-ban", "fa-bank", "fa-bar-chart", "fa-barcode", "fa-bars", "fa-bath", "fa-bathtub", "fa-battery", "fa-battery-0", "fa-battery-1", "fa-battery-2", "fa-battery-3", "fa-battery-4", "fa-battery-empty", "fa-battery-full", "fa-battery-half", "fa-battery-quarter", "fa-battery-three-quarters", "fa-bed", "fa-beer", "fa-bell", "fa-bell-slash", "fa-bicycle", "fa-binoculars", "fa-birthday-cake", "fa-blind", "fa-bluetooth", "fa-bluetooth-b", "fa-bolt", "fa-bomb", "fa-book", "fa-bookmark", "fa-braille", "fa-briefcase", "fa-bug", "fa-building", "fa-bullhorn", "fa-bullseye", "fa-bus", "fa-cab", "fa-calculator", "fa-calendar", "fa-calendar-check", "fa-calendar-minus", "fa-calendar-plus", "fa-calendar-times", "fa-camera", "fa-camera-retro", "fa-car", "fa-caret-square-down", "fa-caret-square-left", "fa-caret-square-right", "fa-caret-square-up", "fa-cart-arrow-down", "fa-cart-plus", "fa-cc", "fa-certificate", "fa-check", "fa-check-circle", "fa-check-square", "fa-child", "fa-circle", "fa-circle-notch", "fa-circle-thin", "fa-clock", "fa-clone", "fa-close", "fa-cloud", "fa-cloud-download", "fa-cloud-upload", "fa-code", "fa-code-fork", "fa-coffee", "fa-cog", "fa-cogs", "fa-comment", "fa-commenting", "fa-comments", "fa-compass", "fa-copyright", "fa-creative-commons", "fa-credit-card", "fa-credit-card-alt", "fa-crop", "fa-crosshairs", "fa-cube", "fa-cubes", "fa-cutlery", "fa-dashboard", "fa-database", "fa-deaf", "fa-deafness", "fa-desktop", "fa-diamond", "fa-dot-circle", "fa-download", "fa-drivers-license", "fa-edit", "fa-ellipsis-h", "fa-ellipsis-v", "fa-envelope", "fa-envelope-open", "fa-envelope-square", "fa-eraser", "fa-exchange", "fa-exclamation", "fa-exclamation-circle", "fa-exclamation-triangle", "fa-external-link", "fa-external-link-square", "fa-eye", "fa-eye-slash", "fa-eyedropper", "fa-fax", "fa-feed", "fa-female", "fa-fighter-jet", "fa-file-archive", "fa-file-audio", "fa-file-code", "fa-file-excel", "fa-file-image", "fa-file-movie", "fa-file-pdf", "fa-file-photo", "fa-file-picture", "fa-file-powerpoint", "fa-file-sound", "fa-file-video", "fa-file-word", "fa-file-zip", "fa-film", "fa-filter", "fa-fire", "fa-fire-extinguisher", "fa-flag", "fa-flag-checkered", "fa-flash", "fa-flask", "fa-folder", "fa-folder-open", "fa-frown", "fa-futbol", "fa-gamepad", "fa-gavel", "fa-gear", "fa-gears", "fa-gift", "fa-glass", "fa-globe", "fa-graduation-cap", "fa-group", "fa-hand-grab", "fa-hand-lizard", "fa-hand-paper", "fa-hand-peace", "fa-hand-pointer", "fa-hand-rock", "fa-hand-scissors", "fa-hand-spock", "fa-hand-stop", "fa-handshake", "fa-hard-of-hearing", "fa-hashtag", "fa-hdd", "fa-headphones", "fa-heart", "fa-heartbeat", "fa-history", "fa-home", "fa-hotel", "fa-hourglass", "fa-hourglass-1", "fa-hourglass-2", "fa-hourglass-3", "fa-hourglass-end", "fa-hourglass-half", "fa-hourglass-start", "fa-i-cursor", "fa-id-badge", "fa-id-card", "fa-image", "fa-inbox", "fa-industry", "fa-info", "fa-info-circle", "fa-institution", "fa-key", "fa-keyboard", "fa-language", "fa-laptop", "fa-leaf", "fa-legal", "fa-lemon", "fa-level-down", "fa-level-up", "fa-life-bouy", "fa-life-buoy", "fa-life-ring", "fa-life-saver", "fa-lightbulb", "fa-line-chart", "fa-location-arrow", "fa-lock", "fa-low-vision", "fa-magic", "fa-magnet", "fa-mail-forward", "fa-mail-reply", "fa-mail-reply-all", "fa-male", "fa-map", "fa-map-marker", "fa-map-pin", "fa-map-signs", "fa-meh", "fa-microchip", "fa-microphone", "fa-microphone-slash", "fa-minus", "fa-minus-circle", "fa-minus-square", "fa-mobile", "fa-mobile-phone", "fa-money", "fa-moon", "fa-mortar-board", "fa-motorcycle", "fa-mouse-pointer", "fa-music", "fa-navicon", "fa-newspaper", "fa-object-group", "fa-object-ungroup", "fa-paint-brush", "fa-paper-plane", "fa-paw", "fa-pencil", "fa-pencil-square", "fa-percent", "fa-phone", "fa-phone-square", "fa-photo", "fa-picture", "fa-pie-chart", "fa-plane", "fa-plug", "fa-plus", "fa-plus-circle", "fa-plus-square", "fa-podcast", "fa-power-off", "fa-print", "fa-puzzle-piece", "fa-qrcode", "fa-question", "fa-question-circle", "fa-quote-left", "fa-quote-right", "fa-random", "fa-recycle", "fa-refresh", "fa-registered", "fa-remove", "fa-reorder", "fa-reply", "fa-reply-all", "fa-retweet", "fa-road", "fa-rocket", "fa-rss", "fa-rss-square", "fa-s15", "fa-search", "fa-search-minus", "fa-search-plus", "fa-send", "fa-server", "fa-share", "fa-share-alt", "fa-share-alt-square", "fa-share-square", "fa-shield", "fa-ship", "fa-shopping-bag", "fa-shopping-basket", "fa-shopping-cart", "fa-shower", "fa-sign-in", "fa-sign-language", "fa-sign-out", "fa-signal", "fa-signing", "fa-sitemap", "fa-sliders", "fa-smile", "fa-snowflake", "fa-soccer-ball", "fa-sort", "fa-sort-alpha-asc", "fa-sort-alpha-desc", "fa-sort-amount-asc", "fa-sort-amount-desc", "fa-sort-asc", "fa-sort-desc", "fa-sort-down", "fa-sort-numeric-asc", "fa-sort-numeric-desc", "fa-sort-up", "fa-space-shuttle", "fa-spinner", "fa-spoon", "fa-square", "fa-star", "fa-star-half", "fa-star-half-empty", "fa-star-half-full", "fa-star-half", "fa-sticky-note", "fa-street-view", "fa-suitcase", "fa-sun", "fa-support", "fa-tablet", "fa-tachometer", "fa-tag", "fa-tags", "fa-tasks", "fa-taxi", "fa-television", "fa-terminal", "fa-thermometer", "fa-thermometer-0", "fa-thermometer-1", "fa-thermometer-2", "fa-thermometer-3", "fa-thermometer-4", "fa-thermometer-empty", "fa-thermometer-full", "fa-thermometer-half", "fa-thermometer-quarter", "fa-thermometer-three-quarters", "fa-thumb-tack", "fa-thumbs-down", "fa-thumbs-up", "fa-ticket", "fa-times", "fa-times-circle", "fa-times-rectangle", "fa-tint", "fa-toggle-down", "fa-toggle-left", "fa-toggle-off", "fa-toggle-on", "fa-toggle-right", "fa-toggle-up", "fa-trademark", "fa-trash", "fa-tree", "fa-trophy", "fa-truck", "fa-tty", "fa-tv", "fa-umbrella", "fa-universal-access", "fa-university", "fa-unlock", "fa-unlock-alt", "fa-unsorted", "fa-upload", "fa-user", "fa-user-circle", "fa-user-plus", "fa-user-secret", "fa-user-times", "fa-users", "fa-vcard", "fa-video-camera", "fa-volume-control-phone", "fa-volume-down", "fa-volume-off", "fa-volume-up", "fa-warning", "fa-wheelchair", "fa-wheelchair-alt", "fa-wifi", "fa-window-close", "fa-window-maximize", "fa-window-minimize", "fa-window-restore", "fa-wrench", "fa-american-sign-language-interpreting", "fa-asl-interpreting", "fa-assistive-listening-systems", "fa-audio-description", "fa-blind", "fa-braille", "fa-cc", "fa-deaf", "fa-deafness", "fa-hard-of-hearing", "fa-low-vision", "fa-question-circle", "fa-sign-language", "fa-signing", "fa-tty", "fa-universal-access", "fa-volume-control-phone", "fa-wheelchair", "fa-wheelchair-alt", "fa-hand-grab", "fa-hand-lizard", "fa-hand-paper", "fa-hand-peace", "fa-hand-pointer", "fa-hand-rock", "fa-hand-scissors", "fa-hand-spock", "fa-hand-stop", "fa-thumbs-down", "fa-thumbs-up", "fa-ambulance", "fa-automobile", "fa-bicycle", "fa-bus", "fa-cab", "fa-car", "fa-fighter-jet", "fa-motorcycle", "fa-plane", "fa-rocket", "fa-ship", "fa-space-shuttle", "fa-subway", "fa-taxi", "fa-train", "fa-truck", "fa-wheelchair", "fa-wheelchair-alt", "fa-genderless", "fa-intersex", "fa-mars", "fa-mars-double", "fa-mars-stroke", "fa-mars-stroke-h", "fa-mars-stroke-v", "fa-mercury", "fa-neuter", "fa-transgender", "fa-transgender-alt", "fa-venus", "fa-venus-double", "fa-venus-mars", "fa-file", "fa-file-archive", "fa-file-audio", "fa-file-code", "fa-file-excel", "fa-file-image", "fa-file-movie", "fa-file", "fa-file-pdf", "fa-file-photo", "fa-file-picture", "fa-file-powerpoint", "fa-file-sound", "fa-file-text", "fa-file-video", "fa-file-word", "fa-file-zip", "fa-info-circle", "fa-circle-notch", "fa-cog", "fa-gear", "fa-refresh", "fa-spinner", "fa-check-square", "fa-circle", "fa-dot-circle", "fa-minus-square", "fa-plus-square", "fa-square", "fa-cc-amex", "fa-cc-diners-club", "fa-cc-discover", "fa-cc-jcb", "fa-cc-mastercard", "fa-cc-paypal", "fa-cc-stripe", "fa-cc-visa", "fa-credit-card", "fa-credit-card-alt", "fa-google-wallet", "fa-paypal", "fa-area-chart", "fa-bar-chart", "fa-line-chart", "fa-pie-chart", "fa-bitcoin", "fa-btc", "fa-cny", "fa-dollar", "fa-eur", "fa-euro", "fa-gbp", "fa-gg", "fa-gg-circle", "fa-ils", "fa-inr", "fa-jpy", "fa-krw", "fa-money", "fa-rmb", "fa-rouble", "fa-rub", "fa-ruble", "fa-rupee", "fa-shekel", "fa-sheqel", "fa-try", "fa-turkish-lira", "fa-usd", "fa-viacoin", "fa-won", "fa-yen", "fa-align-center", "fa-align-justify", "fa-align-left", "fa-align-right", "fa-bold", "fa-chain", "fa-chain-broken", "fa-clipboard", "fa-columns", "fa-copy", "fa-cut", "fa-dedent", "fa-eraser", "fa-file", "fa-file-text", "fa-files", "fa-floppy", "fa-font", "fa-header", "fa-indent", "fa-italic", "fa-link", "fa-list", "fa-list-alt", "fa-list-ol", "fa-list-ul", "fa-outdent", "fa-paperclip", "fa-paragraph", "fa-paste", "fa-repeat", "fa-rotate-left", "fa-rotate-right", "fa-save", "fa-scissors", "fa-strikethrough", "fa-subscript", "fa-superscript", "fa-table", "fa-text-height", "fa-text-width", "fa-th", "fa-th-large", "fa-th-list", "fa-underline", "fa-undo", "fa-unlink", "fa-angle-double-down", "fa-angle-double-left", "fa-angle-double-right", "fa-angle-double-up", "fa-angle-down", "fa-angle-left", "fa-angle-right", "fa-angle-up", "fa-arrow-circle-down", "fa-arrow-circle-left", "fa-arrow-circle-right", "fa-arrow-circle-up", "fa-arrow-down", "fa-arrow-left", "fa-arrow-right", "fa-arrow-up", "fa-arrows", "fa-arrows-alt", "fa-arrows-h", "fa-arrows-v", "fa-caret-down", "fa-caret-left", "fa-caret-right", "fa-caret-square-down", "fa-caret-square-left", "fa-caret-square-right", "fa-caret-square-up", "fa-caret-up", "fa-chevron-circle-down", "fa-chevron-circle-left", "fa-chevron-circle-right", "fa-chevron-circle-up", "fa-chevron-down", "fa-chevron-left", "fa-chevron-right", "fa-chevron-up", "fa-exchange", "fa-hand-down", "fa-hand-left", "fa-hand-right", "fa-hand-up", "fa-long-arrow-down", "fa-long-arrow-left", "fa-long-arrow-right", "fa-long-arrow-up", "fa-toggle-down", "fa-toggle-left", "fa-toggle-right", "fa-toggle-up", "fa-arrows-alt", "fa-backward", "fa-compress", "fa-eject", "fa-expand", "fa-fast-backward", "fa-fast-forward", "fa-forward", "fa-pause", "fa-pause-circle", "fa-play", "fa-play-circle", "fa-random", "fa-step-backward", "fa-step-forward", "fa-stop", "fa-stop-circle", "fa-youtube-play", "fa-500px", "fa-adn", "fa-amazon", "fa-android", "fa-angellist", "fa-apple", "fa-bandcamp", "fa-behance", "fa-behance-square", "fa-bitbucket", "fa-bitbucket-square", "fa-bitcoin", "fa-black-tie", "fa-bluetooth", "fa-bluetooth-b", "fa-btc", "fa-buysellads", "fa-cc-amex", "fa-cc-diners-club", "fa-cc-discover", "fa-cc-jcb", "fa-cc-mastercard", "fa-cc-paypal", "fa-cc-stripe", "fa-cc-visa", "fa-chrome", "fa-codepen", "fa-codiepie", "fa-connectdevelop", "fa-contao", "fa-css3", "fa-dashcube", "fa-delicious", "fa-deviantart", "fa-digg", "fa-dribbble", "fa-dropbox", "fa-drupal", "fa-edge", "fa-eercast", "fa-empire", "fa-envira", "fa-etsy", "fa-expeditedssl", "fa-fa", "fa-facebook", "fa-facebook-f", "fa-facebook-official", "fa-facebook-square", "fa-firefox", "fa-first-order", "fa-flickr", "fa-font-awesome", "fa-fonticons", "fa-fort-awesome", "fa-forumbee", "fa-foursquare", "fa-free-code-camp", "fa-ge", "fa-get-pocket", "fa-gg", "fa-gg-circle", "fa-git", "fa-git-square", "fa-github", "fa-github-alt", "fa-github-square", "fa-gitlab", "fa-gittip", "fa-glide", "fa-glide-g", "fa-google", "fa-google-plus", "fa-google-plus-circle", "fa-google-plus-official", "fa-google-plus-square", "fa-google-wallet", "fa-gratipay", "fa-grav", "fa-hacker-news", "fa-houzz", "fa-html5", "fa-imdb", "fa-instagram", "fa-internet-explorer", "fa-ioxhost", "fa-joomla", "fa-jsfiddle", "fa-lastfm", "fa-lastfm-square", "fa-leanpub", "fa-linkedin", "fa-linkedin-square", "fa-linode", "fa-linux", "fa-maxcdn", "fa-meanpath", "fa-medium", "fa-meetup", "fa-mixcloud", "fa-modx", "fa-odnoklassniki", "fa-odnoklassniki-square", "fa-opencart", "fa-openid", "fa-opera", "fa-optin-monster", "fa-pagelines", "fa-paypal", "fa-pied-piper", "fa-pied-piper-alt", "fa-pied-piper-pp", "fa-pinterest", "fa-pinterest-p", "fa-pinterest-square", "fa-product-hunt", "fa-qq", "fa-quora", "fa-ra", "fa-ravelry", "fa-rebel", "fa-reddit", "fa-reddit-alien", "fa-reddit-square", "fa-renren", "fa-resistance", "fa-safari", "fa-scribd", "fa-sellsy", "fa-share-alt", "fa-share-alt-square", "fa-shirtsinbulk", "fa-simplybuilt", "fa-skyatlas", "fa-skype", "fa-slack", "fa-slideshare", "fa-snapchat", "fa-snapchat-ghost", "fa-snapchat-square", "fa-soundcloud", "fa-spotify", "fa-stack-exchange", "fa-stack-overflow", "fa-steam", "fa-steam-square", "fa-stumbleupon", "fa-stumbleupon-circle", "fa-superpowers", "fa-telegram", "fa-tencent-weibo", "fa-themeisle", "fa-trello", "fa-tripadvisor", "fa-tumblr", "fa-tumblr-square", "fa-twitch", "fa-twitter", "fa-twitter-square", "fa-usb", "fa-viacoin", "fa-viadeo", "fa-viadeo-square", "fa-vimeo", "fa-vimeo-square", "fa-vine", "fa-vk", "fa-wechat", "fa-weibo", "fa-weixin", "fa-whatsapp", "fa-wikipedia-w", "fa-windows", "fa-wordpress", "fa-wpbeginner", "fa-wpexplorer", "fa-wpforms", "fa-xing", "fa-xing-square", "fa-y-combinator", "fa-y-combinator-square", "fa-yahoo", "fa-yc", "fa-yc-square", "fa-yelp", "fa-yoast", "fa-youtube", "fa-youtube-play", "fa-youtube-square", "fa-warning", "fa-ambulance", "fa-h-square", "fa-heart", "fa-heartbeat", "fa-hospital", "fa-medkit", "fa-plus-square", "fa-stethoscope", "fa-user-md", "fa-wheelchair", "fa-wheelchair-alt", "fa-meh", "fa-times"
];

    const iconSearch = document.getElementById('icon-search');
    const iconList = document.getElementById('icon-list');
    const iconModal = document.getElementById('icon-modal');
    const iconInput = document.getElementById('{{ $name }}');

    // Fungsi untuk menampilkan ikon di dalam modal
    function showIcons(searchTerm = '') {
        iconList.innerHTML = ''; // Reset list
        const filteredIcons = allIcons.filter(icon => icon.toLowerCase().includes(searchTerm.toLowerCase()));

        if (filteredIcons.length > 0) {
            filteredIcons.forEach(icon => {
                const li = document.createElement('li');
                li.classList.add('p-2', 'cursor-pointer', 'hover:bg-gray-200');
                li.innerHTML = `<i class="fas ${icon} mr-2"></i>${icon}`;
                
                li.onclick = () => {
                    iconInput.value = "fas " + icon; // Menyimpan ikon yang dipilih
                    closeIconModal(); // Menutup modal
                };
                iconList.appendChild(li);
            });
        }
    }

    function openIconModal() {
        iconModal.classList.remove('hidden');
        showIcons(); // Menampilkan semua ikon pada modal
    }

    function closeIconModal() {
        iconModal.classList.add('hidden');
    }

    iconSearch.addEventListener('input', () => {
        showIcons(iconSearch.value);
    });

    window.addEventListener('DOMContentLoaded', () => {
        showIcons();
    });
</script>
