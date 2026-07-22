$(document).on('click', '.load_modal', function(e) {
    e.preventDefault();
    const url = $(this).data('url'); // URL to load content from

    $.ajax({
        url: url,
        type: 'GET',
        success: function(response) {
            $('#dynamicModal .modal-body').html(response); // Load the content
            $('#dynamicModal').modal('show'); // Show the modal
        },
        error: function(xhr) {
            console.error("An error occurred:", xhr.statusText);
        }
    });
});
