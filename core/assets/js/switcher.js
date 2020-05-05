/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */
!function(t,e,i,n){function s(e,i){this.container=t(e),this.options=t.extend({},h,i),this._defaults=h,this._name=o,this.togglers=[],this.elements=[],this.current=null,this.init()}var o="switcher",h={onShow:function(){},onHide:function(){},cookieName:"switcher",togglerSelector:"a",elementSelector:"div.tab",elementPrefix:"#page-",element:"#main"};s.prototype={init:function(){var n=this;if(this.togglers=this.container.find(this.options.togglerSelector),this.elements=t(this.options.element).find(this.options.elementSelector),0!=this.togglers.length&&this.togglers.length==this.elements.length){this.hideAll(),this.togglers.on("click",function(e){e.preventDefault(),n.display(t(this).attr("id"))});var s=i.location.hash.substring(1);"undefined"!=typeof Storage&&(s||(s=localStorage.getItem(this.options.elementPrefix+"active"))),s&&this.has(s)||(s=t(this.togglers[0]).attr("id")),this.display(s),i.location.hash&&setTimeout(function(){e.scrollTo(0,0)},1)}},has:function(e){var i=t("#"+e),n=t(this.options.elementPrefix+e);return i.length&&n.length?!0:!1},display:function(n){var s=t("#"+n),o=t(this.options.elementPrefix+n);return null==s||null==o||s==this.current?this:(null!=this.current&&(this.hide(t(this.options.elementPrefix+this.current)),t("#"+this.current).removeClass("active")),this.show(o),s.addClass("active"),this.current=s.attr("id"),"undefined"!=typeof Storage&&localStorage.setItem(this.options.elementPrefix+"active",this.current),i.location.hash=this.current,void t(e).scrollTop(0))},hide:function(t){t.hide(),this.options.onHide()},hideAll:function(){this.elements.hide(),this.togglers.removeClass("active")},show:function(t){t.show(),this.options.onShow()}},t.fn[o]=function(e){return this.each(function(){t.data(this,"plugin_"+o)||t.data(this,"plugin_"+o,new s(this,e))})}}(jQuery,window,document);