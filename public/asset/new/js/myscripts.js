$(document).ready(function(){

	 var scrollLink =$('.scroll');



	 // scrollink statred

	 scrollLink.click(function(e){

	 	e.preventDefault();

	 	$('body,html').animate({

	 		scrollTop:$(this.hash).offset().top

	 	},1000);	 	

	 });



	 // activelinkswiching

	 $(window).scroll(function(){

	 	var scollbarlocation =

	 	$(this).scrollTop();



	 	scrollLink.each(function(){

	 		var sectionOffset =

	 		$(this.hash).offset().top - 20;



	 		 if (sectionOffset <= scrollbarlocation) {



	 		 	$(this).parent().addClass('active');

	 		 	$(this).parent().siblings().removeClass('active');

	 		 }

	 	})

	 })

})


