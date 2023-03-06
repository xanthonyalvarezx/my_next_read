

const bookWrapper = document.getElementById('book-wrapper');
const paginationDiv = document.getElementById('pagination-div');
const nextPage = document.getElementById('next-page');
const previousPage = document.getElementById('previous-page');



let savedBooks = []
let currentBook;

let book = {};
let bookListHTML = ``
let endHTML = ``
let oldSearchValue = "";

let start = 0; 
let max = 40;



let searchTerm = ""

/**
 * The function getSearchTerm takes in an event as an argument and sets the value of the searchTerm
 * variable to the value of the event target.
 * @param event - The event object is automatically passed to the event handler by the browser.
 */
function getSearchTerm(event){
    searchTerm = event.target.value
    
    
}



/**
 * I'm trying to get the books that match the search value first and then the ones that don't match the
 * search value.
 * @param startIndex - The position in the collection at which to start. The index of the first item is
 * 0.
 */

const  getBooks = async (startIndex) => {

    bookListHTML = ``
    endHTML = ``
   
    let searchValue = document.getElementById("search-value").value;
    if(searchValue!== oldSearchValue){
        oldSearchValue = searchValue;
        start = 0;
    }
    
    await fetch(`https://www.googleapis.com/books/v1/volumes?q=${searchTerm}:${searchValue}&fields=items(volumeInfo)&startIndex=${start}&maxResults=${max}&langRestriction=en&key=AIzaSyDjokEZzqF2e8a0n4Jcla0wRyUYqCvMFyU`)
        .then(response => response.json())
        .then(response => {
            if(Object.entries(response)[0][1].length < 40){
                nextPage.style.display = "none"
            }else{
                nextPage.style.display = "block"
            }
            

           
            paginationDiv.removeAttribute('hidden');
            console.log(start)
            if(start === 0){
                previousPage.style.display = "none"
            }else{
                previousPage.style.display = "block"
            }

            
            
           response.items.forEach(element => {
        
           book['title'] =  element.volumeInfo.title;
           book['subtitle'] =  element.volumeInfo.subtitle
           book['description'] =  element.volumeInfo.description
           if(element.volumeInfo.imageLinks){

           book['image'] =  element.volumeInfo.imageLinks.thumbnail
           }else{
            book['image'] = 'No Picture Available'
           }

           book['maturityRating'] =  element.volumeInfo.maturityRating
           book['publisher'] =  element.volumeInfo.publisher
           book['publishedDate'] =  element.volumeInfo.publisher
           book['pageCount'] =  element.volumeInfo.pageCount
           book['language'] =  element.volumeInfo.language
           book['categories'] = element.volumeInfo.categories
           book['authors'] = element.volumeInfo.authors
           book['isbn'] = element.volumeInfo.industryIdentifiers[0].identifier
           
            if( searchValue === book.authors[0] || searchValue === book.authors[1]){
            bookListHTML +=
            `
                <div class="book text-center" onclick="(getBookDetails)">
                    <h3 style="color:white;">${book.title}</h3>
                    <span class="subtitle">${book.subtitle? book.subtitle:''}<span>
                    `

                    bookListHTML +=
                    `
                    <div class="image-div">
                        <img onclick="getBookDetails(${book.isbn})" src='${book.image}' alt="book cover" />
                    </div>
                </div>
                `
        }else{

            endHTML +=   `
            <div class="book text-center" onclick="(getBookDetails)">
                <h3 style="color:white;">${book.title}</h3>
                <span class="subtitle">${book.subtitle? book.subtitle:''}<span>
                `

                endHTML +=
                `
                <div class="image-div">
                    <img onclick="getBookDetails(${book.isbn})" src='${book.image}' alt="book cover" />
                </div>
            </div>
                `


        }
    
    });
           
           
 }).catch(err => console.error(err));

bookWrapper.innerHTML = bookListHTML
bookWrapper.innerHTML += endHTML

}

/**
 * The function pagination() takes in a parameter called startIndex. If startIndex is equal to 'next',
 * then the start variable is incremented by the max variable. If startIndex is equal to 'previous',
 * then the start variable is decremented by the max variable. If startIndex is neither 'next' nor
 * 'previous', then the function does nothing.
 * @param startIndex - the index of the first book to be displayed
 */
function pagination(startIndex){
switch(startIndex){

    case 'next':
        start += max
        console.log("START INDEX",start)
        
        break;
    case 'previous':
        start -= max
        break;
    default:
        break;
        
    }
    getBooks()
}

/**
 * It takes an ISBN number as a parameter, makes a fetch request to the Google Books API, and then
 * displays the book details in the DOM.
 * @param isbn - The ISBN number of the book you want to query.
 */
const getBookDetails = async (isbn) => {
    paginationDiv.setAttribute('hidden',true);

    console.log('ISBN',isbn)

    await fetch(`https://www.googleapis.com/books/v1/volumes?q=isbn:${isbn}&fields=items(volumeInfo)&key=AIzaSyDjokEZzqF2e8a0n4Jcla0wRyUYqCvMFyU`)
        .then(response => response.json())
        .then(response => {
            

            if(!response.items){
                
                bookWrapper.innerHTML = 
                `
                <div style="width:100vw" class="text-center">
                    <h3> Sorry There is no Info on this edition.</h3>
                    
                         <button  type="button" onclick="getBooks()" class="button-30  mb-5 ms-5 text-center" value="" style="height:40px;"/>Back</button>
                    
                </div>
                `
            }else{
                currentBook = response.items[0].volumeInfo;
                
            bookWrapper.innerHTML = 
            `
            <div id="single-book-wrapper">
               
                    <h3>${response.items[0].volumeInfo.title}</h3>
                    <sub class="mb-5">${response.items[0].volumeInfo.subtitle}</sub>
                
               
               
                    <img src="${response.items[0].volumeInfo.imageLinks.thumbnail}" alt="book cover" style="width:200px"/>
               
                <div class="book-info row mt-5 mb-4">

                    <div class="col-4">
                        <label class="fw-bold" for="category">Category:</label><br/>
                        ${response.items[0].volumeInfo.categories[0]}
                    </div>

                    <div class="col-4">
                        <label class="fw-bold" for="maturity">Maturity Rating:</label><br/>
                        ${response.items[0].volumeInfo.maturityRating}
                        </div>
                        
                        <div class="col-4">
                        <label class="fw-bold" for="page-count">Page Count:</label><br/>
                        ${response.items[0].volumeInfo.pageCount}
                    </div>
                </div>
                <div class="book-info row">

                    <div class="col-4">
                        <label class="fw-bold" for="publisher">Publisher:</label><br/>
                        ${response.items[0].volumeInfo.publisher}
                    </div>

                    <div class="col-4">
                        <label class="fw-bold"  for="published">Published:</label><br/>
                        ${response.items[0].volumeInfo.publishedDate}
                        </div>
                        
                        <div class="col-4">
                        <label  class="fw-bold" for="isbn">ISBN:</label>
                        ${isbn}
                    </div>
                </div>
                <div class="book-description row mt-5">

                    <div class="col-10">
                        <label  class="fw-bold" for="description">Description:</label><br><br/>
                        ${response.items[0].volumeInfo.description}
                    </div>
                </div>
                <div id="back" class="row text-center">

                <div class="col-6">
                     <button id="saVE-button"  type="button" onclick="saveBook()" class="button-30  mb-5 text-center" value="" style="height:40px;"/>Add To Collection</button>
                </div>
                    <div class="col-6">
                         <button id="back-button"  type="button" onclick="getBooks()" class="button-30  mb-5 text-center" value="" style="height:40px;"/>Back</button>
                    </div>
                </div>
            </div>
            `
    }})

}


function saveBook(){
    savedBooks.push(currentBook);

    let call = fetch('/save/collection',{
        method: 'POST',
        body: JSON.stringify(savedBooks)
    })


    call.then(response => response.json())
        .then(data=>{
            console.log(data)
        }).catch(err => console.error(err))
        
    
}


function getCollection (){
    console.log('clicked');
    let call = fetch('/collection',{
        method: 'GET'
        
    })

    call.then(response => response.json())
    .then(data=>{
        console.log('COLLECTION',data)

    }).catch(err => console.error(err))

}
// AIzaSyDjokEZzqF2e8a0n4Jcla0wRyUYqCvMFyU 