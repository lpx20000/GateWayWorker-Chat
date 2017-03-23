<!DOCTYPE html>
<html>
<head>
    <meta content="text/html; charset=UTF-8" http-equiv="Content-Type">
    <meta charset="utf-8">
    <meta name="renderer" content="webkit">
    <link media="all" href="./static/css/style.css?v=2222" type="text/css" rel="stylesheet">
    <link media="all" href="./static/css/shake.css?v=2222" type="text/css" rel="stylesheet">
    <link rel="stylesheet" href="./test/layui/build/css/layui.css"  media="all">
</head>
<body>
        <div id="body">
            <div id="menu-pannel">
                <div class="profile"></div>
                <ul class="main-menus" id="main-menus"></ul>
            </div>
            <div id="menu-pannel-body">
                <div id="sub-menu-pannel" class="conv-list-pannel">
                    <div class="conv-lists-box" id="user-lists" style="display: none">
                        <div class="layui-collapse" lay-filter="test" style="width: auto">
                            <div class="layui-colla-item">
                                <h2 class="layui-colla-title">在线用户列表</h2>
                                <div class="layui-colla-content">
                                    <div class="conv-lists"><a href="#" id="conv-lists"></a></div>
                                </div>
                            </div>
                            <div class="layui-colla-item">
                                <h2 class="layui-colla-title">私聊用户</h2>
                                <div class="layui-colla-content">
                                    <div class="conv-lists"><a href="#" id="private-lists"></a></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="content-pannel">
                    <div class="conv-detail-pannel">
                        <div class="nocontent-logo" style="display:none;" >
                            <div>
                                <img alt="欢迎" src="/static/images/noimg.png">
                            </div>
                        </div>
                        <div class="content-pannel-body chat-box-new" id="chat-box">
                            <div class="main-chat chat-items" id="chat-lists">
                                <div class="msg-items" id="chatLineHolder"></div>
                            </div>
                        </div>
                        <div>
                            <div class="send-msg-box-wrapper">
                                <div class="input-area" style="display:none;">
                                    <ul class="tool-bar">
                                        <li class="tool-item">
                                            <i class="iconfont tool-icon tipper-attached emotion_btn" title="表情"></i>
                                            <div class="faceDiv"></div>
                                        </li>
                                        <li class="tool-item">
                                            <i class="iconfont tool-icon icon-card tipper-attached" onclick="upload()" title="图片"></i>
                                        </li>
                                    </ul>
                                    <span class="user-guide">Enter 发送 , Ctrl+Enter 换行</span>
                                    <div class="msg-box" style="height:100%;">
                                        <textarea class="textarea input-msg-box" onkeydown="" id="chattext"></textarea>
                                    </div>
                                </div>
                                <div class="action-area" style="display:none;">
                                    <a href="javascript:;" class="send-message-button" id="sendMessage">发送</a>
                                </div>
                                <div id="loginbox" class="area" style="width:100%;text-align:center;display:block;">
                                    <form action="javascript:void(0)">
                                        <div class="clearfix" style="margin-top:35px">
                                            <input name="name" id="name" style="margin-right:20px;width:250px;" placeholder="请输入昵称" class="fm-input" value="" type="text">
                                            <input id="email" class="fm-input" style="margin-right:20px;width:250px;" name="email" placeholder="请输入Email" type="text">
                                            <button type="submit" class="blue big" id="login">登录</button>
                                        </div>

                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<script src="./static/my/jquery.js"></script>
<script src="./static/my/layer/layer.js"></script>
<script src="./test/layui/build/layui.js" charset="utf-8"></script>
<script src="./static/my/face.js"></script>
<script src="./static/my/template.js"></script>
<script src="./static/my/websocket.js"></script>
<script>
    layui.use(['element', 'layer'], function(){
        var element = layui.element();
        var layer = layui.layer;

        //监听折叠
        element.on('collapse(test)', function(data){
            return  socket.send(JSON.stringify({"type": "check","message": 'checkUserList'}));
        });
    });
</script>

</body>
</html>
