/*!
 * IE10 viewport hack for Surface/desktop Windows 8 bug
 * Copyright 2014-2015 Twitter, Inc.
 * Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE)
 */

// See the Getting Started docs for more information:
// http://getbootstrap.com/getting-started/#support-ie10-width

(function () {
    'use strict';

    if (navigator.userAgent.match(/IEMobile\/10\.0/)) {
        var msViewportStyle = document.createElement('style')
        msViewportStyle.appendChild(
                document.createTextNode(
                        '@-ms-viewport{width:auto!important}'
                        )
                )
        document.querySelector('head').appendChild(msViewportStyle)
    }

})();

//IE10 END

$(document).ready(function () {
    $('[data-toggle="offcanvas"]').click(function () {
        $('.row-offcanvas').toggleClass('active');
    });
    /*$('.nav-justified .dropdown-toggle').click(function () {
     var location = $(this).attr('href');
     window.location.href = location;
     return false;
     });*/
    /* copy loaded thumbnails into carousel */
});

$(document).ready(function () {
    $("[rel='tooltip']").tooltip();

    $('.deal').hover(
            function () {
                $(this).find('.caption').slideDown(250); //.fadeIn(250)
            },
            function () {
                $(this).find('.caption').slideUp(250); //.fadeOut(205)
            }
    );
    $('.linkout[data-href]').each(function () {
        $(this).attr('href', $(this).attr('data-href'));
    });
});
/*
 $(document).ready(function () {
 $.fn.bootstrapDropdownHover();
 });*/

$(document).ready(function () {
    $('#Carousel').carousel({
        interval: 50000
    })
});