contentScriptTwitter = {};


/* send */
/*chrome.runtime.sendMessage(id,{greeting: "hello"}, function(response) {
	//profileElement.click();
});
*/

/* recive */
chrome.runtime.onMessage.addListener(function(request, sender, callback) {
	if(window.location.host.indexOf('twitter.com') == -1) 
		return;
	if(request.action == 'twitter_alreadyLoaded')
		callback({alreadyLoaded: true});
	else if(request.action == 'twitter_load')
		contentScriptTwitter.load();       
	return true;  /* Will respond asynchronously */
});
	
contentScriptTwitter.load = function() {
	program.addHtml('loading', '<div id="loading" class="modal modal-hidden column-center"><i class="fas fa-sync-alt fa-spin fa-3x fa-fw"></i><span class="sr-only">Cargando...</span></div><div id="loading-simple" class="modal-simple modal-hidden column-center"><i class="fas fa-cog fa-spin fa-2x fa-fw"></i><span class="sr-only">Cargando...</span></div>');		
	program.addHtml('working-modal','<div class="modal-close">x</div><div id="working-modal" class="modal modal-hidden column-center"> <div id="working-modal-title" class="row-center"><i class="fas fa-robot"></i> ¡Estoy trabajando! Espera...</div><div id="working-modal-desc"><i class="fas fa-sync fa-spin fa-4x fa-fw"></i></div></div>');	
}
	
