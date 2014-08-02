(function () {

  var socket = require('../controllers/socket_controller');
  var location = require('../modules/location');

  function messageCallback (data) {
    var msg = JSON.parse(data);
    document.getElementById('message-container').innerHTML += '<p><span>' +
      msg.name + '&nbsp;&nbsp;' + msg.time + 
      ':</span><br><span>' +
      msg.content +
      '</span></p>';
  }

  window.onload = function () {
    socket.startServer(messageCallback);
    location.getLocationByIp(function (data) {
      console.log(data);
    });
  }

})();