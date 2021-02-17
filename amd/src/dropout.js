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
            console.log(content);
            Vue.use(Vuetify)
            Vue.component('pagination', Pagination);
            Vue.component('chart', ChartDynamic);
            Vue.component('pageheader', Pageheader);
            let vue = new Vue({
                delimiters: ["[[", "]]"],
                el: "#dropout",
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

                        dropout: content.dropout,
                        selected_cluster: [],
                        cluster_users: [],
                        selected_user: {},
                        search: null,
                        week_modules_chart_data: [],
                        week_modules_chart_categories: [],
                    }
                },
                beforeMount(){
                    if (this.dropout.clusters.length) {
                        this.set_modules_in_sections();
                        this.selected_cluster = this.dropout.clusters[0];
                        this.change_cluster(this.selected_cluster.users);
                    };
                },
                mounted(){
                    document.querySelector("#sessions-loader").style.display = "none";
                    document.querySelector("#dropout").style.display = "block";
                },
                computed :{

                },
                methods : {
                    get_help_content(){
                        let helpcontents = `Texto de Ayuda`;
                        return helpcontents;
                    },

                    set_modules_in_sections() {
                        let sectionsMap = new Map();
                        let sectionid = 0;
                        this.dropout.cms.forEach(cm => {
                            sectionid = Number(cm.section);
                            if (!sectionsMap.has(sectionid)) {
                                sectionsMap.set(sectionid, [cm]);
                            } else {
                                sectionsMap.get(sectionid).push(cm);
                            }
                        });
                        this.dropout.sections.forEach(section => {
                            sectionid = Number(section.sectionid);
                            section.sectionid = sectionid;
                            section.modules = sectionsMap.get(sectionid);
                        });
                    },

                    change_cluster(users) {
                        let selected_users = [];
                        this.dropout.users.forEach(user => {
                            if (users.includes(user.id)) {
                                selected_users.push(user);
                            }
                        });
                        this.cluster_users = selected_users;
                        this.selected_user = this.cluster_users[0] || {};
                        // console.log(this.cluster_users);
                        console.log(this.selected_user);
                        this.calculate_modules_access_by_week();
                    },

                    build_modules_access_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'bar',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.modules_access_chart_title,
                        };
                        chart.xAxis = {
                            type: 'category',
                        };
                        chart.yAxis = {
                            allowDecimals: false,
                            title: {
                                enabled: true,
                                text: this.strings.modules_amount,
                            }
                        };
                        chart.tooltip = {
                            formatter: function () {
                                let module_text = (this.y == 1) ? vue.strings.module_label : vue.strings.modules_label;
                                return '<b>' + this.key + '</b>: ' + this.y + ' ' + module_text + '<br/>'
                                    + '<i>'+ vue.strings.modules_details + '<i/>';
                            }
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                    point: {
                                    events: {
                                        click: function () {
                                            console.log(this);
                                            vue.open_modules_modal(this.x);
                                            // alert('Category: ' + this.category + ', value: ' + this.y);
                                        }
                                    }
                                }
                            }
                        },
                        chart.legend = {
                            enabled: false
                        };
                        chart.series = [{
                            colorByPoint: true,
                            data: [
                                {name: this.strings.modules_access_chart_series_viewed, y: this.selected_user.cms.viewed},
                                {name: this.strings.modules_access_chart_series_complete, y: this.selected_user.cms.complete},
                                {name: this.strings.modules_access_chart_series_total, y: this.selected_user.cms.total}
                            ]
                        }];
                        return chart;
                    },

                    build_week_modules_chart() {
                        let chart = new Object();
                        chart.chart = {
                            type: 'column',
                            backgroundColor: '#FAFAFA',
                        };
                        chart.title = {
                            text: this.strings.week_modules_chart_title,
                        };
                        chart.xAxis = {
                            categories: this.week_modules_chart_categories,
                            title: {
                                text: null
                            },
                            crosshair: true
                        };
                        chart.yAxis = {
                            allowDecimals: false,
                            title: {
                                text: this.strings.modules_amount,
                            }
                        };
                        chart.tooltip = {
                            shared: true,
                            crosshair: true,
                        };
                        chart.plotOptions = {
                            series: {
                                cursor: 'pointer',
                                point: {
                                    events: {
                                        click: function () {
                                            console.log(this);
                                            vue.open_modules_modal(this.colorIndex, this.x);
                                            // alert('Category: ' + this.category + ', value: ' + this.y);
                                        }
                                    }
                                }
                            }
                        },
                        // chart.tooltip = {
                        //     formatter: function () {
                        //         let module_text = (this.y == 1) ? vue.strings.module_label : vue.strings.modules_label;
                        //         return '<b>' + this.key + '</b>: ' + this.y + ' ' + module_text + '<br/>'
                        //             + '<i>'+ vue.strings.modules_details + '<i/>';
                        //     }
                        // };
                        chart.series = this.week_modules_chart_data;
                        return chart;
                    },

                    calculate_modules_access_by_week() {
                        let sectionid = 0, moduleid = 0, weekcompletecms = 0, weekviewedcms = 0;
                        let modules = [], completecms = [], viewedcms = [], categories = [];
                        let user_cm;
                        this.dropout.weeks.forEach(week => {
                            // console.log({week});
                            weekcompletecms = 0, weekviewedcms = 0;
                            week.sections.forEach(section => {
                                // console.log({section});
                                sectionid = Number(section.sectionid);
                                section.sectionid = sectionid;

                                modules = this.get_section(sectionid);
                                modules.forEach(module => {
                                    // console.log({module});
                                    moduleid = Number(module.id);
                                    module.id = moduleid;

                                    user_cm = this.get_user_module(moduleid);
                                    // console.log(user_cm);
                                    if (user_cm) {
                                        (user_cm.complete) && weekcompletecms++;
                                        (user_cm.viewed) && weekviewedcms++;
                                    }

                                });

                            });
                            completecms.push(weekcompletecms);
                            viewedcms.push(weekviewedcms);
                            categories.push(`${week.name} ${(week.position + 1)}`);
                        });
                        // console.log({completecms, viewedcms});
                        this.week_modules_chart_categories = categories;
                        this.week_modules_chart_data = [
                            { name: this.strings.modules_access_chart_series_viewed, data: viewedcms },
                            { name: this.strings.modules_access_chart_series_complete, data: completecms }
                        ];
                    },

                    open_modules_modal(type, week){
                        console.log({type, week});
                    },

                    get_section(sectionid) {
                        let modules = [];
                        let sections = this.dropout.sections;
                        for (let i = 0; i < sections.length; i++) {
                            if (sections[i].sectionid == sectionid) {
                                modules = sections[i].modules;
                                break;
                            }
                        }
                        return modules;
                    },

                    get_user_module(moduleid) {
                        let module;
                        let cms = this.selected_user.cms.cms;
                        for (let i = 0; i < cms.length; i++) {
                            cms[i].id = Number(cms[i].id);
                            if (cms[i].id == moduleid) {
                                module = cms[i];
                                break;
                            }
                        }
                        return module;
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

                    table_headers(){
                        let headers = [
                            { text: '', value : 'id', align : 'center', sortable : false},
                            { text: this.strings.thead_name , value : 'firstname'},
                            { text: this.strings.thead_lastname , value : 'lastname'},
                            { text: this.strings.thead_progress , value : 'progress_percentage'},
                        ];
                        return headers;
                    },

                    change_user(user) {
                        this.selected_user = user;
                        this.calculate_modules_access_by_week();
                    },

                    get_picture_url(userid){
                        return `${M.cfg.wwwroot}/user/pix.php?file=/${userid}/f1.jpg`;
                    },

                    get_user_fullname(){
                        return `${this.selected_user.firstname} ${this.selected_user.lastname}`;
                    },

                    get_username(){
                        return `@${this.selected_user.username}`;
                    },

                    see_profile () {
                        let id = this.selected_user.id;
                        let url = M.cfg.wwwroot + '/user/view.php?id='+id+'&course='+vue.courseid;
                        window.open(url);
                    },

                    get_progress_message(){
                        let module_label = this.strings.modules_label;
                        let finished_label = this.strings.finisheds_label;
                        if (this.selected_user.cms.complete == 1) {
                            module_label = this.strings.module_label;
                            finished_label = this.strings.finished_label;
                        }
                        return `${this.selected_user.cms.complete} ${module_label} ${finished_label} ${this.strings.of_conector} ${this.dropout.total_cms}`;
                    },

                    get_progress_percentage() {
                        return `${this.selected_user.progress_percentage} %`;
                    },

                    get_student_grade() {
                        let grade = this.selected_user.coursegrade;
                        grade.finalgrade = Number(grade.finalgrade);
                        grade.maxgrade = Number(grade.maxgrade);
                        let student_grade = this.isInt(grade.finalgrade) ? grade.finalgrade : grade.finalgrade.toFixed(2);
                        let max_grade = this.isInt(grade.maxgrade) ? grade.maxgrade : grade.maxgrade.toFixed(2);
                        return `${student_grade}/${max_grade}`;
                    },

                    isInt(n) {
                        return n % 1 === 0;
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