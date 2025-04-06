function fetchBooks() {
    fetch('http://localhost/DigitizedLibraryManagementSystem_Demo/api/books/getAll.php') // Corrected the fetch URL here
        .then(response => response.text()) // Get the response as text
        .then(text => {
            console.log("Response received: ", text); // Log the response for debugging
            try {
                const data = JSON.parse(text); // Attempt to parse the text as JSON
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
            } catch (error) {
                console.error("Error parsing JSON: ", error);
                console.log("Response text: ", text); // Log the text that failed to parse
            }
        })
        .catch(error => console.error("Error fetching books:", error));
}
