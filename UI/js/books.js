document.addEventListener("DOMContentLoaded", function () {
    const booksTable = document.getElementById("books-table")?.getElementsByTagName("tbody")[0];
    const bookForm = document.getElementById("book-form");
    const titleInput = document.getElementById("book-title");
    const authorInput = document.getElementById("book-author");

    function fetchBooks() {
        fetch("api/books/getAll.php")
            .then(response => response.json())
            .then(data => {
                if (!booksTable) return;

                booksTable.innerHTML = ""; // Clear the table

                data.forEach(book => {
                    const row = booksTable.insertRow();
                    row.innerHTML = `
                        <td>${book.id}</td>
                        <td>${book.title}</td>
                        <td>${book.author}</td>
                        <td>
                            <button class="edit" onclick="editBook(${book.id})">Edit</button>
                            <button class="delete" onclick="deleteBook(${book.id})">Delete</button>
                        </td>
                    `;
                });
            })
            .catch(error => console.error("Error fetching books:", error));
    }

    if (bookForm) {
        bookForm.addEventListener("submit", function (e) {
            e.preventDefault();
            const title = titleInput.value.trim();
            const author = authorInput.value.trim();

            if (title && author) {
                fetch("api/books/create.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ title, author })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            fetchBooks();
                            titleInput.value = '';
                            authorInput.value = '';
                        } else {
                            alert("Failed to add book.");
                        }
                    })
                    .catch(err => console.error("Error adding book:", err));
            } else {
                alert("Please fill in both the title and author.");
            }
        });
    }

    window.editBook = function (id) {
        const newTitle = prompt("Enter the new title:");
        const newAuthor = prompt("Enter the new author:");

        if (newTitle && newAuthor) {
            fetch("api/books/update.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id, title: newTitle, author: newAuthor })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        fetchBooks();
                    } else {
                        alert("Failed to update book.");
                    }
                })
                .catch(err => console.error("Error updating book:", err));
        }
    };

    window.deleteBook = function (id) {
        if (confirm("Are you sure you want to delete this book?")) {
            fetch("api/books/delete.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({ id })
            })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        fetchBooks();
                    } else {
                        alert("Failed to delete book.");
                    }
                })
                .catch(err => console.error("Error deleting book:", err));
        }
    };

    // Initial load
    fetchBooks();
});
