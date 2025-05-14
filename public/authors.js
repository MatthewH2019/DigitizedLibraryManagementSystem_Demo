document.addEventListener('DOMContentLoaded', function() {
    // Configuration 
    const API_BASE_URL = '/DigitizedLibraryManagementSystem_Demo/api/authors/getAll.php';
    
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
        
        console.log("Fetching data from:", API_BASE_URL);
        
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
            .then(authors => {
                console.log("Parsed data:", authors);
                displayAuthors(authors);
                showLoading(false);
            })
            .catch(error => {
                console.error("Fetch error:", error);
                showError('Error fetching authors: ' + error.message);
                showLoading(false);
            });
    }

    function displayAuthors(authors) {
        clearTable();
        
        console.log("Type of authors:", typeof authors);
        
        // Handle different response formats
        let authorsArray;
        
        if (Array.isArray(authors)) {
            authorsArray = authors;
        } else if (authors.data && Array.isArray(authors.data)) {
            authorsArray = authors.data;
        } else if (typeof authors === 'object' && authors.message) {
            // API returned a message object (like "No Authors found")
            showError(authors.message);
            return;
        } else {
            console.warn("Unexpected data format:", authors);
            showError('Received data in unexpected format');
            return;
        }
        
        // Check if we have any authors to display
        if (authorsArray.length === 0) {
            showError('No authors found.');
            return;
        }

        authorsArray.forEach(author => {
            const row = document.createElement('tr');
            
            // Handle different property names
            const id = author.id || author.authorid || '';
            const name = author.author || author.name || '';
            
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