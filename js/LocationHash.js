
/**
 * Class JS pour manipuler facilement le fragment `hash` du document (au format `query string`)
 */
var LocationHash = {
	getHash: function() {
		return document.location.hash.replace(/^#/, '');
	},
	setHash: function(hash) {
		hash = hash.toString();
		if (hash) {
			hash = '#'+ hash.replace(/^#/, '');
		}
		var loc = document.location.href.replace(document.location.hash, hash);
		if (document.location.href != loc) {
			document.location.href = loc;
		}
		return this;
	},
	getArg: function(key) {
		var args = this.getArgs();
		return (typeof(args[key]) === 'undefined' ? null : args[key]);
	},
	getArgs: function() {
		var h = this.getHash().split('&');
		var args = {};
		for(var i=0, frag; frag=h[i]; i++) {
			frag = frag.split('=');
			args[ frag[0] ] = (typeof(frag[1]) !== 'undefined' ? this.decode(frag[1]) : null);
		}
		return args;
	},
	setArg: function(key, value) {
		var args = this.getArgs();
		args[ key.toString() ] = value.toString();
		this.setArgs(args);
		return this;
	},
	setArgs: function(args) {
		args = args || {};
		var hash = [];
		for(var key in args) {
			var value = args[ key ];
			if (typeof(value) != 'undefined' && value != 'undefined') {
				hash.push(key +'='+ this.encode(value));
			}
		}
		this.setHash( hash.join('&') );
		return this;
	},
	removeArg: function(key) {
		var args = this.getArgs();
		delete args[ key ];
		this.setArgs(args);
		return this;
	},
	removeArgs: function(/* poly */) {
		var keys = [];
		if (arguments.length === 1) {
			keys = arguments[0];
		} else if (arguments.length) {
			keys = arguments;
		}
		var currentArgs = this.getArgs();
		var args = {};
		for(var key in currentArgs) {
			if (keys.indexOf(key) < 0) {
				args[ key ] = currentArgs[ key ];
			}
		}
		this.setArgs(args);
		return this;
	},
	encode: function(str) {
		return encodeURIComponent( str);
	},
	decode: function(str) {
		return decodeURIComponent( str.replace(/[+]/g, ' ') );
	}
};
