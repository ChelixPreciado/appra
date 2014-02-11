function writeDollar ( ammount ) {
	ammount = ( 1.35 * parseFloat(ammount) );
	$("#dollar").text( '$' + ammount.toFixed(2) );
}

$.fn.setRounded = function( value ){
	$(this).val(Math.round(value / 10));
};

var slider = $('#slider'),
    select = $('#pricing'),
    form = $('#form');

select.on('change', function(){
  slider.val([ null, 10 * $(this).val() ]).change();
});

slider.noUiSlider({
  range: [20,60]
  ,start: [30,50]
  ,connect: true
  ,serialization: {
    resolution: 0.1,
    to: [
      [ $('#min') ],
      [ $('#max') ]
    ]
  }
});

form.change(function(){
  $("#request").html( $(this).serialize() );
}).submit(function(){
  return false;
});


//resolution => incrementos de 0.1
