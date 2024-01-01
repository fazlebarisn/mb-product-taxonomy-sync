
// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('img-page');

if(sectionName){

    sectionName = parseInt(sectionName) + 1;
    var url = 'edit.php?post_type=product&page=product-image-sync&img-page=' + sectionName;

    
    setTimeout(() => {
        window.location.replace(url);
    }, 3000);

}


// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('filter-page');

if(sectionName){

    sectionName = parseInt(sectionName) + 1;
    var url = 'edit.php?post_type=product&page=product-filter-sync&filter-page=' + sectionName;
    setTimeout(() => {
        window.location.replace(url);
    }, 3000);
}




// Create urlParams query string
var urlParams = new URLSearchParams(window.location.search);

// Get value of single parameter
var sectionName = urlParams.get('pagenoforloc');

if(sectionName){

    sectionName = parseInt(sectionName);
    var url = 'edit.php?post_type=product&page=product-sync&pagenoforloc=' + sectionName;
    setTimeout(() => {
        window.location.replace(url);
    }, 10);
}