// --------------------------------------------------------
// Pretty Photo for Lightbox Image
// -------------------------------------------------------- 
/*$(document).ready(function() {	
    $("a[data-gal^='prettyPhoto']").prettyPhoto(); 
});*/

// --------------------------------------------------------
//	Navigation Bar
// -------------------------------------------------------- 	
//$(window).scroll(function(){	
//	"use strict";	
//	var scroll = $(window).scrollTop();
//	if( scroll > 60 ){		
//		$(".navbar").addClass("scroll-fixed-navbar");				
//	} else {
//		$(".navbar").removeClass("scroll-fixed-navbar");
//	}
//});

// --------------------------------------------------------
//	Smooth Scrolling
// -------------------------------------------------------- 	
$(".navbar-nav li a[href^='#']").on('click', function(e) {
    e.preventDefault();
    $('html, body').animate({
        scrollTop: $(this.hash).offset().top
    }, 1000);
});
/* ======= ScrollTo ======= */
$('.scrollto').on('click', function(){

$('body').animate({ scrollTop: $("#solvencia").offset().top }, 800);
return false;
});

$('.scrollto-no-offset').on('click', function(e){

//store hash
var target = this.hash;

e.preventDefault();

        $('body').scrollTo(target, 800, {offset: 0, 'axis':'y'});

});

/* ======= Fixed page nav when scrolled ======= */    
var Contact = function () {

    return {
        
        //Map
        initMap: function () {
        var map;
        $(document).ready(function(){
          map = new GMaps({
                div: '#map',
                lat: 10.483347,
                lng: -66.938701
          });
           var marker = map.addMarker({
                        lat: 10.483347,
                        lng: -66.938701,
            title: 'Administradora Octagon'
        });
        });
        }

    };
}(); 