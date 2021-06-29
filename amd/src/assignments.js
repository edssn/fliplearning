define(["local_fliplearning/vue",
        "local_fliplearning/vuetify",
        "local_fliplearning/axios",
        "local_fliplearning/moment",
        "local_fliplearning/pagination",
        "local_fliplearning/chartdynamic",
        "local_fliplearning/pageheader",
        "local_fliplearning/emailform",
        "local_fliplearning/helpdialog",
    ],
    function(Vue, Vuetify, Axios, Moment, Pagination, ChartDynamic, PageHeader, EmailForm, HelpDialog) {
        "use strict";

        function init(content) {
            // console.logs(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', PageHeader);
            Vue.component('emailform', EmailForm);
            Vue.component('helpdialog', HelpDialog);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#teacher_assignments",
                vuetify: new Vuetify(),
                data() {
                    return {
                        dialog: false,
                        selected_users: [],
                        modulename: "",
                        moduleid: false,
                        strings: content.strings,
                        groups: content.groups,
                        userid: content.userid,
                        courseid: content.courseid,
                        timezone: content.timezone,
                        render_has: content.profile_render,
                        loading: false,
                        errors: [],
                        pages: content.pages,
                        submissions: content.submissions,
                        email_strings: content.strings.email_strings,
                        emailComponent: "",
                        emailTarget: "",

                        access: content.access,
                        assigns_submissions_colors: content.assigns_submissions_colors,
                        access_content_colors: content.access_content_colors,
                        access_chart_categories: [],
                        access_chart_series: [],
                        access_chart_users: [],

                        help_dialog: false,
                        help_contents: [],

                        pluginSectionName: "teacher_assignments",
                        assigns_submissions_chart: "assigns_submissions_chart",
                        content_access_chart: "content_access_chart",
                    }
                },
                beforeMount() {
                    this.generate_access_content_data();
                },
                mounted() {
                    document.querySelector("#teacher_assignments_loader").style.display = "none";
                    document.querySelector("#teacher_assignments").style.display = "block";
                },
                methods: {
                    get_help_content() {
                        let contents = [];
                        contents.push({
                            title: this.strings.section_help_title,
                            description: this.strings.section_help_description,
                        });
                        return contents;
                    },

                    update_interactions(week) {
                        this.loading = true;
                        this.errors = [];
                        let data = {
                            action: "assignments",
                            userid: this.userid,
                            courseid: this.courseid,
                            weekcode: week.weekcode,
                            profile: this.render_has,
                            url: window.location.href
                        }
                        Axios({
                            method: 'get',
                            url: M.cfg.wwwroot + "/local/fliplearning/ajax.php",
                            params: data,
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
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = {
                            text: null,
                        };
                        chart.colors = this.assigns_submissions_colors;
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
                            useHTML: true,
                            formatter: function() {
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
                                return '<small>' + label + '</small><br/>' + '<b>' + serie_name + ': </b>' +
                                    value + ' ' + students_label + '<br/>' + '<small>' + send_mail + '</small>';
                            }
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function() {
                                            vue.emailComponent = "";
                                            vue.emailTarget = "";

                                            let serie_name = this.category.split('</b>');
                                            serie_name = serie_name[0] || '';
                                            serie_name = serie_name.split('<b>');
                                            serie_name = serie_name[1] || '';
                                            vue.email_strings.subject = `${vue.email_strings.subject_prefix} - ${serie_name}`;
                                            vue.emailComponent = vue.assigns_submissions_chart;
                                            vue.emailTarget = this.series.name;

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
                            style: { fontFamily: 'poppins' },
                        };
                        chart.title = { text: null };
                        chart.colors = this.access_content_colors;
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
                            useHTML: true,
                            formatter: function() {
                                let label = this.x;
                                let serie_name = this.series.name;
                                let value = this.y;
                                let students_label = vue.strings.students_text;
                                let send_mail = vue.strings.send_mail;
                                if (value == 1) {
                                    students_label = vue.strings.student_text;
                                }
                                let text = '<small>' + label + '</small><br/>' + '<b>' + serie_name + ': </b>' +
                                    value + ' ' + students_label + '<br/>' + '<small>' + send_mail + '</small>';
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
                                        click: function() {
                                            vue.emailComponent = "";
                                            vue.emailTarget = "";

                                            let serie_name = this.category;
                                            vue.email_strings.subject = `${vue.email_strings.subject_prefix} - ${serie_name}`;
                                            vue.emailComponent = vue.content_access_chart;
                                            vue.emailTarget = this.series.name;

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

                    update_dialog(value) {
                        this.dialog = value;
                    },

                    generate_access_content_data() {
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

                    open_chart_help(chart) {
                        let contents = [];
                        if (chart == this.assigns_submissions_chart) {
                            contents.push({
                                title: this.strings.assigns_submissions_help_title,
                                description: this.strings.assigns_submissions_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.assigns_submissions_help_description_p2,
                            });
                        } else if (chart == this.content_access_chart) {
                            contents.push({
                                title: this.strings.access_content_help_title,
                                description: this.strings.access_content_help_description_p1,
                            });
                            contents.push({
                                description: this.strings.access_content_help_description_p2,
                            });
                        }
                        this.help_contents = contents;
                        if (this.help_contents.length) {
                            this.help_dialog = true;
                            this.saveInteraction (chart, "viewed", "chart_help_dialog", 7);
                        }
                    },

                    update_help_dialog(value) {
                        this.help_dialog = value;
                    },

                    saveInteraction (component, interaction, target, interactiontype) {
                        let data = {
                            action : "saveinteraction",
                            pluginsection : this.pluginSectionName,
                            component,
                            interaction,
                            target,
                            url: window.location.href,
                            interactiontype,
                            courseid : this.courseid,
                            userid : this.userid,
                        };
                        Axios({
                            method:'get',
                            url: `${M.cfg.wwwroot}/local/fliplearning/ajax.php`,
                            params : data,
                        }).then((r) => {}).catch((e) => {});
                    },

                    get_timezone() {
                        let information = `${this.strings.change_timezone} ${this.timezone}`
                        return information;
                    },
                }
            })
        }

        return {
            init: init
        };
    });