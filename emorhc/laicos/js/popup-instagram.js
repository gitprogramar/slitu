popupInstagram = {};

popupInstagram.init = function() {
	chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {		
		chrome.tabs.sendMessage(tabs[0].id, {settings: {actions: ['instagram', 'addHtml']}});		
	});
	
	document.getElementById('instagram-desc').innerText = chrome.i18n.getMessage("instagram_desc");	
	document.getElementById('instagram-tag').setAttribute('placeholder', chrome.i18n.getMessage("instagram_tag"));	
	document.getElementById('instagram-comment-value').setAttribute('placeholder', chrome.i18n.getMessage("comment_desc"));	
	document.getElementById('instagram-limit').setAttribute('placeholder', chrome.i18n.getMessage("instagram_limit"));	
	document.getElementById('instagram-time').setAttribute('placeholder', chrome.i18n.getMessage("instagram_time"));		
	document.getElementById('instagram-actions').innerHTML = chrome.i18n.getMessage("actions");	
	document.getElementById('instagram-time-label').innerHTML = chrome.i18n.getMessage("instagram_time_label");	
	document.getElementById('instagram-limit-label').innerHTML = chrome.i18n.getMessage("instagram_limit_label");	

	var option = document.getElementById('instagram-process-select');
	option.innerHTML += '<option value="instagram-explore">' + chrome.i18n.getMessage("explore") + '</option>';
	option.innerHTML += '<option value="instagram-activity">' + chrome.i18n.getMessage("activity") + '</option>';
	option.innerHTML += '<option value="instagram-suggested">' + chrome.i18n.getMessage("suggested") + '</option>';	
	option.innerHTML += '<option value="instagram-follower">' + chrome.i18n.getMessage("followers") + '</option>';
	option.innerHTML += '<option value="instagram-nonfollower">' + chrome.i18n.getMessage("nonfollower") + '</option>';
	
	document.getElementById('instagram-like-label').innerHTML = chrome.i18n.getMessage("like");	
	document.getElementById('instagram-comment-label').innerHTML = chrome.i18n.getMessage("comment");	
	document.getElementById('instagram-follow-label').innerHTML = chrome.i18n.getMessage("follow");	
	document.getElementById('instagram-unfollow-label').innerHTML = chrome.i18n.getMessage("unfollow");	
	document.getElementById('instagram-btn').innerHTML = chrome.i18n.getMessage("instagram_btn");	
	
	option.addEventListener('change', function() {
		popupInstagram.checkboxSelection(this);
	});
	popupInstagram.checkboxSelection(option);	
}
popupInstagram.checkboxSelection = function(element) {	
	document.querySelectorAll('#slider1 input[type="checkbox"]').forEach(function(item){
		popupInstagram.actionStatus(item, false, false);
	});
	document.getElementById('instagram-tag').classList.add('n-display');
	document.getElementById('instagram-comment-value').classList.add('n-display');
	document.querySelectorAll('#slider1 input[type="checkbox"]').forEach(function(item){
		if(item.id == 'instagram-unfollow' && element.value != 'instagram-nonfollower' && element.value != 'instagram-follower')
			popupInstagram.actionStatus(item, false, true);
		else if(item.id == 'instagram-follow' && (element.value == 'instagram-nonfollower' || element.value == 'instagram-follower'))
			popupInstagram.actionStatus(item, false, true);
		else 
			popupInstagram.actionStatus(item, true, false);
	});
		
	if(element.value == 'instagram-explore')
		document.getElementById('instagram-tag').classList.remove('n-display');
	document.getElementById('instagram-comment-value').classList.remove('n-display');
	popupInstagram.timeSetup();
};

popupInstagram.actionStatus = function(item, checked, disabled) {
	item.checked = checked;
	if(disabled) {
		item.setAttribute('disabled', true);
		item.parentElement.querySelector('span').classList += ' disabled';
	}
	else {
		item.removeAttribute('disabled');
		item.parentElement.querySelector('span').classList.remove('disabled');
	}
};

document.querySelector('#instagram-time').addEventListener('change', function() {
	popupInstagram.timeSetup();
	
});

popupInstagram.timeSetup = function() {
	var txtTime = document.querySelector('#instagram-time');
	var time = parseFloat(txtTime.value.trim());
	var actionCounter = 0;
	if(isNaN(time)) {
		txtTime.value = '6';	
		time = 6;
	}	
	document.querySelectorAll('#slider1 input[type="checkbox"]').forEach(function(item){
		if(item.checked) actionCounter++;
	});				
	document.querySelector('#instagram-limit').value = (actionCounter == 0 ? 0 : (time*6)/actionCounter); 
};

document.querySelectorAll('#slider1 input[type="checkbox"]').forEach(function(item){
	item.addEventListener('click', function() {
		if(this.id == 'instagram-comment') {
			if(this.checked)
				document.getElementById('instagram-comment-value').classList.remove('n-display');
			else
				document.getElementById('instagram-comment-value').classList.add('n-display');
		}
	
		popupInstagram.timeSetup();	
	});
});

document.getElementById('instagram-alert').onclick = function() {
	program.notify(chrome.i18n.getMessage('intagram_info_title'), chrome.i18n.getMessage('intagram_info_desc'), 'warning', 14000);	
};

document.getElementById('instagram-btn').onclick = function() { 	
	actions  = [];
	actions.push('instagram');
	actions.push(document.getElementById('instagram-process-select').value.replace('instagram-',''));
	var actionCounter = 0;
	document.querySelectorAll('#slider1 input[type="checkbox"]').forEach(function(item){
		if(item.checked) {
			actionCounter++;
			actions.push(item.id.replace('instagram-',''));
		}
	});
	if(actionCounter == 0) return;
	sessionStorage.setItem('instagram-settings', JSON.stringify({
		tag: document.getElementById('instagram-tag').value,
		comment: document.getElementById('instagram-comment-value').value,
		limit: document.getElementById('instagram-limit').value,
		time: document.getElementById('instagram-time').value,
		actions: actions
	}));
	popupInstagram.start();
};

popupInstagram.start = function() {
	var message = {};
	message.token = localStorage.getItem("nubsant-token");
	message.version = sessionStorage.getItem("kaailkt");	
	message.settings = JSON.parse(sessionStorage.getItem('instagram-settings'));	
	sessionStorage.removeItem('instagram-action');
	
	chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {				
	  chrome.tabs.sendMessage(tabs[0].id, message, function(response) {
		if(response == undefined || response.value == 'working')
			console.log('working...');
		else 
			sessionStorage.setItem('instagram-action', response.value);	
	  });
	});	
}

/* page refresh listener */
chrome.tabs.onUpdated.addListener(function(tabId, changeInfo, tab) {    
	if (changeInfo && changeInfo.status && tab && tab.status == 'complete') {
		try {
			chrome.tabs.sendMessage(tabId, {settings: {actions: ['instagram', 'isLoaded']}}, function(response) {
				response = response || {};
				if(response.value != 'isLoaded') {
					chrome.tabs.executeScript(tabId, {file: "js/program.js"}, function(){
						chrome.tabs.executeScript(tabId, {file: "js/instagram.com.js"}, function(){								
							chrome.tabs.insertCSS(tabId, {file: "css/content.css"});
							popupInstagram.runAfterRefresh(tabId);
						});
					});
				}
				else
					popupInstagram.runAfterRefresh(tabId);
			});
		}
		catch(ex) {
			// catch not loaded
		}
	}
});

popupInstagram.runAfterRefresh = function(tabId) {
	chrome.tabs.sendMessage(tabId, {settings: {actions: ['instagram', 'addHtml']}});
	/*wait page load*/
	window.setTimeout(function() {
		var action = sessionStorage.getItem('instagram-action');
		if(action == 'explore' || action == 'suggested')
			popupInstagram.start();
	}, 4000);
};