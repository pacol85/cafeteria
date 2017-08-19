const searchedForText = 'hippos';
const unsplashRequest = new XMLHttpRequest();

unsplashRequest.open('GET', `https://api.unsplash.com/search/photos?page=1&query=${searchedForText}`);
unsplashRequest.onload = addImage;
unsplashRequest.setRequestHeader('Authorization', 'Client-ID 0a76721ff6d243d67e27c13e2d25cb97a5da1047025920f07bfc43d302f87163');
unsplashRequest.send();

function addImage(){
    console.log("entro a addImage");
}

function addArticles () {
    console.log("entro a addArticles");
}
const articleRequest = new XMLHttpRequest();
articleRequest.onload = addArticles;
articleRequest.open('GET', `http://api.nytimes.com/svc/search/v2/articlesearch.json?q=${searchedForText}&api-key=a06c0ff77bbf489297c056129dece863`);
articleRequest.send();

