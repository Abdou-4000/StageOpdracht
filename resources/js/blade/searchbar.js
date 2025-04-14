// Searchbar
function search() {
    const searchbar = document.getElementById('searchbar');
    const searchTerm = searchbar.value.toLowerCase();
    const rows = document.querySelectorAll('tbody tr');

    rows.forEach(row => {
        const rowText = row.textContent.toLowerCase();
        row.style.display = rowText.includes(searchTerm) ? '' : 'none';
    });
}

// Attach event listener
document.getElementById('searchbar').addEventListener('input', search);
