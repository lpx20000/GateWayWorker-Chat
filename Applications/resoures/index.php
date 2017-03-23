<script src="./static/my/jquery.js"></script>
<script src="./static/my/layer/layer.js"></script>
<script>
    $(document).ready(function () {
        layer.open({
            type: 2,
            title: false,
            closeBtn: 0, //不显示关闭按钮
            shade: [0],
            area: ['0px', '0px'],
            offset: 'rb', //右下角弹出
            time: 1000, //2秒后自动关闭
            anim: 2,
            content: ['http://gatewayworker.app/chat.php', 'no'], //iframe的url，no代表不显示滚动条
            end: function(){ //此处用于演示
                layer.open({
                    type: 2,
                    title: '聊天室',
                    shadeClose: true,
                    shade: false,
                    maxmin: true, //开启最大化最小化按钮
                    area: ['1200', '580'],
                    content: 'http://gatewayworker.app/chat.php'
                });
            }
        });
    })
</script>

