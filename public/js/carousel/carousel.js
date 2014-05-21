$(document).ready(function() {
    //	Variable number of visible items with variable sizes
    $('#foo').carouFredSel({
        responsive: true,
        width: 'variable',
        height: 'variable',
        align: 'center',
        prev: '#prev',
        next: '#next',
        auto: false
    });
});
