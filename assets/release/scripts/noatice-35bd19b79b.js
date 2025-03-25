!function(e){"function"==typeof define&&define.amd?define(["jquery"],e):"object"==typeof module&&"object"==typeof module.exports?exports=e(require("jquery")):e(jQuery)}(function(n){void 0!==n.easing&&(n.easing.jswing=n.easing.swing);var t=Math.pow,i=Math.sqrt,a=Math.sin,s=Math.cos,o=Math.PI,r=1.70158,u=1.525*r,c=1+r,d=2*o/3,l=2*o/4.5;function f(e){var n=7.5625,t=2.75;return e<1/t?n*e*e:e<2/t?n*(e-=1.5/t)*e+.75:e<2.5/t?n*(e-=2.25/t)*e+.9375:n*(e-=2.625/t)*e+.984375}n.extend(n.easing,{def:"easeOutQuad",swing:function(e){return n.easing[n.easing.def](e)},easeInQuad:function(e){return e*e},easeOutQuad:function(e){return 1-(1-e)*(1-e)},easeInOutQuad:function(e){return e<.5?2*e*e:1-t(-2*e+2,2)/2},easeInCubic:function(e){return e*e*e},easeOutCubic:function(e){return 1-t(1-e,3)},easeInOutCubic:function(e){return e<.5?4*e*e*e:1-t(-2*e+2,3)/2},easeInQuart:function(e){return e*e*e*e},easeOutQuart:function(e){return 1-t(1-e,4)},easeInOutQuart:function(e){return e<.5?8*e*e*e*e:1-t(-2*e+2,4)/2},easeInQuint:function(e){return e*e*e*e*e},easeOutQuint:function(e){return 1-t(1-e,5)},easeInOutQuint:function(e){return e<.5?16*e*e*e*e*e:1-t(-2*e+2,5)/2},easeInSine:function(e){return 1-s(e*o/2)},easeOutSine:function(e){return a(e*o/2)},easeInOutSine:function(e){return-(s(o*e)-1)/2},easeInExpo:function(e){return 0===e?0:t(2,10*e-10)},easeOutExpo:function(e){return 1===e?1:1-t(2,-10*e)},easeInOutExpo:function(e){return 0===e?0:1===e?1:e<.5?t(2,20*e-10)/2:(2-t(2,-20*e+10))/2},easeInCirc:function(e){return 1-i(1-t(e,2))},easeOutCirc:function(e){return i(1-t(e-1,2))},easeInOutCirc:function(e){return e<.5?(1-i(1-t(2*e,2)))/2:(i(1-t(-2*e+2,2))+1)/2},easeInElastic:function(e){return 0===e?0:1===e?1:-t(2,10*e-10)*a((10*e-10.75)*d)},easeOutElastic:function(e){return 0===e?0:1===e?1:t(2,-10*e)*a((10*e-.75)*d)+1},easeInOutElastic:function(e){return 0===e?0:1===e?1:e<.5?-(t(2,20*e-10)*a((20*e-11.125)*l))/2:t(2,-20*e+10)*a((20*e-11.125)*l)/2+1},easeInBack:function(e){return c*e*e*e-r*e*e},easeOutBack:function(e){return 1+c*t(e-1,3)+r*t(e-1,2)},easeInOutBack:function(e){return e<.5?t(2*e,2)*(2*(1+u)*e-u)/2:(t(2*e-2,2)*((1+u)*(2*e-2)+u)+2)/2},easeInBounce:function(e){return 1-f(1-e)},easeOutBounce:f,easeInOutBounce:function(e){return e<.5?(1-f(1-2*e))/2:(1+f(2*e-1))/2}})}),function(s){var o,i,r,t;void 0===s.noatice&&(s.fn.extend({noatice_message:function(e){return this.each(function(){s('<div class="noatice-message" />').html(e).noatice_sanitize_html().appendTo(s(this))})},noatice_sanitize_html:function(){var e=s("<div />").append(this);return e.find('animate, applet, audio, base, bgsound, body, embed, event-source, frame, head, html, iframe, meta, object, script, set, style, title, video, vmlframe, x, xml, xss, [action*="pt:"], [background*="pt:"], [data*="ta:"], [dynsrc*="pt:"], [formaction], [href*="ta:"], [href*="pt:"], [lowsrc*="pt:"], [onbeforeunload], [onblur], [onerror], [onfocus], [oninput], [onkeydown], [onkeypress], [onkeyup], [onload], [onmouseenter], [onmouseleave], [onmousemove], [onmouseout], [onmouseover], [onmouseup], [onmousewheel], [onpagehide], [onpageshow], [onpopstate], [onpropertychange], [onreadystatechange], [onresize], [onscroll], [onstart], [onunload], [poster*="pt:"], [size*="pt:"], [src*="ta:"], [src*="pt:"], [style*="behavior"], [style*="expression"], [style*="pt:"], [value*="pt:"], [xlink\\:href*="pt:"]').remove(),e.children()}}),o=s.noatice=s.noatice||{},s.extend(o,{body:s(document.body),dismiss:"noatice-dismiss",queue:[],ready:!1,running:!1,wrapper:s('<div id="noatifications" />'),init:function(){o.ready||(o.ready=!0,s(window).on("resize",function(){s(".noatification").find(":animated").stop(!0,!0)}),o.body.hasClass(t.rtl_class)&&o.wrapper.addClass("noatifications-rtl"),r.enter(),r.tooltips())}}),i=o.add=o.add||{},s.extend(i,{base:function(e){Array.isArray(e)||(e=[e]),s.each(e,function(e,n){s.isPlainObject(n)&&o.queue.push(s.extend({},t.defaults,n))}),o.running||r.enter()},general:function(e,n,t){t=s.isPlainObject(t)?t:{dismissable:t};i.base(s.extend(t,{css_class:""===e?"noatice-general":e,message:n}))},error:function(e,n){i.general("noatice-error",e,n)},info:function(e,n){i.general("noatice-info",e,n)},success:function(e,n){i.general("noatice-success",e,n)},warning:function(e,n){i.general("noatice-warning",e,n)}}),r=o.methods=o.methods||{},s.extend(r,{enter:function(){var t,e,n,i,a;o.ready&&0<o.queue.length?(o.running=!0,0===o.wrapper.closest(document.documentElement).length&&o.wrapper.noatice_sanitize_html().appendTo(o.body),t=o.queue.shift(),0===(e=t.id?s("#"+t.id):"").length?(e=s('<div class="noatice" />').attr("id",t.id).addClass(t.css_class),n=s('<div class="noatice-inner" />').css("width",o.wrapper.width()).noatice_message(t.message).appendTo(e),t.dismissable&&(e.addClass("noatice-dismissable"),i=s('<div class="noatice-dismiss" />').appendTo(n).on("click",function(){function e(){var e=s(this);e.hasClass("noatice-exited")?(e.remove(),0===o.wrapper.children().length&&o.wrapper.detach()):e.addClass("noatice-exited")}var n=s(this).closest(".noatice-inner").css("width",o.wrapper.width()).closest(".noatice").stop(!0,!0).css("z-index","0");n.animate({"margin-top":"-"+n.height()+"px"},{complete:e,duration:t.duration.down,easing:t.easing.down,queue:!1}).animate({"margin-left":"100%"},{complete:e,duration:t.duration.exit,easing:t.easing.exit,queue:!1})}),"number"==typeof t.dismissable)&&0<t.dismissable&&e.on(o.dismiss,function(){var e=s(this),n=e.data(o.dismiss);n&&clearTimeout(n),e.data(o.dismiss,setTimeout(function(){i.triggerHandler("click")},t.dismissable))}).triggerHandler(o.dismiss),a=function(){r.set_widths(s(this)),r.enter()},"number"==typeof t.delay&&0<t.delay?(r.enter(),a=function(){r.set_widths(s(this))}):t.delay=0,setTimeout(function(){e.noatice_sanitize_html().prependTo(o.wrapper).animate({"margin-left":"0px"},{complete:a,duration:t.duration.enter,easing:t.easing.enter,queue:!1})},t.delay)):(e.triggerHandler(o.dismiss),r.enter())):o.running=!1},set_widths:function(e){e.children().css("width","")},tooltips:function(e){0<(e=e||s(".noatice-tooltip[title], [data-noatice-tooltip]")).length&&(e.filter(".noatice-tooltip[title]").each(function(){var e=s(this);e.data("noatice-tooltip",e.attr("title")).removeAttr("title")}),e.on("focus mouseenter",function(){var e=s(this),n=e.data("noatice-sibling");n||(n=s('<div class="noatice" />').data("noatice-sibling",e).append(s('<span class="noatice-arrow" />')).noatice_message(e.data("noatice-tooltip")).on("noatice-position",function(){var e=s(this).css("width",""),n=e.width(),t=e.data("noatice-sibling"),i=t.offset(),t=t.outerWidth();e.css({left:i.left-(n-t)/2+"px",top:i.top-e.innerHeight()-9+"px",width:n+1+"px"})}),e.data("noatice-sibling",n),e.is("[data-noatice-class]")&&n.addClass(e.data("noatice-class"))),0===n.closest(document.documentElement).length&&n.noatice_sanitize_html().appendTo(o.body),n.stop(!0).triggerHandler("noatice-position"),n.fadeIn("fast")}).on("blur mouseleave",function(){var e=s(this),e=!e.is(":focus")&&e.data("noatice-sibling");e&&e.stop(!0).fadeOut("fast",function(){s(this).detach()})}))}}),t=o.options=o.options||{},s.extend(t,{defaults:{css_class:"",delay:0,dismissable:5e3,duration:{down:400,enter:600,exit:200},easing:{down:"easeOutBounce",enter:"easeOutElastic",exit:"easeOutQuad"},id:"",message:""},rtl_class:"rtl"}),o.init())}(jQuery);