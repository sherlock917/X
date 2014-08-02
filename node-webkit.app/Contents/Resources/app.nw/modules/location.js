var request = require('request');

exports.getLocationByIp = function () {
  request.get('http://api.map.baidu.com/location/ip?ak=wjXSmojTqgXzEM6qLNBxjBjD', function (error, response, body) {
    if (!error && response.statusCode == 200) {
      return body;
    }
  });
}