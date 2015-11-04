$(function(){

    $(".btn.login").on("click", function(){

        $.get("/api/login", function(res){

            console.log(res);

        });

    });


})