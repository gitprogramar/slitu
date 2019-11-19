chrome.runtime.onInstalled.addListener(function() {
  chrome.declarativeContent.onPageChanged.removeRules(undefined, function() {
    chrome.declarativeContent.onPageChanged.addRules([{
      conditions: [new chrome.declarativeContent.PageStateMatcher({
        pageUrl: { hostEquals: 'www.instagram.com', schemes: ['https'] }
      }), new chrome.declarativeContent.PageStateMatcher({
        pageUrl: { hostEquals: 'www.facebook.com', schemes: ['https'] }
      }), new chrome.declarativeContent.PageStateMatcher({
        pageUrl: { hostEquals: 'twitter.com', schemes: ['https'] }
      })],
      actions: [new chrome.declarativeContent.ShowPageAction()]
    }]);
  });
}); 