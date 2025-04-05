document.addEventListener("DOMContentLoaded", function() {
    const authorsTable = document.getElementById("authors-table").getElementsByTagName("tbody")[0];
    const authorForm = document.getElementById("author-form");
    const authorNameInput = document.getElementById("author-name");

    // Fetch and display authors
    function fetchAuthors() {
        fetch("api/authors/read.php")
            .then(response => response.json())
            .then(data => {
                authorsTable.innerHTML = ''; // Clear the table first
                data.forEach(author => {
                    const row = authorsTable.insertRow();
                    row.innerHTML = `
                        <td>${author.id}</td>
                        <td>${author.author}</td> <!-- changed from author.name -->
                        <td>
                            <button class="edit" onclick="editAuthor(${author.id})">Edit</button>
                            <button class="delete" onclick="deleteAuthor(${author.id})">Delete</button>
                        </td>
                    `;
                });
            })
            .catch(error => console.error("Error fetching authors:", error));
    }

    // Add new author
    authorForm.addEventListener("submit", function(event) {
        event.preventDefault();
        const authorName = authorNameInput.value.trim();

        if (authorName) {
            fetch("api/authors/create.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ name: authorName })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchAuthors(); // Refresh the author list
                    authorNameInput.value = ''; // Clear the input field
                } else {
                    alert("Failed to add author");
                }
            })
            .catch(error => console.error("Error adding author:", error));
        } else {
            alert("Please enter a valid name for the author.");
        }
    });

    // Edit author
    window.editAuthor = function(id) {
        const newName = prompt("Enter the new name for the author:");
        if (newName && newName.trim()) {
            fetch("api/authors/update.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id, name: newName.trim() })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchAuthors(); // Refresh the author list
                } else {
                    alert("Failed to update author");
                }
            })
            .catch(error => console.error("Error updating author:", error));
        }
    };

    // Delete author
    window.deleteAuthor = function(id) {
        if (confirm("Are you sure you want to delete this author?")) {
            fetch("api/authors/delete.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json"
                },
                body: JSON.stringify({ id })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    fetchAuthors(); // Refresh the author list
                } else {
                    alert("Failed to delete author");
                }
            })
            .catch(error => console.error("Error deleting author:", error));
        }
    };

    // Initial fetch of authors when page loads
    fetchAuthors();
});
