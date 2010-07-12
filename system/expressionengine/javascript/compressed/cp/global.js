/*!
 * ExpressionEngine - by EllisLab
 *
 * @package		ExpressionEngine
 * @author		ExpressionEngine Dev Team
 * @copyright	Copyright (c) 2003 - 2010, EllisLab, Inc.
 * @license		http://expressionengine.com/user_guide/license.html
 * @link		http://expressionengine.com
 * @since		Version 2.0
 * @filesource
 */

jQuery(document).ready(function(){function l(){var f=EE.SESS_TIMEOUT-6E4,b=EE.XID_TIMEOUT-6E4,c=f<b?f:b,d=false,e,h;h=function(){a.ajax({type:"POST",dataType:"json",url:EE.BASE+"&C=login&M=refresh_xid",success:function(g){a("input[name='XID']").val(g.xid);EE.XID=g.xid;setTimeout(h,b)}})};e=function(){var g='<form><div id="logOutWarning" style="text-align:center"><p>'+EE.lang.session_expiring+'</p><label for="username">'+EE.lang.username+'</label>: <input type="text" id="log_backin_username" name="username" value="" style="width:100px" size="35" dir="ltr" id="username" maxlength="32"  />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<label for="password">'+
EE.lang.password+'</label>: <input class="field" id="log_backin_password" type="password" name="password" value="" style="width:100px" size="32" dir="ltr" id="password" maxlength="32"  />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="submit" id="submit" name="submit" value="'+EE.lang.login+'" class="submit" /><span id="logInSpinner"></span></div></form>',i;if(d===true){finalLogOutTimer(d);return false}else setTimeout(finalLogOutTimer,c);a.ee_notice(g,{type:"custom",open:true,close_on_click:false});i=
a("#logOutWarning");i.find("#log_backin_username").focus();i.find("input#submit").click(function(){var k=i.find("input#log_backin_username").val(),o=i.find("input#log_backin_password").val(),m=a(this),n=i.find("span#logInSpinner");m.hide();n.html('<img src="'+EE.PATH_CP_GBL_IMG+'loader_blackbg.gif" />');a.ajax({type:"POST",dataType:"json",url:EE.BASE+"&C=login&M=authenticate&is_ajax=true",data:{username:k,password:o,XID:EE.XID},success:function(j){d=true;if(j.messageType==="success"){a("input[name='XID']").val(j.xid);
i.slideUp("fast");a.ee_notice(j.message,{type:"custom",open:true});EE.XID=j.xid;d=true;clearTimeout(e);setTimeout(e,c)}else if(j.messageType==="failure"){i.before('<div id="loginCheckFailure">'+j.message+"</div>");n.hide("fast");m.css("display","inline")}else if(j.messageType==="logout")window.location.href=EE.BASE+"&C=login&M=logout&auto_expire=true"}});return false})};EE.SESS_TYPE=="c"?setTimeout(h,b):setTimeout(e,c)}function p(){var f=a(".notice").filter("p.js_hide"),b={success:"message_success",
notice:"message",error:"message_failure"},c=[],d=0,e,h;for(e in b)if(EE.flashdata.hasOwnProperty(b[e])){h=e==="error"?f.filter(".failure").slice(0,1):e==="success"?f.filter(".success").slice(0,1):f.slice(0,1);c[d++]={message:EE.flashdata[b[e]],type:e};h.remove()}c.length&&a.ee_notice(c)}var a=jQuery;a(document).bind("ajaxComplete",function(f,b){if(b.status&&+b.status===401)window.location=EE.BASE+"&"+b.responseText});EE.create_searchbox=function(){var f;f="placeholder"in document.createElement("input")?
function(b,c){this.setAttribute("type","search");a(this).attr({autosave:c,results:"10",placeholder:b})}:function(b){var c=a(this),d=c.css("color");c.focus(function(){c.css("color",d);c.val()===b&&c.val("")}).blur(function(){if(c.val()===""||c.val===b)c.val(b).css("color","#888")}).trigger("blur")};EE.create_searchbox=function(b,c,d){(b=document.getElementById(b))&&f.call(b,c,d)};EE.create_searchbox.apply(EE.create_searchbox,arguments)};EE.create_searchbox("cp_search_keywords",EE.lang.search,"ee_cp_search");
EE.create_searchbox("template_keywords",EE.lang.search_template,"ee_template_search");a('a[rel="external"]').click(function(){window.open(this.href);return false});finalLogOutTimer=function(f){var b=a('<div id="logOutConfirm">'+EE.lang.session_timeout+" </div>"),c=30,d=c,e,h,g,i;g=function(){window.location=EE.BASE+"&C=login&M=logout&auto_expire=true"};i=function(){if(c<1)return setTimeout(g,0);else c===d&&a(window).bind("unload.logout",g);b.dialog("option","title",EE.lang.logout+" ("+(c--||"...")+
")");e=setTimeout(i,1E3)};h={Cancel:function(){a(this).dialog("close")}};h[EE.lang.logout]=g;b.dialog({autoOpen:false,resizable:false,modal:true,title:EE.lang.logout,position:"center",minHeight:"0px",buttons:h,beforeClose:function(){clearTimeout(e);a(window).unbind("unload.logout");c=d;a.ajax({type:"POST",dataType:"json",url:EE.BASE+"&C=login&M=refresh_xid",success:function(k){a("input[name='XID']").val(k.xid);EE.XID=k.xid;a("#logOutWarning").slideUp("fast");l()}});f=false}});a("#logOutConfirm").dialog("open");
a(".ui-dialog-buttonpane button:eq(2)").focus();i();return false};EE.SESS_TIMEOUT&&l();(function(){var f={revealSidebarLink:"77%",hideSidebarLink:"100%"},b=a("#mainContent"),c=a("#sidebarContent"),d=b.height(),e=c.height(),h;if(EE.CP_SIDEBAR_STATE==="off"){b.css("width","100%");a("#revealSidebarLink").css("display","block");a("#hideSidebarLink").hide();c.show();e=c.height();c.hide()}else{c.hide();d=b.height();c.show()}h=e>d?e:d;a("#revealSidebarLink, #hideSidebarLink").click(function(){var g=a(this),
i=g.siblings("a"),k=this.id==="revealSidebarLink";a("#sideBar").css({position:"absolute","float":"",right:"0"});g.hide();i.css("display","block");c.slideToggle();b.animate({width:f[this.id],height:k?h:d},function(){a("#sideBar").css({position:"","float":"right"})});return false})})();EE.flashdata!==undefined&&p();EE.notepad=function(){var f=a("#notePad"),b=a("#notepad_form"),c=a("#notePadTextEdit"),d=a("#notePadControls"),e=a("#notePadText").removeClass("js_show"),h=e.text(),g=c.val();return{init:function(){g&&
e.html(g.replace(/</ig,"&lt;").replace(/>/ig,"&gt;").replace(/\n/ig,"<br />"));f.click(EE.notepad.show);d.find("a.cancel").click(EE.notepad.hide);b.submit(EE.notepad.submit);d.find("input.submit").click(EE.notepad.submit);c.autoResize()},submit:function(){g=a.trim(c.val());var i=g.replace(/</ig,"&lt;").replace(/>/ig,"&gt;").replace(/\n/ig,"<br />");c.attr("readonly","readonly").css("opacity",0.5);d.find("#notePadSaveIndicator").show();a.post(b.attr("action"),{notepad:g,XID:EE.XID},function(){e.html(i||
h).show();c.attr("readonly","").css("opacity",1).hide();d.hide().find("#notePadSaveIndicator").hide()},"json");return false},show:function(){if(d.is(":visible"))return false;var i="";if(e.hide().text()!==h)i=e.html().replace(/<br>/ig,"\n").replace(/&lt;/ig,"<").replace(/&gt;/ig,">");d.show();c.val(i).show().height(0).focus().trigger("keypress")},hide:function(){e.show();c.hide();d.hide();return false}}}();EE.notepad.init();a("#accessoryTabs li a").click(function(){var f=a(this).parent("li"),b=a("#"+
this.className);if(f.hasClass("current")){b.hide();f.removeClass("current")}else{if(f.siblings().hasClass("current")){b.show().siblings(":not(#accessoryTabs)").hide();f.siblings().removeClass("current")}else b.slideDown();f.addClass("current")}return false});(function(){var f=a("#search"),b=f.clone(),c=a("#cp_search_form").find(".searchButton"),d;d=function(){var e=a(this).attr("action"),h={cp_search_keywords:a("#cp_search_keywords").attr("value")};a.ajax({url:e+"&ajax=y",data:h,beforeSend:function(){c.toggle()},
success:function(g){c.toggle();f=f.replaceWith(b);b.html(g);a("#cp_reset_search").click(function(){b=b.replaceWith(f);a("#cp_search_form").submit(d);a("#cp_search_keywords").select();return false})},dataType:"html"});return false};a("#cp_search_form").submit(d)})();a("h4","#quickLinks").click(function(){window.location.href=EE.BASE+"&C=myaccount&M=quicklinks"}).add("#notePad").hover(function(){a(".sidebar_hover_desc",this).show()},function(){a(".sidebar_hover_desc",this).hide()}).css("cursor","pointer");
a("#activeUser").one("mouseover",function(){var f=a('<div id="logOutConfirm">'+EE.lang.logout_confirm+" </div>"),b=30,c=b,d,e,h,g;h=function(){a.ajax({url:EE.BASE+"&C=login&M=logout",async:!a.browser.safari});window.location=EE.BASE+"&C=login&M=logout"};g=function(){if(b<1)return setTimeout(h,0);else b===c&&a(window).bind("unload.logout",h);f.dialog("option","title",EE.lang.logout+" ("+(b--||"...")+")");d=setTimeout(g,1E3)};e={Cancel:function(){a(this).dialog("close")}};e[EE.lang.logout]=h;f.dialog({autoOpen:false,
resizable:false,modal:true,title:EE.lang.logout,position:"center",minHeight:"0px",buttons:e,beforeClose:function(){clearTimeout(d);a(window).unbind("unload.logout");b=c}});a("a.logOutButton",this).click(function(){a("#logOutConfirm").dialog("open");a(".ui-dialog-buttonpane button:eq(2)").focus();g();return false})});a(".js_show").show()});
