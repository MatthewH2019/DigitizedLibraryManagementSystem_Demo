document.addEventListener("DOMContentLoaded", function () {
    const booksTable = document.getElementById("books-table")?.getElementsByTagName("tbody")[0];
    const bookForm = document.getElementById("book-form");
    const titleInput = document.getElementById("book-title");
    const authorInput = document.getElementById("book-author");
    const genreInput = document.getElementById("book-genre");
    const isbnInput = document.getElementById("book-isbn");
    const yearInput = document.getElementById("book-year");
    const availabilityInput = document.getElementById("book-availability");

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
                        <td>${book.authors}</td>
                        <td>${book.genre}</td>
                        <td>${book.isbn}</td>
                        <td>${book.publication_year}</td>
                        <td>${book.availability}</td>
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
            const authors = authorInput.value.trim();
            const genre = genreInput.value.trim();
            const isbn = isbnInput.value.trim();
            const publication_year = yearInput.value.trim();
            const availability = availabilityInput.value;

            if (title && authors && genre && isbn && publication_year && availability) {
                fetch("api/books/create.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        title,
                        authors,
                        genre,
                        isbn,
                        publication_year,
                        availability
                    })
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            fetchBooks();
                            bookForm.reset();
                        } else {
                            alert("Failed to add book.");
                        }
                    })
                    .catch(err => console.error("Error adding book:", err));
            } else {
                alert("Please fill in all fields.");
            }
        });
    }

    window.editBook = function (id) {
        const newTitle = prompt("Enter the new title:");
        const newAuthors = prompt("Enter the new author(s):");
        const newGenre = prompt("Enter the new genre:");
        const newIsbn = prompt("Enter the new ISBN:");
        const newYear = prompt("Enter the new publication year:");
        const newAvailability = prompt("Enter new availability (available/checked_out):");

        if (newTitle && newAuthors && newGenre && newIsbn && newYear && newAvailability) {
            fetch("api/books/update.php", {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify({
                    id,
                    title: newTitle,
                    authors: newAuthors,
                    genre: newGenre,
                    isbn: newIsbn,
                    publication_year: newYear,
                    availability: newAvailability
                })
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
