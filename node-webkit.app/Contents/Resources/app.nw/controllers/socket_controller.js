// // dependencies
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

exports.startServer = function (messageCallback) {
  app.listen(3000);

  io.on('connection', function (socket) { 

    visitors.push(socket);

    socket.emit('id', socket.id);

    socket.on('disconnect', function() {
      for (var i = 0; i < visitors.length; i++) {
        if (visitors[i] == this) {
          visitors.splice(i, 1);
        }
      }
    });

    socket.on('message', function (data) {
      console.log(data);
      console.log(messageCallback);
      messageCallback(data);
    });

  });
}