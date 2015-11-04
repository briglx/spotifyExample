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


})