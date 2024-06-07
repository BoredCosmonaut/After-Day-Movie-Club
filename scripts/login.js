const hereButtonLogin = document.querySelector("#here-login");
const hereButtonRegister = document.querySelector("#here-register");

hereButtonLogin.addEventListener("click", () => {
    document.querySelector("#login").style.display = "none";
    document.querySelector("#register").style.display = "flex";
})

hereButtonRegister.addEventListener("click", ()=> {
    document.querySelector("#login").style.display = "flex";
    document.querySelector("#register").style.display = "none"; 
})