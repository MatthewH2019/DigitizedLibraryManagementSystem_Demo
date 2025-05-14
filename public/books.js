document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const API_BASE_URL = '/DigitizedLibraryManagementSystem_Demo/api/books/getAll.php';
    
    // DOM Elements
    const booksTableBody = document.getElementById('booksTableBody');
    const loadingMessage = document.getElementById('loadingMessage');
    const errorMessage = document.getElementById('errorMessage');
    const searchInput = document.getElementById('searchInput');
    const searchType = document.getElementById('searchType');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');
    
    // Initialize
    fetchAllBooks();
    
    // Event Listeners
    searchBtn.addEventListener('click', performSearch);
    resetBtn.addEventListener('click', fetchAllBooks);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });
    
    // Functions
    function fetchAllBooks() {
        showLoading(true);
        clearError();
        
        console.log("Fetching books data from:", API_BASE_URL);
        
        fetch(API_BASE_URL)
            .then(response => {
                console.log("Response status:", response.status);
                
                // Get raw text first for debugging
                return response.text().then(text => {
                    console.log("Raw response:", text);
                    
                    // Check if text is empty
                    if (!text.trim()) {
                        throw new Error("Empty response received from server");
                    }
                    
                    try {
                        // Try to parse the JSON
                        return JSON.parse(text);
                    } catch (err) {
                        console.error("JSON parse error:", err);
                        throw new Error(`JSON parsing failed: ${err.message}. Raw: ${text.substr(0, 100)}...`);
                    }
                });
            })
            .then(books => {
                console.log("Parsed books data:", books);
                displayBooks(books);
                showLoading(false);
            })
            .catch(error => {
                console.error("Fetch error:", error);
                showError('Error fetching books: ' + error.message);
                showLoading(false);
            });
    }
    
    function performSearch() {
        const searchValue = searchInput.value.trim();
        if (!searchValue) {
            fetchAllBooks();
            return;
        }
        
        const searchTypeValue = searchType.value;
        
        // Get all books first
        showLoading(true);
        clearError();
        
        fetch(API_BASE_URL)
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
            .then(books => {
                // Filter books based on search criteria
                let filteredBooks = [];
                
                if (Array.isArray(books)) {
                    filteredBooks = books.filter(book => {
                        const searchLower = searchValue.toLowerCase();
                        
                        switch(searchTypeValue) {
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
                }
                
                if (filteredBooks.length > 0) {
                    displayBooks(filteredBooks);
                } else {
                    showError('No books found matching your search criteria.');
                    clearTable();
                }
                
                showLoading(false);
            })
            .catch(error => {
                showError('Error searching books: ' + error.message);
                showLoading(false);
            });
    }
    
    function displayBooks(books) {
        clearTable();
        
        // Handle different response formats
        let booksArray;
        
        if (Array.isArray(books)) {
            booksArray = books;
        } else if (books.data && Array.isArray(books.data)) {
            booksArray = books.data;
        } else if (typeof books === 'object' && books.message) {
            // API returned a message object (like "No Books Found")
            showError(books.message);
            return;
        } else {
            console.warn("Unexpected data format:", books);
            showError('Received data in unexpected format');
            return;
        }
        
        // Check if we have any books to display
        if (booksArray.length === 0) {
            showError('No books found.');
            return;
        }
        
        booksArray.forEach(book => {
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
                <td>${book.title || ''}</td>
                <td>${authorsText}</td>
                <td>${book.genre || ''}</td>
                <td>${book.isbn || ''}</td>
                <td>${book.publication_year || ''}</td>
                <td>${availabilityText}</td>
            `;
            
            booksTableBody.appendChild(row);
        });
    }
    
    function clearTable() {
        booksTableBody.innerHTML = '';
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