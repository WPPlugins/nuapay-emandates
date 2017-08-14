(function(funcName, baseObj) {
    var readyList = [];
    var readyFired = false;
    var readyEventHandlersInstalled = false;
    function ready() {
        if (!readyFired) {
            readyFired = true;
            for (var i = 0; i < readyList.length; i++) {
                readyList[i].fn.call(window, readyList[i].ctx);
            }
            readyList = [];
        }
    }
    
    function readyStateChange() {
        if ( document.readyState === 'complete' ) {
            ready();
        }
    }
    baseObj[funcName] = function(callback, context) {
        if (readyFired) {
            setTimeout(function() {callback(context);}, 1);
            return;
        } else {
            readyList.push({fn: callback, ctx: context});
        }
        if (document.readyState === 'complete') {
            setTimeout(ready, 1);
        } else if (!readyEventHandlersInstalled) {
            if (document.addEventListener) {
                document.addEventListener('DOMContentLoaded', ready, false);
                window.addEventListener('load', ready, false);
            } else {
                document.attachEvent('onreadystatechange', readyStateChange);
                window.attachEvent('onload', ready);
            }
            readyEventHandlersInstalled = true;
        }
    };
})('npDocumentReady', window);

var EventHelper = {
	addEvent : function(target, name, func) {
		if (target.addEventListener) {
			target.addEventListener(name, func, false);
		} else {
			target.attachEvent('on' + ieName, func);
		}
	},
	removeEvent : function(target, name, func) {
		if (target.removeEventListener) {
			target.removeEventListener(name, func);
		} else {
			target.detachEvent('on' + name, func);
		}
	}
};

var DocOverflow = {
	hide : function() {
		var original = {
			html : document.documentElement.style.overflow,
			body : document.body.style.overflow
		};
		document.body.style.overflow = 'hidden';
		document.documentElement.style.overflow = 'hidden';
		return original;
	},
	show : function(original) {
		document.body.style.overflow = original.html;
		document.documentElement.style.overflow = original.body;
	}
};

var CheckoutEvent = {CANCEL : 0, SUCCESS : 1, ERROR : 2};

var ResultModal = {
	appModalWindowId : 'np-overlay-modal',
	appModalBodyId : 'np-overlay-modal-content',
	appModalCloseBtId : 'np-overlay-modal-close',
		
	overflow : {html : null, body : null},
	
	show : function(text) {
		var m = this;
		
		var modal = document.getElementById(m.appModalWindowId);
		var modalBody = document.getElementById(m.appModalBodyId);
		var bt = document.getElementById(m.appModalCloseBtId);
		
		m.overflow = DocOverflow.hide();
		modalBody.innerHTML = text;
		modal.style.display = 'block';
		
		EventHelper.addEvent(bt, 'click', ResultModal.hide);
	},
	
	hide : function(e) {
		
		var modal = document.getElementById(ResultModal.appModalWindowId);
		var bt = document.getElementById(ResultModal.appModalCloseBtId);
		
		DocOverflow.show(ResultModal.overflow);
		modal.style.display = 'none';
		
		EventHelper.removeEvent(bt, 'click', ResultModal.closeFn);
	}
};

var EApp = {	
	
	DEF_BUTTON_TEXT : NP_Script_Params.text.subscribe,
	DEF_BUTTON_CSS_CLASS : 'subscribe-button',
	DEF_BUTTON_ID : 'checkoutBtn',
	DEF_FRAME_ID : 'paymentFrame',
		
	button : null,
	iframe : null,
	
	overflow : {html : null, body : null},

	appButtonContainerId : 'np-overlay-button-container',
	
	show : function() {
		var app = this;
		app.overflow = DocOverflow.hide();
		app.iframe.style.display = 'block';
	},
	hide : function() {
		var app = this;
		DocOverflow.show(app.overflow);
		app.iframe.style.display = 'none';
	},
	__createButton : function() {
		var b = document.createElement('button');
		var app = this;
		app.button = b;
			
		b.type		= 'button';
		b.id		= app.DEF_BUTTON_ID;
		b.innerHTML = app.DEF_BUTTON_TEXT;
		b.className = app.DEF_BUTTON_CSS_CLASS;
			
		var openFunc = function(e) {
			var btt = this;//button with attached  openFunc as click event
			//Usses cross domain msg - JSON.stringify no supported in < IE8
			var formData = {
					action : 'enrichMandate',
					customer : {name:null, email:null, mobile:null, address:null, address2:null, city:null, country:null}, 
					mandate : {iban:null}
			};
			//JSON is not supported in all browsers
			EApp.iframe.contentWindow.postMessage(JSON.stringify(formData), '*');
			EApp.show();
		};
		
		EventHelper.addEvent(b, 'click', openFunc);
			
		var buttonContainer = document.getElementById(app.appButtonContainerId);
		
		buttonContainer.appendChild(b);
	},

	init : function(url) {	
		var app = this;
		var f = null;
		
		f = document.createElement('iframe');
		f.id = app.DEF_FRAME_ID;
		f.name = app.DEF_FRAME_ID;
		f.src = url;
		f.frameBorder = 0;
		f.style.display = 'none';
		f.style.position = 'fixed';
		f.style.top = '0px';
		f.style.left = '0px';
		f.style.bottom = '0px';
		f.style.right = '0px';
		f.style.width = '100%';
		f.style.height = '100%';
		f.style.margin = '0';
		f.style.padding = '0';
		f.style.zIndex = '999999';
		f.style.overflow = 'hidden';
		f.style.border = '0 none';
		
		app.iframe = f;
		
		var loadFn = function(e) {
	        app.__createButton();
	    };
	    
	    var keyUpFn = function(e) {
			if (e.keyCode == 27) {//ESC
				app.hide();
		    }
	    };
	    
	    EventHelper.addEvent(f, 'load', loadFn);
	    EventHelper.addEvent(document, 'keyup', keyUpFn);
		
		document.body.appendChild(f);
	}
};

function merchantCheckoutMessageListner(event) {
	//JSON is not supported in all browsers
	var result = JSON.parse(event.data || '{}');
	if (typeof(result) === 'object' && ('action' in result) && result.action === 'CHECKOUT_RESULT') {
		var text = '';
		if (result.status == CheckoutEvent.CANCEL) {
			text = 'Operation canceled. If you want to add new mandate click on Subscribe button.';
		} else if (result.status == CheckoutEvent.SUCCESS) {
			text = 'Mandate ' + result.details.umr + ' has been created';
		} else if (result.status == CheckoutEvent.ERROR) {
			text = 'Erorr';
		}
		
		EApp.hide();
		ResultModal.show(text);
	}
};

(function() {
	npDocumentReady(function() {
		EApp.init(NP_Script_Params.url);
		addEventListener('message', merchantCheckoutMessageListner, false);
	});
})();