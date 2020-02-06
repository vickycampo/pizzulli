var localURL = window.location.href;
var method = "GET";
var url = "GetInfo.php";
var body = "";

createRequestObj();
configureRequesObj( method , url , body , "getInfoReturn ( jsonObj )");

function getInfoReturn ( jsonObj )
{
     console.log( jsonObj );
}
