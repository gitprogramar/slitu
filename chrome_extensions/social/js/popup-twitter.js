popupTwitter = {};
popupTwitter.init = function() {
	chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {		
		chrome.tabs.sendMessage(tabs[0].id, {settings: {actions: ['tiwtter', 'addHtml']}});		
	});
	document.getElementById('twitterRemoveDesc').innerText = chrome.i18n.getMessage("comming_soon");
};