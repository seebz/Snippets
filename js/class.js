
/**
	Usage :
	
	var User = new Class({
		id: null,
		name: 'undefined',
		_construct: function(id, name) {
			this.id   = id;
			this.name = name;
		},
		getId: function() {
			return this.id;
		},
		getName: function() {
			return this.name;
		}
	});

	var Admin = new Class(User, {
		doSomething: function() {
			console.log('something is being done');
		}
	});


	var user = new User(42, 'Seebz');
	console.log( user.getName() );

	var admin = new Admin(1, 'BigBoss');
	console.log( admin.getName() );
	admin.doSomething();
	
 */

function Class(parent, body) {
	if (typeof(body)=='undefined' && typeof(parent)=='object') {
		body = parent;
		parent = null;
	}
	
	var _Class = function() {
		this._parent = parent;
		if (typeof(this._construct)=='function') {
			this._construct.apply(this, arguments);
		}
	};
	if (typeof(parent)=='function') {
		for(var prop in parent.prototype) {
			_Class.prototype[prop] = parent.prototype[prop];
		}
	}
	for(var prop in body) {
		_Class.prototype[prop] = body[prop];
	}
	
	return _Class;
}

