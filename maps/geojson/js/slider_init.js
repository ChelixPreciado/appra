var slider = $('#slider');

slider.noUiSlider({
	range: [20,60]
	,start: [30,50]
	,connect: true
	,step: 1
	,serialization: {
		resolution: 1,
		to: [
			[$('#min'), minSpan],
			[$('#max'), maxSpan]
		]
	}
});

$('#disable-checkbox').click(toggle);

function toggle(){
	if ( this.checked ) {
		slider.attr( 'disabled', 'disabled' );
	} else {
		slider.removeAttr( 'disabled' );
	}
}

function minSpan(value) {
	$('#min-span').text(value);
}

function maxSpan(value) {
	$('#max-span').text(value);
}
//resolution => incrementos de 1
//step => saltos de 5 en 5

