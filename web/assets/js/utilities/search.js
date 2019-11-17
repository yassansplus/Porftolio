$(function () {
    let route = Routing.generate('getArticles')
    let data;
    var options = {
        keys: ['title'],
        // id: ['title']
    }
    let fuse;


    $.ajax({
        url: route,
        type: "GET",
        success: function (result) {
            // Override global options
             data = JSON.parse(result)
            fuse = new Fuse(data, options)
        },
        error: function (e) {
            console.log(e);
        }
    })

    $("#search").keypress(function (e) {
        let searchResults = fuse.search($(this).val());
        let resultBox = $("#searchResult");
        resultBox.empty();
        console.log(fuse.search($(this).val()));
        for (let i = 0; i < searchResults.length; i++) {
            let searchLink = Routing.generate('blog_show',{slug : searchResults[i].slug})

            $("#searchResult").append('<a class="dropdown-item linkSearch" href="'+searchLink+'">' + searchResults[i].title + '</a>')

        }
        if (e.which === 13 && searchResults.length !== 0){

            document.location.href =  Routing.generate('blog_search',{slug : searchResults[0]})
        }

    })
    $('.linkSearch').click(function(){
        console.log('ok')
        let URLredirection = $(this).attr("href");
        document.location.href =  URLredirection;
    })
})