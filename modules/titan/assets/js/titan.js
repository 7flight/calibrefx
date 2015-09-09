console.log('titan!');

var Titan = function ( args ) {

	if ( typeof( args ) == 'undefined' ) {
		return false;
	}

	if ( typeof( args.name ) == 'undefined' ) {
		return false;
	}

	var self = this;

	self.opt = {
		name 		: 	args.name,
		el 			: 	( typeof( args.el ) == 'undefined' ? window : args.el )
	};

	self.opt.eve = {
		'create' 	: 	[self],
		'build' 	: 	[self],
		'init' 		: 	[self]
	};

	for ( var eve in self.opt.eve ) {

		jQuery( self.opt.el ).trigger( ''+ self.opt.name +'::'+ eve +'', self.opt.eve[eve] );
	}
};

