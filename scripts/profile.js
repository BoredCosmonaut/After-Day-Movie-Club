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
        console.log(saveAmountElement.innerText);
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

let myPostsButton = document.getElementById("my-post-container");
let sharedPostsButton = document.getElementById("shared-posts-container");

let myPosts = document.getElementById("my-post-timeline-container");
let sharedPosts = document.getElementById("shared-posts-timeline-container");

myPostsButton.addEventListener("click", () => {
    if(myPosts.style.display === "flex") {
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    else {
        myPosts.style.display = "flex"
        sharedPosts.style.display = "none"
    }
})

sharedPostsButton.addEventListener("click", () => {
    console.log("shared-button")
    if(sharedPosts.style.display === "flex") {
        window.scrollTo({top: 0, behavior: 'smooth'});
    }
    else {
        myPosts.style.display = "none"
        sharedPosts.style.display = "flex"
    }
})

const profilePictureEdit = document.getElementById("profile-image-edit-container");

document.getElementById("edit-button").addEventListener("click", () => {
    profilePictureEdit.style.display = "flex"
    //Locks the screen
    document.body.classList.add("lock-scroll");
    //Scrools to the top
    window.scrollTo({
        top: 0,
        behavior: 'smooth'
    });
})

document.getElementById("cancel-button").addEventListener("click", (e) => {
    e.preventDefault();
    profilePictureEdit.style.display = "none"
    document.body.classList.remove("lock-scroll");
})

document.getElementById("change-button").addEventListener("click", (e) => {
    profilePictureEdit.style.display = "none"
    location.reload();
})

const profileImages = document.querySelectorAll(".image-option")

profileImages.forEach(image => {
    image.addEventListener("click", (e) => {
        //Gets clicked image
        const img = e.target;
        //Gets the required values
        const picName = img.getAttribute("pic-name");
        const picId =img.getAttribute("id")
        console.log(picId)

        //Resets border color
        profileImages.forEach(img => {
            img.style.borderColor = "";
        });

        //Changes the clicked elements border color
        document.getElementById(`${picId}`).style.borderColor = "red";
        //Php logic
        fetch("logic/change-profile-image.php", {
            method: "POST",
            headers: {
                'Content-Type' : 'application/json'
            },
            body: JSON.stringify({picName: picName})
        })
        .then(response => response.json())
        .then(data => {
            if(!data.success) {
                console.log("data failure");
            }
        })
        .catch(error => {
            console.error("Error:", error);
        })
    })
})

const deleteButtons = document.querySelectorAll(".delete-post-button");

deleteButtons.forEach(deleteButton => {
    deleteButton.addEventListener("click", (e) => {
        document.body.classList.add("lock-scroll");
        const post = e.target;
        const postId = post.getAttribute("id");
        const desContainerId = `${postId}dc`;
        console.log(desContainerId)
        document.getElementById(desContainerId).style.display = "flex";
        const cancelButtonId = `${postId}cb`;
        const deleteButtonId = `${postId}db`;
        document.getElementById(cancelButtonId).addEventListener("click", (e) => {
            document.getElementById(desContainerId).style.display = "none";
            document.body.classList.remove("lock-scroll");
        })
        document.getElementById(deleteButtonId).addEventListener("click", (e) => {
            //Php logic
            console.log(postId)
            fetch("logic/delete-post.php", {
                method: "POST",
                headers: {
                    'Content-Type' : 'application/json'
                },
                body: JSON.stringify({postId: postId})
            })
            .then(response => response.json())
            .then(data => {
                if(!data.success) {
                    console.log("data failure");
                }
                document.getElementById(desContainerId).style.display = "none";
                location.reload();
            })
            .catch(error => {
                console.error("Error:", error);
            })
        })
    })
})