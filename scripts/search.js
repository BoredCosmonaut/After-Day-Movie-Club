
const movieButtons = document.querySelectorAll("#search-movie-image");

movieButtons.forEach(button => {
    button.addEventListener("click",(e) => {
        const img = e.target;
        const movieName = img.getAttribute("movie-name");
        const movieId = img.getAttribute("movie-id");
        window.location.href = `movie-page.php?movieId=${encodeURIComponent(movieId)}&movieName=${encodeURIComponent(movieName)}`;
    })
})