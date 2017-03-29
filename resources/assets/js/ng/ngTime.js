angular.module('ngTime', ['ngTime.services']);

angular.module('ngTime.services', [])
.factory('timeService', ['$interval', function($interval) {
    var now = new Date();

    $interval(function() {
        now = new Date();
    }, 30000);

    return {
        diff: function(time) {
            var timediff = now - new Date(time);
            if( timediff > 24*60*60*1000 ){
                return Math.floor(timediff/24/60/60/1000)+'天前';
            }else
            if( timediff > 60*60*1000 ){
                return Math.floor(timediff/60/60/1000)+'小時前';
            }else
            if( timediff > 60*1000 ){
                return Math.floor(timediff/60/1000)+'分鐘前';
            }else{
                return Math.floor(timediff/1000)+'秒前';
            }
        }
    };

}]);