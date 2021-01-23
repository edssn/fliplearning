define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartcomponent",
        "local_fliplearning/pageheader",
        "local_fliplearning/emailform",
        "local_fliplearning/alertify",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, Chart, Pageheader, Emailform, Alertify) {
        "use strict";

        function init(content) {
            console.log(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', Chart);
            Vue.component('pageheader', Pageheader);
            Vue.component('emailform', Emailform);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#submissions",
                vuetify: new Vuetify(),
                data() {
                    return {
                        dialog : false,
                        selected_users : [],
                        moduleid : false,
                        strings : content.strings,
                        groups : content.groups,
                        userid : content.userid,
                        courseid : content.courseid,
                        timezone : content.timezone,
                        render_has : content.profile_render,
                        loading : false,
                        errors : [],
                        pages : content.pages,
                        submissions: content.submissions,
                        email_strings: content.strings.email_strings,
                    }
                },

                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#submissions").style.display = "block";
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
                            action : "assignments",
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
                                this.submissions = response.data.data.submissions;
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

                    build_chart_submissions() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.assignsubs_chart_title,
                        };
                        chart.xAxis = {
                            categories: this.submissions.categories,
                            crosshair: true,
                        };
                        chart.yAxis = {
                            min: 0,
                            title: {
                                text: this.strings.assignsubs_chart_yaxis
                            }
                        };
                        chart.tooltip = {
                            valueSuffix: " estudiantes",
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                    point: {
                                    events: {
                                        click: function () {
                                            let serie_name = this.category.split('</b>');
                                            serie_name = serie_name[0] || '';
                                            serie_name = serie_name.split('<b>');
                                            serie_name = serie_name[1] || '';
                                            vue.email_strings.subject = vue.email_strings.subject_prefix+" - "+serie_name;

                                            let x = this.x;
                                            let column = this.series.colorIndex;
                                            vue.dialog = true;
                                            vue.selected_users = vue.submissions.users[x][column];
                                            vue.moduleid = vue.submissions.modules[x];
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = this.submissions.data;
                        chart.credits = {
                            enabled: false
                        };
                        chart.lang = {
                            noData: this.strings.no_data,
                        };
                        return chart;
                    },

                    update_dialog (value) {
                        this.dialog = value;
                    }
                }
            })
        }

        return {
            init : init
        };
    });