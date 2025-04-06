document.addEventListener("DOMContentLoaded", function () {
    const authorsTable = document.getElementById("authors-table")?.getElementsByTagName("tbody")[0];
    const authorForm = document.getElementById("author-form");
    const authorNameInput = document.getElementById("author-name");

    // Fetch and display all authors
    function fetchAuthors() {
        fetch("api/authors/getAll.php")
            .then(response => response.json())
            .then(data => {
                if (authorsTable) {
                    authorsTable.innerHTML = '';
                    data.forEach(author => {
                        const row = authorsTable.insertRow();
                        row.innerHTML = `
                            <td>${author.id}</td>
                            <td>${author.name}</td>
                            <td>
                                <button onclick="editAuthor(${author.id}, '${author.name}')">Edit</button>
                                <button onclick="deleteAuthor(${author.id})">Delete</button>
                            </td>
                        `;
                    });
                }
            })
            .catch(error => console.error("Error fetching authors:", error));
    }

    // Add a new author
    if (authorForm) {
        authorForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const name = authorNameInput.value.trim();

            if (!name) {
                alert("Author name is required.");
                return;
            }

            fetch("api/authors/create.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ name }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchAuthors();
                        authorNameInput.value = '';
                    } else {
                        alert("Failed to add author.");
                    }
                })
                .catch(error => console.error("Error adding author:", error));
        });
    }

    // Edit author
    window.editAuthor = function (id, currentName) {
        const newName = prompt("Enter new name for the author:", currentName);
        if (newName && newName.trim()) {
            fetch("api/authors/update.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ id, name: newName.trim() }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchAuthors();
                    } else {
                        alert("Failed to update author.");
                    }
                })
                .catch(error => console.error("Error updating author:", error));
        }
    };

    // Delete author
    window.deleteAuthor = function (id) {
        if (confirm("Are you sure you want to delete this author?")) {
            fetch("api/authors/delete.php", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                },
                body: JSON.stringify({ id }),
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        fetchAuthors();
                    } else {
                        alert("Failed to delete author.");
                    }
                })
                .catch(error => console.error("Error deleting author:", error));
        }
    };

    fetchAuthors();
});
