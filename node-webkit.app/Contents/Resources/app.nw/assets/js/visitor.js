(function () {

  var socket;

  var _id = '';
  var _name = '';
  var _target = '';
  var _current = '';

  // not mine......

var colorList = ["rgb(104,63,37)","rgb(207,129,60)","rgb(232,203,75)","rgb(140,158,79)","rgb(64,110,140)","rgb(80,49,81)","rgb(185,42,86)"];
var currentNumber;

window.onload = function(){
  initBtnColor();
  initChatBtn();
  initCanvasBtn();
}
function initBtnColor(){
  var userDivList = document.getElementsByClassName("user-div");

  for (var i = userDivList.length - 1; i >= 0; i--) {
    userDivList[i].style.backgroundColor = colorList[i%colorList.length];
  };
}

function initChatBtn(){
  var chatBtnList = document.getElementsByClassName("chat-btn");

  currentNumber=-1;
  for (var i = chatBtnList.length - 1; i >= 0; i--) {
    chatBtnList[i].clicked=false;
    chatBtnList[i].number = i;
    chatBtnList[i].onclick = function(){
      if(currentNumber==-1||currentNumber==this.number){
        currentNumber = this.number;
        if(this.clicked){
            chatHide();
            currentNumber =-1;
          }else{
            chatShow();
          }
          this.clicked = !this.clicked;
      }
    }
  };

  function chatShow(){
      var wrap = document.getElementById("user-list");
      var aside = document.getElementById("chat-wrap");

      if(wrap!=null&&aside!=null)
      {
        initChatView();

        addClass(aside,"active");
        addClass(wrap,"scale");
      }
    }
    function chatHide(){
      var wrap = document.getElementById("user-list");
      var aside = document.getElementById("chat-wrap");

      if(wrap!=null&&aside!=null)
      {
        removeClass(aside,"active");
        removeClass(wrap,"scale");

        setTimeout(resetChat,400);
      }
    }
}

function initChatView(){
  var chatName = document.getElementById("chat-name");

  chatName.innerHTML = "与"+document.getElementsByClassName("user-name")[currentNumber].innerHTML+"交谈中";
  chatName.style.color = colorList[currentNumber%colorList.length];
  document.getElementById("visitor-input").style.backgroundColor = colorList[currentNumber%colorList.length];

  _current = document.getElementsByClassName("user-name")[currentNumber].id;
}

function appendChat(type,words){
  var arr="right",style="";
  switch(type){
    case 1:
      arr = "left";
      style = "style='background-color:"+colorList[currentNumber%colorList.length]+";'";
      break;
    case 2:
      arr = "right";
      break;
  }


  var item = 
  "<div class='chat-item'>"+
    "<div class='"+arr+"-chat' "+style+" >"+
        words+
    "</div>"+
  "</div>";

  document.getElementsByClassName("chat-content-inner")[0].innerHTML+=item;

}

function resetChat(){
  document.getElementsByClassName("chat-content-inner")[0].innerHTML = "";
  document.getElementById("visitor-input").value = null;
}

function initCanvasBtn(){
  var canvasBtn = document.getElementsByClassName("canvas-btn")[0];
  canvasBtn.clicked= false;
  canvasBtn.onclick = function(){
    if(this.clicked){
      canvasHide();
    }else{
      canvasShow();
    }
    this.clicked != this.clicked;
  }
  function canvasHide(){
    var wrap = document.getElementById("user-list");
      var aside = document.getElementById("canvasWrap");

      if(wrap!=null&&aside!=null)
      {
        removeClass(aside,"active");
        removeClass(wrap,"scaleLeft");
      }
  }
  function canvasShow(){
    var wrap = document.getElementById("user-list");
      var aside = document.getElementById("canvasWrap");

      if(wrap!=null&&aside!=null)
      {
        initCanvas();

        addClass(aside,"active");
        addClass(wrap,"scaleLeft");
      }
  }
}

function initCanvas(){
  var canvasWrap = document.getElementById("canvasWrap");
  var canvas = document.getElementById("canvas");
  canvas.width = canvas.offsetWidth;
  canvas.height = canvas.offsetHeight;
  var context = canvas.getContext("2d");

  canvas.style.left = -parseInt(canvas.width/2)+parseInt(canvasWrap.offsetWidth/2)+"px";
  canvas.style.top = -parseInt(canvas.width/2)+parseInt(canvasWrap.offsetHeight/2)+"px";

  var canvasLeft = parseInt(canvas.style.left);
  var canvasTop = parseInt(canvas.style.top);

  document.getElementById("move").innerHTML = "x: " + canvasLeft + " y: "+ canvasTop ;


  var backX;
  var backY;
  canvas.addEventListener("touchstart",function(e){
    e.preventDefault();
    var touch = e.targetTouches[0];

    document.getElementById("p1").innerHTML = e.targetTouches[0].pageX +" "+ e.targetTouches[0].pageY;
    if(e.targetTouches[1]!=null)
    document.getElementById("p2").innerHTML = e.targetTouches[1].pageX +" "+ e.targetTouches[1].pageY;

    if(e.targetTouches[1]==null){

      backX = touch.pageX-canvasLeft;
      backY = touch.pageY-canvasTop;

    }else{
      backX = (e.targetTouches[0].pageX + e.targetTouches[1].pageX)/2-canvasLeft;
      backY = (e.targetTouches[0].pageY + e.targetTouches[1].pageY)/2-canvasTop;
    }
  });
  canvas.addEventListener("touchmove",function(e){
    e.preventDefault();
    var touch = e.targetTouches[0];

    if(e.targetTouches[1]==null){
      context.save();
      context.strokeStyle = "rgb(0,0,0)";
      context.moveTo(backX,backY);
      context.lineTo(touch.pageX-canvasLeft,touch.pageY-canvasTop);
      context.stroke();
      context.restore();

      backX = touch.pageX-canvasLeft;
      backY = touch.pageY-canvasTop;
    }else{
      var nowX = (e.targetTouches[0].pageX + e.targetTouches[1].pageX)/2 -canvasLeft;
      var nowY = (e.targetTouches[0].pageY + e.targetTouches[1].pageY)/2 -canvasTop;

      document.getElementById("move").innerHTML = "x: " + nowX + " y: "+ nowY ;

      canvas.style.left = parseInt(canvas.style.left) + (nowX-backX) + "px";
      canvas.style.top = parseInt(canvas.style.top) + (nowY-backY) + "px";

      canvasLeft = parseInt(canvas.style.left);
      canvasTop = parseInt(canvas.style.top);

      backX = (e.targetTouches[0].pageX + e.targetTouches[1].pageX)/2-canvasLeft;
      backY = (e.targetTouches[0].pageY + e.targetTouches[1].pageY)/2-canvasTop;
    }

  });
  canvas.addEventListener("touchend",function(e){
    
  });
}

  // not mine end......

  // dom event handlers
  document.getElementById('visitor-login').onclick = function () {
    var value = document.getElementById('visitor-name').value;
    if (value != '') {
      document.getElementById('login-window').style.opacity = 0;
      document.getElementById('login-window').style.zIndex = -1;

      // socket start
      socket = io('http://0.0.0.0:3000');

      // socket event handlers
      socket.on('connect', function () {
        socket.emit('set_name', value);
      });

      socket.on('id', idHandler);
      socket.on('message', massageHandler);
      socket.on('whisper', whisperHandler);
      socket.on('all_user', allUserHandler);
      socket.on('new_user', newUserHandler);
      socket.on('user_leave', userLeaveHandler);
    }
  }

  document.getElementById('visitor-submit').onclick = function () {
    var name = document.getElementById('visitor-name').value;
    var content = document.getElementById('visitor-input').value;
    if (content != '') {
      var msg = JSON.stringify({
        origin : _id,
        target : document.getElementsByClassName("user-name")[currentNumber].id,
        name : name,
        content : content,
        time : Tool.getFormattedTime()
      });
      selfMassageHandler(msg)
      socket.emit('whisper', msg);
    }

    // 
    // appendChat(1,"这是一个左实例这是一个左实例这是一个左实例这是一个左实例");
    // appendChat(1,"这是一个左实例");
    // appendChat(1,"这是一个左实例这是一个左实例这是一个左实例这是一个左实例");
    // appendChat(1,"这是一个左实例");
    return false;
  }

  // functions
  function idHandler (id) {
    _id = id;
  }

  function massageHandler (data) {
    var msg = JSON.parse(data);
    // document.getElementById('message-container').innerHTML += '<p><span>'
    //   + msg.name + '&nbsp;&nbsp;' + msg.time
    //   + '</span><br><span>'
    //   + msg.content
    //   + '</span></p>';
  }

  function whisperHandler (data) {
    var msg = JSON.parse(data);
    var userList = document.getElementsByClassName('user-name');
    var chatWrap = document.getElementById('chat-wrap');
    var wrap = document.getElementById("user-list");
    var aside = document.getElementById("chat-wrap");
    if (_current != msg.origin) {
      removeClass(aside,"active");
      removeClass(wrap,"scale");
      setTimeout(resetChat,400);
    }
    if (chatWrap.className != 'active') {
      for (var i = 0; i < userList.length; i++) {
        if (userList[i].id == msg.origin) {
          currentNumber = i;
          break;
        }
      }
      initChatView();
      addClass(aside,"active");
      addClass(wrap,"scale");
      appendChat(1, msg.content);
    } else {
      appendChat(1, msg.content);
    }
    document.getElementById('chat-content').scrollTop = 1000000000;
  }

  function selfMassageHandler (data) {
    var msg = JSON.parse(data);
    appendChat(2, msg.content);
    document.getElementById('visitor-input').value = '';
    document.getElementById('chat-content').scrollTop = 1000000000;
    // document.getElementById('message-container').innerHTML += '<p><span>'
    //   + msg.name + '&nbsp;&nbsp;' + msg.time
    //   + '</span><br><span>'
    //   + msg.content
    //   + '</span></p>';
  }

  function allUserHandler (all) {
    var users = JSON.parse(all);
    var list = document.getElementById('user-list');
    for (var i = 0; i < users.length; i++) {
      list.innerHTML += '<div class="user-div">' + 
        '<a class="chat-btn" href="javascirpt:;"></a>' + 
        '<p class="user-name" id="' + users[i].id + '">' + users[i].name + '</p>' +
        '</div>';
    }
    initBtnColor();
    initChatBtn();
  }

  function newUserHandler (data) {
    var user = JSON.parse(data);
    document.getElementById('user-list').innerHTML += '<div class="user-div">' + 
      '<a class="chat-btn" href="javascirpt:;"></a>' + 
      '<p class="user-name" id="' + user.id + '">' + user.name + '</p>' +
      '</div>';
    initBtnColor();
    initChatBtn();
  }

  function userLeaveHandler (data) {
    var user = JSON.parse(data);
    var userList = document.getElementById('user-list');
    var userName = document.getElementsByClassName('user-name');
    for (var i = 0; i < userName.length; i++) {
      if (userName[i].id == user.id) {
        userList.removeChild(userName[i].parentNode);
        break;
      }
    }
  }

})();