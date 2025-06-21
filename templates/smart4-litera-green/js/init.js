document.addEventListener("DOMContentLoaded", function() {
    
    document.querySelectorAll('ul.menulevel1').forEach(el => {
        el.classList.add('nav', 'navbar-nav', 'mr-auto');
    });

    document.querySelectorAll('ul.menulevel1 > li:first-child').forEach(el => {
        el.classList.add('nav-item');
    });

    document.querySelectorAll('ul.menulevel1 li a').forEach(el => {
        el.classList.add('nav-link');
    });

    document.querySelectorAll('li.docs, li.sdocs').forEach(el => {
        el.classList.add('nav-item', 'dropdown');
    });

    document.querySelectorAll(
        'ul.menulevel2, ul.menulevel3, ul.menulevel4, ' +
        'ul.menulevel5, ul.menulevel6, ul.menulevel7, ' +
        'ul.menulevel8, ul.menulevel9'
    ).forEach(el => {
        el.classList.add('dropdown-menu');
    });
});

 
$('.nav li > span').each(function() {
var $this = $(this);
$this.replaceWith('<a class="navlink dropdown-toggle xhspan" href="#" onclick="return false;">' + $this.text() + '</a>');
}); 

 	  
 (function() {
   			$('<i id="to-top"></i>').appendTo($('body'));

			$(window).scroll(function() {
				if($(this).scrollTop() != 0) {
					$('#to-top').fadeIn();	
				} else {
					$('#to-top').fadeOut();
				}
			});
			
			$('#to-top').click(function() {
				$('body,html').animate({scrollTop:0},100);
			});	

	})();					  
   

   





