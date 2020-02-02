<script>

d = "09/15/2018";
unix = checkDate(d);
d1 = timeConverter(unix);
console.log(d);
console.log(new Date(d));
console.log(unix);
console.log(d1);
console.log(new Date(d1));

     
        function checkDate(str){
           if (str == parseInt(str)) {
              return str;
           }
           return parseInt(new Date(str).getTime())/1000;

           if (str.indexOf("-")==-1) {
             return str;
           } else {
             var tmp = str.split("-");
             return tmp[1]+"/"+tmp[0]+"/"+tmp[2];
           }
        }
     
        function timeConverter(UNIX_timestamp){
          var a = new Date(UNIX_timestamp * 1000);
          var months = ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'];
          var year = a.getFullYear();
          //var month = months[a.getMonth()];
          var month = a.getMonth()+1;
          var date = a.getDate();
          var hour = a.getHours();
          var min = a.getMinutes();
          var sec = a.getSeconds();
          month = month>9 ? month : '0'+month;
          date = date>9 ? date : '0'+date;
          var time =  month + '/' + date + '/' + year;
          return time;
        }
     
</script>