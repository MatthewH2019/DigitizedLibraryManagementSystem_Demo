document.addEventListener('DOMContentLoaded', () => {
    const API_BASE = '/api/authors';
    const tableBody = document.querySelector('#authors-table tbody');
    const form = document.getElementById('author-form');
    const nameInput = document.getElementById('author-name');
    const feedback = document.getElementById('form-feedback');
  
    // 1. Load and render authors
    function fetchAuthors() {
      fetch(`${API_BASE}/read.php`)
        .then(res => res.json())
        .then(data => {
          tableBody.innerHTML = '';
          data.forEach(a => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${a.id}</td>
              <td>${a.author}</td>
              <td>
                <button class="edit-btn" data-id="${a.id}">Edit</button>
                <button class="delete-btn" data-id="${a.id}">Delete</button>
              </td>
            `;
            tableBody.appendChild(tr);
          });
          // wire up buttons
          document.querySelectorAll('.delete-btn').forEach(btn =>
            btn.addEventListener('click', () => deleteAuthor(btn.dataset.id))
          );
          document.querySelectorAll('.edit-btn').forEach(btn =>
            btn.addEventListener('click', () => editAuthor(btn.dataset.id))
          );
        })
        .catch(err => {
          tableBody.innerHTML = `<tr><td colspan="3">Error: ${err.message}</td></tr>`;
          console.error(err);
        });
    }
  
    // 2. Add a new author
    form.addEventListener('submit', e => {
      e.preventDefault();
      const authorName = nameInput.value.trim();
      if (!authorName) return;
  
      fetch(`${API_BASE}/create.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ author: authorName })
      })
        .then(res => res.json())
        .then(json => {
          if (json.success || json.id) {
            feedback.textContent = `Added author (ID ${json.id || json.id})`;
            feedback.className = 'feedback success';
            nameInput.value = '';
            fetchAuthors();
          } else {
            throw new Error(json.message || 'Create failed');
          }
        })
        .catch(err => {
          feedback.textContent = `Error: ${err.message}`;
          feedback.className = 'feedback error';
          console.error(err);
        });
    });
  
    // 3. Edit an author
    function editAuthor(id) {
      const newName = prompt('Enter new name:');
      if (!newName) return;
  
      fetch(`${API_BASE}/update.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, author: newName.trim() })
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            fetchAuthors();
          } else {
            throw new Error(json.message || 'Update failed');
          }
        })
        .catch(err => {
          alert('Error: ' + err.message);
          console.error(err);
        });
    }
  
    // 4. Delete an author
    function deleteAuthor(id) {
      if (!confirm('Delete author ID ' + id + '?')) return;
  
      fetch(`${API_BASE}/delete.php`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id })
      })
        .then(res => res.json())
        .then(json => {
          if (json.success) {
            fetchAuthors();
          } else {
            throw new Error(json.message || 'Delete failed');
          }
        })
        .catch(err => {
          alert('Error: ' + err.message);
          console.error(err);
        });
    }
  
    // initial load
    fetchAuthors();
  });