document.addEventListener("DOMContentLoaded", function () {
    const booksTable = document.getElementById("books-table")?.getElementsByTagName("tbody")[0];
    const bookForm = document.getElementById("book-form");
    const titleInput = document.getElementById("book-title");
    const authorInput = document.getElementById("book-author");
    const genreInput = document.getElementById("book-genre");
    const isbnInput = document.getElementById("book-isbn");
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
                        <td>${book.genre}</td>
                        <td>${book.title}</td>
                        <td>${book.authors}</td>
                        <td>${book.isbn}</td>
                        <td>${book.availability}</td>
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
            const availability = availabilityInput.value;

            if (title && authors && genre && isbn && availability) {
                fetch("api/books/create.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({
                        title,
                        authors,
                        genre,
                        isbn,
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

    // Initial load
    fetchBooks();
});
