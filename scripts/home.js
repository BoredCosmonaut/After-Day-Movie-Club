//Home,bookmark,profile sites all use this script
const hearts = document.querySelectorAll(".post-heart");
const bookmarks = document.querySelectorAll(".post-bookmark");
const shares = document.querySelectorAll(".post-share");
//Api variables
const apiKey = "07d9cb5df4a8406a8102e12f7cc5fb56";
const request = `https://newsapi.org/v2/top-headlines?country=us&category=entertainment&apiKey=${apiKey}`
const articleTitle = [];
const articleUrl = [];
const articleImage = [];
const counter = 0;

//Gets the articles from news api
function getArticle(request) {
    return fetch(request)
        .then(response => {
                if(!response.ok) {
                    throw new Error("Couldnt get data")
                }
                return response.json();
            })
        .then(data => {
            return data;
        })
}

//These get the articles from json and puts them into an array
async function showArticle() {
        await getArticle(request)
            .then(data => {
                console.log(data);
                data.articles.forEach((article,index) => {
                    //if article doesnt have an image it doesnt show if it does puts them into an array
                    if(article.urlToImage != null) {
                        articleTitle[index] = article.title
                        articleUrl[index] = article.url
                        articleImage[index] = article.urlToImage
                    }
                })
            })
        placeArticle(articleTitle,articleUrl,articleImage);
}

//Puts the items in divs
function placeArticle(articleTitle,articleUrl,articleImage) {
    const articleContainer = document.getElementById("articles");
    for(let i = 0; i < articleImage.length;i++) {
        if(articleImage[i] != null) {
            if(i == 15) {
                break;
            } else {
                //Creates the article box
                const artDiv = document.createElement("div");
                artDiv.classList.add("article-div");
                artDiv.setAttribute("news-id", i);
                artDiv.style.cursor = 'pointer';

                //Creates the image and the link
                const newsText = document.createElement("a");
                const newsImage = document.createElement("img");

                //Adds the classes to them
                newsText.classList.add("news-text");
                newsImage.classList.add("news-image");
                
                newsText.setAttribute("news-id", `${i}n`);
                newsImage.src = articleImage[i];
                newsText.innerText = articleTitle[i];
                newsText.href = articleUrl[i];
                
                //Appends the element to them 
                artDiv.append(newsImage);
                artDiv.append(newsText);
                articleContainer.append(artDiv);
            }
        }
    }
    //Tis code blocks is for when you click on a box it sends you to a link
    const news = document.querySelectorAll(".article-div");
    news.forEach(newsBox => {
    newsBox.addEventListener("click",(e) => {
        newsBox.target = "_blank";
        console.log("hiii")
        const clickedBox = e.target;
        const clickedBoxId = clickedBox.getAttribute("news-id");
        const newsId = `${clickedBoxId}n`;
        const link = document.querySelector(`a[news-id="${newsId}"]`).href;
        window.open(link, '_blank');
        })
    })
}
showArticle();




//Below are the codes for sharing,liking and saving posts
hearts.forEach(heart => {
    heart.addEventListener("click", (e) => {
        //Gets the correct clicked icon
        const img = e.target;
        const postId = img.getAttribute("data-post-id");
        let action = "";
        const likeAmountElement = document.getElementById(`${postId}h`);
        console.log(likeAmountElement);
        //if a post isnt liked do this
        if(e.target.src.endsWith("i-heart.svg")) {
            console.log("target filled")
            //Changes the amount of likes
            likeAmountElement.innerText = parseInt(likeAmountElement.innerText) + 1;
            //changes the source tı filled heart
            e.target.src = "Images/Icons/i-heart-red.svg";
            //Changes the action to add
            action = "add";
        }
        //Does the same thing if a post is already liked changes the action to remove
        else if(e.target.src.endsWith("i-heart-red.svg")) {
            likeAmountElement.innerText = parseInt(likeAmountElement.innerText) - 1;
            console.log("target unfilled")
            e.target.src = "Images/Icons/i-heart.svg";
            action = "remove";
        }
        //Using fetch to call the php logic
        fetch("logic/likedReviews.php", {
            method: "POST",
            //This is needed to send data tp php
            headers: {
                'Content-Type' : 'application/json'
            },
            //Sends post id and the action to php
            body:JSON.stringify({action: action, postId: postId})
        })
        .then(response => response.json())
        .then(data => {
            //If any error happens rollback the changes
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

//Read the heart button commments to understand this code
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
