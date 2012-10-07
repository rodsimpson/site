$(document).ready(function () {

   $('ul.sf-menu').superfish();
  
    /* Menu slide down and hide */
     /*
    $('.main-menu li:has(ul)').addClass('submenu');

    $('.main-menu').on('mouseenter', 'li', function () {
        $(this).children('ul').hide().stop(true, true).fadeIn("normal");
    }).on('mouseleave', 'li', function () {
        $(this).children('ul').stop(true, true).fadeOut("normal");
    });
*/
    //Fixing responsive menu
    $(window).resize(function () {
        $('.main-menu').children('ul').children('li').children('ul').hide();
        $('.main-menu').children('ul').children('li').children('ul').children('li').children('ul').hide();
    });

});