(function () {

  var socket = io('http://0.0.0.0:3000');

  var _id = '';
  var _name = '';

  // socket event handlers
  socket.on('connect', function () {

  });

  socket.on('id', function (id) {
    _id = id;
  });

  // dom event handlers
  document.getElementById('visitor-submit').onclick = function () {
    var name = document.getElementById('visitor-name').value;
    var content = document.getElementById('visitor-input').value;
    socket.emit('message', JSON.stringify({
      name : name,
      content : content,
      time : Tool.getFormattedTime()
    }));
    return false;
  }

})();