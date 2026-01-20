<div class="d-flex align-items-center justify-content-between mb-3">
    <div>
        <h2 class="mb-1">Search Photos</h2>
        <div class="text-muted">Type a fragment of a photo title</div>
    </div>
</div>

<div class="card shadow-sm mb-3">
    <div class="card-body">
        <label class="form-label">Title contains</label>
        <input id="searchBox" type="text" class="form-control" placeholder="Start typing..." autocomplete="off">
        <div class="form-text">Results update as you type (AJAX).</div>
    </div>
</div>

<div id="searchResults"></div>

<script>
(function () {
    const input = document.getElementById('searchBox');
    const results = document.getElementById('searchResults');

    let timer = null;

    async function runSearch() {
        const q = input.value || '';
        const url = 'index.php?action=search_ajax&q=' + encodeURIComponent(q);
        const res = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
        results.innerHTML = await res.text();
    }

    input.addEventListener('keyup', function () {
        clearTimeout(timer);
        timer = setTimeout(runSearch, 150);
    });a

    // initial
    runSearch();
})();
</script>
