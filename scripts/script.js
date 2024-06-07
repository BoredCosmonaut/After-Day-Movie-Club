//Home,bookmark,profile sites all use this script
const hearts = document.querySelectorAll(".post-heart");
const bookmarks = document.querySelectorAll(".post-bookmark");
const shares = document.querySelectorAll(".post-share");

hearts.forEach(heart => {
    heart.addEventListener("click", (e) => {
        const img = e.target;
        const postId = img.getAttribute("data-post-id");
        let action = "";
        const likeAmountElement = document.getElementById(`${postId}h`);
        console.log(likeAmountElement);
        if(e.target.src.endsWith("i-heart.svg")) {
            console.log("target filled")
            likeAmountElement.innerText = parseInt(likeAmountElement.innerText) + 1;
            e.target.src = "Images/Icons/i-heart-red.svg";
            action = "add";
        }
        else if(e.target.src.endsWith("i-heart-red.svg")) {
            likeAmountElement.innerText = parseInt(likeAmountElement.innerText) - 1;
            console.log("target unfilled")
            e.target.src = "Images/Icons/i-heart.svg";
            action = "remove";
        }
        fetch("logic/likedReviews.php", {
            method: "POST",
            headers: {
                'Content-Type' : 'application/json'
            },
            body:JSON.stringify({action: action, postId: postId})
        })
        .then(response => response.json())
        .then(data => {
            if(!data.success) {
                //Revert icon change if it fails
                console.log("data failure");
                if(action == "add") {
                    img.src = "Images/Icons/i-heart.svg";;
                } else {
                    img.src = "Images/Icons/i-heart-red.svg";
                }
                alert("There was a problem saving this post");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            //Revert the icon change on error
            if(action === "add") {
                img.src = "Images/Icons/i-heart.svg";
            } else {
                img.src = "Images/Icons/i-heart-red.svg";
            }
        })
    })
})

bookmarks.forEach(bookmark => {
    bookmark.addEventListener("click", (e) => {
        const img = e.target;
        const postId = img.getAttribute("data-post-id");
        let action = "";
        const saveAmountElement = document.getElementById(postId);
        console.log(saveAmountElement);
        if(e.target.src.endsWith("i-bookmark.svg")) {
            console.log("target filled")
            saveAmountElement.innerText = parseInt(saveAmountElement.innerText) + 1;
            e.target.src = "Images/Icons/i-bookmark-filled.png";
            action = "add";
        }
        else if(e.target.src.endsWith("i-bookmark-filled.png")) {
            saveAmountElement.innerText = parseInt(saveAmountElement.innerText) - 1;
            console.log("target unfilled")
            e.target.src = "Images/Icons/i-bookmark.svg";
            action = "remove";
        }
        fetch("logic/savedReviews.php", {
            method: "POST",
            headers: {
                'Content-Type' : 'application/json'
            },
            body:JSON.stringify({action: action, postId: postId})
        })
        .then(response => response.json())
        .then(data => {
            if(!data.success) {
                //Revert icon change if it fails
                console.log("data failure");
                if(action == "add") {
                    img.src = "Images/Icons/i-bookmark.png";;
                } else {
                    img.src = "Images/Icons/i-bookmark-filled.png";
                }
                alert("There was a problem saving this post");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            //Revert the icon change on error
            if(action === "add") {
                img.src = "Images/Icons/i-bookmark.svg";
            } else {
                img.src = "Images/Icons/i-bookmark-filled.png";
            }
        })
    })
})

shares.forEach(share => {
    share.addEventListener("click", (e) => {
        const img = e.target;
        const postId = img.getAttribute("data-post-id");
        let action = "";
        const saveAmountElement = document.getElementById(`${postId}s`);
        console.log(saveAmountElement);
        if(e.target.src.endsWith("i-share.svg")) {
            console.log("target filled")
            saveAmountElement.innerText = parseInt(saveAmountElement.innerText) + 1;
            e.target.src = "Images/Icons/i-share-filled.png";
            action = "add";
        }
        else if(e.target.src.endsWith("i-share-filled.png")) {
            saveAmountElement.innerText = parseInt(saveAmountElement.innerText) - 1;
            console.log("target unfilled")
            e.target.src = "Images/Icons/i-share.svg";
            action = "remove";
        }
        fetch("logic/sharedReviews.php", {
            method: "POST",
            headers: {
                'Content-Type' : 'application/json'
            },
            body:JSON.stringify({action: action, postId: postId})
        })
        .then(response => response.json())
        .then(data => {
            if(!data.success) {
                //Revert icon change if it fails
                console.log("data failure");
                if(action == "add") {
                    img.src = "Images/Icons/i-share.png";;
                } else {
                    img.src = "Images/Icons/i-share-filled.png";
                }
                alert("There was a problem saving this post");
            }
        })
        .catch(error => {
            console.error("Error:", error);
            //Revert the icon change on error
            if(action === "add") {
                img.src = "Images/Icons/i-share.svg";
            } else {
                img.src = "Images/Icons/i-share-filled.png";
            }
        })
    })
})
