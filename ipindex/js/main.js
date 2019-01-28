apiUrl = 'http://127.0.0.1:8000';

hljs.initHighlightingOnLoad();

vm = new Vue({
    el: "#header",
    data: {
        area: "",
        ip: "",
        isp: "",
        target: "",
    },
    created() {
        this.getIP()
    },
    methods: {
        getIP() {
            this.doAction('');
        },
        searchIP() {
            this.doAction(vm.target);
        },
        doAction(text) {
            this.$http.get(apiUrl+"?ip="+text).then(function (res) {
                vm.ip = res.body.ip;
                vm.area = res.body.area;
                vm.isp = res.body.isp;
            }, function () {
                alert("抱歉，API 调用出错");
            });
        }

    }
});