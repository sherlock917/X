(function () {

  var exec = require('child_process').exec;

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

  function drawAddress () {
    exec('ifconfig', function (error, stdout, stderr) {
      if (error) {
        console.log(error);
      } else {
        if (stdout.match(/(192.168.[0-9]*.[0-9]*)/)) {
          var url = 'http://' + stdout.match(/(192.168.[0-9]*.[0-9]*)/).pop() + ':3000';
          new QRCode(document.getElementById("qrcode"), url);
        }
      }
    });
  }

  window.onload = function () {
    socket.startServer(messageCallback);
    drawAddress();
  }

})();