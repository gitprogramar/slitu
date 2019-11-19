window.onload = function() { 		
	/*
	var json ={
		action: "generateToken",
		date: "2019-02-10"
	};
	program.post("/api/product/post.php", json, function(response) {
		console.log(response.value);
	});

	var json ={
			action: "generateToken",
			date: "2018-10-27"
		};
		program.post("/api/product/post.php", json, function(response) {
			console.log(response.value);
		});

	93D074628-3190E3648-91D0A5B14
	*/

	program.get("https://nubsant.com/api/product/get.php?token="+localStorage.getItem("nubsant-token"), function(response) {
		if(response.value) {
			document.getElementById('version-type').innerHTML = chrome.i18n.getMessage("version_type_pro");
			sessionStorage.removeItem('kaailkt');
		}
		else {
			document.getElementById('version-type').innerHTML = chrome.i18n.getMessage("version_upgrade");
			document.getElementById('version-change-to-pro').innerText = chrome.i18n.getMessage("version_upgrade_request");
			document.getElementById('version-change-to-pro').setAttribute('href', chrome.i18n.getMessage("contact_url")+'?m='+chrome.i18n.getMessage("version_upgrade_message"));
			document.getElementById('code-btn').value = chrome.i18n.getMessage("code_btn");
			document.getElementById('code').setAttribute('placeholder', chrome.i18n.getMessage("code_title"));
			document.getElementById('code-area').classList.remove('n-display');
			sessionStorage.setItem('kaailkt', Math.floor(Math.random() * 4796));
		} 
	}, undefined, 4000, function() { 
		program.notify(chrome.i18n.getMessage("error_title"), chrome.i18n.getMessage("error_desc"), 'fail', 10000); 
	});
	
	document.getElementById('name').innerText = chrome.runtime.getManifest().name;
	document.getElementById('version').innerText = 'v'+chrome.runtime.getManifest().version;	
	document.querySelectorAll('.tooltip').forEach(function(item) {
		item.setAttribute('data-tooltip', chrome.i18n.getMessage("help"));
	});
	document.getElementById('report-title').innerText = chrome.i18n.getMessage("report_title");
	document.getElementById('report-desc').innerText = chrome.i18n.getMessage("report_desc");
	document.getElementById('report-desc').setAttribute('href', chrome.i18n.getMessage('contact_url')+'?m='+chrome.i18n.getMessage("report_message"));
	document.getElementById('report-before').innerText = chrome.i18n.getMessage("report_before");
	document.getElementById('report-alert').innerText = chrome.i18n.getMessage("report_alert");
	document.getElementById('report-alert').addEventListener('click', function() {
		program.notify(chrome.i18n.getMessage('report_title'), chrome.i18n.getMessage('error_desc'), 'warning', 14000);	
	});
	document.getElementById('terms-text').innerText = chrome.i18n.getMessage("terms_text");
	document.getElementById('terms-text').setAttribute('href', chrome.i18n.getMessage("terms_link"));
	
    program.buttonBind();
    /*year*/
    var year = document.querySelector("#copyright #year");
    if (year != undefined) {
        year.innerHTML = new Date().getFullYear();    
    }    
	
	program.slider.init('slider1', null);	
	program.animate('#logo', 'translate-left', 5000);
	program.animate('#title', 'translate-right', 5000);
	program.animate('#copyright', 'translate-bottom', 7000);
	window.setTimeout(function() {
		program.animate('.tooltip svg', 'zoom-in-diagonal', 7000);
	}, 2000);
	
	popupInstagram.init();
	popupFacebook.init();
	popupTwitter.init();
	
	/*
	chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {		  
		if(tabs[0].url.indexOf('instagram.com') != -1) {
			instagram  
		}
		else if(tabs[0].url.indexOf('facebook.com') != -1) {
			facebook
		}
		else if(tabs[0].url.indexOf('twitter.com') != -1) {
			twitter 	
		}
	});
	*/
};

document.getElementById('code-btn').onclick = function() {
	localStorage.setItem("nubsant-token", document.getElementById('code').value);
	program.get("https://nubsant.com/api/product/get.php?token="+localStorage.getItem("nubsant-token"), function(response) {
		if(response.value) {
			document.getElementById('version-type').innerHTML = chrome.i18n.getMessage("version_type_pro");
			document.getElementById('version-change-to-pro').classList.add('n-display');
			document.getElementById('code-area').classList.add('n-display');
			sessionStorage.removeItem('kaailkt');
			chrome.tabs.query({active: true, currentWindow: true}, function(tabs) {				
				chrome.tabs.sendMessage(tabs[0].id, {settings: {actions: ['instagram', 'refresh']}}, function(response) {
					console.log('page refresh');			
				});
			});	
		}
		else {
			program.notify(chrome.i18n.getMessage("invalid_code"), chrome.i18n.getMessage("invalid_code_desc"), 'fail', 7000);
			sessionStorage.setItem('kaailkt', Math.floor(Math.random() * 7352));			
		} 
	});
};
