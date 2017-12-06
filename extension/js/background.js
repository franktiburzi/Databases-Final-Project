
chrome.browserAction.onClicked.addListener( function(tab) {

	chrome.windows.create({ url: "http://localhost/CMSC424/Insert/insert.php", type: 	"popup" });

});
