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

let stanBHover = false;
let stanBFocus = false;

function aktualizujB(){
    if(stanBHover || stanBFocus){
        $('#anim-b').stop(true,false).animate({
            width: '220px',
            height: '120px'
        },300);
    } else {
        $('#anim-b').stop(true,false).animate({
            width: '160px',
            height: '90px'
        },300);
    }
}

$('#anim-b').on('mouseenter', function(){
    stanBHover = true;
    aktualizujB();
}).on('mouseleave',function(){
    stanBHover = false;
    aktualizujB();
}).on('focus',function(){
    stanBFocus = true;
    aktualizujB();
}).on('blur',function(){
    stanBFocus = false;
    aktualizujB();
});

let kroki=0;
const bazowaSzer = 160;
const bazowaWys = 90;

$('#anim-c').on('click',function(){
    if($(this).is(':animated')){
        return;
    }
    if(kroki>= 5) {
        return;
    }
    kroki++;
    $('#status-anim-c').text('Kroków: ' + kroki);
    const nowaSzerokosc= bazowaSzer + (kroki*20);
    const nowaWysokosc = bazowaWys + (kroki*10);
    $(this).animate({
        width: nowaSzerokosc + 'px',
        height: nowaWysokosc + 'px'
    },300);
});