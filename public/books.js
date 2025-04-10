document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const API_BASE_URL = 'https://digitizedlibrarymanagementsystem-demo.onrender.com/api/books/'; // Update with your actual API URL
    
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
        
        fetch(API_BASE_URL)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(books => {
                displayBooks(books);
                showLoading(false);
            })
            .catch(error => {
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
        const url = `${API_BASE_URL}/?${searchTypeValue}=${encodeURIComponent(searchValue)}`;
        
        showLoading(true);
        clearError();
        
        fetch(url)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(books => {
                if (Array.isArray(books) && books.length > 0) {
                    displayBooks(books);
                } else if (books.message) {
                    // API returned a message object
                    showError(books.message);
                    clearTable();
                } else {
                    // No books found
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
        
        // Check if books is an array or if it's an object with a data property
        const booksArray = Array.isArray(books) ? books : (books.data || []);
        
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