document.addEventListener('DOMContentLoaded', function() {
    // Configuration
    const API_BASE_URL = 'https://digitizedlibrarymanagementsystem-demo.onrender.com/api/authors/'; // Update with your actual API URL
    
    // DOM Elements
    const authorsTableBody = document.getElementById('authorsTableBody');
    const loadingMessage = document.getElementById('loadingMessage');
    const errorMessage = document.getElementById('errorMessage');
    const searchInput = document.getElementById('searchInput');
    const searchType = document.getElementById('searchType');
    const searchBtn = document.getElementById('searchBtn');
    const resetBtn = document.getElementById('resetBtn');

    // Initialize
    fetchAllAuthors();

    // Event Listeners
    searchBtn.addEventListener('click', performSearch);
    resetBtn.addEventListener('click', fetchAllAuthors);
    searchInput.addEventListener('keypress', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Functions
    function fetchAllAuthors() {
        showLoading(true);
        clearError();

        fetch(API_BASE_URL)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(authors => {
                displayAuthors(authors);
                showLoading(false);
            })
            .catch(error => {
                showError('Error fetching authors: ' + error.message);
                showLoading(false);
            });
    }

    function performSearch() {
        const searchValue = searchInput.value.trim();
        if (!searchValue) {
            fetchAllAuthors();
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
            .then(authors => {
                if (Array.isArray(authors) && authors.length > 0) {
                    displayAuthors(authors);
                } else if (authors.message) {
                    showError(authors.message);
                    clearTable();
                } else {
                    showError('No authors found matching your search criteria.');
                    clearTable();
                }
                showLoading(false);
            })
            .catch(error => {
                showError('Error searching authors: ' + error.message);
                showLoading(false);
            });
    }

    function displayAuthors(authors) {
        clearTable();

        const authorsArray = Array.isArray(authors) ? authors : (authors.data || []);
        
        // Check if we there are any authors to display
        if (authorsArray.length === 0) {
            showError('No authors found.');
            return;
        }

        authorsArray.forEach(author => {
            const row = document.createElement('tr');
            const id = author.authorid;
            const name = author.name;
            
            row.innerHTML = `
                <td>${id}</td>
                <td>${name}</td>
            `;
            authorsTableBody.appendChild(row);
        });
    }


    function clearTable() {
        authorsTableBody.innerHTML = '';
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
