
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
                published: published,
                nonce: my_ajax_object.publish_unpublish_nonce // Added nonce for security
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
                id: postId,
                nonce: my_ajax_object.delete_post_nonce // Added nonce for security
            },
            success: (response) => {
                if (response.success) {
                    button.closest('tr').remove(); // Update the selector to remove the row
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
            }
        });
    }

    function handleUpDownButtonClick() {
        let button = $(this);
        let id = button.closest('tr').find('.row-id').text();
        let action = button.hasClass('up-button') ? 'up' : 'down';

        $.ajax({
            url: my_ajax_object.ajax_url,
            type: 'POST',
            data: {
                action: 'move_post',
                id: id,
                direction: action,
                //nonce: my_ajax_object.move_post_nonce // Add nonce for security
            },
            success: (response) => {
                if (response.success) {
                    location.reload(); // Consider a more efficient way to update the order without reloading
                } else {
                    console.error('Error:', response);
                }
            },
            error: (jqXHR, textStatus, errorThrown) => {
                console.error('Error:', textStatus, errorThrown);
            }
        });
    }

    // Event binding for the delete button
    $('.delete-button').on('click', handleDeleteButton);

    // Event binding for the publish/unpublish button
    $('.publish-button').on('click', function () {
        handlePublishUnpublish(this);
    });

    // Toggle button text on hover for publish/unpublish button
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

    // Event binding for the up/down reorder buttons
    $('.up-button, .down-button').on('click', handleUpDownButtonClick);
});
