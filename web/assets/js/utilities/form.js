$(function(){

    let idArticle;

    $('.deleteArticle').click(function(){
        let that = $(this)
        idArticle = $(this).attr('data-id');
        let route  = Routing.generate('blog_delete',{id : idArticle })

            $.ajax({
                url: route,
                type: "delete",
                success: function(result) {
                   that.closest('tr').remove();
                },
                error: function(e){
                    console.log(e);
                }
            })

    });

    $('.editArticle').click(function () {
        let that = $(this)
        let modal = $('#exampleModal')

        idArticle = $(this).attr('data-id');

        setData( idArticle)
        modal.attr('data-id',idArticle);
        let route  = Routing.generate('blog_edit',{id : idArticle })

    })


    function setData(idArticle){
        let route = Routing.generate('getArticle',{id : idArticle })
        $.ajax({
            url: route,
            type: "GET",
            success: function(result) {
                result = JSON.parse(result)
                $('#titre').val(result.title)
                $('#slug').val(result.slug);
                $('#article').val(result.blogArticle);
                $('#published').prop('checked',result.published);


            },
            error: function(e){
                console.log(e);
            }
        })
    }

    //Fonction pour modifier un Poste.
    $('#editPost').submit(function (e) {
        e.preventDefault()
         let formSerialized = $(this).serialize();
        let route = Routing.generate('blog_edit',{id : idArticle })
        $.ajax({
            url: route,
            type: "POST",
            data: formSerialized,
            success: function(result) {
                toastr.success(result, 'Fiouuuuu ! ', {timeOut: 5000})
                setInterval(function () {
                    location.reload()
                },1000)


            },
            error: function(e){
                console.log(e);
            }
        })


    })
});