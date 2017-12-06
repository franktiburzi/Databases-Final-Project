
chrome.browserAction.onClicked.addListener( function(tab) {
	chrome.tabs.getSelected(null,function(tab) {
    var tablink = tab.url;
			chrome.windows.create({ url: 'http://localhost/CMSC424/Insert/browserInsert.php?var='
			+ encodeURIComponent(tablink), type: 	"popup" });
		});
});

//window.location.href = 'http://localhost/CMSC424/Insert/browserInsert.php?var=' + encodeURIComponent('https://www.google.com.ar/#hl=es-419&q=urlencode+javascript')
