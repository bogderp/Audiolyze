function dynamicHeight() {
    var height = $(window).height();
    height = parseInt(height) + 'px';
    $("main").css('height',height);
}

(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) return;
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&appId=171546376229575&version=v2.0";
	fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));

$(document).ready(function(){
    $(window).on('beforeunload', function() {
        $(window).scrollTop(0);
    });
    dynamicHeight();
    $(window).bind('resize', dynamicHeight);

    $('#requestForm').on('submit',function(e) {
        e.preventDefault();
        requestData = $(this).serialize();
        console.log(requestData);
        $.ajax({
                url: "webFiles/php/request.php",
                type: "POST",
                data: requestData,
                success: function(data) {
                    console.log(data);
                    $("#requests").modal('hide');
                }
        });  
    });

    $(function () {
        $('[data-toggle="tooltip"]').tooltip();
    })

    isVisible = 0;
    $('.mobile').click(function(){
        if(!isVisible) {
            $('.mobileMenu li').fadeIn('slow');
            isVisible = 1;
        } else {
            $('.mobileMenu li').fadeOut('slow');
            isVisible = 0;
        }
        
    })

    $('#mobileimg').click(function(){
        if(!isVisible) {
            $('.mobileMenu li').fadeIn('slow');
            isVisible = 1;
        } else {
            $('.mobileMenu li').fadeOut('slow');
            isVisible = 0;
        }
        
    })

}); 