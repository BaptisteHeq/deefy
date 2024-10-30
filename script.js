document.addEventListener('DOMContentLoaded', () => {
    const toggleButton = document.getElementById('playlist-toggle');
    const playlistOptions = document.querySelector('.playlist-options');

    toggleButton.addEventListener('click', () => {
        const isVisible = playlistOptions.style.display === 'flex';
        playlistOptions.style.display = isVisible ? 'none' : 'flex';
    });

    // Optionnel : fermer le menu si on clique en dehors
    document.addEventListener('click', (event) => {
        if (!toggleButton.contains(event.target) && !playlistOptions.contains(event.target)) {
            playlistOptions.style.display = 'none';
        }
    });
});