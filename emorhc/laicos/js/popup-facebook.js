popupFacebook = {};
popupFacebook.init = function() {
	chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {		
		chrome.tabs.sendMessage(tabs[0].id, {settings: {actions: ['facebook', 'addHtml']}});		
	});
	document.getElementById('facebookRemoveDesc').innerText = chrome.i18n.getMessage("comming_soon");	
};