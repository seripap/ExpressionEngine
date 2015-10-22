/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		EllisLab Dev Team
 * @copyright	Copyright (c) 2003 - 2015, EllisLab, Inc.
 * @license		https://ellislab.com/expressionengine/user-guide/license.html
 * @link		http://ellislab.com
 * @since		Version 2.0
 * @filesource
 */
/*
 @todo Documentation, reconsider class names

$(textarea).getSelectedText();
$(textarea).getSelectedRange();

selection = $(textarea).createSelection(start, end);
selection.replaceWith('[code]'+selection.getSelectedText()+'[/code]');

$(textarea).insertAtCursor('abc');
$(textarea).scrollToCursor();
*/
!function(){function e(e){this.el=e,this.lastIdx=-2,this.currentIdx=0,document.selection&&(this.range=this.el.createTextRange())}function t(t){e.call(this,t);var i=$(this.el),n=i.scrollTop(9999).scrollTop(),s=i.val();i.val(s+"\n"),new_height=i.scrollTop(9999).scrollTop(),i.val(s).scrollTop(0),this.textarea_line_height=new_height-n,this.jQ_el=i}
// ---------------------------------
function i(e){this.el=e,this.sel=new t(this.el)}if(e.prototype={createSelection:function(e,t){if(this.el.focus(),"selectionStart"in this.el)this.el.selectionStart=e,this.el.selectionEnd=t;else if(document.selection){var i=document.selection.createRange();i.moveStart("character",-this.el.value.length),i.collapse(),i.moveStart("character",e),i.moveEnd("character",t-e),i.select()}return this},getSelectedText:function(){return"selectionStart"in this.el?this.el.value.substr(this.el.selectionStart,this.el.selectionEnd-this.el.selectionStart):document.selection?(this.el.focus(),document.selection.createRange().text):void 0},getSelectedRange:function(){if("selectionStart"in this.el)return{start:this.el.selectionStart,end:this.el.selectionEnd};if(document.selection){var e=document.selection.createRange(),t=Math.abs(e.duplicate().moveEnd("character",-1e5));return selectionStart=t-e.text.length,{start:selectionStart,end:t}}},replaceWith:function(e){var t;return this.el.focus(),"selectionStart"in this.el?(t=this.el.selectionStart+e.length,this.el.value=this.el.value.substr(0,this.el.selectionStart)+e+this.el.value.substr(this.el.selectionEnd,this.el.value.length),this.el.setSelectionRange(t,t)):document.selection&&(document.selection.createRange().text=e),this},selectNext:function(e){if("selectionStart"in this.el){var t=this.currentIdx;chunk=t>0?this.el.value.substring(this.currentIdx):this.el.value,this.currentIdx=chunk.indexOf(e),-1!=this.currentIdx?(this.createSelection(t+this.currentIdx,t+this.currentIdx+e.length),this.lastIdx=t+this.currentIdx,this.currentIdx+=t+e.length):this.lastIdx!=this.currentIdx&&(this.lastIdx=-1,this.currentIdx=0,this.selectNext(e))}else if(document.selection){
// This is actually easier in IE - whoa!
this.el.focus();var i=this.range.findText(e,1,0);i?(this.range.select(),this.range.collapse(!1)):this.range=this.el.createTextRange()}},resetCycle:function(){this.lastIdx=-2,this.currentIdx=0,document.selection&&(this.range=this.el.createTextRange())}},jQuery){
// Add any methods that require jQuery support
var n=function(){};n.prototype=e.prototype,t.prototype=new n,t.prototype.constructor=t,t.prototype.scrollToCursor=function(){
// IE already does this when you create a selection, so we only hack
// around the others
if("selectionStart"in this.el){for(var e=this.getSelectedRange(),t=this.jQ_el.val().substr(0,e.start).split("\n"),i=t.length,n=0;n<t.length;n++)length_ratio=t[n].length/this.el.cols,length_ratio>1&&(i+=Math.ceil(length_ratio));i=i>5?i-5:0,this.jQ_el.scrollTop((i-5)*this.textarea_line_height)}return this}}else t=e;
// ---------------------------------
if(i.prototype={getSelectionObj:function(){return this.sel},createSelection:function(e,t){return this.sel.createSelection(e,t)},getSelectedText:function(){return this.sel.getSelectedText()},getSelectedRange:function(){return this.sel.getSelectedRange()},insertAtCursor:function(e){this.sel.replaceWith(e)},selectNext:function(e){return this.sel.selectNext(e),this.sel},_resize:function(){var e=this.sel.getSelectedRange();e.start==e.end&&e.end==this.el.value.length&&(this.el.value+="\n",this.sel.createSelection(e.end,e.end)),this.el.scrollHeight>this.el.clientHeight&&$(this.el).height(this.el.scrollHeight+10)},autoResize:function(){var e=this,t=$(this.el);t.css("overflow","hidden"),t.keypress(function(){e._resize()}),t.keyup(function(t){13==t.keyCode&&e._resize()})}},jQuery)
// And we want methods to be available the traditional jQuery way
for(func in i.prototype)jQuery.fn[func]=function(e){return function(){var t=Array.prototype.slice.call(arguments),n=this.data("txtarea");return n||(n=new i(this[0]),this.data("txtarea",n)),n[e].apply(n,t)}}(func);window.Txtarea=i}();