define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartDynamic, Pageheader) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify);
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', Pageheader);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#time",
                vuetify: new Vuetify(),
                data() {
                    return {
                        strings : content.strings,
                        groups : content.groups,
                        userid : content.userid,
                        courseid : content.courseid,
                        timezone : content.timezone,
                        render_has : content.profile_render,
                        loading : false,
                        errors : [],
                        pages : content.pages,
                        inverted_time: content.inverted_time,
                    }
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#time").style.display = "block";
                },
                computed :{

                },
                methods : {
                    get_help_content(){
                        let helpcontents = `Texto de Ayuda`;
                        return helpcontents;
                    },

                    update_interactions(week){
                        this.loading = true;
                        this.errors = [];
                        let data = {
                            action : "time",
                            userid : this.userid,
                            courseid : this.courseid,
                            weekcode : week.weekcode,
                            profile : this.render_has,
                        }
                        Axios({
                            method:'get',
                            url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                            params : data,
                        }).then((response) => {
                            if (response.status == 200 && response.data.ok) {
                                this.inverted_time = response.data.data.inverted_time;
                            } else {
                                this.error_messages.push(this.strings.error_network);
                            }
                        }).catch((e) => {
                            this.errors.push(this.strings.api_error_network);
                        }).finally(() => {
                            this.loading = false;
                        });
                        return this.data;
                    },

                    build_chart_inverted_time() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.title,
                        };
                        chart.xAxis = {
                            type: 'category',
                        };
                        chart.yAxis = {
                            title: {
                                enabled: true,
                                text: this.strings.x_axis,
                            }
                        };
                        chart.tooltip = {
                            formatter: function () {
                                return '<b>' + vue.strings.inverted_time + ': </b>'
                                    + vue.inverted_time.inverted_time_converted + '<br/>'
                                    + '<b>' + vue.strings.expected_time + ': </b>'
                                    + vue.inverted_time.expected_time_converted + '<br/>';
                            }
                        };
                        chart.legend = {
                            enabled: false
                        };
                        chart.series = [{
                            colorByPoint: true,
                            data: this.inverted_time.data
                        }];
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    get_timezone(){
                        let information = `${this.strings.ss_change_timezone} ${this.timezone}`
                        return information;
                    },
                }
            })
        }

        return {
            init : init
        };
    });