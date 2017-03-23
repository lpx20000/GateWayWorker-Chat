/**
 * Created by Administrator on 2017/3/17.
 */

    var template = {
        //用户列表
        userList: function (value) {
            // '<div id="user_'+value.id+'"  class="small-52 with-border user-avatar">' +
            return '<div id="user_'+value.id+'" onclick="socket.talkPrivate('+value.id+')" class="user-avatar">' +
                '<img style="border-radius: 50%;width: 40px;margin-left: 10px;" src="'+value.image+'" alt="">' +
                '<h4 style="display: inline-block;position: relative;left: 10px;">'+value.name+'' +
                '</h4>&nbsp;&nbsp;' +
                '<span login_at = "'+value.login_at+'" style="font-size: 3px;float: right;color: rgb(189, 190, 191);">'+value.login_at.slice(11)+'</span><hr style="color: red"><div>';
        },
       //当前无在线用户
        emptyUserList:function (value) {
            return '<p id="emptyUserList" style="padding-left: 10px;color:rgb(189, 190, 191)">'+value+'</p>';
        },
        //新用户进来
        newUser: function (user) {
            return '<div id="user_'+user.id+'" onclick="socket.talkPrivate('+user.id+')"  class="user-avatar">' +
                '<img style="border-radius: 50%;width: 40px;margin-left: 10px;" src="'+user.image+'" alt="">' +
                '<h4 style="display: inline-block;position: relative;left: 10px;">'+user.name+'' +
                '</h4>&nbsp;&nbsp;' +
                '<span login_at = "'+user.login_at+'" style="font-size: 3px;padding-top:10px;float: right;color: rgb(189, 190, 191);">'+user.login_at.slice(11)+'</span><hr style="color: red"><div>';
        },
        //当前用户说话位置
        currentUser: function (img, name, message) {
          return '<div style="display: block;" class="msg-box">' +
            '<div class="chat-item me"><div class="clearfix"><div class="avatar">' +
            '<div class="normal user-avatar" style="background-image: url('+img+');">' +
            '</div></div><div class="msg-bubble-box"><div class="msg-bubble-area">' +
            '<div><div class="msg-bubble"><pre class="text">'+message+'</pre></div></div></div></div></div></div></div>';
        },
        //用户私聊
        privateTalk: function (value) {
            return '<div id="private_'+value.id+'" class="user-avatar">' +
                '<img style="border-radius: 50%;width: 40px;margin-left: 10px;" src="'+value.image+'" alt="">' +
                '<h4 style="display: inline-block;position: relative;left: 10px;">'+value.name+'' +
                '</h4>&nbsp;&nbsp;' +
                '<span style="font-size: 3px;float: right;color: rgb(189, 190, 191);">'+value.login_at.slice(11)+'</span><hr style="color: red"><div>';
        },
        //接受其他用户消息
        otherUser: function (img, name, message, time) {
            return '<div style="display: block;" class="msg-box"><div class="chat-item not-me"><div class="chat-profile-info clearfix">' +
            '<span class="profile-wrp"><span class="name clearfix"><span class="name-text">'+name+'</span></span></span>' +
            '<span class="chat-time">'+time+'</span></div><div class="clearfix"><div class="avatar">' +
            '<div class="normal user-avatar" onclick="socket.remindUser(this)" uname="'+name+'" style="background-image: url('+img+');">' +
            '</div></div><div class="msg-bubble-box"><div class="msg-bubble-area">' +
            '<div class="msg-bubble"><pre class="text">'+message+'</pre></div></div></div></div></div></div>';
        },
        //当其用户登录成功头像位置
        currentImage: function (id, name, image) {
            return '<li id="currentUser" class="big-52 with-border user-avatar" uid="'+id+'"' +
                ' title="'+name+'" ' +
                'style="background-image: url('+image+');margin-left: 10px;" img="'+image+'"></li>';
        }
    };
