console.log("Wersja jQuery: " + $.fn.jquery);
console.log("Czy dostępne animate: " + (typeof $.fn.animate === 'function'));


const motionQuery= window.matchMedia('(prefers-reduced-motion: reduce)');
if(motionQuery.matches){
    $.fx.off = true;
}

/* 
    PSEUDOKOD - Animacja A:
    1. Utwórz zmienną logiczną stanu (stanA = false).
    2. Po kliknięciu odwróć stan i zaktualizuj aria-pressed.
    3. Zatrzymaj kolejkę przez .stop(true, false).
    4. Animuj do 240x130 (jeśli true) lub 160x90 (jeśli false).
    */

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

/* 
    PSEUDOKOD - Animacja B:
    1. Utwórz zmienne dla hover i focus.
    2. Przy mouseenter/mouseleave/focus/blur aktualizuj odpowiedni stan.
    3. Jeśli którykolwiek stan jest aktywny, animuj powiększenie.
    4. Użyj .stop(true, false) by uniknąć kolejkowania przy szybkich ruchach.
    */

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

/* 
    PSEUDOKOD - Animacja C:
    1. Blokuj nowe kliknięcia w trakcie animacji używając .is(':animated').
    2. Zablokuj powiększanie po osiągnięciu limitu 5 kroków.
    3. Wylicz wymiary ze stałych (baza + kroki * wartość).
    */
   
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

$('#btn-reset').on('click',function(){
    stanA=false;
    $('#anim-a')
    .attr('aria-pressed','false')
    .stop(true,false)
    .css({
        width: '160px',
        height: '90px'
    });
    
    kroki=0;
    $('#status-anim-c').text('Kroków: ' + kroki);
    $('#anim-c')
    .stop(true,false)
    .css({
        width: '160px',
        height: '90px'
    });
});

$('#btn-ajax').on('click',function(e){
    e.preventDefault();
    const $przycisk=$(this);
    const $obszarWyniku = $('#status-ajax');

    $przycisk.prop('disabled',true);
    $obszarWyniku.text('Ładowanie...')
    $.ajax({
        url:'status.php',
        method: 'GET',
        dataType: 'text',
        timeout: 5000
    })
    .done(function(odpowiedz){
        $obszarWyniku.text(odpowiedz);
    })
    .fail(function(){
        $obszarWyniku.text('Wystąpił błąd, nie udało połączyć się z serwerem')
    })
    .always(function(){
        $przycisk.prop('disabled',false);
    });
});