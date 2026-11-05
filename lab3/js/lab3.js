let stanA=false;
$('#anim-a').on('click', function(){
    stanA=!stanA;
    $(this).attr('aria-pressed',stanA);
    if(stanA){
        $(this).stop(true,false).animate({
            width:'240px',
            height:'130px'
        },300);
    }else{
        $(this).stop(true,false).animate({
            width:'160px',
            height:'90px'
        },300);
    }
});