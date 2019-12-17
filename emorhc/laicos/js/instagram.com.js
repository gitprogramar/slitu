nbInstagram = {};
nbInstagram.UI = {};
nbInstagram.UI.explore = {};
nbInstagram.BKG = {};
nbInstagram.API = {};
nbInstagram.COMMON = {};
nbInstagram.counter = 0;
nbInstagram.limit = 2;
nbInstagram.time = 1;
nbInstagram.version = '3458';

nbInstagram.followers = [];
nbInstagram.blocked = [];

/* send */
/*chrome.runtime.sendMessage(id,{greeting: "hello"}, function(response) {
	//profileElement.click();
});
*/

/* recive */
chrome.runtime.onMessage.addListener(function(message, sender, callback) {
	var response = {};
	var actions = message.settings.actions;
	var settings = message.settings;
	if(!actions.includes('instagram') || location.host.indexOf('instagram.com') == -1)
		return;	
	else if(actions.includes('isLoaded'))
		response.value = 'isLoaded'; /*check content script is loaded*/
	else if(actions.includes('addHtml'))
		program.addHtml(); /*first load */
	else if(actions.includes('refresh'))
		location.reload();			
	else if(!program.isWorking()) {
		nbInstagram.version = message.version;		
		if(settings.limit.trim() != '' && !isNaN(settings.limit) && (nbInstagram.version == null || program.validate(message.token))) {
			nbInstagram.limit = settings.limit;
			if(settings.time.trim() != '' && !isNaN(settings.time))
				nbInstagram.time = settings.time;
		}
		else {
			nbInstagram.limit = 2;
			nbInstagram.time = 0.1;
		}		
		
		nbInstagram.time = nbInstagram.time/(nbInstagram.limit*(actions.length-2))*60*60*1000; 
		
		if(actions.includes('explore')) {
			response.value = 'explore';
			nbInstagram.UI.explore.start(message);
		}
		else if(actions.includes('activity'))
			nbInstagram.BKG.activity(message);			
		else if(actions.includes('suggested')) {
			response.value = 'suggested';
			nbInstagram.UI.suggested(message, true);
		}
		else if(actions.includes('follower'))	
			nbInstagram.BKG.follower(message);	
		else if(actions.includes('nonfollower'))	
			nbInstagram.BKG.nonfollower(message);	
	}
	if(response.value == undefined)
		response.value = 'working';
	callback(response);
	return true;  /* Will respond asynchronously */
});

/*UI explore*/
nbInstagram.UI.explore.start = function(data) {	
	var searchUrl = '/explore/' + (data.settings.tag.trim() == '' ? '' : 'tags/' + data.settings.tag.replace(/ /g,''));
	if(location.href.indexOf(searchUrl) == -1) {
		location.href = searchUrl;
		return;
	}		
	data = nbInstagram.COMMON.start(data);
	
	setTimeout(function() {
		var posts = document.querySelectorAll('a[href*="/p/"]');
		if (posts.length <= 1) {
			program.finish("robot_finished", nbInstagram.version);
			program.endWatch('Total time:');
			return;
		}		
		posts[0].click();
			
		setTimeout(function() {
			document.querySelector('#working-modal-unfollow').style.display = 'none';
			document.querySelector('#working-modal-summary').style.zIndex = 1;
			nbInstagram.UI.explore.next(data);			
		}, 6000, data);	
	}, 5000);
	
};
nbInstagram.UI.explore.next = function self(data) {	
	nbInstagram.API.graphql(location.pathname).then(function(graphql) {
		if(typeof(graphql) == 'undefined') {
			console.log('attemping to continue...');
			nbInstagram.UI.explore.continue(data, self);
			return;
		}
		
		data.media = {};
		data.media.id = graphql.shortcode_media.id;		
		data.media.shortcode = graphql.shortcode_media.shortcode;
		data.user = graphql.shortcode_media.owner;
		
		if(program.robotDuplicate(data.user.username)) {
			console.log('duplicate: ' + data.user.username);
			nbInstagram.UI.explore.continue(data, self);
			return;
		}
		
		nbInstagram.COMMON.recursiveAction(0, data).then(function() {
			nbInstagram.counter++;
			nbInstagram.UI.explore.continue(data, self);
		});		
	});			
};
nbInstagram.UI.explore.continue = function(data, callback) {
	var blocked = false;
	document.querySelectorAll('h3').forEach(function(item) {
		if(item.innerText == chrome.i18n.getMessage("blocked")) blocked = true;
	});
	var nextBtn = document.querySelector('a[class*="coreSpriteRightPaginationArrow"]');	
	if(blocked || nextBtn == null || nbInstagram.counter >= nbInstagram.limit) {
		program.finish("robot_finished", nbInstagram.version);
		program.endWatch('Total time:');		
		if(blocked) alert(chrome.i18n.getMessage("blocked_recommend"));
		return;
	}
	nextBtn.click();
	setTimeout(function() {
		callback(data);
	}, 6000);
};
/*BKG (background) activity*/
nbInstagram.BKG.activity = function(data) {
	data = nbInstagram.COMMON.start(data);
	
	nbInstagram.API.graphql('/accounts/activity/').then(async function(graphql_activity) {		
		var users = [];
		graphql_activity.user.activity_feed.edge_web_activity_feed.edges.forEach(function(item) {
			users.push(item.node.user);
		});
		nbInstagram.COMMON.actionsByUsers(data, users);
	});
};
/*UI suggested*/
nbInstagram.UI.suggested = function self(data, init) {		
	if(init) {
		var searchUrl = '/explore/people/suggested/';	
		if(location.href.indexOf(searchUrl) == -1) {		
			location.href = searchUrl;
			return;
		}	
		data = nbInstagram.COMMON.start(data);	
	}
	
	var links = document.querySelectorAll('a');
	var users = [];
	links.forEach(function(item) {
		if(item.title != undefined && item.title != '') {
			var user = {};
			user.username = item.title;
			users.push(user);
		}
	});
	
	if(users.length == 0)
	{			
		program.finish("robot_finished", nbInstagram.version);
		program.endWatch('Total time:');
		return;
	}
	else if(users.length >= nbInstagram.limit) {
		/*we have enough users to work on*/
		nbInstagram.COMMON.actionsByUsers(data, users);
		return;		
	}
	else {
		window.scrollTo(0,document.body.scrollHeight);		
		setTimeout(function() {
			if((window.innerHeight + window.pageYOffset) >= document.body.scrollHeight) {
				/* all suggested users listed */
				nbInstagram.COMMON.actionsByUsers(data, users);
				return;
			}
			index++;
			self(data, false);
		}, 6000, users);
	}
};
/*BKG (background) followers*/
nbInstagram.BKG.follower = async function(data) {	
	data = nbInstagram.COMMON.start(data);	
	nbInstagram.API.accounts('followers').then(function(response) {				
		var users = [];
		response.forEach(function(value) {					
			var user = {};
			user.username = value;
			users.push({username: value});
		});
		if(users.length == 0) {
			program.finish("robot_finished", nbInstagram.version);
			program.endWatch('Total time:');
			return;						
		}
		nbInstagram.COMMON.actionsByUsers(data, users);
		return;		
	});
};
/*BKG (background) non followers*/
nbInstagram.BKG.nonfollower = async function(data) {	
	data = nbInstagram.COMMON.start(data);
	nbInstagram.API.accounts('followers').then(function(followers) {
		nbInstagram.followers = followers;
		nbInstagram.API.accounts('following').then(function(following) {				
			var nonfollowers = following.diff(nbInstagram.followers).reverse();								
			var users = [];
			nonfollowers.forEach(function(value) {					
				var user = {};
				user.username = value;
				users.push({username: value});
			});
			if(users.length == 0) {
				program.finish("robot_finished", nbInstagram.version);
				program.endWatch('Total time:');
				return;						
			}
			nbInstagram.COMMON.actionsByUsers(data, users);
			return;				
		});
	});
};
/*API*/
nbInstagram.API.graphql = async function self(value) {
	return fetch(value+'?__a=1') 
	.then(function(response) {
		if(!response.ok) throw new Error(nbInstagram.COMMON.error(response));
		return response.json();
	})
	.then(function(json) {		
		return json.graphql;		
	})
	.catch((error) => {
		console.log(error);
	});
};
nbInstagram.API.action = async function self(action, data) {
	var api = '/web/';	
	var options = {};
	var headers = new Headers();
	headers.append('X-CSRFToken', program.Cookies.get('csrftoken'));
	options.headers = headers;
	options.method = 'POST';
	var info = '';
		
	if(action == 'like') {
		api += 'likes/'+data.media.id+'/like/';
		info = '<a href="/p/'+data.media.shortcode+'" target="_blank">'+data.user.username+'</span>';
		if(data.media.id == undefined) api = '';		
	}
	else if(action == 'comment') {		
		api += 'comments/'+data.media.id+'/add/';
		var comment = data.settings.comment;
		if(comment == undefined || comment.trim() == '')
			comment = chrome.i18n.getMessage("comment_" + (Math.floor(Math.random() * (15 - 1)) + 1));
		var formData  = new FormData();
		formData.append('comment_text', '@' + data.user.username + ' ' + program.htmlentities.decode(comment));
		options.body = formData;
		info = '<a href="/p/'+data.media.shortcode+'" target="_blank">'+data.user.username+'</span>';
		if(data.media.id == undefined || data.media.comments_disabled /*|| user.is_private*/) api = '';		
	}
	else if(action == 'follow') {
		api += 'friendships/'+data.user.id+'/follow/';
		info = '<a href="/'+data.user.username+'" target="_blank">'+data.user.username+'</span>';
		if(data.user.followed_by_viewer) api = '';
	}
	else if(action == 'unfollow') {
		api += 'friendships/'+data.user.id+'/unfollow/';
		info = '<a href="/'+data.user.username+'" target="_blank">'+data.user.username+'</span>';
		if(!data.user.followed_by_viewer) api = '';
	}
	
	if(api == '' || data.user.has_blocked_viewer || data.user.country_block || nbInstagram.blocked.includes(action)) return;
	
	return fetch(api, options) 		 
	.then(function(response) {
		if(response.url == 'https://www.instagram.com/') {
			/*possible blocked account or specific action*/
			if(!nbInstagram.blocked.includes(action))
				nbInstagram.blocked.push(action);			
			if(nbInstagram.blocked.length == data.settings.actions.length)
				alert(chrome.i18n.getMessage("blocked_recommend"));	
			return;
		}
		if(!response.ok) throw new Error(nbInstagram.COMMON.error(response));
		program.robotInfo(action, info);
		return;
	})
	.catch((error) => {
		console.log(error);
	});
};
nbInstagram.API.accounts = async function self(action, data, page) {
	var api = '/accounts/';
	if(action == 'followers')
		api += 'access_tool/accounts_following_you';
	else if(action == 'following')
		api += 'access_tool/accounts_you_follow';
	else if(action == 'pending')
		api += 'access_tool/current_follow_requests';	
	
	if (typeof(data)=="undefined")
		data = [];
	return fetch(api + '?__a=1'+(typeof(page)!='undefined' ? '&cursor='+page : '')) 
	.then(function(response) {
		if(!response.ok) {
			console.log('nbInstagram.API.accounts fetch ' + response);
			return;
		}
		return response.json();
	})
	.then(function(json) {				
		json.data.data.forEach(function(item){
			data.push(item.text);
		});
		if(json.data.cursor != null)
			return self(action, data, json.data.cursor);
		console.log(action + ' users finished');
		return data;
	})
	.catch((error) => {
		console.log('nbInstagram.API.accounts catch ' + error);
	});
};
/*COMMON*/
nbInstagram.COMMON.start = function(data) {
	program.startWatch();		
	program.robot();			
	data.settings.actions.shift();	
	data.settings.actions.shift();	
	return data;
};
nbInstagram.COMMON.actionsByUsers = async function self(data, users) {
	for(var user of users) {
		var blocked = false;
		document.querySelectorAll('h3').forEach(function(item) {
			if(item.innerText == chrome.i18n.getMessage("blocked")) blocked = true;
		});			
		if(blocked || nbInstagram.counter >= nbInstagram.limit) {
			program.finish("robot_finished", nbInstagram.version);
			program.endWatch('Total time:');		
			if(blocked) alert(chrome.i18n.getMessage("blocked_recommend"));
			return;
		}
		
		await nbInstagram.API.graphql('/'+user.username+'/').then(async function(graphql) {
			if(typeof(graphql) == 'undefined') {
				console.log('attemping to continue...');				
				await nbInstagram.COMMON.sleep();				
				return;
			}

			data.user =	graphql.user;
			data.media = {};
			var hasMedia = false;
			graphql.user.edge_owner_to_timeline_media.edges.forEach(function(media){
				if(!media.node.comments_disabled && !hasMedia) {						
					data.media = media.node;						
					hasMedia = true;
				}
			});
			await nbInstagram.COMMON.recursiveAction(0, data).then(function() {
				nbInstagram.counter++;
			});
		});			
	};
};
nbInstagram.COMMON.recursiveAction = async function self(index, data) {
	var action = data.settings.actions[index];
	if(action == undefined)
		return;
	await nbInstagram.API.action(action, data).then(async function() {
		await nbInstagram.COMMON.sleep();
		index++;
		return self(index, data);
	});
};
nbInstagram.COMMON.sleep = function () {
    return new Promise(resolve => setTimeout(resolve, nbInstagram.time));
}	
nbInstagram.COMMON.error = function (response) {
	var error = '\n';
	error += 'Status: ' + response.status + '\n';
	error += 'Text: ' + response.statusText + '\n';
	error += 'Url: ' + response.url;
	return error;
}

/*OLD: User interface behaviour*/
/*
nbInstagram.nonFollowers = [];
nbInstagram.unFollowerBtns = [];

nbInstagram.UI.explore.like = function() {
    var like = Math.floor(Math.random() * 2);
    if (like == 1 && nbInstagram.likeCounter < nbInstagram.limit) {
        var btn = document.querySelector('span[aria-label="'+chrome.i18n.getMessage("like")+'"');
		if(btn != null){
			btn.click();
			nbInstagram.likeCounter++;
			document.getElementById('like-counter').innerText = nbInstagram.likeCounter;
			var account = document.querySelectorAll('header h2')[0].innerText;
			program.robotInfo('like', '<a href="/'+account+'" target="_blank">'+account+'</span>');
		}	
    }
    setTimeout(function() {
		var blocked = false;
		document.querySelectorAll('h3').forEach(function(item) {
			if(item.innerText == chrome.i18n.getMessage("blocked")) blocked = true;
		});
        if (!blocked & (nbInstagram.followCounter < nbInstagram.limit
			|| nbInstagram.likeCounter < nbInstagram.limit)) {
            nbInstagram.nextUI();
        } else {			
			document.querySelectorAll('div[role="dialog"] button').forEach(function(item) {
				if(item.innerHTML == chrome.i18n.getMessage("close"))
					item.click();
			});						
			program.finish("robot_finished", nbInstagram.version);
			program.endWatch('Total time:');
			if(blocked)
				alert(chrome.i18n.getMessage("blocked"));
        }
    }, program.getRandomMillSeconds(6, 9));
};
nbInstagram.UI.explore.follow = function() {
    var follow = Math.floor(Math.random() * 2);
    if (follow == 1 && nbInstagram.followCounter < nbInstagram.limit) {
        var btn = document.querySelector('div[role="dialog"] button');
        if (btn != null && btn.textContent == chrome.i18n.getMessage("follow")) {
            btn.click();
            nbInstagram.followCounter++;	
			document.getElementById('follow-counter').innerText = nbInstagram.followCounter;
			var account = document.querySelectorAll('header h2')[0].innerText;
			program.robotInfo('follow', '<a href="/'+account+'" target="_blank">'+account+'</span>');
        }
    }
    setTimeout(function() {
        nbInstagram.likeUI();
    }, program.getRandomMillSeconds(6, 9));
};

nbInstagram.startUnfollow = function(data, callback) {
	var response = {};
	var profileElement = document.querySelector('span[aria-label="' + chrome.i18n.getMessage("profile") + '"]').parentElement;
	if(program.isWorking()){
		response.value = true;
	}
	else if(profileElement.href != window.location.href) {		
		response.value = 'fromUnfollow';
		profileElement.click();
	}
	else {
		var modalTitle = '<i class="fas fa-robot"></i> '+chrome.i18n.getMessage("robot_working");
		//modalTitle += ' <span id="working-stop" onclick="alert(\'ok\')" class="btn btn-transparent" style="font-size: .3em;margin-left: 2%;">';
		//modalTitle += chrome.i18n.getMessage("robot_stop")+'</span>';
		var modalBody = '<div id="working-modal-loading" class="row-center"><i class="fas fa-sync fa-spin fa-2x fa-fw"></i>';
		modalBody += '</div><div id="working-modal-summary" style="margin-bottom: 5%;"></div>';
		modalBody += '<div id="robot-stop" style="font-size: .8em;">*'+chrome.i18n.getMessage("robot_stop")+'</div>';
		modalBody += '<div id="robot-upgrade"><p>'+chrome.i18n.getMessage("version_upgrade")+'</p>';
		modalBody += '<a href="'+chrome.i18n.getMessage("version_upgrade_url")+'?m='+chrome.i18n.getMessage("version_upgrade_message");
		modalBody += '" target="_blank">'+chrome.i18n.getMessage("version_upgrade_request")+'</a> </div>';
		program.modalShow('working-modal', modalTitle, modalBody, 10);
		nbInstagram.version = data.version;
		if(data.version != null || !program.validate(data.token))
			nbInstagram.limit = 5;
		setTimeout(function() {
			nbInstagram.getFollowers();
		}, 2000);
	}
	if(response.value == undefined)
		response.value = true;
	callback(response);
};

nbInstagram.getFollowers = function() {
document.querySelector('a[href*="/followers"]').click();
    setTimeout(function() {
        var container = document.querySelector('h1 div').parentNode.parentNode.parentNode.parentNode.children[1];
        nbInstagram.scrollToEndAsync(container, 'followers');
    }, 5000);
};
nbInstagram.getFollowings = function() {
    document.querySelector('a[href*="/following"]').click();
    setTimeout(function() {
        var container = document.querySelector('h1 div').parentNode.parentNode.parentNode.parentNode.children[2];
        nbInstagram.scrollToEndAsync(container, 'followings');
    }, 5000);
};
nbInstagram.removeNonFollowers = function() {
    nbInstagram.following.forEach(function(item) {
        if (!nbInstagram.followers.includes(item)) {
            nbInstagram.nonFollowers.push(item);
        }
    });
    if (nbInstagram.nonFollowers.length == 0) {
		program.finish("robot_finished", nbInstagram.version);
		return;
	}
    var container = document.querySelector('h1 div').parentNode.parentNode.parentNode.parentNode.children[2];
    container.querySelectorAll('div a').forEach(function(item) {
        if (nbInstagram.nonFollowers.includes('/' + item.innerText + '/')) {
            nbInstagram.unFollowerBtns.push(item.parentNode.parentNode.parentNode.parentNode.querySelector('div button'));
        }
    });	
    if (nbInstagram.unFollowerBtns.length > 0) {
		console.log(nbInstagram.unFollowerBtns);
		nbInstagram.unFollowerBtns.reverse();
		console.log('reverse');
		console.log(nbInstagram.unFollowerBtns);
		nbInstagram.unFollowClick(0);
	}
};
nbInstagram.unFollowClick = function(index) {	
    nbInstagram.unFollowerBtns[index].click();	
    setTimeout(function() {
        document.querySelectorAll('button[tabindex="0"]').forEach(function(item) {
            if (item.innerText == chrome.i18n.getMessage("unfollow")) {
                item.click();
            }
        });
		var userUrl = nbInstagram.nonFollowers[index].substr(0,nbInstagram.nonFollowers[index].lastIndexOf('/'));
		console.log(userUrl);
		document.getElementById('working-modal-summary').innerHTML += '<span>'+userUrl+'</span>';
        index++;
		console.log(index);
		nbInstagram.unFollowCounter++;
        if (nbInstagram.unFollowerBtns[index] != undefined && nbInstagram.unFollowCounter < nbInstagram.limit) 
			nbInstagram.unFollowClick(index);
        else {
            var close = document.querySelector('button span[aria-label="'+chrome.i18n.getMessage("close")+'"]');          
			if(close != null)
				close.click();
            program.finish("unfollowers_remove", nbInstagram.version);			
        }
    }, program.getRandomMillSeconds(40, 60));
};
nbInstagram.scrollToEndAsync = function(container, type) {
    var currentHeight = container.firstChild.clientHeight - container.clientHeight;
    if (container.scrollTop >= currentHeight) { 
		//scrolled to the end 
        container.querySelectorAll('a').forEach(function(element) {
            if (type == 'followers') {
                if (!nbInstagram.followers.includes(element.pathname)) 
					nbInstagram.followers.push(element.pathname);
            } else if (type == 'followings') {
                if (!nbInstagram.following.includes(element.pathname)) 
					nbInstagram.following.push(element.pathname);
            }
        });
        console.log('Async scroll finished: ' + type);
        if (type == 'followers') {
            var close = document.querySelector('button span[aria-label="'+chrome.i18n.getMessage("close")+'"]');
            close.click();
            setTimeout(function() {
                nbInstagram.getFollowings();
            }, 3000);
        } else if (type == 'followings') {
            setTimeout(function() {
                nbInstagram.removeNonFollowers();
            }, 3000);
        }
        return;
    }
    container.scrollTop = currentHeight;
    setTimeout(function() {
        nbInstagram.scrollToEndAsync(container, type);
    }, 5000);
};
*/

/*OLD API calls*/
/*
nbInstagram.updateAccounts = function self(action, data, page, callback) {
	
	if(data[page] == undefined) {		
				
		program.finish("robot_finished", nbInstagram.version);
		program.endWatch('Total time:');
		return;
	}
	else if (page == 0) {
		document.querySelector('#working-modal-summary').style.zIndex = 1;
	}
	
	fetch('/'+data[page]+'/?__a=1') 
	.then(function(response) {
		return response.json();
	})
	.then(function(json) {		
		callback(action, json.graphql.user, page, self);
		return;
	})
	.catch((error) => {
		alert(error);
	});
};
nbInstagram.friendship = function self(action, data, page, callback) {
	fetch('/web/friendships/'+data.id+'/'+action+'/',
	{
		headers: {
		  'X-CSRFToken': program.Cookies.get('csrftoken')
		},
		method: "POST"
	}) 
	.then(function(response) {
		if(response.ok) {
			console.log(data.username);
			program.robotInfo(action, '<a href="/'+data.username+'" target="_blank">'+data.username+'</span>');
		}
		else
			console.log(action + ' failed for: '+data.username);
		
		if(action == 'follow') {
			nbInstagram.followCounter++;
			document.getElementById('follow-counter').innerText = nbInstagram.followCounter;
		}
		else if(action == 'unfollow') {
			nbInstagram.unFollowCounter++;
			document.getElementById('unfollow-counter').innerText = ' ('+nbInstagram.unFollowCounter+')';
		}
		
		if (nbInstagram.followCounter < nbInstagram.limit 
			|| nbInstagram.unFollowCounter < nbInstagram.limit) {
			setTimeout(callback, program.getRandomMillSeconds(60, 80), action, data, page++, self);
		}
		else {
			
			program.finish("robot_finished", nbInstagram.version);
			program.endWatch('Total time:');
			return;
		}
	})
	.catch((error) => {
		alert(error);
	});
};
nbInstagram.BKG.follower = async function(data) {	
	data = nbInstagram.COMMON.start(data);	
	nbInstagram.API.accounts('followers').then(function(response) {
		nbInstagram.followers = response;
		nbInstagram.API.accounts('pending').then(function(pending) {			
			var followers = nbInstagram.followers.diff(pending);
			if(followers.length == 0) {
				program.finish("robot_finished", nbInstagram.version);
				program.endWatch('Total time:');
				return;						
			}
			var users = [];
			followers.forEach(function(value) {					
				var user = {};
				user.username = value;
				users.push({username: value});
			});
			nbInstagram.COMMON.actionsByUsers(data, users);
			return;				
		});
		
	});
};
*/

