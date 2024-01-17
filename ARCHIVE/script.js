// This is a jQuery function that runs when the document is ready
jQuery(function ($) {
    // Initialize the Swiper slider
    // Swiper is a powerful JavaScript library to implement responsive, accessible, flexible, touch-enabled carouses/sliders on your mobile websites and apps.
    var swiper = new Swiper('.swiper', {
        slidesPerView: 1,
        spaceBetween: 10,
        navigation: {
            nextEl: '.next-right-arrow',
            prevEl: '.pre-left-arrow',
        },
        breakpoints: {
            // Breakpoints in Swiper are used to change slider's configuration (like slidesPerView, spaceBetween) dynamically on window resize event.
            // when window width is >= 320px
            480: {
                slidesPerView: 1, // Number of slides per view (slides visible at the same time on slider's container).
                spaceBetween: 10 // Distance between slides in px.
            },
            // when window width is >= 480px
            768: {
                slidesPerView: 2, // Number of slides per view (slides visible at the same time on slider's container).
                spaceBetween: 10 // Distance between slides in px.
            },
            // when window width is >= 640px
            1024: {
                slidesPerView: 3, // Number of slides per view (slides visible at the same time on slider's container).
                spaceBetween: 10 // Distance between slides in px.
            }
        }
        // Add more options if needed
        // For more options, you can refer to the official Swiper API documentation: https://swiperjs.com/swiper-api
    });

    // Fetch LinkedIn posts using the AJAX request
    // AJAX is a technique for creating fast and dynamic web pages.
    $.ajax({
        url: ajax_object.ajax_url, // The URL to which the request is sent
        type: 'POST', // The type of HTTP method (post, get, etc)
        data: {
            action: 'get_linkedin_posts' // Data to be sent to the server
        },
        success: function (response) {
            // A function to be run when the request succeeds
            if (response.success) {
                // Process the rows returned in response.data
                var rows = response.data; // The data returned from the server
                var processedRows = []; // An array to store the processed rows
                for (var i = 0; i < rows.length; i++) {
                    var row = rows[i]; // The current row
                    var processedRow = {}; // An object to store the processed row

                    // Loop through each key in the row
                    for (var key in row) {
                        // Check if the row has the key as its own property
                        if (row.hasOwnProperty(key)) {
                            // Add the key-value pair to the processed row
                            processedRow[key] = row[key];
                        }
                    }

                    // Add the processed row to the processedRows array
                    processedRows.push(processedRow);
                }

                // Use the processedRows array to create and display slider items
                $('.li-placeholder').hide();
                processedRows.forEach(function (post) {
                    post.images = post.images.filter((img) => img !== '');
                    let imagesHtml = '';
                    if (post.images.length === 1) {
                        imagesHtml = `<div class="li-single-img" style="background-image: url('${post.images[0].replace(/\\/g, '')}')"></div>`;
                    } else if (post.images.length === 2) {
                        imagesHtml = post.images.map(img => `<div class="li-img-two" style="background-image: url('${img.replace(/\\/g, '')}')"></div>`).join('');
                    } else if (post.images.length >= 3) {
                        imagesHtml = `<div class="li-img-three-main" style="background-image: url('${post.images[0].replace(/\\/g, '')}')"></div>` +
                            `<div class="li-img-three-sec-container">` +
                            `<div class="li-img-three-sec" style="background-image: url('${post.images[1].replace(/\\/g, '')}')"></div>` +
                            `<div class="li-img-three-sec" style="background-image: url('${post.images[2].replace(/\\/g, '')}')"></div>` +
                            `</div>`;
                    }
                    var slide = document.createElement('div');
                    slide.className = 'swiper-slide';
                    slide.addEventListener('click', function () {
                        //DONE: Add the URN to URL for the post
                        window.open('https://www.linkedin.com/feed/update/' + post.urn, '_blank');
                    });
                    slide.innerHTML = `
                        <div class="li-icon-white">
                        <svg style="width: 30px; height: 30px; overflow: visible; fill: rgb(255, 255, 255);" viewBox="0 0 448 512"><path d="M100.28 448H7.4V148.9h92.88zM53.79 108.1C24.09 108.1 0 83.5 0 53.8a53.79 53.79 0 0 1 107.58 0c0 29.7-24.1 54.3-53.79 54.3zM447.9 448h-92.68V302.4c0-34.7-.7-79.2-48.29-79.2-48.29 0-55.69 37.7-55.69 76.7V448h-92.78V148.9h89.08v40.8h1.3c12.4-23.5 42.69-48.3 87.88-48.3 94 0 111.28 61.9 111.28 142.3V448z"></path></svg>
                        </div>
                        <div class="img-container">
                            ${imagesHtml}
                        </div>
                        
                        <div class="info-container">
                            <div class="li-author-img" style="background-image: url('${post.profilePicture}')"></div>
                            <div class="section-company section-company">${post.author}</div>
                            <div class="section-author-date">
                                <span class="li-author-username">@${post.username} . </span>
                                <span class="li-post-age">${post.age} ago</span>
                            </div>
                            <p class="section-body">${post.copy}</p>
                            <div class="section-interactions">
                            <span><svg style="width: 24px; height: 24px; overflow: visible; fill: rgb(0, 122, 255);" viewBox="0 0 24 24"><path fill="none" d="M0 0h24v24H0z"></path><path d="M12 4c-4.41 0-8 3.59-8 8s3.59 8 8 8 8-3.59 8-8-3.59-8-8-8zm5.9 8.3-2.1 4.9c-.22.51-.74.83-1.3.8H9c-1.1 0-2-.9-2-2v-5c-.02-.38.13-.74.4-1L12 5l.69.69c.18.19.29.44.3.7v.2L12.41 10H17c.55 0 1 .45 1 1v.8c.02.17-.02.35-.1.5z" opacity=".3"></path><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path><path d="M17 10h-4.59l.58-3.41v-.2c-.01-.26-.12-.51-.3-.7L12 5l-4.6 5c-.27.26-.42.62-.4 1v5c0 1.1.9 2 2 2h5.5c.56.03 1.08-.29 1.3-.8l2.1-4.9c.08-.15.12-.33.1-.5V11c0-.55-.45-1-1-1z"></path></svg></span>
                                <span class="li-post-reactions">${post.reactions} . </span>
                                <span class="li-post-comments">${post.comments}</span>
                            </div>
                        </div>
                        
                        
                    </div>
                    `;
                    swiper.appendSlide(slide);
                });


            }
        }
    });
});
