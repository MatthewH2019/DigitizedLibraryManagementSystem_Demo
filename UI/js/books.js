document.addEventListener('DOMContentLoaded', () => {
    const API = '/api/books';
    const tbody = document.querySelector('#books-table tbody');
    const form = document.getElementById('book-form');
    const titleInput = document.getElementById('book-title');
    const authorInput = document.getElementById('book-author');
    const fb = document.getElementById('book-feedback');
  
    function loadBooks() {
      fetch(`${API}/read.php`)
        .then(r => r.json())
        .then(data => {
          tbody.innerHTML = '';
          data.forEach(b => {
            const tr = document.createElement('tr');
            tr.innerHTML = `
              <td>${b.id}</td>
              <td>${b.title}</td>
              <td>${b.author_id}</td>
              <td>
                <button class="edit" data-id="${b.id}">Edit</button>
                <button class="delete" data-id="${b.id}">Delete</button>
              </td>`;
            tbody.appendChild(tr);
          });
          tbody.querySelectorAll('.delete').forEach(btn =>
            btn.addEventListener('click', () => deleteBook(btn.dataset.id))
          );
          tbody.querySelectorAll('.edit').forEach(btn =>
            btn.addEventListener('click', () => editBook(btn.dataset.id))
          );
        })
        .catch(e => tbody.innerHTML = `<tr><td colspan="4">Error: ${e.message}</td></tr>`);
    }
  
    form.addEventListener('submit', e => {
      e.preventDefault();
      fetch(`${API}/create.php`, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({
          title: titleInput.value.trim(),
          author_id: Number(authorInput.value)
        })
      })
        .then(r => r.json())
        .then(j => {
          if (j.success) {
            fb.textContent = 'Book added!';
            fb.className = 'feedback success';
            form.reset();
            loadBooks();
          } else throw new Error(j.message);
        })
        .catch(e => { fb.textContent = e.message; fb.className = 'feedback error'; });
    });
  
    function editBook(id) {
      const newTitle = prompt('New title?');
      const newAuthor = prompt('New author ID?');
      if (!newTitle || !newAuthor) return;
      fetch(`${API}/update.php`, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({id, title: newTitle, author_id: Number(newAuthor)})
      })
        .then(r => r.json())
        .then(j => {
          if (j.success) loadBooks();
          else throw new Error(j.message);
        })
        .catch(e => alert('Error: '+e.message));
    }
  
    function deleteBook(id) {
      if (!confirm(`Delete book ${id}?`)) return;
      fetch(`${API}/delete.php`, {
        method: 'POST',
        headers: {'Content-Type':'application/json'},
        body: JSON.stringify({id})
      })
        .then(r => r.json())
        .then(j => {
          if (j.success) loadBooks();
          else throw new Error(j.message);
        })
        .catch(e => alert('Error: '+e.message));
    }
  
    loadBooks();
  });