$('.btn-delete').click(function (e) {

    let jobId = $(this).attr('data-id');
    let thisBlock = $('.blockJob[data-id='+jobId+']')
    let route = Routing.generate('job_delete', {id: jobId})
    $.ajax({
        url: route,
        type: "delete",
        success: function (result) {
            toastr.success(result, 'Fiouuuuu ! ', {timeOut: 5000})
            thisBlock.remove()


        },
        error: function (e) {
            console.log(e);
        }
    })
})