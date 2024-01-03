jQuery(document).ready(function ($) {

    function handlePublishUnpublish(buttonElement) {
        let button = $(buttonElement);
        let id = button.data("id");
        let published = button.data("published");
        button.text('...').addClass('loading');

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'publish_unpublish',
                id: id,
                published: published
            },
            success: (response) => {
                if (response.success) {
                    button.text(published ? 'Publish' : 'Unpublish').removeClass('loading');
                    button.data("published", !published);
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
                button.text(published ? 'Unpublish' : 'Publish').removeClass('loading');
            }
        });
    }

    function handleDeleteButton(e) {
        e.preventDefault();
        let button = $(this);
        let postId = button.data('id');

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'delete_post',
                id: postId
            },
            success: (response) => {
                if (response.success) {
                    $('#post-' + postId).remove();
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
            }
        });
    }

    function handleFormSubmit(e) {
        e.preventDefault();
        let form = $(this).closest('form');
        let button = $(this);
        button.val('...').addClass('loading');

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: form.serialize(),
            success: (response) => {
                if (response.success) {
                    button.val('Delete').removeClass('loading');
                    form.closest('tr').remove();
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
                button.val('Delete').removeClass('loading');
            }
        });
    }

    function handleUpDownButtonClick() {
        let button = $(this);
        let id = button.closest('tr').find('.row-id').text();
        let action = button.hasClass('up-button') ? 'move_up' : 'move_down';

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: action,
                id: id,
            },
            success: (response) => {
                if (response.success) {
                    location.reload();
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
            }
        });
    }

    $('.delete-button').on('click', handleDeleteButton);

    $('form input[type=submit]').on('click', handleFormSubmit);

    $('.publish-button').on('click', function () {
        handlePublishUnpublish(this);
    });

    $('.publish-button').hover(function () {
        let button = $(this);
        let published = button.data("published");
        if (published == 1) {
            button.text('Unpublish');
        } else {
            button.text('Publish');
        }
    }, function () {
        let button = $(this);
        let published = button.data("published");
        if (published == 1) {
            button.text('Published');
        } else {
            button.text('Unpublished');
        }
    });

    $('.up-button, .down-button').on('click', handleUpDownButtonClick);
});