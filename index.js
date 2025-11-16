let handbookData = [];

// Load JSON data when page loads
document.addEventListener("DOMContentLoaded", () => {
    fetch('handbook_data.sql')
        .then(res => res.json())
        .then(data => {
            if (Array.isArray(data)) {
                handbookData = data;
            } else if (data.keywords && Array.isArray(data.keywords)) {
                handbookData = data.keywords;
            } else {
                console.error("Invalid JSON structure.");
            }
        })
        .catch(err => console.error("Failed to load handbook data:", err));
});

// Highlight matched keyword
function highlight(text, keyword) {
    const regex = new RegExp(`(${keyword})`, 'gi');
    return text.replace(regex, '<mark>$1</mark>');
}

// Handle form submission
function handleCombinedSearch(event) {
    event.preventDefault();

    const query = document.getElementById("combinedSearch").value.trim().toLowerCase();
    const resultsDiv = document.getElementById("combinedResults");

    if (!query) {
        resultsDiv.classList.add("hidden");
        resultsDiv.innerHTML = "<p>Please enter a keyword.</p>";
        return false;
    }

    if (!Array.isArray(handbookData) || handbookData.length === 0) {
        resultsDiv.innerHTML = "<p>Handbook data not loaded yet.</p>";
        resultsDiv.classList.remove("hidden");
        return false;
    }

    // Show loading spinner
    resultsDiv.innerHTML = `<div class="spinner"></div>`;
    resultsDiv.classList.remove("hidden");

    // Filter handbook data
    const matched = handbookData.filter(item =>
        item.keyword.toLowerCase().includes(query) ||
        (item.content && item.content.toLowerCase().includes(query))
    );

    let output = '';

    if (matched.length > 0) {
        output += `<h3>Student Handbook Topics:</h3>`;
        matched.forEach(item => {
            output += `
                <div class="keyword-result">
                    <strong>${highlight(item.keyword, query)}</strong>
                    <p>${highlight(item.content || item.definition || '', query)}</p>
                </div>
            `;
        });
    } else {
        output = `<p>No results found for "<strong>${query}</strong>".</p>`;
    }

    resultsDiv.innerHTML = output;

    // Save search to DB
    saveSearchToDB(query);

    return false;
}

// Save search query to PHP backend
function saveSearchToDB(query) {
    fetch('search_db.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: new URLSearchParams({ keyword: query })
    }).then(res => res.text())
      .then(data => console.log('Search saved:', data))
      .catch(err => console.error('Failed to save search:', err));
}

// Trigger search manually (optional)
function triggerSearch(searchQuery) {
    document.getElementById("combinedSearch").value = searchQuery;
    const mockEvent = { preventDefault: () => {} };
    handleCombinedSearch(mockEvent);
}

// Feedback (optional, for future upgrade)
function sendFeedback(query, responseText, rating) {
    fetch('feedback.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded'},
        body: new URLSearchParams({ query, response: responseText, rating })
    })
    .then(r => r.json())
    .then(data => {
        if (data.ok) console.log('Thanks for feedback');
    })
    .catch(err => console.error('Feedback error:', err));
}
// assumes handbookData loaded as earlier
document.addEventListener('DOMContentLoaded', ()=> {
  const input = document.getElementById('combinedSearch');
  const form = document.getElementById('searchForm');
  const suggestions = document.getElementById('suggestions');
  const results = document.getElementById('combinedResults');
  const langToggle = document.getElementById('langToggle');

  // Typeahead suggestions (local)
  input.addEventListener('input', ()=> {
    const q = input.value.trim().toLowerCase();
    if (q.length < 2) { suggestions.style.display='none'; return; }
    const matches = handbookData.filter(it => it.keyword.toLowerCase().includes(q)).slice(0,8);
    if (matches.length===0) { suggestions.style.display='none'; return; }
    suggestions.innerHTML = matches.map(m=>`<button type="button" class="list-group-item list-group-item-action suggestion-item" data-q="${m.keyword}">${m.keyword} <br><small class="text-muted">${(m.content||'').slice(0,80)}</small></button>`).join('');
    suggestions.style.display='block';
  });

  // click suggestion
  suggestions.addEventListener('click', (ev)=>{
    const btn = ev.target.closest('.suggestion-item');
    if (!btn) return;
    input.value = btn.dataset.q;
    suggestions.style.display='none';
    form.dispatchEvent(new Event('submit'));
  });

  // submit search
  form.addEventListener('submit', async (ev)=>{
    ev.preventDefault();
    const q = input.value.trim();
    if (!q) return;
    results.innerHTML = `<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>`;

    // send to server (db + openai fallback). include lang
    const formData = new URLSearchParams();
    formData.append('keyword', q);
    formData.append('lang', langToggle.value || 'en');

    const r = await fetch('search_db.php', { method:'POST', body: formData });
    const data = await r.json();

    // render
    let html = '';
    if (data.source === 'db' || data.source === 'db_via_openai') {
      html += '<div class="list-group">';
      data.results.forEach(row=>{
        html += `<div class="list-group-item"><h6>${row.keyword}</h6><p>${row.content || ''}</p></div>`;
      });
      html += '</div>';
    } else if (data.source === 'openai') {
      html = `<div class="card"><div class="card-body">${data.answer}</div></div>`;
    } else {
      html = `<div class="alert alert-warning">No results found for "<strong>${q}</strong>".</div>`;
    }
    results.innerHTML = html;

    // Save to history view is handled server-side; we can also refresh history listing if open
  });

  // history button
  document.getElementById('historyBtn').addEventListener('click', async ()=>{
    const r = await fetch('my_history.php');
    const rows = await r.json();
    const body = document.getElementById('historyBody');
    body.innerHTML = rows.map(r=>`<div class="mb-2"><strong>${r.user_message}</strong><br><small class="text-muted">${r.created_at}</small></div>`).join('');
    const modal = new bootstrap.Modal(document.getElementById('historyModal'));
    modal.show();
  });

});
