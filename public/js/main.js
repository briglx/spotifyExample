$(function(){

    $(".btn.categories").on("click", function(){

        $.get("/api/categories", function(res){

            console.log(res);

            // Append Categories
            var items = res.categories.items;

            for (var i = 0; i < items.length; i++) {
                category = items[i];

                $(".target.categories").append($("<p>" + category.name + "</p>"));

            }   

        });

    });

    $(".btn.playlist").on("click", function(){

        $.get("/api/playlist", function(res){

            console.log(res);

            // Append Categories
            var items = res.playlist.items;

            for (var i = 0; i < items.length; i++) {
                playlist = items[i];

                $(".target.playlist").append($("<p>" + playlist.name + "</p>"));

            }   

        });

    });


})