define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
        "local_fliplearning/emailform",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartDynamic, Pageheader, Emailform) {
        "use strict";

        function init(content) {
            // console.log(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
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
                        modulename : "",
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

                        access: content.access,
                        access_chart_categories: [],
                        access_chart_series: [],
                        access_chart_users: [],
                    }
                },
                beforeMount(){
                    this.generate_access_content_data();
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#submissions").style.display = "block";
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
                                this.access = response.data.data.access;
                                this.generate_access_content_data();
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

                    build_assigns_submissions_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: null,
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.xAxis = {
                            categories: this.submissions.categories,
                            crosshair: true,
                        };
                        chart.yAxis = {
                            min: 0,
                            title: {
                                text: this.strings.assignsubs_chart_yaxis
                            },
                            allowDecimals: false,
                        };
                        chart.tooltip = {
                            formatter: function () {
                                let label = this.x.split('</b>');
                                label = label[0] || '';
                                label = label.split('<b>');
                                label = label[1] || '';
                                let serie_name = this.series.name;
                                let value = this.y;
                                let students_label = vue.strings.students_text;
                                let send_mail = vue.strings.send_mail;
                                if (value == 1) {
                                    students_label = vue.strings.student_text;
                                }
                                let text = '<b>' + label +'</b><br/>' + '<b>' + serie_name +': </b>' +
                                            value + ' ' + students_label + '<br/>' + send_mail;
                                return text;
                            }
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
                                            vue.modulename = "assign";
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = this.submissions.data;
                        return chart;
                    },

                    build_access_content_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: null,
                        };
                        chart.title = {text: null};
                        chart.xAxis = {
                            categories: this.access_chart_categories,
                            title: { text: null },
                            crosshair: true,
                        };
                        chart.yAxis = {
                            min: 0,
                            title: {
                                text: this.strings.access_chart_yaxis_label,
                            },
                            labels: {
                                overflow: 'justify'
                            },
                            allowDecimals: false,
                        };
                        chart.tooltip = {
                            formatter: function () {
                                let label = this.x;
                                let serie_name = this.series.name;
                                let value = this.y;
                                let students_label = vue.strings.students_text;
                                let send_mail = vue.strings.send_mail;
                                if (value == 1) {
                                    students_label = vue.strings.student_text;
                                }
                                let text = '<b>' + label +'</b><br/>' + '<b>' + serie_name +': </b>' +
                                    value + ' ' + students_label + '<br/>' + send_mail;
                                return text;
                            }
                        };
                        chart.plotOptions = {
                            bar: {
                                dataLabels: {
                                    enabled: false
                                }
                            },
                            series: {
                                cursor: 'pointer',
                                    point: {
                                    events: {
                                        click: function () {
                                            let serie_name = this.category;
                                            vue.email_strings.subject = vue.email_strings.subject_prefix+" - "+serie_name;
                                            let x = this.x;
                                            let column = this.series.colorIndex;
                                            let users = vue.get_users(vue.access_chart_users[x][column]);
                                            vue.selected_users = users;
                                            let module = vue.get_moduletype(this.category);
                                            vue.modulename = module.type;
                                            vue.moduleid = module.id;
                                            vue.dialog = true;
                                        }
                                    }
                                }
                            }
                        };
                        chart.series = this.access_chart_series;
                        return chart;
                    },

                    update_dialog (value) {
                        this.dialog = value;
                    },

                    generate_access_content_data () {
                        let usersIds = [];
                        this.access.users.forEach(user => {
                            usersIds.push(Number(user.id));
                        });
                        let selected_types_labels = [];
                        this.access.types.forEach(item => {
                            if (item.show) {
                                selected_types_labels.push(item.type);
                            }
                        });
                        let selected_modules = [];
                        this.access.modules.forEach(module => {
                            if (selected_types_labels.includes(module.type)) {
                                selected_modules.push(module);
                            }
                        });
                        let categories = [];
                        let modules_users = [];
                        let access_users_data = [];
                        let no_access_users_data = [];
                        selected_modules.forEach(module => {
                            categories.push(module.name);
                            let access_users = module.users;
                            let no_access_users = usersIds.filter(x => !access_users.includes(x));
                            access_users_data.push(access_users.length);
                            no_access_users_data.push(no_access_users.length);
                            modules_users.push([access_users, no_access_users]);
                        });
                        let series = [
                            { name: this.strings.access, data: access_users_data },
                            { name: this.strings.no_access, data: no_access_users_data },
                        ];
                        this.access_chart_categories = categories;
                        this.access_chart_series = series;
                        this.access_chart_users = modules_users;
                    },

                    get_users(ids) {
                        let users = [];
                        this.access.users.forEach(user => {
                            let userid = Number(user.id);
                            if (ids.includes(userid)) {
                                users.push(user);
                            }
                        });
                        return users;
                    },

                    get_moduletype(modulename) {
                        let mod;
                        this.access.modules.forEach(module => {
                            if (module.name === modulename) {
                                mod = module;
                            }
                        });
                        return mod;
                    },

                    info() {
                        console.log('Open modal');
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