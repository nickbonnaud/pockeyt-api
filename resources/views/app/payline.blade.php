<!DOCTYPE html>
<html lang="en"><head><meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <body>
        Hello
    </body>
</html>


<script type="text/javascript">
	/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};

/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {

/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId])
/******/ 			return installedModules[moduleId].exports;

/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			exports: {},
/******/ 			id: moduleId,
/******/ 			loaded: false
/******/ 		};

/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);

/******/ 		// Flag the module as loaded
/******/ 		module.loaded = true;

/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}


/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;

/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;

/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";

/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(0);
/******/ })
/************************************************************************/
/******/ ({

/***/ 0:
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	var _veriform = __webpack_require__(227);

	var _veriform2 = _interopRequireDefault(_veriform);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	window.Payline = {
	  openTokenizeCardForm: function openTokenizeCardForm(options, callback) {
	    return new _veriform2.default().showForm('payline-tokenize-card', options, callback);
	  }
	};

/***/ },

/***/ 177:
/***/ function(module, exports, __webpack_require__) {

	var global    = __webpack_require__(178)
	  , core      = __webpack_require__(179)
	  , ctx       = __webpack_require__(180)
	  , hide      = __webpack_require__(182)
	  , PROTOTYPE = 'prototype';

	var $export = function(type, name, source){
	  var IS_FORCED = type & $export.F
	    , IS_GLOBAL = type & $export.G
	    , IS_STATIC = type & $export.S
	    , IS_PROTO  = type & $export.P
	    , IS_BIND   = type & $export.B
	    , IS_WRAP   = type & $export.W
	    , exports   = IS_GLOBAL ? core : core[name] || (core[name] = {})
	    , expProto  = exports[PROTOTYPE]
	    , target    = IS_GLOBAL ? global : IS_STATIC ? global[name] : (global[name] || {})[PROTOTYPE]
	    , key, own, out;
	  if(IS_GLOBAL)source = name;
	  for(key in source){
	    // contains in native
	    own = !IS_FORCED && target && target[key] !== undefined;
	    if(own && key in exports)continue;
	    // export native or passed
	    out = own ? target[key] : source[key];
	    // prevent global pollution for namespaces
	    exports[key] = IS_GLOBAL && typeof target[key] != 'function' ? source[key]
	    // bind timers to global for call from export context
	    : IS_BIND && own ? ctx(out, global)
	    // wrap global constructors for prevent change them in library
	    : IS_WRAP && target[key] == out ? (function(C){
	      var F = function(a, b, c){
	        if(this instanceof C){
	          switch(arguments.length){
	            case 0: return new C;
	            case 1: return new C(a);
	            case 2: return new C(a, b);
	          } return new C(a, b, c);
	        } return C.apply(this, arguments);
	      };
	      F[PROTOTYPE] = C[PROTOTYPE];
	      return F;
	    // make static versions for prototype methods
	    })(out) : IS_PROTO && typeof out == 'function' ? ctx(Function.call, out) : out;
	    // export proto methods to core.%CONSTRUCTOR%.methods.%NAME%
	    if(IS_PROTO){
	      (exports.virtual || (exports.virtual = {}))[key] = out;
	      // export proto methods to core.%CONSTRUCTOR%.prototype.%NAME%
	      if(type & $export.R && expProto && !expProto[key])hide(expProto, key, out);
	    }
	  }
	};
	// type bitmap
	$export.F = 1;   // forced
	$export.G = 2;   // global
	$export.S = 4;   // static
	$export.P = 8;   // proto
	$export.B = 16;  // bind
	$export.W = 32;  // wrap
	$export.U = 64;  // safe
	$export.R = 128; // real proto method for `library` 
	module.exports = $export;

/***/ },

/***/ 178:
/***/ function(module, exports) {

	// https://github.com/zloirock/core-js/issues/86#issuecomment-115759028
	var global = module.exports = typeof window != 'undefined' && window.Math == Math
	  ? window : typeof self != 'undefined' && self.Math == Math ? self : Function('return this')();
	if(typeof __g == 'number')__g = global; // eslint-disable-line no-undef

/***/ },

/***/ 179:
/***/ function(module, exports) {

	var core = module.exports = {version: '2.4.0'};
	if(typeof __e == 'number')__e = core; // eslint-disable-line no-undef

/***/ },

/***/ 180:
/***/ function(module, exports, __webpack_require__) {

	// optional / simple context binding
	var aFunction = __webpack_require__(181);
	module.exports = function(fn, that, length){
	  aFunction(fn);
	  if(that === undefined)return fn;
	  switch(length){
	    case 1: return function(a){
	      return fn.call(that, a);
	    };
	    case 2: return function(a, b){
	      return fn.call(that, a, b);
	    };
	    case 3: return function(a, b, c){
	      return fn.call(that, a, b, c);
	    };
	  }
	  return function(/* ...args */){
	    return fn.apply(that, arguments);
	  };
	};

/***/ },

/***/ 181:
/***/ function(module, exports) {

	module.exports = function(it){
	  if(typeof it != 'function')throw TypeError(it + ' is not a function!');
	  return it;
	};

/***/ },

/***/ 182:
/***/ function(module, exports, __webpack_require__) {

	var dP         = __webpack_require__(183)
	  , createDesc = __webpack_require__(191);
	module.exports = __webpack_require__(187) ? function(object, key, value){
	  return dP.f(object, key, createDesc(1, value));
	} : function(object, key, value){
	  object[key] = value;
	  return object;
	};

/***/ },

/***/ 183:
/***/ function(module, exports, __webpack_require__) {

	var anObject       = __webpack_require__(184)
	  , IE8_DOM_DEFINE = __webpack_require__(186)
	  , toPrimitive    = __webpack_require__(190)
	  , dP             = Object.defineProperty;

	exports.f = __webpack_require__(187) ? Object.defineProperty : function defineProperty(O, P, Attributes){
	  anObject(O);
	  P = toPrimitive(P, true);
	  anObject(Attributes);
	  if(IE8_DOM_DEFINE)try {
	    return dP(O, P, Attributes);
	  } catch(e){ /* empty */ }
	  if('get' in Attributes || 'set' in Attributes)throw TypeError('Accessors not supported!');
	  if('value' in Attributes)O[P] = Attributes.value;
	  return O;
	};

/***/ },

/***/ 184:
/***/ function(module, exports, __webpack_require__) {

	var isObject = __webpack_require__(185);
	module.exports = function(it){
	  if(!isObject(it))throw TypeError(it + ' is not an object!');
	  return it;
	};

/***/ },

/***/ 185:
/***/ function(module, exports) {

	module.exports = function(it){
	  return typeof it === 'object' ? it !== null : typeof it === 'function';
	};

/***/ },

/***/ 186:
/***/ function(module, exports, __webpack_require__) {

	module.exports = !__webpack_require__(187) && !__webpack_require__(188)(function(){
	  return Object.defineProperty(__webpack_require__(189)('div'), 'a', {get: function(){ return 7; }}).a != 7;
	});

/***/ },

/***/ 187:
/***/ function(module, exports, __webpack_require__) {

	// Thank's IE8 for his funny defineProperty
	module.exports = !__webpack_require__(188)(function(){
	  return Object.defineProperty({}, 'a', {get: function(){ return 7; }}).a != 7;
	});

/***/ },

/***/ 188:
/***/ function(module, exports) {

	module.exports = function(exec){
	  try {
	    return !!exec();
	  } catch(e){
	    return true;
	  }
	};

/***/ },

/***/ 189:
/***/ function(module, exports, __webpack_require__) {

	var isObject = __webpack_require__(185)
	  , document = __webpack_require__(178).document
	  // in old IE typeof document.createElement is 'object'
	  , is = isObject(document) && isObject(document.createElement);
	module.exports = function(it){
	  return is ? document.createElement(it) : {};
	};

/***/ },

/***/ 190:
/***/ function(module, exports, __webpack_require__) {

	// 7.1.1 ToPrimitive(input [, PreferredType])
	var isObject = __webpack_require__(185);
	// instead of the ES6 spec version, we didn't implement @@toPrimitive case
	// and the second argument - flag - preferred type is a string
	module.exports = function(it, S){
	  if(!isObject(it))return it;
	  var fn, val;
	  if(S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
	  if(typeof (fn = it.valueOf) == 'function' && !isObject(val = fn.call(it)))return val;
	  if(!S && typeof (fn = it.toString) == 'function' && !isObject(val = fn.call(it)))return val;
	  throw TypeError("Can't convert object to primitive value");
	};

/***/ },

/***/ 191:
/***/ function(module, exports) {

	module.exports = function(bitmap, value){
	  return {
	    enumerable  : !(bitmap & 1),
	    configurable: !(bitmap & 2),
	    writable    : !(bitmap & 4),
	    value       : value
	  };
	};

/***/ },

/***/ 193:
/***/ function(module, exports, __webpack_require__) {

	// 19.1.2.14 / 15.2.3.14 Object.keys(O)
	var $keys       = __webpack_require__(194)
	  , enumBugKeys = __webpack_require__(207);

	module.exports = Object.keys || function keys(O){
	  return $keys(O, enumBugKeys);
	};

/***/ },

/***/ 194:
/***/ function(module, exports, __webpack_require__) {

	var has          = __webpack_require__(195)
	  , toIObject    = __webpack_require__(196)
	  , arrayIndexOf = __webpack_require__(200)(false)
	  , IE_PROTO     = __webpack_require__(204)('IE_PROTO');

	module.exports = function(object, names){
	  var O      = toIObject(object)
	    , i      = 0
	    , result = []
	    , key;
	  for(key in O)if(key != IE_PROTO)has(O, key) && result.push(key);
	  // Don't enum bug & hidden keys
	  while(names.length > i)if(has(O, key = names[i++])){
	    ~arrayIndexOf(result, key) || result.push(key);
	  }
	  return result;
	};

/***/ },

/***/ 195:
/***/ function(module, exports) {

	var hasOwnProperty = {}.hasOwnProperty;
	module.exports = function(it, key){
	  return hasOwnProperty.call(it, key);
	};

/***/ },

/***/ 196:
/***/ function(module, exports, __webpack_require__) {

	// to indexed object, toObject with fallback for non-array-like ES3 strings
	var IObject = __webpack_require__(197)
	  , defined = __webpack_require__(199);
	module.exports = function(it){
	  return IObject(defined(it));
	};

/***/ },

/***/ 197:
/***/ function(module, exports, __webpack_require__) {

	// fallback for non-array-like ES3 and non-enumerable old V8 strings
	var cof = __webpack_require__(198);
	module.exports = Object('z').propertyIsEnumerable(0) ? Object : function(it){
	  return cof(it) == 'String' ? it.split('') : Object(it);
	};

/***/ },

/***/ 198:
/***/ function(module, exports) {

	var toString = {}.toString;

	module.exports = function(it){
	  return toString.call(it).slice(8, -1);
	};

/***/ },

/***/ 199:
/***/ function(module, exports) {

	// 7.2.1 RequireObjectCoercible(argument)
	module.exports = function(it){
	  if(it == undefined)throw TypeError("Can't call method on  " + it);
	  return it;
	};

/***/ },

/***/ 200:
/***/ function(module, exports, __webpack_require__) {

	// false -> Array#indexOf
	// true  -> Array#includes
	var toIObject = __webpack_require__(196)
	  , toLength  = __webpack_require__(201)
	  , toIndex   = __webpack_require__(203);
	module.exports = function(IS_INCLUDES){
	  return function($this, el, fromIndex){
	    var O      = toIObject($this)
	      , length = toLength(O.length)
	      , index  = toIndex(fromIndex, length)
	      , value;
	    // Array#includes uses SameValueZero equality algorithm
	    if(IS_INCLUDES && el != el)while(length > index){
	      value = O[index++];
	      if(value != value)return true;
	    // Array#toIndex ignores holes, Array#includes - not
	    } else for(;length > index; index++)if(IS_INCLUDES || index in O){
	      if(O[index] === el)return IS_INCLUDES || index || 0;
	    } return !IS_INCLUDES && -1;
	  };
	};

/***/ },

/***/ 201:
/***/ function(module, exports, __webpack_require__) {

	// 7.1.15 ToLength
	var toInteger = __webpack_require__(202)
	  , min       = Math.min;
	module.exports = function(it){
	  return it > 0 ? min(toInteger(it), 0x1fffffffffffff) : 0; // pow(2, 53) - 1 == 9007199254740991
	};

/***/ },

/***/ 202:
/***/ function(module, exports) {

	// 7.1.4 ToInteger
	var ceil  = Math.ceil
	  , floor = Math.floor;
	module.exports = function(it){
	  return isNaN(it = +it) ? 0 : (it > 0 ? floor : ceil)(it);
	};

/***/ },

/***/ 203:
/***/ function(module, exports, __webpack_require__) {

	var toInteger = __webpack_require__(202)
	  , max       = Math.max
	  , min       = Math.min;
	module.exports = function(index, length){
	  index = toInteger(index);
	  return index < 0 ? max(index + length, 0) : min(index, length);
	};

/***/ },

/***/ 204:
/***/ function(module, exports, __webpack_require__) {

	var shared = __webpack_require__(205)('keys')
	  , uid    = __webpack_require__(206);
	module.exports = function(key){
	  return shared[key] || (shared[key] = uid(key));
	};

/***/ },

/***/ 205:
/***/ function(module, exports, __webpack_require__) {

	var global = __webpack_require__(178)
	  , SHARED = '__core-js_shared__'
	  , store  = global[SHARED] || (global[SHARED] = {});
	module.exports = function(key){
	  return store[key] || (store[key] = {});
	};

/***/ },

/***/ 206:
/***/ function(module, exports) {

	var id = 0
	  , px = Math.random();
	module.exports = function(key){
	  return 'Symbol('.concat(key === undefined ? '' : key, ')_', (++id + px).toString(36));
	};

/***/ },

/***/ 207:
/***/ function(module, exports) {

	// IE 8- don't enum bug keys
	module.exports = (
	  'constructor,hasOwnProperty,isPrototypeOf,propertyIsEnumerable,toLocaleString,toString,valueOf'
	).split(',');

/***/ },

/***/ 210:
/***/ function(module, exports, __webpack_require__) {

	// 7.1.13 ToObject(argument)
	var defined = __webpack_require__(199);
	module.exports = function(it){
	  return Object(defined(it));
	};

/***/ },

/***/ 215:
/***/ function(module, exports, __webpack_require__) {

	module.exports = { "default": __webpack_require__(216), __esModule: true };

/***/ },

/***/ 216:
/***/ function(module, exports, __webpack_require__) {

	__webpack_require__(217);
	var $Object = __webpack_require__(179).Object;
	module.exports = function defineProperty(it, key, desc){
	  return $Object.defineProperty(it, key, desc);
	};

/***/ },

/***/ 217:
/***/ function(module, exports, __webpack_require__) {

	var $export = __webpack_require__(177);
	// 19.1.2.4 / 15.2.3.6 Object.defineProperty(O, P, Attributes)
	$export($export.S + $export.F * !__webpack_require__(187), 'Object', {defineProperty: __webpack_require__(183).f});

/***/ },

/***/ 221:
/***/ function(module, exports, __webpack_require__) {

	"use strict";

	Object.defineProperty(exports, "__esModule", {
	  value: true
	});

	var _keys = __webpack_require__(222);

	var _keys2 = _interopRequireDefault(_keys);

	exports.extend = extend;
	exports.unpick = unpick;
	exports.includes = includes;

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	function extend(target) {
	  var result = {};
	  for (var index = 0; index < arguments.length; index++) {
	    var source = arguments[index] || {};
	    for (var key in source) {
	      if (Object.prototype.hasOwnProperty.call(source, key)) {
	        result[key] = source[key];
	      }
	    }
	  }
	  return result;
	}

	function unpick(object, keys) {
	  return (0, _keys2.default)(object).reduce(function (result, key) {
	    if (!includes(keys, key)) {
	      result[key] = object[key];
	    }
	    return result;
	  }, {});
	}

	function includes(array, searchElement) {
	  if (array.includes !== undefined) {
	    return array.includes(searchElement);
	  }

	  var currentElement = void 0;
	  var k = 0;
	  var len = array.length;

	  while (k < len) {
	    currentElement = array[k];
	    if (searchElement === currentElement || searchElement !== searchElement && currentElement !== currentElement) {
	      // NaN !== NaN
	      return true;
	    }
	    k++;
	  }
	  return false;
	}

/***/ },

/***/ 222:
/***/ function(module, exports, __webpack_require__) {

	module.exports = { "default": __webpack_require__(223), __esModule: true };

/***/ },

/***/ 223:
/***/ function(module, exports, __webpack_require__) {

	__webpack_require__(224);
	module.exports = __webpack_require__(179).Object.keys;

/***/ },

/***/ 224:
/***/ function(module, exports, __webpack_require__) {

	// 19.1.2.14 Object.keys(O)
	var toObject = __webpack_require__(210)
	  , $keys    = __webpack_require__(193);

	__webpack_require__(225)('keys', function(){
	  return function keys(it){
	    return $keys(toObject(it));
	  };
	});

/***/ },

/***/ 225:
/***/ function(module, exports, __webpack_require__) {

	// most Object methods by ES6 should accept primitives
	var $export = __webpack_require__(177)
	  , core    = __webpack_require__(179)
	  , fails   = __webpack_require__(188);
	module.exports = function(KEY, exec){
	  var fn  = (core.Object || {})[KEY] || Object[KEY]
	    , exp = {};
	  exp[KEY] = exec(fn);
	  $export($export.S + $export.F * fails(function(){ fn(1); }), 'Object', exp);
	};

/***/ },

/***/ 227:
/***/ function(module, exports, __webpack_require__) {

	'use strict';

	Object.defineProperty(exports, "__esModule", {
	  value: true
	});

	var _classCallCheck2 = __webpack_require__(228);

	var _classCallCheck3 = _interopRequireDefault(_classCallCheck2);

	var _createClass2 = __webpack_require__(229);

	var _createClass3 = _interopRequireDefault(_createClass2);

	var _helpers = __webpack_require__(230);

	var _object = __webpack_require__(221);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	var FRAME_STYLES = {
	  "-webkit-tap-highlight-color": "transparent",
	  "overflow-x": "hidden",
	  "overflow-y": "auto",
	  "z-index": "2147483647",
	  background: "transparent",
	  border: "0px none transparent",
	  display: "block",
	  height: "100%",
	  left: "0px",
	  margin: "0px",
	  padding: "0px",
	  position: "fixed",
	  top: "0px",
	  visibility: "visible",
	  width: "100%"
	};

	var HOST = 'https://vgs-assets.s3.amazonaws.com';
	function getFormUrl(formId) {
	  return HOST + '/forms/' + formId + '/index.html';
	}

	function postOpenMessage(frame, options) {
	  var message = (0, _object.extend)({
	    message: 'open'
	  }, options);
	  frame.contentWindow.postMessage(message, '*');
	}

	var VeriForm = function () {
	  function VeriForm() {
	    (0, _classCallCheck3.default)(this, VeriForm);
	  }

	  (0, _createClass3.default)(VeriForm, [{
	    key: 'close',
	    value: function close(data, callback) {
	      this.currentFrame.parentNode.removeChild(this.currentFrame);
	      this.currentFrame = undefined;
	      if (data.state === 'success' && callback) {
	        callback(data.response);
	      }
	    }
	  }, {
	    key: 'showForm',
	    value: function showForm(formId, options, callback) {
	      var _this = this;

	      var currentFrame = (0, _helpers.buildFrame)(getFormUrl(formId), FRAME_STYLES);

	      currentFrame.onload = function () {
	        postOpenMessage(currentFrame, options);
	      };
	      document.body.appendChild(currentFrame);
	      this.currentFrame = currentFrame;

	      window.addEventListener('message', function (event) {
	        if (_this.currentFrame && event.data.message === 'closeVeriformPopup') {
	          _this.close(event.data, callback);
	        }
	      }, false);
	    }
	  }]);
	  return VeriForm;
	}();

	exports.default = VeriForm;

/***/ },

/***/ 228:
/***/ function(module, exports) {

	"use strict";

	exports.__esModule = true;

	exports.default = function (instance, Constructor) {
	  if (!(instance instanceof Constructor)) {
	    throw new TypeError("Cannot call a class as a function");
	  }
	};

/***/ },

/***/ 229:
/***/ function(module, exports, __webpack_require__) {

	"use strict";

	exports.__esModule = true;

	var _defineProperty = __webpack_require__(215);

	var _defineProperty2 = _interopRequireDefault(_defineProperty);

	function _interopRequireDefault(obj) { return obj && obj.__esModule ? obj : { default: obj }; }

	exports.default = function () {
	  function defineProperties(target, props) {
	    for (var i = 0; i < props.length; i++) {
	      var descriptor = props[i];
	      descriptor.enumerable = descriptor.enumerable || false;
	      descriptor.configurable = true;
	      if ("value" in descriptor) descriptor.writable = true;
	      (0, _defineProperty2.default)(target, descriptor.key, descriptor);
	    }
	  }

	  return function (Constructor, protoProps, staticProps) {
	    if (protoProps) defineProperties(Constructor.prototype, protoProps);
	    if (staticProps) defineProperties(Constructor, staticProps);
	    return Constructor;
	  };
	}();

/***/ },

/***/ 230:
/***/ function(module, exports) {

	

	Object.defineProperty(exports, "__esModule", {
	  value: true
	});
	exports.style = style;
	exports.buildFrame = buildFrame;
	function style(object) {
	  var styles = [];
	  for (var cssName in object) {
	    if (object.hasOwnProperty(cssName)) {
	      styles.push(cssName + ': ' + object[cssName]);
	    }
	  }
	  return styles.join(';');
	}

	function buildFrame(url, cssStyles) {
	  var frame = document.createElement('iframe');
	  frame.src = url;
	  frame.style = style(cssStyles);
	  frame.setAttribute('frameborder', '0');
	  frame.setAttribute('allowtransparency', 'true');
	  return frame;
	}

/***/ }

/******/ });
</script>

<script type="text/javascript">
  document.addEventListener("DOMContentLoaded", function(event) {
    Payline.openTokenizeCardForm({
      applicationName: 'Test',
      applicationId: 'AP3UKRi9QBmgAjv9v4iKuH7T',
    }, function (tokenizedResponse) {
      document.getElementById('preview').innerText = JSON.stringify(tokenizedResponse, null, '  ');
    });
  });
</script>
