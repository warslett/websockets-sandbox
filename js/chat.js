var conn = new WebSocket('ws://localhost:8910');

conn.onopen = function (e) {
    console.log("Connection established!");
};

window.app = new Vue({
    el: '#app',
    data: {
        chathistory: [],
        message: ''
    },
    methods: {
        sendmsg: function (event) {
            conn.send(this.message);
        }
    }
});

conn.onmessage = function (e) {
    app.chathistory.push(e.data);
    app.message = "";
};