wp.customize.controlConstructor['kirki-number'] = wp.customize.Control.extend({

	ready: function() {

		'use strict';

		var control = this,
		    element = this.container.find( 'input' ),
		    min     = -99999,
		    max     = 99999,
		    step    = 1;

		// Set minimum value.
		if ( 'undefined' !== typeof control.params.choices && 'undefined' !== typeof control.params.choices.min ) {
			min = control.params.choices.min;
		}

		// Set maximum value.
		if ( 'undefined' !== typeof control.params.choices && 'undefined' !== typeof control.params.choices.max ) {
			max = control.params.choices.max;
		}

		// Set step value.
		if ( 'undefined' !== typeof control.params.choices && 'undefined' !== typeof control.params.choices.step ) {
			step = control.params.choices.step;
			if ( 'any' === control.params.choices.step ) {
				step = '0.001';
			}
		}

		// Init the spinner
		jQuery( element ).spinner({
			min: min,
			max: max,
			step: step
		});

		// On change

		var lastValue = undefined;

		var onChange = _.debounce(function() {
			var value = jQuery( element ).val();
			if(lastValue !== value){
				lastValue = value;
				control.setting.set( value  );
			}			
		},600);


		this.container.on( 'mousedown keydown', 'input', function(){
			lastValue = this.value;
		} );


		this.container.on( 'change click keyup paste', 'input', onChange );

	}

});
