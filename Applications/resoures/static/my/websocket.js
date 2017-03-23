/**
 * Created by Administrator on 2017/3/15.
 */

$(document).ready(function () {
    socket.init();
});
var socket = {
    data: {
        socket: null,
        remind: '',
    },
    init: function () {
        this.data.socket = new WebSocket("ws://127.0.0.1:9501");
        this.start();
    },
    start: function () {
        this.open();
        this.message();
        this.close();
        this.error();
        this.sendMessage();
        this.login();
    },
    open: function () {
        this.data.socket.onopen = function (event) {
            console.log('open the socket');
        }
    },
    message: function (event) {
        this.data.socket.onmessage = function (event) {
           var message = jQuery.parseJSON(event.data);
            console.log(message);
            var userList = $('#conv-lists');
            var emptyUserList = $('#emptyUserList');
            switch (message.type) {
                case 'error':
                    socket.layerAlert(message.data.message, '错误', 2);
                    break;
                case 'success':
                    var info = message.data;
                    layer.msg(info.message);
                    var currentUser = $('#main-menus');
                    emptyUserList.remove();
                    currentUser.html(template.currentImage(info.user.id, info.user.name, info.user.image));
                    socket.userList(info);
                    break;
                case 'newUser':
                    var user = message.data.user;
                    emptyUserList.remove();
                    userList.append(template.newUser(user));
                    break;
                case 'message':
                    socket.chatAppend(message.data.user.image, message.data.user.name, message.data.message, message.data.user.time);
                    break;
                case 'remind':
                    layer.open({
                        title:false,
                        content: message.data.message,
                        btn: '我知道了'
                    });
                    break;
                case 'logout':
                    if (!$.isEmptyObject(message.data.user)) {
                        layer.msg(message.data.message);
                        var logout = $('#user_'+message.data.user.id);
                        logout.remove();
                        socket.send(JSON.stringify({"type": "check","message": 'checkUserList'}));
                    }
                    break;
                case 'check':
                    emptyUserList.remove();
                    var emptyUserListInfo = template.emptyUserList('当前暂无用户在线...');
                    userList.append(emptyUserListInfo);
                    break;
            }
        }
    },
    close: function () {
        this.data.socket.onclose = function () {
            layer.msg('服务器出错，请稍后重新登录');
        }
    },
    error: function () {
        this.data.socket.onerror = function () {
            layer.msg('服务器出错，请重新登录', '错误', 2);
        }
    },
    send: function (data) {
        if (this.data.socket.readyState == 1) {
            this.data.socket.send(data);
        }
    },
    sendMessage: function () {
        $('#sendMessage').bind("click", function () {
            var chatText = $('#chattext');

            var message = chatText.val();
            if ($.trim(message)) {
                //标签处理
                var messageFace = face.checkFaceMessage(message);

                var safeMessage = socket.removeJsCode(messageFace);
                //群聊
                if (safeMessage.indexOf("@") >= 0) {
                    // var currentMessage = chatText.val() + '<span style="color: red">'+'@' + remindUser + '</span>';
                    // safeMessage = safeMessage.replace(/@/, '<span style="color: red">'+'@' + remindUser + '</span>');

                    var pattern = /@(.*?)\s/g;
                    var match = safeMessage.match(pattern);
                    for (var i in match) {
                        console.log(match[i]);
                        safeMessage = safeMessage.replace(match[i], '<span style="color: red">'+match[i]+'</span>');
                    }
                }
                socket.currentUserSay(safeMessage);
                chatText.val('');
                return  socket.send(JSON.stringify({"type": "all","message": safeMessage}));
            }
            layer.msg('消息不得为空...');
        });
    },
    userList: function (info) {
        var userList = $('#conv-lists');
        var allUser = info.allUser;
        $.each(allUser, function (index, value) {
            if (info.user.name != value.name){
                userList.append(template.userList(value));
            }
        });
    },
    currentUserSay:function (message) {
        var currentUser = $('#currentUser');
        var img = currentUser.attr('img');
        var name = currentUser.attr('title');
        var uid = currentUser.attr('uid');
        this.currentAppend(img, name, message);
    },
    currentAppend:function (img, name, message) {
        // var myDate = new Date();
        // var nowTime = myDate.toLocaleDateString() + '&nbsp;&nbsp;' +  myDate.toLocaleTimeString();
        var chatBody = $('#chatLineHolder');
        chatBody.append(template.currentUser(img, name, message));
        var chatScroll = document.getElementById('chat-lists');
        chatScroll.scrollTop = chatScroll.scrollHeight;
    },
    chatAppend: function (img, name, message, time) {
        var chatBody = $('#chatLineHolder');
        chatBody.append(template.otherUser(img, name, message, time));
    },
    remindUser: function (obj) {
        var remindUser = $(obj).attr('uname');
        var chatText = $('#chattext');
        // var currentMessage = chatText.val() + '<span style="color: red">'+"@" + remindUser+'</span>';
        // var currentMessage = chatText.val() + '<span style="color: red">'+'@' + remindUser + '</span>';
        var currentMessage = chatText.val() +'@' + remindUser + ' ';
        chatText.val(currentMessage);
    },
    removeJsCode: function (message) {
        var newMessage = message.replace(/\<script\>/, '&lt;script&gt;');
        newMessage = newMessage.replace(/\<\/script\>/, '&lt;\/script&gt;');
        return newMessage;
    },
    login: function () {
        $('#login').bind("click", function (event) {
            var name = $('#name').val();
            var email = $('#email').val();
            if (name && email) {
                socket.send(JSON.stringify({"type": "login","name": name,"email": email}));
                return socket.showTable();
            }
            //layer提示
            socket.layerAlert('用户名或邮箱不得为空', '错误', 2);
        });
    },
    talkPrivate: function (id) {
        // console.log($('#private_'+id));
        // // if ($('#private_'+id)) return;
        // var private = $('#user_'+id);
        // var obj = {};
        // obj.id = id;
        // obj.image = private.find('img').attr('src');
        // obj.name = private.find('h4').html();
        // obj.login_at = private.find('span').attr('login_at');
        // var privateUser = template.privateTalk(obj);
        // var privateList = $('#private-lists');
        // privateList.append(privateUser);
        // $('.layui-colla-content').addClass('layui-show');
        // $('#private_'+id).css('background-color', 'rgb(189, 190, 191)');
        // $('#chattext').focus();
    },
    showTable: function () {
        $('#loginbox').css('display', 'none');
        $('.action-area').css('display', 'block');
        $('.input-area').css('display', 'block');
        $('#user-lists').css('display', 'block');
    },
    layerAlert: function (message, title, icon) {
        layer.alert(message, {
            title: title,
            skin: 'layui-layer-molv' ,
            icon: icon,
            closeBtn: 1,
            time: 3000
        });
    },
};



