$(function(){

  
    $('#Postcomment').submit(function (e) {
        e.preventDefault();
        let that = $(this)
        let idArticle = $(this).attr('data-id');
        let formSerialized = that.serialize();
        let route  = Routing.generate('post_comment',{id : idArticle })


        $.ajax({
            url: route,
            data : formSerialized,
            type: "GET",
            success: function(result) {
                // Override global options

                toastr.success(result, 'Fiouuuuu ! ', {timeOut: 5000})
                that.hide();
                $('#remerciement').text("Votre commentaire est bien posté !")

            },
            error: function(e){
                console.log(e);
            }
        })

    })

});