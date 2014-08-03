(function () {

  var request = require('request');
  var exec = require('child_process').exec;

  var gui = require('nw.gui');
  var win = gui.Window.get();

  var socket = require('../controllers/socket_controller');
  var location = require('../modules/location');

  function messageCallback (data) {
    var msg = JSON.parse(data);
    document.getElementById('message-container').innerHTML += '<p><span>' +
      msg.name + '&nbsp;&nbsp;' + msg.time + 
      ':</span><br><span>' +
      msg.content +
      '</span></p>';

    request.get('http://192.168.199.172:8888/index.php/messages/save?name=' + msg.name + '&messagetext=' + msg.content, function (error, response, body) {
      if (!error && response.statusCode == 200) {
        
      } else if (error) {
        console.log(error);
      }
    });
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

// not my shit
var canvasWrap = document.getElementById("canvasWrap");
var canvas = document.getElementById("canvas");
var context = canvas.getContext("2d");
var canvasLeft = parseInt(canvas.style.left);
var canvasTop = parseInt(canvas.style.top);
var backX;
var backY;

function initCanvas(){
  
  canvas.width = 1000;
  canvas.height = 1000;

}
// not my shit end ...

  function touchStartCallback (data) {
    var pos = JSON.parse(data);
    backX = pos.backX;
    backY = pos.backY;
  }

  function touchMoveCallback (data) {
    var pos = JSON.parse(data);
    context.save();
    context.strokeStyle = "rgb(0,0,0)";
    context.moveTo(backX,backY);
    context.lineTo(pos.lineX,pos.lineY);
    context.stroke();
    context.restore();
    backX = pos.backX;
    backY = pos.backY;
  }

  window.onload = function () {
    socket.startServer(messageCallback, touchStartCallback, touchMoveCallback);
    drawAddress();
  }

})();