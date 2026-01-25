<?php
session_start();

function checkLogin() {
    if (!file_exists('app/creds')) {
        header('Location: login/login.php');
        exit();
    }
}

checkLogin();

function processM3uUrl($url, $file) {
    // The '@' suppresses errors, which can make debugging hard.
    // It's generally better to handle potential errors.
    $context = stream_context_create(['http' => ['timeout' => 15]]);
    $response = @file_get_contents($url, false, $context);

    if ($response !== false && !empty($response)) {
        $lines = explode("\n", $response);
        $channels = [];
        $currentChannel = null;

        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, '#EXTINF') === 0) {
                // Reset for each new channel entry
                $currentChannel = ['id' => '', 'name' => '', 'logo' => '', 'group' => 'Other'];

                preg_match('/tvg-id="([^"]*)"/', $line, $idMatch);
                // Sanitize ID to ensure it's a valid string for localStorage and data attributes
                $currentChannel['id'] = !empty($idMatch[1]) ? trim($idMatch[1]) : '';

                preg_match('/tvg-logo="([^"]*)"/', $line, $logoMatch);
                $currentChannel['logo'] = $logoMatch[1] ?? '';

                preg_match('/group-title="([^"]*)"/', $line, $groupMatch);
                if (!empty($groupMatch[1])) {
                    $currentChannel['group'] = $groupMatch[1];
                }

                $parts = explode(',', $line);
                $channelName = trim(end($parts));
                $currentChannel['name'] = $channelName;

                // If tvg-id is empty, create a fallback ID from the channel name
                if (empty($currentChannel['id'])) {
                    $currentChannel['id'] = 'custom_' . preg_replace('/[^a-zA-Z0-9]/', '', $channelName);
                }

            } elseif ($currentChannel !== null && (strpos($line, 'http://') === 0 || strpos($line, 'https://') === 0)) {
                // Only add the channel if it has a valid stream URL and a name
                if (!empty($currentChannel['name'])) {
                    $channels[] = $currentChannel;
                }
                $currentChannel = null; // Reset for the next entry
            }
        }

        $json = json_encode($channels, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        file_put_contents($file, $json);
        return true;
    }
    // If the download fails, create an empty JSON array to prevent errors on the frontend
    file_put_contents($file, '[]');
    return false;
}

$file = 'app/js/playlist.json';

// Handle the refresh request
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['refresh'])) {
    if (file_exists($file)) {
        unlink($file);
    }
    header('Location: ' . $_SERVER['PHP_SELF']);
    exit();
}

// Caching logic: Refresh the playlist if it's missing or from a previous day
if (!file_exists($file) || date('Ymd', filemtime($file)) !== date('Ymd')) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'];
    $dir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $localPlaylistUrl = $protocol . $host . $dir . '/playlist.php';
    processM3uUrl($localPlaylistUrl, $file);
}

// Generate the public URL for the modal
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
$host = $_SERVER['HTTP_HOST'];
$dir = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
$publicPlaylistUrl = $protocol . $host . $dir . '/playlist.php';

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no" />
    <title>Tataplay</title>
    <link rel="icon" href="Logo/Tataplay.gif" type="image/gif" sizes="16x16">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        :root {
            --bg-color: #f8f9fa;
            --text-color: #212529;
            --card-bg: #ffffff;
            --shadow-color: rgba(0, 0, 0, 0.08);
            --border-color: #dee2e6;
            --accent-color: #007bff;
            --accent-color-dark: #0056b3;
            --favorite-color: #ffc107;
            --skeleton-bg: #e9ecef;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #121212;
                --text-color: #e9ecef;
                --card-bg: #1e1e1e;
                --shadow-color: rgba(0, 0, 0, 0.4);
                --border-color: #343a40;
                --accent-color: #0d6efd;
                --accent-color-dark: #0a58ca;
                --skeleton-bg: #2c2c2c;
            }
        }
        /* Base styles */
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Inter', sans-serif; background-color: var(--bg-color); color: var(--text-color); }
        .header { background-color: var(--card-bg); padding: 1rem 1.5rem; border-bottom: 1px solid var(--border-color); box-shadow: 0 2px 4px var(--shadow-color); display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 1rem; position: sticky; top: 0; z-index: 1000; }
        .header-title { font-size: 1.5rem; font-weight: 700; background: linear-gradient(to right, var(--accent-color), #00c6ff); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .search-container { position: relative; flex-grow: 1; max-width: 400px; }
        #search { width: 100%; padding: 0.75rem 1rem 0.75rem 2.5rem; font-size: 1rem; border: 1px solid var(--border-color); border-radius: 2rem; background-color: var(--bg-color); color: var(--text-color); transition: border-color 0.2s, box-shadow 0.2s; }
        #search:focus { outline: none; border-color: var(--accent-color); box-shadow: 0 0 0 3px color-mix(in srgb, var(--accent-color) 25%, transparent); }
        .search-container .fa-search { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: var(--text-color); opacity: 0.5; }
        .header-actions { display: flex; align-items: center; flex-wrap: wrap; gap: 0.75rem; }
        .action-btn { display: flex; align-items: center; gap: 0.5rem; padding: 0.6rem 1.2rem; font-size: 0.9rem; font-weight: 500; border: 1px solid transparent; border-radius: 2rem; cursor: pointer; transition: all 0.2s ease; color: #fff; background-color: var(--accent-color); }
        .action-btn:hover { background-color: var(--accent-color-dark); transform: translateY(-2px); box-shadow: 0 4px 8px var(--shadow-color); }
        
        /* --- NEW: Category Filter Styles --- */
        .category-filter-container { position: relative; }
        .category-filter-container .fa-layer-group { position: absolute; left: 1rem; top: 50%; transform: translateY(-50%); color: #fff; opacity: 0.7; pointer-events: none; }
        #category-filter { -webkit-appearance: none; -moz-appearance: none; appearance: none; padding-right: 2.5rem; padding-left: 2.5rem; background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23ffffff' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m2 5 6 6 6-6'/%3e%3c/svg%3e"); background-repeat: no-repeat; background-position: right 0.75rem center; background-size: 16px 12px; border: none; }
        
        #favorites-filter-btn { background-color: #6c757d; }
        #favorites-filter-btn.active { background-color: var(--favorite-color); color: #000; border-color: #ffc107; box-shadow: 0 0 8px color-mix(in srgb, var(--favorite-color) 40%, transparent); }
        .main-content { padding: 2rem 1.5rem; }
        #playlist { display: grid; grid-template-columns: repeat(auto-fill, minmax(160px, 1fr)); gap: 1.5rem; }
        @keyframes fadeInUp { from { opacity: 0; transform: translateY(20px); } to { opacity: 1; transform: translateY(0); } }
        .card-link { text-decoration: none; color: inherit; opacity: 0; animation: fadeInUp 0.5s ease-out forwards; }
        .channel-card { background-color: var(--card-bg); border-radius: 12px; padding: 1rem; text-align: center; border: 1px solid var(--border-color); box-shadow: 0 4px 6px var(--shadow-color); transition: transform 0.3s ease, box-shadow 0.3s ease; position: relative; overflow: hidden; }
        .channel-card:hover { transform: translateY(-5px) scale(1.03); box-shadow: 0 8px 15px var(--shadow-color), 0 0 20px color-mix(in srgb, var(--accent-color) 30%, transparent); }
        .play-icon-overlay { position: absolute; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); color: white; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; opacity: 0; transition: opacity 0.3s ease; pointer-events: none; }
        .channel-card:hover .play-icon-overlay { opacity: 1; }
        .favorite-toggle-btn { position: absolute; top: 8px; right: 8px; font-size: 1.25rem; color: var(--border-color); cursor: pointer; z-index: 2; transition: color 0.2s, transform 0.2s; }
        .favorite-toggle-btn:hover { transform: scale(1.2); }
        .favorite-toggle-btn.is-favorite { color: var(--favorite-color); font-weight: 900; text-shadow: 0 0 5px rgba(0,0,0,0.5); }
        .channel-logo-wrapper { width: 100%; aspect-ratio: 1 / 1; margin-bottom: 1rem; display: flex; align-items: center; justify-content: center; background-color: var(--bg-color); border-radius: 8px; padding: 0.5rem; }
        .channel-logo { max-width: 100%; max-height: 100%; object-fit: contain; }
        .channel-name { font-size: 0.9rem; font-weight: 500; word-wrap: break-word; }
        .skeleton-card { background-color: var(--card-bg); border-radius: 12px; padding: 1rem; border: 1px solid var(--border-color); }
        .skeleton { background-color: var(--skeleton-bg); border-radius: 8px; position: relative; overflow: hidden; }
        .skeleton::after { content: ''; position: absolute; top: 0; left: -150%; width: 150%; height: 100%; background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--skeleton-bg) 50%, #fff), transparent); animation: skeleton-shine 1.5s infinite; }
        @media (prefers-color-scheme: dark) { .skeleton::after { background: linear-gradient(90deg, transparent, color-mix(in srgb, var(--skeleton-bg) 50%, #000), transparent); } }
        .skeleton-logo { width: 100%; aspect-ratio: 1 / 1; margin-bottom: 1rem; }
        .skeleton-text { height: 1rem; width: 80%; margin: 0 auto; }
        @keyframes skeleton-shine { 0% { left: -150%; } 100% { left: 150%; } }
        .message-container { text-align: center; padding: 3rem; width: 100%; grid-column: 1 / -1; color: var(--text-color); opacity: 0.7; }

        /* Modal Styles */
        .modal-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.6); display: flex; align-items: center; justify-content: center; z-index: 2000; opacity: 0; visibility: hidden; transition: opacity 0.3s ease, visibility 0.3s ease; }
        .modal-overlay.visible { opacity: 1; visibility: visible; }
        .modal-content { background-color: var(--card-bg); padding: 1.5rem 2rem; border-radius: 12px; box-shadow: 0 5px 15px rgba(0,0,0,0.3); width: 90%; max-width: 500px; position: relative; transform: scale(0.95); transition: transform 0.3s ease; }
        .modal-overlay.visible .modal-content { transform: scale(1); }
        .modal-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem; padding-bottom: 1rem; border-bottom: 1px solid var(--border-color); }
        .modal-header h3 { font-size: 1.25rem; margin: 0; }
        .modal-close-btn { font-size: 1.5rem; font-weight: bold; border: none; background: none; color: var(--text-color); cursor: pointer; opacity: 0.7; }
        .modal-close-btn:hover { opacity: 1; }
        .modal-body label { font-weight: 500; display: block; margin-bottom: 0.5rem; }
        .url-input-group { display: flex; gap: 0.5rem; }
        #playlistUrlInput { flex-grow: 1; padding: 0.6rem; font-size: 1rem; border: 1px solid var(--border-color); border-radius: 6px; background-color: var(--bg-color); color: var(--text-color); }
        .copy-btn { padding: 0.6rem 1rem; border: none; border-radius: 6px; background-color: var(--accent-color); color: #fff; cursor: pointer; transition: background-color 0.2s; min-width: 80px; }
        .copy-btn:hover { background-color: var(--accent-color-dark); }
    </style>
</head>
<body>

    <header class="header">
        <h1 class="header-title">Tataplay</h1>
        <div class="search-container">
            <i class="fas fa-search"></i>
            <input id="search" type="text" placeholder="Search channels...">
        </div>
        <div class="header-actions">
            <div class="category-filter-container">
                <i class="fas fa-layer-group"></i>
                <select id="category-filter" class="action-btn">
                    <option value="all">All Categories</option>
                </select>
            </div>
            <button id="favorites-filter-btn" class="action-btn">
                <i class="fas fa-star"></i> Favorites
            </button>
            <form method="post" style="margin: 0;">
                <button type="submit" name="refresh" class="action-btn">
                    <i class="fas fa-sync-alt"></i> Refresh
                </button>
            </form>
            <button class="action-btn" onclick="openPlaylistUrlModal()">
                <i class="fas fa-music"></i> Playlist
            </button>
            <button class="action-btn" onclick="location.href='login/logout.php'">
                <i class="fas fa-sign-out-alt"></i> Logout
            </button>
        </div>
    </header>

    <main class="main-content">
        <div id="playlist"></div>
    </main>

    <div id="playlistModal" class="modal-overlay">
        <div class="modal-content">
            <div class="modal-header">
                <h3>Share Playlist</h3>
                <button class="modal-close-btn" onclick="closePlaylistUrlModal()">&times;</button>
            </div>
            <div class="modal-body">
                <label for="playlistUrlInput">Playlist URL:</label>
                <div class="url-input-group">
                    <input type="text" id="playlistUrlInput" value="<?= htmlspecialchars($publicPlaylistUrl) ?>" readonly>
                    <button class="copy-btn" onclick="copyPlaylistUrl()">Copy</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const playlistDiv = document.getElementById("playlist");
            const searchInput = document.getElementById("search");
            const favoritesFilterBtn = document.getElementById("favorites-filter-btn");
            const categoryFilter = document.getElementById("category-filter");
            const FAVORITES_KEY = 'tataplay_favorites';

            let allChannels = [];
            let favorites = getFavorites();

            function getFavorites() {
                const favs = localStorage.getItem(FAVORITES_KEY);
                return favs ? JSON.parse(favs) : [];
            }
            function saveFavorites(favs) {
                localStorage.setItem(FAVORITES_KEY, JSON.stringify(favs));
            }

            function showMessage(text) {
                playlistDiv.innerHTML = `<div class="message-container"><h3>${text}</h3></div>`;
            }

            function renderSkeletons() {
                let skeletonHTML = '';
                for (let i = 0; i < 18; i++) {
                    skeletonHTML += `<div class="skeleton-card"><div class="skeleton skeleton-logo"></div><div class="skeleton skeleton-text"></div></div>`;
                }
                playlistDiv.innerHTML = skeletonHTML;
            }
            
            function populateCategories(channels) {
                const categories = [...new Set(channels.map(channel => channel.group).filter(Boolean))];
                categories.sort();
                categories.forEach(category => {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    categoryFilter.appendChild(option);
                });
            }

            function renderPlaylist() {
                const searchTerm = searchInput.value.toLowerCase();
                const selectedCategory = categoryFilter.value;
                const favoritesOnly = favoritesFilterBtn.classList.contains('active');
                
                let filteredChannels = allChannels;

                if (favoritesOnly) {
                    filteredChannels = filteredChannels.filter(channel => favorites.includes(channel.id));
                }
                if (selectedCategory !== 'all') {
                    filteredChannels = filteredChannels.filter(channel => channel.group === selectedCategory);
                }
                if (searchTerm) {
                    filteredChannels = filteredChannels.filter(channel => channel.name.toLowerCase().includes(searchTerm));
                }

                playlistDiv.innerHTML = '';

                if (filteredChannels.length === 0) {
                    showMessage("No channels match your criteria.");
                    return;
                }

                filteredChannels.forEach((item, index) => {
                    const isFavorite = favorites.includes(item.id);
                    const cardLink = document.createElement("a");
                    
                    // --- MODIFIED: Sanitize ID for play.php link ---
                    // Removes all non-numeric characters from the ID before creating the link
                    const numericId = item.id.replace(/\D/g, '');
                    cardLink.href = `play.php?id=${encodeURIComponent(numericId)}`;
                    // --- END MODIFICATION ---

                    cardLink.target = "_blank";
                    cardLink.className = "card-link";
                    cardLink.title = item.name;
                    // Use the original, full ID for favorites and internal tracking
                    cardLink.dataset.id = item.id;
                    cardLink.style.animationDelay = `${index * 30}ms`;

                    cardLink.innerHTML = `
                        <div class="channel-card">
                            <div class="favorite-toggle-btn ${isFavorite ? 'is-favorite' : ''}" data-channel-id="${item.id}">
                                <i class="fas fa-star"></i>
                            </div>
                            <div class="channel-logo-wrapper">
                                <img class="channel-logo" src="${item.logo || ''}" alt="${item.name}" loading="lazy" onerror="this.style.display='none'; this.parentElement.style.display='none';">
                            </div>
                            <span class="channel-name">${item.name}</span>
                            <div class="play-icon-overlay"><i class="fas fa-play"></i></div>
                        </div>`;
                    playlistDiv.appendChild(cardLink);
                });
            }
            
            // Main execution flow
            renderSkeletons();

            fetch("app/js/playlist.json")
                .then(response => {
                    if (!response.ok) { throw new Error('Could not load playlist.json'); }
                    return response.json();
                })
                .then(playlist => {
                    allChannels = playlist;
                    if (playlist.length === 0) {
                        showMessage("No channels found. Try refreshing the playlist.");
                        return;
                    }
                    populateCategories(allChannels);
                    renderPlaylist();
                })
                .catch(error => {
                    console.error('Error fetching playlist:', error);
                    showMessage("Error: Could not load the channel list.");
                });

            // Event Listeners
            searchInput.addEventListener('input', renderPlaylist);
            categoryFilter.addEventListener('change', renderPlaylist);
            favoritesFilterBtn.addEventListener('click', function() {
                this.classList.toggle('active');
                renderPlaylist();
            });

            playlistDiv.addEventListener('click', function(event) {
                const favoriteBtn = event.target.closest('.favorite-toggle-btn');
                if (favoriteBtn) {
                    event.preventDefault();
                    event.stopPropagation();

                    const channelId = favoriteBtn.dataset.channelId;
                    const isFavorited = favorites.includes(channelId);

                    if (isFavorited) {
                        favorites = favorites.filter(id => id !== channelId);
                        favoriteBtn.classList.remove('is-favorite');
                    } else {
                        favorites.push(channelId);
                        favoriteBtn.classList.add('is-favorite');
                    }
                    saveFavorites(favorites);
                    
                    // If we are in favorites-only view, removing a favorite should make it disappear
                    if (favoritesFilterBtn.classList.contains('active')) {
                        renderPlaylist();
                    }
                }
            });
        });

        // Modal Functions
        const playlistModal = document.getElementById('playlistModal');
        
        function openPlaylistUrlModal() {
            playlistModal.classList.add('visible');
        }

        function closePlaylistUrlModal() {
            playlistModal.classList.remove('visible');
        }

        playlistModal.addEventListener('click', function(event) {
            if (event.target === playlistModal) {
                closePlaylistUrlModal();
            }
        });

        function copyPlaylistUrl() {
            const input = document.getElementById('playlistUrlInput');
            const copyBtn = document.querySelector('.copy-btn');
            input.select();
            input.setSelectionRange(0, 99999);
            navigator.clipboard.writeText(input.value).then(() => {
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!';
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                }, 2000);
            });
        }
    </script>
</body>
</html>