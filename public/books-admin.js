document.addEventListener('DOMContentLoaded', function() {
    // API Endpoints
    const BOOKS_API = '/DigitizedLibraryManagementSystem_Demo/api/books';
    const AUTHORS_API = '/DigitizedLibraryManagementSystem_Demo/api/authors/getAll.php';
    
    // DOM Elements
    const booksTableBody = document.getElementById('booksTableBody');
    const loadingMessage = document.getElementById('loadingMessage');
    const errorMessage = document.getElementById('errorMessage');
    
    // Tab Navigation
    const tabButtons = document.querySelectorAll('.tab-button');
    const tabContents = document.querySelectorAll('.tab-content');
    
    // Form Elements - Add Book
    const addBookForm = document.getElementById('add-book-form');
    const addTitle = document.getElementById('add-title');
    const addIsbn = document.getElementById('add-isbn');
    const addGenre = document.getElementById('add-genre');
    const addYear = document.getElementById('add-year');
    const addAvailability = document.getElementById('add-availability');
    const addAuthors = document.getElementById('add-authors');
    const addSuccess = document.getElementById('add-success');
    const addError = document.getElementById('add-error');
    
    // Form Elements - Edit Book
    const editBookForm = document.getElementById('edit-book-form');
    const editBookSelect = document.getElementById('edit-book-select');
    const editId = document.getElementById('edit-id');
    const editTitle = document.getElementById('edit-title');
    const editIsbn = document.getElementById('edit-isbn');
    const editGenre = document.getElementById('edit-genre');
    const editYear = document.getElementById('edit-year');
    const editAvailability = document.getElementById('edit-availability');
    const editAuthors = document.getElementById('edit-authors');
    const editSuccess = document.getElementById('edit-success');
    const editError = document.getElementById('edit-error');
    const editCancel = document.getElementById('edit-cancel');
    
    // Form Elements - Delete Book
    const deleteBookForm = document.getElementById('delete-book-form');
    const deleteBookSelect = document.getElementById('delete-book-select');
    const deleteBookInfo = document.getElementById('delete-book-info');
    const deleteTitle = document.getElementById('delete-title');
    const deleteAuthors = document.getElementById('delete-authors');
    const deleteIsbn = document.getElementById('delete-isbn');
    const deleteGenre = document.getElementById('delete-genre');
    const deleteSuccess = document.getElementById('delete-success');
    const deleteError = document.getElementById('delete-error');
    
    // Search Elements
    const searchInput = document.getElementById('searchInput');
    const searchType = document.getElementById('searchType');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');
    
    // Global Data
    let allBooks = [];
    let allAuthors = [];
    
    // Initialize
    initializeTabs();
    loadAuthors().then(() => {
        loadBooks();
    });
    
    // Event Listeners
    searchBtn.addEventListener('click', performSearch);
    resetBtn.addEventListener('click', resetSearch);
    addBookForm.addEventListener('submit', handleAddBook);
    editBookForm.addEventListener('submit', handleEditBook);
    deleteBookForm.addEventListener('submit', handleDeleteBook);
    editBookSelect.addEventListener('change', handleEditBookSelect);
    deleteBookSelect.addEventListener('change', handleDeleteBookSelect);
    editCancel.addEventListener('click', resetEditForm);
    
    // Tab Navigation Functions
    function initializeTabs() {
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                // Remove active class from all buttons and contents
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabContents.forEach(content => content.classList.remove('active'));
                
                // Add active class to clicked button
                button.classList.add('active');
                
                // Show corresponding content
                const tabId = button.getAttribute('data-tab');
                document.getElementById(`${tabId}-tab`).classList.add('active');
            });
        });
    }
    
    // Load Books Function
    function loadBooks() {
        showLoading(true);
        clearError();
        
        fetch(`${BOOKS_API}/getAll.php`)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        throw new Error(`JSON parsing failed: ${err.message}. Raw: ${text.substr(0, 100)}...`);
                    }
                });
            })
            .then(books => {
                allBooks = Array.isArray(books) ? books : [];
                displayBooks(allBooks);
                populateBookDropdowns();
                showLoading(false);
            })
            .catch(error => {
                console.error("Fetch error:", error);
                showError('Error fetching books: ' + error.message);
                showLoading(false);
            });
    }
    
    // Load Authors Function
    function loadAuthors() {
        return fetch(AUTHORS_API)
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Server responded with status ${response.status}`);
                }
                return response.text().then(text => {
                    try {
                        return JSON.parse(text);
                    } catch (err) {
                        throw new Error(`JSON parsing failed: ${err.message}`);
                    }
                });
            })
            .then(authors => {
                allAuthors = Array.isArray(authors) ? authors : [];
                populateAuthorDropdowns();
                return allAuthors;
            })
            .catch(error => {
                console.error("Error fetching authors:", error);
                return [];
            });
    }
    
    // Display Books Function
    function displayBooks(books) {
        booksTableBody.innerHTML = '';
        
        books.forEach(book => {
            const row = document.createElement('tr');
            
            // Format authors
            let authorsText = '';
            if (book.authors && book.authors.length > 0) {
                authorsText = book.authors.map(author => author.name).join(', ');
            }
            
            // Availability formatting
            const availabilityText = book.availability ? 
                '<span class="available">Available</span>' : 
                '<span class="unavailable">Unavailable</span>';
            
            row.innerHTML = `
                <td>${book.id || ''}</td>
                <td>${book.title || ''}</td>
                <td>${authorsText}</td>
                <td>${book.genre || ''}</td>
                <td>${book.isbn || ''}</td>
                <td>${book.publication_year || ''}</td>
                <td>${availabilityText}</td>
                <td>
                    <button class="edit-btn" data-id="${book.id}">Edit</button>
                    <button class="delete-btn" data-id="${book.id}">Delete</button>
                </td>
            `;
            
            booksTableBody.appendChild(row);
        });
        
        // Add event listeners to action buttons
        document.querySelectorAll('.edit-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const bookId = btn.getAttribute('data-id');
                // Switch to edit tab
                document.querySelector('[data-tab="edit"]').click();
                // Select the book in the dropdown
                editBookSelect.value = bookId;
                handleEditBookSelect();
            });
        });
        
        document.querySelectorAll('.delete-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const bookId = btn.getAttribute('data-id');
                // Switch to delete tab
                document.querySelector('[data-tab="delete"]').click();
                // Select the book in the dropdown
                deleteBookSelect.value = bookId;
                handleDeleteBookSelect();
            });
        });
    }
    
    // Populate Author Dropdowns
    function populateAuthorDropdowns() {
        const authorDropdowns = [addAuthors, editAuthors];
        
        authorDropdowns.forEach(dropdown => {
            dropdown.innerHTML = '';
            
            allAuthors.forEach(author => {
                const option = document.createElement('option');
                option.value = author.id;
                option.textContent = author.author;
                dropdown.appendChild(option);
            });
        });
    }
    
    // Populate Book Dropdowns
    function populateBookDropdowns() {
        const bookDropdowns = [editBookSelect, deleteBookSelect];
        
        bookDropdowns.forEach(dropdown => {
            // Clear all options except the first one
            const firstOption = dropdown.options[0];
            dropdown.innerHTML = '';
            dropdown.appendChild(firstOption);
            
            allBooks.forEach(book => {
                const option = document.createElement('option');
                option.value = book.id;
                option.textContent = `${book.title} (ID: ${book.id})`;
                dropdown.appendChild(option);
            });
        });
    }
    
    // Handle Add Book
    function handleAddBook(e) {
        e.preventDefault();
        
        // Get form values
        const title = addTitle.value.trim();
        const isbn = addIsbn.value.trim();
        const genre = addGenre.value.trim();
        const year = addYear.value ? parseInt(addYear.value) : null;
        const availability = parseInt(addAvailability.value);
        
        // Get selected authors
        const authorIds = Array.from(addAuthors.selectedOptions).map(option => parseInt(option.value));
        
        // Validate required fields
        if (!title || !isbn) {
            showAddError('Title and ISBN are required fields');
            return;
        }
        
        // Prepare book data
        const bookData = {
            title: title,
            isbn: isbn,
            genre: genre,
            publication_year: year,
            availability: availability,
            authors: authorIds
        };
        
        // Send request to API
        fetch(`${BOOKS_API}/create.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(bookData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server responded with status ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Show success message
            showAddSuccess('Book added successfully!');
            
            // Reset form
            addBookForm.reset();
            
            // Reload books
            loadBooks();
        })
        .catch(error => {
            console.error('Error adding book:', error);
            showAddError('Error adding book: ' + error.message);
        });
    }
    
    // Handle Edit Book Select
    function handleEditBookSelect() {
        const bookId = editBookSelect.value;
        
        if (!bookId) {
            resetEditForm();
            return;
        }
        
        const book = allBooks.find(b => b.id == bookId);
        
        if (!book) {
            showEditError('Book not found');
            return;
        }
        
        // Populate form fields
        editId.value = book.id;
        editTitle.value = book.title || '';
        editIsbn.value = book.isbn || '';
        editGenre.value = book.genre || '';
        editYear.value = book.publication_year || '';
        editAvailability.value = book.availability ? '1' : '0';
        
        // Select authors
        Array.from(editAuthors.options).forEach(option => {
            option.selected = book.authors && book.authors.some(author => author.id == option.value);
        });
    }
    
    // Handle Edit Book
    function handleEditBook(e) {
        e.preventDefault();
        
        // Get form values
        const id = editId.value;
        const title = editTitle.value.trim();
        const isbn = editIsbn.value.trim();
        const genre = editGenre.value.trim();
        const year = editYear.value ? parseInt(editYear.value) : null;
        const availability = parseInt(editAvailability.value);
        
        // Get selected authors
        const authorIds = Array.from(editAuthors.selectedOptions).map(option => parseInt(option.value));
        
        // Validate required fields
        if (!id || !title || !isbn) {
            showEditError('Book ID, Title, and ISBN are required fields');
            return;
        }
        
        // Prepare book data
        const bookData = {
            bookid: id,
            title: title,
            isbn: isbn,
            genre: genre,
            publication_year: year,
            availability: availability,
            authors: authorIds
        };
        
        // Send request to API
        fetch(`${BOOKS_API}/update.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(bookData)
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server responded with status ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            showEditSuccess('Book updated successfully!');
            
            resetEditForm();
            
            loadBooks();
        })
        .catch(error => {
            console.error('Error updating book:', error);
            showEditError('Error updating book: ' + error.message);
        });
    }
    
    // Handle Delete Book Select
    function handleDeleteBookSelect() {
        const bookId = deleteBookSelect.value;
        
        if (!bookId) {
            deleteBookInfo.style.display = 'none';
            return;
        }
        
        const book = allBooks.find(b => b.id == bookId);
        
        if (!book) {
            showDeleteError('Book not found');
            deleteBookInfo.style.display = 'none';
            return;
        }
        
        // Populate book info
        deleteTitle.textContent = book.title || '';
        deleteIsbn.textContent = book.isbn || '';
        deleteGenre.textContent = book.genre || '';
        
        // Format authors
        let authorsText = '';
        if (book.authors && book.authors.length > 0) {
            authorsText = book.authors.map(author => author.name).join(', ');
        }
        deleteAuthors.textContent = authorsText;
        
        // Show book info
        deleteBookInfo.style.display = 'block';
    }
    
    // Handle Delete Book
    function handleDeleteBook(e) {
        e.preventDefault();
        
        const bookId = deleteBookSelect.value;
        
        if (!bookId) {
            showDeleteError('Please select a book to delete');
            return;
        }
        
        // Confirm deletion
        if (!confirm('Are you sure you want to delete this book? This action cannot be undone.')) {
            return;
        }
        
        // Send request to API
        fetch(`${BOOKS_API}/delete.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id: bookId })
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`Server responded with status ${response.status}`);
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            
            // Show success message
            showDeleteSuccess('Book deleted successfully!');
            
            // Reset form
            deleteBookForm.reset();
            deleteBookInfo.style.display = 'none';
            
            // Reload books
            loadBooks();
        })
        .catch(error => {
            console.error('Error deleting book:', error);
            showDeleteError('Error deleting book: ' + error.message);
        });
    }
    
    // Search Functions
    function performSearch() {
        const searchValue = searchInput.value.trim();
        
        if (!searchValue) {
            resetSearch();
            return;
        }
        
        const searchTypeValue = searchType.value;
        const filteredBooks = allBooks.filter(book => {
            const searchLower = searchValue.toLowerCase();
            
            switch (searchTypeValue) {
                case 'title':
                    return book.title && book.title.toLowerCase().includes(searchLower);
                case 'author_name':
                    return book.authors && book.authors.some(author => 
                        author.name && author.name.toLowerCase().includes(searchLower));
                case 'genre':
                    return book.genre && book.genre.toLowerCase().includes(searchLower);
                case 'isbn':
                    return book.isbn && book.isbn.toLowerCase().includes(searchLower);
                default:
                    return false;
            }
        });
        
        if (filteredBooks.length > 0) {
            displayBooks(filteredBooks);
        } else {
            booksTableBody.innerHTML = `<tr><td colspan="8">No books found matching your search criteria.</td></tr>`;
        }
    }
    
    function resetSearch() {
        searchInput.value = '';
        displayBooks(allBooks);
    }
    
    // Reset Form Functions
    function resetEditForm() {
        editBookForm.reset();
        editBookSelect.value = '';
        editId.value = '';
        hideEditMessages();
    }
    
    // Message Functions
    function showAddSuccess(message) {
        addSuccess.textContent = message;
        addSuccess.style.display = 'block';
        addError.style.display = 'none';
        setTimeout(() => { addSuccess.style.display = 'none'; }, 5000);
    }
    
    function showAddError(message) {
        addError.textContent = message;
        addError.style.display = 'block';
        addSuccess.style.display = 'none';
    }
    
    function showEditSuccess(message) {
        editSuccess.textContent = message;
        editSuccess.style.display = 'block';
        editError.style.display = 'none';
        setTimeout(() => { editSuccess.style.display = 'none'; }, 5000);
    }
    
    function showEditError(message) {
        editError.textContent = message;
        editError.style.display = 'block';
        editSuccess.style.display = 'none';
    }
    
    function hideEditMessages() {
        editSuccess.style.display = 'none';
        editError.style.display = 'none';
    }
    
    function showDeleteSuccess(message) {
        deleteSuccess.textContent = message;
        deleteSuccess.style.display = 'block';
        deleteError.style.display = 'none';
        setTimeout(() => { deleteSuccess.style.display = 'none'; }, 5000);
    }
    
    function showDeleteError(message) {
        deleteError.textContent = message;
        deleteError.style.display = 'block';
        deleteSuccess.style.display = 'none';
    }
    
    function showLoading(show) {
        loadingMessage.style.display = show ? 'block' : 'none';
    }
    
    function showError(message) {
        errorMessage.textContent = message;
        errorMessage.style.display = 'block';
    }
    
    function clearError() {
        errorMessage.textContent = '';
        errorMessage.style.display = 'none';
    }
});