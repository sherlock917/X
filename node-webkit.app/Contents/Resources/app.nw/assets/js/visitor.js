(function () {

  var socket;

  var _id = '';
  var _name = '';

  // dom event handlers
  document.getElementById('visitor-login').onclick = function () {
    var value = document.getElementById('visitor-name').value;
    if (value != '') {
      socket = io('http://0.0.0.0:3000');

      // socket event handlers
      socket.on('connect', function () {
        socket.emit('set_name', value);
      });

      socket.on('id', idHandler);
      socket.on('message', massageHandler);
      socket.on('new_user', newUserHandler);
    }
  }

  document.getElementById('visitor-submit').onclick = function () {
    var name = document.getElementById('visitor-name').value;
    var content = document.getElementById('visitor-input').value;
    var msg = JSON.stringify({
      name : name,
      content : content,
      time : Tool.getFormattedTime()
    });
    selfMassageHandler(msg)
    socket.emit('message', msg);
    return false;
  }

  // functions
  function idHandler (id) {
    _id = id;
  }

  function massageHandler (data) {
    var msg = JSON.parse(data);
    document.getElementById('message-container').innerHTML += '<p><span>'
      + msg.name + '&nbsp;&nbsp;' + msg.time
      + '</span><br><span>'
      + msg.content
      + '</span></p>';
  }

  function selfMassageHandler (data) {
    var msg = JSON.parse(data);
    document.getElementById('message-container').innerHTML += '<p><span>'
      + msg.name + '&nbsp;&nbsp;' + msg.time
      + '</span><br><span>'
      + msg.content
      + '</span></p>';
  }

  function newUserHandler (data) {
    var user = JSON.parse(data);
    console.log(user.name);
    console.log(user.id);
  }

})();