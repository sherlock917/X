var app = require('http').createServer(server);
var io = require('socket.io')(app);
var url = require('url');
var fs = require('fs');

var visitors = [];

function server (req, res) {
  var path = (url.parse(req.url).pathname == '/') 
  ? '/views/visitor.html' 
  : url.parse(req.url).pathname;
  var mime = path.split('.').pop();
  
  if (mime == 'js') {
    mime = 'text/javascript';
  } else if (mime == 'jpg' || mime == 'png') {
    mime = 'image/' + mime;
  } else {
    mime = 'text/' + mime;
  }

  fs.readFile('.' + path, function (err, data) {
    if (err) {
      res.writeHead(404);
      res.end();
    } else {
      res.writeHead(200, {'Content-Type' : mime});
      res.end(data);
    }
  });
}

function getAllVisitors () {
  var result = []
  for (var i = 0; i < visitors.length; i++) {
    result.push({name : visitors[i].name, id : visitors[i].id});
  }
  return result;
}

exports.startServer = function (messageCallback) {
  app.listen(3000);

  io.on('connection', function (socket) { 

    socket.on('disconnect', function() {
      this.broadcast.emit('user_leave', JSON.stringify({
        name : this.name,
        id : this.id
      }));
      for (var i = 0; i < visitors.length; i++) {
        if (visitors[i] == this) {
          visitors.splice(i, 1);
        }
      }
    });

    socket.on('set_name', function (name) {
      socket.name = name;
      socket.emit('id', socket.id);
      socket.emit('all_user', JSON.stringify(getAllVisitors()));
      socket.broadcast.emit('new_user', JSON.stringify({
        name : socket.name,
        id : socket.id
      }));
      visitors.push(socket);
    });

    socket.on('message', function (data) {
      socket.broadcast.emit('message', data);
      messageCallback(data);
    });

    socket.on('whisper', function (data) {
      var msg = JSON.parse(data);
      for (var i = 0; i < visitors.length; i++) {
        if (msg.target == visitors[i].id) {
          visitors[i].emit('whisper', data);
          break;
        }
      }
    })

  });
}